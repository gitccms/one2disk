<?php
session_start();
function isAdmin()
{
    return $_SESSION['isAdmin'];
}
function isLogin()
{
    return $_SESSION['isLogin'];
}
?>