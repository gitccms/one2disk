<?php
function getPreviewByPath($path="") {
    global $SHARE_ROOT;
    if($path==''||$path=='/'||$path=='//')
        $path='';
    $path=$SHARE_ROOT.$path;
    $url = "https://graph.microsoft.com/v1.0/me/drive/root:".$path.":/preview";
    echo "<textarea>";
    echo $url;
    echo "</textarea><textarea>";
    $url = "https://graph.microsoft.com/v1.0/me/drive/root:".rawurlencode("/WEB/Public/CODES/python/0OM.py").":/preview";
    echo $url;
    echo "</textarea>";
    $body = json_encode(new stdClass());
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body); // 关键：发送请求体
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . readAccessToken(),
        'Content-Type: application/json',
        'Content-Length: ' . strlen($body)
    ]);

    $res = curl_exec($ch);
    curl_close($ch);
    echo $res;

    $data = json_decode($res, true);
//    $data = json_decode(json_encode(['value'=>$res,'url'=>$url],JSON_UNESCAPED_UNICODE), true);
    return $data;
}