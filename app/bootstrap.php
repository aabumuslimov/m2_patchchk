<?php

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);
ini_set('upload_max_filesize', '15M');
set_time_limit(120);
umask(0);

define('DS', DIRECTORY_SEPARATOR);
define('BP', dirname(__DIR__) . DS);

define('VAR_DIR_NAME', 'var');
define('UPLOAD_PATH', 'var/uploads/');
define('STATS_PATH', 'var/stats/');

require_once __DIR__ . '/autoload.php';