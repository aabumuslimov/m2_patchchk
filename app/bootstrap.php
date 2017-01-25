<?php

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);
ini_set('upload_max_filesize', '15M');
set_time_limit(120);
umask(0);

//define('INIT_REPOSITORY', 'ee11422');
define('NO_AJAX', isset($_POST['no_ajax']) && $_POST['no_ajax'] == 1);

define('DS', DIRECTORY_SEPARATOR);
define('BP', dirname(__DIR__) . DS);

define('UPLOAD_PATH', 'var/uploads/');
define('STATS_PATH', 'var/stats/');
