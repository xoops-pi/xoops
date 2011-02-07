<?php
/**
 * Template engine
 *
 * @copyright       Perfect World
 * @author          Taiwen Jiang <jiangtaiwene@wanmei.com>
 * @version         $Id$
 */


/**
 * set SMARTY_DIR to absolute path to Smarty library files.
 * Sets SMARTY_DIR only if user application has not already defined it.
 */
if (!defined('SMARTY_DIR')) {
    define('SMARTY_DIR', XOOPS::path("www") . DIRECTORY_SEPARATOR . '/class/smarty' . DIRECTORY_SEPARATOR);
}

/**
 * Base class: Smarty template engine
 */
require SMARTY_DIR . 'Smarty.class.php';

/**
 * Template engine
 */
class Legacy_Smarty_Engine extends Smarty
{
    protected $cache_id;
    public $currentTemplate; // = "db:system_dummy.html";

    public function __construct($options = array())
    {
        parent::__construct();
        $this->left_delimiter = '<{';
        $this->right_delimiter = '}>';

        $this->cache_dir = XOOPS::path("var") . DIRECTORY_SEPARATOR . "cache" . DIRECTORY_SEPARATOR . "smarty" . DIRECTORY_SEPARATOR . "cache";
        $this->compile_dir = XOOPS::path("var") . DIRECTORY_SEPARATOR . "cache" . DIRECTORY_SEPARATOR . "smarty" . DIRECTORY_SEPARATOR . "compile";

        $this->template_dir = XOOPS_THEME_PATH;
        $this->plugins_dir = array(
            SMARTY_DIR . DIRECTORY_SEPARATOR . 'plugins',
            SMARTY_DIR . DIRECTORY_SEPARATOR . 'xoops_plugins'
        );
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

        $this->setOptions($options);
    }

    public function setOptions($options = array())
    {
        $properties = array(
            'caching'           => false,
            'compile_check'     => false,
            'debugging'         => false,
            'force_compile'     => false,
            'template_class'    => '',
            'error_unassigned'  => false,
        );
        foreach ($options as $key => $val) {
            if (array_key_exists($key, $properties)) {
                $this->$key = $val;
            }
        }
        if (XOOPS::service('logger')->silent()) {
            $this->debugging = false;
        }
    }

    /**
     * Create compile_id with purposes of distinguishing theme set, module and domain. Template set is not considered
     */
    public function setCompileId($theme_set = null, $module_dirname = null)
    {
        $this->_compile_id = $this->compile_id = $this->generateCompileId($theme_set, $module_dirname);
        return $this;
    }

    public function generateCompileId($theme_set = null, $module_dirname = null)
    {
        $segs = array();
        $segs[] = is_null($module_dirname)
                        ? XOOPS::config('identifier')
                        : $module_dirname;
        $segs[] = is_null($theme_set)
                        ? XOOPS::config('theme_set')
                        : $theme_set;
        $segs = array_filter($segs);
        $compile_id = empty($segs) ? null : implode('-',  $segs);

        return $compile_id;
    }

    /**
     * Create cache_id with purposes of distinguishing cache level
     */
    public function setCacheId($cache_id = null, $level = null)
    {
        $this->cache_id = Xoops_Zend_Cache::generateId($cache_id, $level);
        return $this;
    }

    /**
     * test to see if valid cache exists for this template
     *
     * @param string $tpl_file name of template file
     * @param string $cache_id
     * @param string $compile_id
     * @return string|false results of {@link _read_cache_file()}
     */
    public function is_cached($tpl_file, $cache_id = null, $compile_id = null)
    {
        $cache_id = is_null($cache_id) ? $this->cache_id : $cache_id;
        return parent::isCached($tpl_file, $cache_id, $compile_id);
    }

    /**
     * fetches a rendered Smarty template
     *
     * @param string $template the resource handle of the template file or template object
     * @param mixed $cache_id cache id to be used with this template
     * @param mixed $compile_id compile id to be used with this template
     * @param object $ |null $parent next higher level of Smarty variables
     * @return string rendered template output
     */
    public function fetch($template, $cache_id = null, $compile_id = null, $parent = null, $display = false)
    {
        $this->currentTemplate = $template;
        $cache_id = is_null($cache_id) ? $this->cache_id : $cache_id;
        try {
            $output = parent::fetch($template, $cache_id, $compile_id, $parent, $display);
        } catch (Exception $e) {
            trigger_error("<pre>" . $e->__toString() . "</pre><br />" . $template);
            $output = $e->getMessage();
        }

        return $output;
    }

    /**
     * Clears module and theme specified compiled templates
     *
     * @see Smarty_Internal_Utility::clearCompiledTemplate
     */
    public function clearTemplate($module_dirname = null, $theme_set = null)
    {
        $compile_id = $this->generateCompileId($theme_set, $module_dirname);
        return $this->clearCompiledTemplate(null, $compile_id);
    }

    /**
     * Clears module and theme specified caches
     *
     * @see Smarty_Internal_Cache::clear
     */
    public function clearCaches($module_dirname = null, $theme_set = null)
    {
        $compile_id = $this->generateCompileId($theme_set, $module_dirname);
        return $this->clearCache(null, null, $compile_id);
    }

    /**
     * Clears module caches and compiled templates
     */
    public function clearModuleCache($module_dirname = null)
    {
        if (empty($module_dirname)) {
            $this->clearTemplate('', '');
            $this->clearCache('', '');
            return true;
        }

        $themes = XOOPS::service("registry")->theme->read();
        foreach (array_keys($themes) as $theme) {
            $this->clearTemplate($module_dirname, $theme);
            $this->clearCache($module_dirname, $theme);
        }
        return true;
    }

    /**
     * Clears caches of a specified cache_id
     */
    public function clearCacheByCacheId($cache_id, $module_dirname = null)
    {
        if (empty($module_dirname)) {
            if (XOOPS::registry("module")) {
                $module_dirname = XOOPS::registry("module")->dirname;
            }
        }
        if (empty($cache_id) || empty($module_dirname)) {
            return false;
        }

        $themes = XOOPS::service("registry")->theme->read();
        foreach (array_keys($themes) as $theme) {
            $compile_id = $this->generateCompileId($theme, $module_dirname);
            $this->cache->clear(null, $cacheId, $compile_id);
        }
        return true;
    }

    public function getVersion()
    {
        return $this->_version;
    }
}
