<?php
/**
 * XOOPS template engine class
 *
 * See the enclosed file LICENSE for licensing information.
 * If you did not receive this file, get it at http://www.fsf.org/copyleft/gpl.html
 *
 * @copyright       The XOOPS project http://sourceforge.net/projects/xoops/
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author          Kazumi Ono <onokazu@xoops.org>
 * @author          Skalpa Keo <skalpa@xoops.org>
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * @package         kernel
 * @subpackage      core
 * @version         $Id: template.php 2711 2009-01-22 10:01:21Z phppp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}
/**
 * Base class: Smarty template engine
 */
define("SMARTY_DIR", XOOPS_ROOT_PATH . "/class/smarty/");
require_once SMARTY_DIR . 'Smarty.class.php';

/**
 * Template engine
 *
 * @package        kernel
 * @subpackage    core
 *
 * @author        Kazumi Ono     <onokazu@xoops.org>
 * @copyright    (c) 2000-2003 The Xoops Project - www.xoops.org
 */
class XoopsTpl extends Smarty
{
    function XoopsTpl()
    {
        global $xoopsConfig;

        $this->left_delimiter = '<{';
        $this->right_delimiter = '}>';

        $this->template_dir = XOOPS_THEME_PATH;
        $this->cache_dir = XOOPS_VAR_PATH . "/cache/smarty/cache";
        $this->compile_dir = XOOPS_VAR_PATH . "/cache/smarty/compile";

        $this->compile_check = ( $xoopsConfig['theme_fromfile'] == 1 );
        $this->plugins_dir = array(
            XOOPS_ROOT_PATH . '/class/smarty/xoops_plugins',
            XOOPS_ROOT_PATH . '/class/smarty/plugins',
        );
        if ( $xoopsConfig['debug_mode'] ) {
            $this->debugging_ctrl = 'URL';
            if ( $xoopsConfig['debug_mode'] == 3 ) {
                $this->debugging = true;
            }
        }
        $this->Smarty();
        $this->setCompileId();

        $this->assign( array(
            'xoops_url' => XOOPS_URL,
            'xoops_rootpath' => XOOPS_ROOT_PATH,
            'xoops_langcode' => _LANGCODE,
            'xoops_charset' => _CHARSET,
            'xoops_version' => XOOPS_VERSION,
            'xoops_upload_url' => XOOPS_UPLOAD_URL
        ) );
    }

    /**
     * Renders output from template data
     *
     * @param   string  $data        The template to render
     * @param    bool    $display    If rendered text should be output or returned
     * @return  string  Rendered output if $display was false
     **/
    function fetchFromData( $tplSource, $display = false, $vars = null )
    {
        if ( !function_exists('smarty_function_eval') ) {
            require_once SMARTY_DIR . '/plugins/function.eval.php';
        }
        if ( isset( $vars ) ) {
            $oldVars = $this->_tpl_vars;
            $this->assign( $vars );
            $out = smarty_function_eval( array('var' => $tplSource), $this );
            $this->_tpl_vars = $oldVars;
            return $out;
        }
        return smarty_function_eval( array('var' => $tplSource), $this );
    }

    function touch( $resourceName )
    {
        $isForced = $this->force_compile;
        $this->force_compile = true;
        $this->clear_cache( $resourceName );
        $result = $this->_compile_resource( $resourceName, $this->_get_compile_path( $resourceName ) );
        $this->force_compile = $isForced;
        return $result;
    }

    /**
     * returns an auto_id for auto-file-functions
     *
     * @param string $cache_id
     * @param string $compile_id
     * @return string|null
     */
    function _get_auto_id($cache_id = null, $compile_id = null)
    {
        if (isset($cache_id))
            return (isset($compile_id)) ? $compile_id . '|' . $cache_id  : $cache_id;
        elseif(isset($compile_id))
            return $compile_id;
        else
            return null;
    }

    function setCompileId($module_dirname = null, $theme_set = null, $template_set = null)
    {
        global $xoopsConfig, $xoopsModule;
        $template_set = empty($template_set) ? $xoopsConfig['template_set'] : $template_set;
        $theme_set = empty($theme_set) ? $xoopsConfig['theme_set'] : $theme_set;
        $module_dirname = empty($module_dirname) ? (empty($xoopsModule) ? "system" : $xoopsModule->getVar("dirname", "n")) : $module_dirname;
        $this->compile_id = $module_dirname . '-' . $theme_set . '-' . $template_set;
        $this->_compile_id = $this->compile_id;
    }

    function clearCache($module_dirname = null, $theme_set = null, $template_set = null)
    {
        $compile_id = $this->compile_id;
        $this->setCompileId($module_dirname, $template_set, $theme_set);
        $_params = array('auto_base' => $this->cache_dir,
                        'auto_source' => null,
                        'auto_id' => $this->compile_id,
                        );
        $this->_compile_id = $this->compile_id = $compile_id;
        require_once(SMARTY_CORE_DIR . 'core.rm_auto.php');
        return smarty_core_rm_auto($_params, $this);
    }

    /**
     * @deprecated DO NOT USE THESE METHODS, ACCESS THE CORRESPONDING PROPERTIES INSTEAD
     */
    function xoops_setTemplateDir($dirname) {        $this->template_dir = $dirname;            }
    function xoops_getTemplateDir() {                return $this->template_dir;                }
    function xoops_setDebugging($flag=false) {        $this->debugging = is_bool($flag) ? $flag : false;    }
    function xoops_setCaching( $num = 0 ) {            $this->caching = (int)$num;                }
    function xoops_setCompileDir($dirname) {        $this->compile_dir = $dirname;            }
    function xoops_setCacheDir($dirname) {            $this->cache_dir = $dirname;            }
    function xoops_canUpdateFromFile() {            return $this->compile_check;            }
    function xoops_fetchFromData( $data ) {            return $this->fetchFromData( $data );    }
    function xoops_setCacheTime( $num = 0 )
    {
        if ( ( $num = (int)$num ) <= 0) {
            $this->caching = 0;
        } else {
            $this->cache_lifetime = $num;
        }
    }
}

/**
 * function to update compiled template file in templates_c folder
 *
 * @param   string  $tpl_id
 * @param   boolean $clear_old
 * @return  boolean
 **/
function xoops_template_touch($tpl_id, $clear_old = true)
{
    $tplfile_handler =& xoops_gethandler('tplfile');
    $tplfile =& $tplfile_handler->get($tpl_id);

    if ( is_object($tplfile) ) {
        $file = $tplfile->getVar( 'tpl_file', 'n' );
        $tpl = new XoopsTpl();
        return $tpl->touch( "db:$file" );
    }
    return false;
}

/**
 * Clear the module cache
 *
 * @param   int $mid    Module ID
 * @return
 **/
function xoops_template_clear_module_cache($mid)
{
    $block_arr = XoopsBlock::getByModule($mid);
    $count = count($block_arr);
    if ($count > 0) {
        $xoopsTpl = new XoopsTpl();
        $xoopsTpl->xoops_setCaching(2);
        for ($i = 0; $i < $count; $i++) {
            if ($block_arr[$i]->getVar('template') != '') {
                $xoopsTpl->clear_cache('db:'.$block_arr[$i]->getVar('template'), 'blk_'.$block_arr[$i]->getVar('bid'));
            }
        }
    }
}
?>