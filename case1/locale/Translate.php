<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$lang = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
if (strlen($lang) < 2) {
    $lang = "en";
} else {
    $lang = substr($lang,0,2);
}
define("BASE_PATH", __DIR__);
define("LANG", $lang);

require_once 'I18N.php';
/**
 * 全局翻译方法
 * @param type $message
 * @param type $category
 * @param type $params
 * @param type $language
 * @return type
 */
function t($message, $params = array(), $category = "*", $language = "")
{
	return I18N::getInstance()->translate($category, $message, $params, $language);
}
/**
 * 全局翻译方法
 * @param type $message
 * @param type $category
 * @param type $params
 * @param type $language
 * @return type
 */
function __($message, $params = array(), $category = "*", $language = "")
{
	return I18N::getInstance()->translate($category, $message, $params, $language);
}
