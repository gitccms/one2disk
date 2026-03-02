<?php
require_once __DIR__."/getItemByID.php";
require_once __DIR__."/getItemByPath.php";
require_once __DIR__."/getPreviewByID.php";
require_once __DIR__."/getPreviewByPath.php";
require_once __DIR__."/getChildrenByID.php";
require_once __DIR__."/getChildrenByPath.php";
//$config = require_once __DIR__."/../.config/config.php";

$PATH = $_GET['path']??'/';
$ID = $_GET['id']??'';