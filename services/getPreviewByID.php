<?php
function getPreviewByID($id) {
    $url = "https://graph.microsoft.com/v1.0/me/drive/items/".$id."/preview";
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
    // 调试用：如果报错，看这里
    // $info = curl_getinfo($ch);
    curl_close($ch);
    return json_decode($res, true);
}
?>