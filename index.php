<?php
///* one2drive
// * @author Yocen Chang
// * @2026-02-16
// * .config中存储本地配置文件，cache目录存取所有缓存文件，包含token等
// * utils中存储本地封装的一些函数等
// * services中封装调用微软函数的接口
// * theme中存储前端界面主题
// * */
?>
<?php
$config = require_once "./.config/config.php";
if(isset($_GET[$config['ADMIN_CODE']])){
    header("Location:./controller/admin/?".$config['ADMIN_CODE']);
}

require_once "./services/index.php";
require_once "./lib/readAccessToken.php";
require_once "./lib/readTimeStamp.php";
require_once "./lib/isTokenValid.php";
if(!isTokenValid()){
    $config['isSign']=false;
    header("Location:./controller/admin/oauth.php?refreshToken&redirect_uri=".urlencode($_SERVER['REQUEST_URI']));
}
$ACCESS_TOKEN=readAccessToken();
$SHARE_ROOT = $config['SHARE_ROOT'];

$queryString = $_SERVER['QUERY_STRING'];
$pathPart = explode('&', $queryString)[0];
$currentPath = !empty($pathPart) ? rawurldecode($pathPart) : '/';

$datas = getChildrenByPath($currentPath,false);
if(isset($datas)) $datas = isset($datas['value'])?$datas['value']:$datas;
//这种情况下可能是文件，也有可能是错误路径需要分情况处理
//1.文件
if($datas['@odata.null']){
    $datas = ["infos"=>[],"getUrl"=>null];
    $datas['infos'] = getItemByPath($currentPath,false);
    if(!isset($_GET['preview']))
        header("Location:".$datas['infos']['@microsoft.graph.downloadUrl']);
    $id = $_GET['preview'];
    $datas['getUrl']= getPreviewByID($id)['getUrl'];
}elseif($datas['error']){
    //2.仍然错误
    echo "<h1>Path&nbsp;&nbsp;Erro</h1>";
}
$theme=$config['THEME']??'nginx';

#过滤文件
$hiddenFileNames = array_filter(array_map('trim', explode(",", $config['HIDDEN_NAME_LIST'])));
$excludePattern = !empty($hiddenFileNames)
    ? '/(' . implode('|', array_map('preg_quote', $hiddenFileNames)) . ')/i'
    : null;#正则预编译
?>
<pre class="">
<?php

//echo json_encode($datas,JSON_PRETTY_PRINT|JSON_PRETTY_PRINT);
?>
</pre>
<?php
include_once "./theme/$theme.php";
?>











<?php

function formatSize($bytes) {
    if ($bytes <= 0) return "-"; // 文件夹通常不显示大小
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    $i = 0;
    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }
    return round($bytes, 2) . ' ' . $units[$i];
}
?>
