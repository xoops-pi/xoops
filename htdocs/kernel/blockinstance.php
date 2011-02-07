<?php
/**
 * Legacy blockinstance handler
 *
 * Deprecated, just for backward compat with XOOPS 2.2; requires PHP 5.0+
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code 
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @package         kernel
 * @since           2.3.0
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * @version         $Id: blockinstance.php 2075 2008-09-12 13:48:30Z phppp $
 */

class XoopsBlockInstance
{
    function __construct()
    {
    }
    
    function __call($name, $args)
    {
        trigger_error("Class '" . __CLASS__ . "' is deprecated" . (empty($name) ? "" : " thus the method '{$name}' is not executed") . "!", E_USER_WARNING);
        return null;
    }
    
    function __set($name, $args)
    {
        trigger_error("Class '" . __CLASS__ . "' is deprecated" . (empty($name) ? "" : " thus the variable '{$name}' is not set") . "!", E_USER_WARNING);
        return false;
    }
    
    function __get($name)
    {
        trigger_error("Class '" . __CLASS__ . "' is deprecated" . (empty($name) ? "" : " thus the variable '{$name}' is not vailable") . "!", E_USER_WARNING);
        return null;
    }
}

class XoopsBlockInstanceHandler
{
    function __construct()
    {
    }
    
    function __call($name, $args)
    {
        trigger_error("Class '" . __CLASS__ . "' is deprecated" . (empty($name) ? "" : " thus the method '{$name}' is not executed") . "!", E_USER_WARNING);
        return null;
    }
    
    function __set($name, $args)
    {
        trigger_error("Class '" . __CLASS__ . "' is deprecated" . (empty($name) ? "" : " thus the variable '{$name}' is not set") . "!", E_USER_WARNING);
        return false;
    }
    
    function __get($name)
    {
        trigger_error("Class '" . __CLASS__ . "' is deprecated" . (empty($name) ? "" : " thus the variable '{$name}' is not vailable") . "!", E_USER_WARNING);
        return null;
    }
}
?>