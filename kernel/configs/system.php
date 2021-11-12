<?php

define('FIGAROO_DEVMODE', $_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == 'figaroodev');

define('FIGAROO_DEBUGGING', FIGAROO_DEVMODE);

define('SRAND_KEY',  1);
define('CRYPT_KEY',  '');
define('SECRET_KEY', '');
define('LINKS_KEY',  '');

define('ERROR_REPORTING', E_ALL);

define('PHP_LOG_ERRORS', false);

define('FG_DEV_EMAIL', '');

define('FG_SEND_ERRORS', 300);
