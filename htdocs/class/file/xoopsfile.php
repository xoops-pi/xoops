<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code 
 which is considered copyrighted (c) material of the original comment or credit authors.
 
 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

/**
 * File factory For XOOPS
 *
 * @copyright   The XOOPS project http://www.xoops.org/
 * @license     http://www.fsf.org/copyleft/gpl.html GNU public license
 * @since       2.3.0
 * @author      Taiwen Jiang <phppp@users.sourceforge.net>
 * @version     $Id: xoopsfile.php 1509 2008-04-27 10:18:38Z phppp $
 * @package     class
 * @subpackage  file
 */
 
class XoopsFile 
{
    //static $instance;
    
    function __construct()
    {
    }
    
    function XoopsFile()
    {
        $this->__construct();
    }
    
    function getInstance()
    {
        static $instance;
        if (!isset($instance)) {
            $class = __CLASS__;
            $instance = new $class();
        }
        return $instance;
    }

    function load($name = "file")
    {
        switch ($name) {
        case "folder":
            if (!class_exists("XoopsFolderHandler")) {
                require dirname(__FILE__) . "/folder.php";
            }
            break;
        case "file":
        default:
            if (!class_exists("XoopsFileHandler")) {
                require dirname(__FILE__) . "/file.php";
            }
            break;
        }
        
        return true;
    }

    function getHandler($name = "file", $path = false, $create = false, $mode = null)
    {
        $handler = null;
        XoopsFile::load($name);
        $class = "Xoops" . ucfirst($name). "Handler";
        if (class_exists($class)) {
            $handler = new $class($path, $create, $mode);
        } else {
            trigger_error("Class '{$class}' not exist", E_USER_WARNING);
        }
        
        return $handler;
    }
}
?>