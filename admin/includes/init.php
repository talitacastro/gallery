<?php

defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);
define('SITE_ROOT', DS . 'XAMPP' . DS . 'htdocs' . DS . 'gallery');
defined('INCLUDES_PATH') ? null : define('INCLUDES_PATH' , SITE_ROOT . DS . 'admin' . DS. 'includes');

require_once("functions.php");
include("new_config.php");
include("database.php");
include("db_object.php");
include("user.php");
include("photo.php");
include("comment.php");
include("session.php");



?>