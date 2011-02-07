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
 * Xoops Localization function
 *
 * @copyright   The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license     http://www.fsf.org/copyleft/gpl.html GNU public license
 * @package     core
 * @since       2.3.0
 * @author      Taiwen Jiang <phppp@users.sourceforge.net>
 * @version     $Id: xoopslocal.php 1501 2008-04-26 11:47:55Z phppp $
 */

class XoopsLocalWrapper
{
    public static function load($language = null)
    {
        if (class_exists('XoopsLocal', false)) return true;
        require XOOPS_ROOT_PATH . "/class/xoopslocal.php";
        if (empty($language)) {
            $language = preg_replace("/[^a-z0-9_\-]/i", "", $GLOBALS["xoopsConfig"]["language"]);
        }
        if (empty($language) ||!include XOOPS_ROOT_PATH . "/language/" . $language . "/locale.php" ) {
            include XOOPS_ROOT_PATH . "/language/english/locale.php";
        }
        return true;
    }
}

function xoops_local()
{
    // get parameters
    $func_args = func_get_args();
    $func = array_shift($func_args);
    // local method defined
    return call_user_func_array(array("XoopsLocal", $func), $func_args);
}

XoopsLocalWrapper::load();
?>