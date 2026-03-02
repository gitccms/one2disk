<?php
$config = require_once "../../.config/config.php";
require_once "../../lib/writeToken.php";
require_once "../../lib/readAccessToken.php";
require_once "../../lib/readRefreshToken.php";
// 路由控制
if(isset($_GET['login']))       Login($config['CLIENT_ID'],$config['REDIRECT_URI']);
if(isset($_GET['code']))         getToken($config['CLIENT_ID'],$config['CLIENT_SECRET'],$config['REDIRECT_URI']);
if(isset($_GET['refreshToken'])) refreshToken($config['CLIENT_ID'],$config['CLIENT_SECRET'],$config['REDIRECT_URI']);

if(isset($_GET['logout'])){
    $url = "https://login.microsoftonline.com/common/oauth2/v2.0/logout?post_logout_redirect_uri=".urlencode('https://api.yocen.top');
    header("Location: " . $url);
}

// 1. 登录跳转,getcode阶段
function Login($cid,$rurl)
{
    $url = "https://login.microsoftonline.com/Common/oauth2/v2.0/authorize?";
    $parameters = [
        'client_id' => $cid,
        'scope' => 'files.readwrite offline_access',
        'redirect_uri' => $rurl,
        'response_type' => 'code'
    ];
    header("Location: " . $url . http_build_query($parameters));
    exit;
}
// 2. 授权码换Token
function getToken($cid, $cs, $rurl)
{
    $code = $_GET['code'] ?? '';
    if (!$code) die("Missing Code");

    $url = "https://login.microsoftonline.com/Common/oauth2/v2.0/token";
    $parameters = [
        'client_id' => $cid,
        'redirect_uri' => $rurl,
        'client_secret' => $cs,
        'code' => $code,
        'grant_type' => 'authorization_code'
    ];

    try {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameters));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/x-www-form-urlencoded"]);

        $res = curl_exec($ch);
        $data = json_decode($res, true);
        curl_close($ch);

        if (isset($data['error'])) {
            echo "<h3 style='color: darkred'>授权失败</h3>";
            echo "<a href='?refreshToken'>手动刷新测试</a><br><br>";
            echo "<textarea style='width:90%;height:200px;'>" . $data['error_description'] . "</textarea>";
        } else {
            global $config;
            $config['isSgin']=true;
            $content = "<?php\nreturn ".var_export($config, true).";\n?>";
            file_put_contents($configPath, $content);

            writeToken($data['access_token'],$data['refresh_token'],$data['expires_in']);
            echo "<h3 style='color: darkgreen'>授权成功</h3>";
            echo "<a href='?refreshToken'>手动刷新测试</a><br><br>";
            echo "<textarea style='width:100%;height:300px;'>" . $data['access_token'] . "</textarea>";
            echo "<textarea style='width:100%;height:300px;'>" . $data['refresh_token'] . "</textarea>";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
// 3. 刷新 Token
function refreshToken($cid, $cs, $rurl)
{
    $rt = readRefreshToken() ?? '';
    if (!$rt) die("No Refresh Token in Cookie. Please Login again.");

    $url = "https://login.microsoftonline.com/Common/oauth2/v2.0/token";
    $parameters = [
        'client_id' => $cid,
        'redirect_uri' => $rurl,
        'client_secret' => $cs,
        'refresh_token' => $rt,
        'grant_type' => 'refresh_token'
    ];

    try {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameters));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/x-www-form-urlencoded"]);

        $res = curl_exec($ch);
        $data = json_decode($res, true);
        curl_close($ch);

        if (isset($data['error'])) {
            header("Content-Type: application/json");
            echo json_encode($data);
        } else {
            writeToken($data['access_token'], $data['refresh_token'],$data['expires_in']);
            if (isset($_GET['redirect_uri'])) {
                header("Location: " . $_GET['redirect_uri']);
            } else {
                if(isset($_GET['api'])){
                    header("Content-Type: application/json");
                    echo json_encode(["status" => "refreshed", "token" => $data['access_token']]);
                    return;
                }
                echo "<h3 style='color: darkgreen'>刷新成功</h3>";
                echo "<a href='?refreshToken'>手动刷新测试</a><br><br>";
                echo "<textarea style='width:100%;height:300px;'>" . $data['access_token'] . "</textarea>";
                echo "<textarea style='width:100%;height:300px;'>" . $data['refresh_token'] . "</textarea>";
            }
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
<?php   if(empty($_GET)){?>
您好
<?php }?>
