<?php
// ini
error_reporting(-1);
$start = microtime(true);
session_start();
// ini end

// require
require_once(__DIR__ . '/engine/function.php');
//require_once(__DIR__ . '/libs/phpQuery.php');
require_once(__DIR__ . '/libs/rb.php');

// define

define('POSTER_DIR',    'upload/poster/' );
define('POSTER_NAME',    'logo_' );
define('POSTER',    POSTER_DIR.POSTER_NAME );

define('SCREEN_DIR',    'upload/screen/' );
define('SCREEN_NAME',    'img_' );
define('SCREEN',    SCREEN_DIR.SCREEN_NAME );

// db config
define('DB_DIR',    'movie_txt/' );

// db

define('DB','kp.api.db');
define('HOST','localhost');
//define('HOST','mysql.zzz.com.ua');
define('CHARSET','utf8');

$db = [
    'dsn' => 'mysql:host='.HOST.';dbname='.DB.';charset='.CHARSET.'',
    'user' => 'root',
    'pass' => 'root'
];

R::setup($db['dsn'], $db['user'], $db['pass']);
//R::freeze( true );
//R::fancyDebug( true );

if( !R::testConnection() )
{
    exit('Нет Соединения с БД');
}

R::ext('xDispense', function($table){
    return R::getRedBean()->dispense($table);
});