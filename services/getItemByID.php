<?php
function getItemByID($id) {
    $url = "https://graph.microsoft.com/v1.0/me/drive/items/".$id;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . readAccessToken(),
        'Content-Type: application/json'
    ]);

    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true);
}
?>