<?php
function writeToken($accessToken, $refreshToken,$expires_in=3600)
{
    try{
        $tokenData = [
            'time_stamp'=>time()+$expires_in,
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken
        ];
        file_put_contents(__DIR__.'/../.config/cache/token.json', json_encode($tokenData));
        return "success";
    }catch (Exception $e){
        return $e->getMessage();
    }
}
