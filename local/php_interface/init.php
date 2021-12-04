<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
require_once( $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/autoload.php');
define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/log.txt");

function debug($data){
    echo '<pre>';
    var_dump($data);echo '<br>';
    echo '</pre>';
}

function testAgent()
{
//    AddMessage2Log('testAgent');
    $obj = new \lib\Activity();
    $obj->set_cached_data();

    return "testAgent();";
}
