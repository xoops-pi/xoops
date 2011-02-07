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
 * CAPTCHA class For XOOPS
 *
 * @copyright       The XOOPS project http://sourceforge.net/projects/xoops/
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @since           2.3.0
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * @version         $Id: xoopscaptcha.php 2256 2008-10-07 16:27:47Z phppp $
 * @package         class
 * @subpackage      CAPTCHA
 * @todo            enable templates; documentation
 */

class XoopsCaptcha
{
    
    //static $instance;
    var $active;
    var $handler;
    var $path_basic;
    var $path_plugin;
    var $name;
    
    var $config    = array();
    
    var $message = array(); // Logging error messages
    
    function __construct()
    {
        xoops_loadLanguage("captcha");

        // Load static configurations
        $this->path_basic = XOOPS_ROOT_PATH . "/class/captcha";
        $this->path_plugin = XOOPS_ROOT_PATH . "/Frameworks/captcha";
        $this->config = $this->loadConfig();
        $this->name = $this->config['name'];
    }
    
    function XoopsCaptcha()
    {
        $this->__construct();
    }
    
    function &getInstance()
    {
        static $instance;
        if (!isset($instance)) {
            $class = __CLASS__;
            $instance = new $class();
        }
        return $instance;
    }
    
    function loadConfig($filename = null)
    {
        $filename = empty($filename) ? "config.php" : "config.{$filename}.php";
        $config = @include "{$this->path_basic}/{$filename}";
        if ($config_plugin = @include "{$this->path_plugin}/{$filename}") {
            foreach ($config_plugin as $key => $val) {
                $config[$key] = $val;
            }
        }
        
        return $config;
    }
    
    function isActive( )
    {
        if (isset($this->active)) {
            return $this->active;
        }
        
        if ( !empty($this->config["disabled"]) ) {
            $this->active = false;
            return $this->active;
        }
        
        if ( !empty($this->config["skipmember"]) && is_object($GLOBALS["xoopsUser"]) ) {
            $this->active = false;
            return $this->active;
        }
        
        if ( !isset($this->handler) ) {
            $this->loadHandler();
        }
        $this->active = isset( $this->handler );
        
        return $this->active;
    }
    
    function loadHandler($name = null)
    {
        $name = !empty($name) ? $name : (empty($this->config['mode']) ? 'text' : $this->config['mode']);
        $class = 'XoopsCaptcha' . ucfirst($name);
        if (!empty($this->handler) && get_class($this->handler) == $class) {
            return $this->handler;
        }
        
        $this->handler = null;
        if (! @include_once "{$this->path_basic}/{$name}.php" ) {
            include_once "{$this->path_plugin}/{$name}.php";
        }
        if (! class_exists($class) ) {
            $class = 'text';
            require_once "{$this->path_basic}/text.php";
        }
        $handler = new $class($this);
        if ($handler->isActive()) {
            $this->handler = $handler;
        }
        return $this->handler;
        
    }
    
    function setConfigs($configs)
    {
        foreach ($configs as $key => $val) {
            $this->setConfig($key, $val);
        }
        
        return true;
    }
    
    function setConfig($name, $val)
    {
        if (isset($this->$name)) {
            $this->$name = $val;
        } else {
            $this->config[$name] = $val;
        }
        
        return true;
    }
    
