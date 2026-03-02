<?php
function readAccessToken()
{
    $tokenFilePath = __DIR__."/../.config/cache/token.json";
    $tokenJson = json_decode(file_get_contents($tokenFilePath),true);
    return $tokenJson['access_token'];
}
return readAccessToken();
?>