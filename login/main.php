<?
session_start();
date_default_timezone_set('Asia/Tehran');

global $config;
require_once('config.php');
require_once('locale-' . $config['lang'] . '.php');
require_once('common.php');
require_once('db.php');
require_once ('access.php')
?>
