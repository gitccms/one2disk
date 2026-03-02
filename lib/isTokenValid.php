<?php
function isTokenValid()
{
    $expireTime = (int)readTimeStamp()??0;
    $nowTime = time();
    return $expireTime-$nowTime>300;
}
?>