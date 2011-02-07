<?php
/**
 * Cache handlers
 *
 * @copyright       The XOOPS project http://sourceforge.net/projects/xoops/
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * @since           1.00
 * @version         $Id: functions.cache.php 2223 2008-10-04 04:46:03Z phppp $
 * @package         Frameworks
 * @subpackage      art
 */

if (!defined("FRAMEWORKS_ART_FUNCTIONS_CACHE")):
define("FRAMEWORKS_ART_FUNCTIONS_CACHE", true);

function mod_generateCacheId_byGroup($groups = null)
{
    global $xoopsUser;
    
    if (!empty($groups) && is_array($groups)) {
    } elseif (is_object( $xoopsUser )) {
        $groups = $xoopsUser->getGroups();
    }
    if (!empty($groups) && is_array($groups)) {
        sort($groups);
        $contentCacheId = substr( md5(implode(",", $groups).XOOPS_DB_PASS.XOOPS_DB_NAME), 0, strlen(XOOPS_DB_USER) * 2 );
    } else {
        $contentCacheId = XOOPS_GROUP_ANONYMOUS;
    }
    
    return $contentCacheId;
}

function mod_generateCacheId($groups = null)
{
    return mod_generateCacheId_byGroup($groups);
}

function mod_createFile($data, $name = null, $dirname = null, $root_path = XOOPS_CACHE_PATH)
{
    global $xoopsModule;

    $name = ($name) ? $name : strval(time());
    $dirname = ($dirname) ? $dirname : (is_object($xoopsModule) ? $xoopsModule->getVar("dirname", "n") : "system");

    xoops_load("cache");
    $key = "{$dirname}_{$name}";
    return XoopsCache::write($key, $data);
}

function mod_createCacheFile($data, $name = null, $dirname = null)
{
    return mod_createFile($data, $name, $dirname);
}

function mod_createCacheFile_byGroup($data, $name = null, $dirname = null, $groups = null)
{
    $name .= mod_generateCacheId_byGroup();
    return mod_createCacheFile($data, $name, $dirname);
}

function mod_loadFile($name, $dirname = null, $root_path = XOOPS_CACHE_PATH)
{
    global $xoopsModule;

    $data = null;
    
    if (empty($name)) return $data;
    $dirname = ($dirname) ? $dirname : (is_object($xoopsModule) ? $xoopsModule->getVar("dirname", "n") : "system");
    xoops_load("cache");
    $key = "{$dirname}_{$name}";
    return XoopsCache::read($key);
}

function mod_loadCacheFile($name, $dirname = null)
{
    $data = mod_loadFile($name, $dirname);
    return $data;
}

function mod_loadCacheFile_byGroup($name, $dirname = null, $groups = null)
{
    $name .= mod_generateCacheId_byGroup();
    $data = mod_loadFile($name, $dirname);
    return $data;
}

/* Shall we use the function of glob for better performance ? */

function mod_clearFile($name = "", $dirname = null, $root_path = XOOPS_CACHE_PATH)
{
    if (empty($dirname)) {
        $pattern = ($dirname) ? "{$dirname}_{$name}.*\.php" : "[^_]+_{$name}.*\.php";
        if ($handle = opendir($root_path)) {
            while (false !== ($file = readdir($handle))) {
                if (is_file($root_path . '/' . $file) && preg_match("/^{$pattern}$/", $file)) {
                    @unlink($root_path . '/' . $file);
                }
            }
            closedir($handle);
        }
    } else {
        foreach (glob($root_path . "/{$dirname}_{$name}*.php") as $file) {
            @unlink($file);
        }
    }
    return true;
}

function mod_clearCacheFile($name = "", $dirname = null)
{
    return mod_clearFile($name, $dirname);
}

function mod_clearSmartyCache($pattern = "")
{
    global $xoopsModule;
    
    if (empty($pattern)) {
        $dirname = (is_object($xoopsModule) ? $xoopsModule->getVar("dirname", "n") : "system");
        $pattern = "/(^{$dirname}\^.*\.html$|blk_{$dirname}_.*[^\.]*\.html$)/";
    }
    if ($handle = opendir(XOOPS_CACHE_PATH)) {
        while (false !== ($file = readdir($handle))) {
            if (is_file(XOOPS_CACHE_PATH . '/' . $file) && preg_match($pattern, $file)) {
                @unlink(XOOPS_CACHE_PATH . '/' . $file);
            }
        }
        closedir($handle);
    }
    return true;
}

endif;
?>