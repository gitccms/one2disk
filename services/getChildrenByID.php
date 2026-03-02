<?php
function getChildrenByID($id="",$thumbnails=false) {
    $url = "https://graph.microsoft.com/v1.0/me/drive/items/".rawurlencode($path).":/children";
    $url = $url . ($thumbnails?'?$expand=thumbnails':'');

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . readAccessToken(),
        'Content-Type: application/json'
    ]);

    $res = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($res, true);
//    $data = json_decode(json_encode(['value'=>$res,'url'=>$url],JSON_UNESCAPED_UNICODE), true);
    return $data;
}