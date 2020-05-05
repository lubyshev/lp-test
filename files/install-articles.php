<?php
/*
 * Установочный скрипт для модуля "Статьи"
 */

declare(strict_types=1);

$dir  = $_SERVER['PWD'];
$path = explode('/', $_SERVER['PWD']);
define('FILES_ROOT', realpath(dirname($_SERVER['SCRIPT_FILENAME'])).'/src');
define('YII_ROOT', implode('/', array_splice($path, 0, -3)));

copyFiles();

// FUNCTIONS

function copyFiles()
{

}
