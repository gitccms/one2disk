<?php
function getChildrenByPath($path="",$thumbnails=false) {
    global $SHARE_ROOT,$ACCESS_TOKEN;
    if($path==''||$path=='/'||$path=='//')
        $path='';
    $path=$SHARE_ROOT.$path;
    $url = "https://graph.microsoft.com/v1.0/me/drive/root:".rawurlencode($path).":/children";
    $url = $url . ($thumbnails?'?$expand=thumbnails':'');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $ACCESS_TOKEN,
        'Content-Type: application/json'
    ]);
    $res = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($res, true);
    return $data;
}