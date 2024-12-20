<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!defined('ROOT_URL')) define('ROOT_URL', 'http://localhost/marjublog/');
if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_USER')) define('DB_USER', 'root');
if (!defined('DB_PASS')) define('DB_PASS', '');
if (!defined('DB_NAME')) define('DB_NAME', 'blogsite');
