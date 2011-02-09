<?php
/**
 * Functions handling module configs
 *
 * @copyright       The XOOPS project http://sourceforge.net/projects/xoops/
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * @since           1.00
 * @version         $Id: functions.config.php 2223 2008-10-04 04:46:03Z phppp $
 * @package         Frameworks
 * @subpackage      art
 */

if(!defined("FRAMEWORKS_ART_FUNCTIONS_CONFIG")):
define("FRAMEWORKS_ART_FUNCTIONS_CONFIG", true);

/**
 * Load configs of a module
 *
 *
 * @param    string    $dirname    module dirname
 * @return    array
 */
function mod_loadConfig($dirname = "")
{
    if (empty($dirname) && empty($GLOBALS["xoopsModule"])) {
        return null;
    }
    $dirname = !empty($dirname) ? $dirname : $GLOBALS["xoopsModule"]->getVar("dirname");
    
    if (isset($GLOBALS["xoopsModule"]) && is_object($GLOBALS["xoopsModule"]) && $GLOBALS["xoopsModule"]->getVar("dirname", "n") == $dirname){
        if (isset($GLOBALS["xoopsModuleConfig"])) {
            $moduleConfig =& $GLOBALS["xoopsModuleConfig"];
        } else {
            return null;
        }
    } else {
        xoops_load("cache");
        if (!$moduleConfig = XoopsCache::read("{$dirname}_config")) {
            $moduleConfig = mod_fetchConfig($dirname);
            XoopsCache::write("{$dirname}_config", $moduleConfig);
        }
    }
    if ($customConfig = @include XOOPS_ROOT_PATH . "/modules/{$dirname}/include/plugin.php") {
        $moduleConfig = array_merge($moduleConfig, $customConfig);
    }
    return $moduleConfig;
}

function mod_loadConfg($dirname = "") 
{
    return mod_loadConfig($dirname);
}

/**
 * Fetch configs of a module from database
 *
 *
 * @param    string    $dirname    module dirname
 * @return    array
 */
function mod_fetchConfig($dirname = "")
{
    if (empty($dirname)) {
        return null;
    }
    
    $module_handler =& xoops_gethandler('module');
    if (!$module = $module_handler->getByDirname($dirname)) {
        trigger_error("Module '{$dirname}' does not exist", E_USER_WARNING);
        return null;
    }

    $config_handler =& xoops_gethandler('config');
    $criteria = new CriteriaCompo(new Criteria('conf_modid', $module->getVar('mid')));
    $configs = $config_handler->getConfigs($criteria);
    foreach (array_keys($configs) as $i) {
        $moduleConfig[$configs[$i]->getVar('conf_name')] = $configs[$i]->getConfValueForOutput();
    }
    unset($module, $configs);
    
    return $moduleConfig;
}

function mod_fetchConfg($dirname = "")
{
    return mod_fetchConfig($dirname);
}

/**
 * clear config cache of a module
 *
 *
 * @param    string    $dirname    module dirname
 * @return    bool
 */
function mod_clearConfig($dirname = "")
{
    if (empty($dirname)) {
        return false;
    }
    
    xoops_load("cache");
    return XoopsCache::delete("{$dirname}_config"); 
}

function mod_clearConfg($dirname = "")
{
    return mod_clearConfig($dirname);
}

endif;
?>