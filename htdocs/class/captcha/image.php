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
 * Image CAPTCHA class For XOOPS
 *
 * @copyright       The XOOPS project http://sourceforge.net/projects/xoops/
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @since           2.3.0
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * @version         $Id: image.php 2256 2008-10-07 16:27:47Z phppp $
 * @package         class
 * @subpackage      CAPTCHA
 */

class XoopsCaptchaImage extends XoopsCaptchaMethod
{

    
    function __construct($handler = null)
    {
        parent::__construct($handler);
        parent::loadConfig('image');
    }
    
    function XoopsCaptchaImage($handler = null)
    {
        $this->__construct($handler);
    }
    
    function isActive()
    {
        if (!extension_loaded('gd')) {
            trigger_error("GD library is not loaded", E_USER_WARNING);
            return false;
        } else {
            $required_functions = array("imagecreatetruecolor", "imagecolorallocate", "imagefilledrectangle", "imagejpeg", "imagedestroy", "imageftbbox"); 
            foreach ($required_functions as $func) {
                if (!function_exists($func)) {
                    trigger_error("Function {$func} is not defined", E_USER_WARNING);
                    return false;
                }
            }
        }
        
        return true;
    }
    
    function render()
    {
        $js = "<script type='text/javascript'>
                function xoops_captcha_refresh(imgId)
                {
                    xoopsGetElementById(imgId).src = '" . XOOPS_URL . "/". $this->config["imageurl"]. "?refresh='+Math.random();
                }
                </script>";
        $image = $this->loadImage();
        $image .= "<br /><a href=\"javascript: xoops_captcha_refresh('" . ($this->config['name']) . "')\">" . _CAPTCHA_REFRESH . "</a>";
        $input = "<input type='text' name='" . $this->config["name"] . "' id='" . $this->config["name"] . "' size='" . $this->config["num_chars"] . "' maxlength='" . $this->config["num_chars"] . "' value='' />";
        $rule = $this->config["rule_text"];
        $rule .= "<br />" . ( empty($this->config["casesensitive"]) ? _CAPTCHA_RULE_CASEINSENSITIVE : _CAPTCHA_RULE_CASESENSITIVE );
        if ( !empty($this->config["maxattempt"]) ) {
            $rule .=  "<br />" . sprintf( $this->config["maxattempt_text"], $this->config["maxattempt"] );
        }
        
        return $js . $image . '<br /><br />' . $input . '<br />' . $rule;
    }

    function loadImage()
    {
        $ret = "<img id='" . ($this->config['name']) . "' src='" . XOOPS_URL . "/" . $this->config["imageurl"] . "' onclick=\"this.src='" . XOOPS_URL . "/" . $this->config["imageurl"] . "?refresh='+Math.random()"."\" style='cursor: pointer; vertical-align: middle;' alt='' />";
        
        return $ret;
    }
}
?>