    /** 
     * Verify user submission
     */
    function verify($skipMember = null, $name = null) 
    {
        $sessionName = empty($name) ? $this->name : $name;
        $skipMember = ($skipMember === null) ? @$_SESSION["{$sessionName}_skipmember"] : $skipMember;
        $maxAttempts = intval( @$_SESSION["{$sessionName}_maxattempts"] );
        
        $is_valid = false;
        
        // Skip CAPTCHA verification if disabled 
        if ( !$this->isActive() ) {
            $is_valid = true;
        
        // Skip CAPTCHA for member if set
        } elseif ( is_object($GLOBALS["xoopsUser"]) && !empty($skipMember) ) {
            $is_valid = true;
            
        // Kill too many attempts
        } elseif (!empty($maxAttempts) && !empty($_SESSION["{$sessionName}_attempt"]) > $maxAttempts) {
            $this->message[] = _CAPTCHA_TOOMANYATTEMPTS;
        
        // Verify the code
        } elseif (!empty($_SESSION["{$sessionName}_code"])) {
            $func = !empty($this->config["casesensitive"]) ? "strcmp" : "strcasecmp";
            $is_valid = ! $func( trim(@$_POST[$sessionName]), $_SESSION["{$sessionName}_code"] );
        }
        
        //if(!empty($maxAttempts)) {
            if (!$is_valid) {
                // Increase the attempt records on failure
                $_SESSION["{$sessionName}_attempt"]++;
                // Log the error message
                $this->message[] = _CAPTCHA_INVALID_CODE;
                
            } else {
                // reset attempt records on success
                $_SESSION["{$sessionName}_attempt"] = null;
            }
        //}
        
        $this->destroyGarbage(true);
        
        return $is_valid;
    }
    
    function getCaption()
    {
        return defined("_CAPTCHA_CAPTION") ? constant("_CAPTCHA_CAPTION") : "";
    }
    
    function getMessage()
    {
        return implode("<br />", $this->message);
    }

    /**
     * Destory historical stuff
     */
    function destroyGarbage($clearSession = false) 
    {
        $this->loadHandler();
        if (is_callable($this->handler, "destroyGarbage")) {
            $this->handler->destroyGarbage();
        }
        
        if ($clearSession) {
            $_SESSION[$this->name . '_name'] = null;
            $_SESSION[$this->name . '_skipmember'] = null;
            $_SESSION[$this->name . '_code'] = null;
            $_SESSION[$this->name . '_maxattempts'] = null;
        }
        
        return true;
    }

    function render()
    {
        $_SESSION[$this->name . '_name'] = $this->name;
        $_SESSION[$this->name . '_skipmember'] = $this->config["skipmember"];
        
        $form = "";
        if( !$this->active || empty($this->config["name"]) ) {
            return $form;
        }
        
        $maxAttempts = $this->config["maxattempt"];
        if (!empty($maxAttempts)) {
            $_SESSION[$this->name . '_maxattempts'] = $maxAttempts;
        }
        
        // Failure on too many attempts
        if (!empty($maxAttempts) && @$_SESSION[$this->name . '_attempt'] > $maxAttempts) {
            $form = _CAPTCHA_TOOMANYATTEMPTS;
        // Load the form element
        } else {
            $form = $this->loadForm();
        }
        
        return $form;
    }
    
    function setCode($code = null)
    {
        $code = ($code === null) ? $this->handler->getCode() : $code;
        if (!empty($code)) {
            $_SESSION[$this->name . '_code'] = $code;
            return true;
        }
        return false;
    }
    
    function loadForm()
    {
        $form = $this->handler->render();
        $this->setCode();
        
        return $form;
    }
}
    
/**
 * Abstract class for CAPTCHA method
 *
 * Currently there are two types of CAPTCHA forms, text and image
 * The default mode is "text", it can be changed in the priority:
 * 1 If mode is set through XoopsFormCaptcha::setConfig("mode", $mode), take it
 * 2 Elseif mode is set though captcha/config.php, take it
 * 3 Else, take "text"
 *
 */
class XoopsCaptchaMethod
{
    var $handler;
    var $config;
    var $code;
    
    function __construct($handler = null)
    {
        $this->handler = $handler;
    }
    
    function XoopsCaptchaMethod($handler = null)
    {
        $this->__construct($handler);
    }
    
    function isActive()
    {
        return true;
    }
    
    function loadConfig($name)
    {
        
        $this->config = empty($name) ? $this->handler->config : array_merge( $this->handler->config, $this->handler->loadConfig($name) );
    }

    function getCode()
    {
        return strval( $this->code );
    }
    
    function render()
    {
    }
}

?>