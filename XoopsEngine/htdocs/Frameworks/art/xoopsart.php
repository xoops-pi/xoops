<?php
/**
 * Xoops Frameworks addon: art
 *
 * @copyright       The XOOPS project http://sourceforge.net/projects/xoops/
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * @since           1.00
 * @version         $Id: xoopsart.php 1901 2008-07-26 04:05:57Z phppp $
 * @package         Frameworks
 */
 
class XoopsArt 
{
    function __construct()
    {
    }
    
    function XoopsArt()
    {
        $this->__construct();
    }
    
    /**
     * Load a collective functions of Frameworks
     *
     * @param    string    $group        name of  the collective functions, empty for functions.php
     * @return    bool
     */
    function loadFunctions($group = "")
    {
        return include_once FRAMEWORKS_ROOT_PATH . "/art/functions.{$group}" . (empty($group) ? "" : "." ) . "php";
    }
}
?>