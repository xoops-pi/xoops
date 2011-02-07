<?php
/**
 * common functions
 *
 * @copyright       The XOOPS project http://sourceforge.net/projects/xoops/
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * @since           1.00
 * @version         $Id: functions.php 2223 2008-10-04 04:46:03Z phppp $
 * @package         Frameworks
 * @subpackage      art
 */

if (!defined("FRAMEWORKS_ART_FUNCTIONS")):
define("FRAMEWORKS_ART_FUNCTIONS", true);

defined("FRAMEWORKS_ART_FUNCTIONS_INI") || include_once dirname(__FILE__) . "/functions.ini.php";
load_functions("cache");
load_functions("user");
load_functions("locale");
load_functions("admin");


/**
 * get MySQL server version
 * 
 * In some cases mysql_get_client_info is required instead
 *
 * @return     string
 */
function mod_getMysqlVersion($conn = null)
{
    static $mysql_version;
    if (isset($mysql_version)) return $mysql_version;
    if (!is_null($conn)) {
        $version = mysql_get_server_info($conn);
    } else {
        $version = mysql_get_server_info();
    }
    return $mysql_version;
}

endif;
?>