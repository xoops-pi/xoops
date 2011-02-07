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
 * Text CAPTCHA class For XOOPS
 *
 * @copyright       The XOOPS project http://sourceforge.net/projects/xoops/
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @since           2.3.0
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * @version         $Id: text.php 1530 2008-05-01 09:22:47Z phppp $
 * @package         class
 * @subpackage      CAPTCHA
 */

class XoopsCaptchaText extends XoopsCaptchaMethod
{
    function render()
    {
        parent::render();
        parent::loadConfig();
        $form = $this->loadText()  . "&nbsp;&nbsp; <input type='text' name='".$this->config["name"]."' id='".$this->config["name"]."' size='" . $this->config["num_chars"] . "' maxlength='" . $this->config["num_chars"] . "' value='' />";
        $form .= "<br />" . $this->config["rule_text"];
        if ( !empty($this->config["maxattempt"]) ) {
            $form .=  "<br />". sprintf( $this->config["maxattempt_text"], $this->config["maxattempt"] );
        }
        
        return $form;
    }

    function loadText()
    {
        $val_a = rand(0, 9);
        $val_b = rand(0, 9);
        if ($val_a > $val_b) {
            $expression = "{$val_a} - {$val_b} = ?";
            $this->code = $val_a - $val_b;
        } else {
            $expression = "{$val_a} + {$val_b} = ?";
            $this->code = $val_a + $val_b;
        }

        return "<span style='font-style: normal; font-weight: bold; font-size: 100%; font-color: #333; border: 1px solid #333; padding: 1px 5px;'>{$expression}</span>";
    }

}
?>