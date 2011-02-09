<?php
/**
 * Legacy View
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       The Xoops Engine http://sourceforge.net/projects/xoops/
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * @since           3.0
 * @package         Legacy_Core
 * @version         $Id$
 */

class Legacy_View extends Xoops_Zend_View
{
    public static $paths = array(
        'app'       => 'apps',
        'module'    => 'modules',
        'plugin'    => 'plugins',
        'theme'     => '',
    );
    /**
     * Template engine
     * @var Legacy_Smarty_Template
     */
    protected $engine = null;

    /**
     * Layout theme
     * @var string
     */
    protected $theme = 'legacy';
    protected $themeEngine;

    /**
     * Instances of helper objects.
     *
     * @var array
     */
    private $_helper = array();

    /**
     * Map of helper => class pairs to help in determining helper class from
     * name
     * @var array
     */
    private $_helperLoaded = array();


    /**
     * Callback for escaping.
     *
     * @var string
     */
    private $_escape = 'htmlspecialchars';

    /**
     * Encoding to use in escaping mechanisms; defaults to utf-8
     * @var string
     */
    private $_encoding = 'UTF-8';

    /**
     * Constructor
     *
     * @param  array $config
     * @return void
     */
    public function __construct($config = array())
    {
        $this->init();
    }

    /**
     * Return the template engine object
     *
     * Lazy load the template object instead of loading in init()
     *
     * @return
     */
    public function getEngine()
    {
        if (!isset($this->engine)) {
            $this->engine = new Legacy_Smarty_Engine();
        }
        return $this->engine;
    }

    public function loadTheme($options = array())
    {
        if (!isset($this->themeEngine)) {
            $options['view'] = $this;
            $this->themeEngine = new Legacy_Theme($options);
        }
        return $this->themeEngine;
    }

    public function setTheme($theme = "legacy")
    {
        /*
        if ("default" != $theme) {
            if (!array_key_exists($theme, Xoops::service("registry")->theme->read())) {
                return $this;
            }
        }
        */

        $this->theme = $theme;
        return $this;
    }

    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * Get a view script
     *
     * @return string
     */
    public function getViewScript($vars = array())
    {
        //if (!isset($this->viewScript)) {
            $vars['template'] = null;
            // Accept legacy content template
            if (!isset($vars['template']) && !empty($GLOBALS['xoopsOption']['template_main'])) {
                $template_main = $GLOBALS['xoopsOption']['template_main'];
                if (false == strpos($template_main, ":")) {
                    $template_main = 'db:' . $template_main;
                }
                $vars['template'] = $template_main;
            }

            // Accept custom template set in action
            if (!isset($vars['template']) && isset($this->template)) {
                $vars['template'] = $this->template;
            }

            $this->viewScript = $vars['template'] ?: "";
        //}

        return $this->viewScript;
    }

    /**
     * Return a themable file resource path
     *
     * Path options:
     * www/, themes/, modules/, apps/
     *
     * @param string    $path
     * @param bool      $isAbsolute return faul path
     * @return string
     */
    public function resourcePath($path, $isAbsolute = false)
    {
        // File name prepended with resource type, for db:file.name, file:file.name or app:file.name
        // Or full path under WIN: C:\Path\To\Template
        // Return directly
        if (!empty($path) && false !== strpos($path, ":")) {
            return $path;
        }

        $path       = trim($path, "/");
        $section    = "";
        $module     = "";
        $append     = "";
        $segs = explode("/", $path, 2);
        $section = $segs[0];
        if (!empty($segs[1])) {
            $append = $segs[1];
        }
        if (isset(static::$paths[$section])) {
            $sectionPath = (empty(static::$paths[$section]) ? "" : static::$paths[$section] . "/") . $append;
        } else {
            $sectionPath = $path;
        }
        $theme_path = XOOPS::path("theme") . "/{$this->theme}/{$sectionPath}";
        // Found in theme
        if (file_exists($theme_path)) {
            return $isAbsolute ? $theme_path : "theme/{$this->theme}/{$sectionPath}";
        }

        return $isAbsolute ? Xoops::path($path) : $path;
    }
}