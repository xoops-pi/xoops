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
 * XOOPS Utilities
 *
 * @copyright       The XOOPS project http://www.xoops.org/
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @since           2.3.0
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * @version         $Id: utility.php 1336 2008-02-16 12:08:20Z phppp $
 * @package         class
 * @subpackage      utility
 */
 
class XoopsUtility 
{
    function __construct()
    {
    }
    
    function XoopsUtility()
    {
        $this->__construct();
    }
    
    function recursive($handler, $data)
    {
        if (is_array($data)) {
            $return = array_map(array("XoopsUtility", "recursive"), $handler, $data);
            return $return;
        }

        // single function
        if ( is_string($handler) ) {
            return function_exists($handler) ? $handler($data) : $data;
        }
        
        // Method of a class
        if ( is_array($handler) ) {
            return call_user_func(array($handler[0], $handler[1]), $data);
        }
        
        return $data;
    }
}
?>