<?php
//$Id: logger_render.php 2499 2008-11-23 04:55:54Z phppp $
if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}
/**
 * this file is for backward compatibility only
 * @package kernel
 * @subpackage  logger
 **/
/**
 * Load the new XoopsLogger class
 **/

require_once XOOPS_ROOT_PATH . '/class/logger/render.php';
trigger_error("Instance of " . __FILE__ . " file is deprecated, check 'XoopsLogger' in class/logger/xoopslogger.php");

?>