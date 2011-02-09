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
 * configuraions of CAPTCHA class For XOOPS
 *
 * @copyright       The XOOPS project http://sourceforge.net/projects/xoops/
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @since           2.3.0
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * @version         $Id: config.php 1530 2008-05-01 09:22:47Z phppp $
 * @package         class
 * @subpackage      CAPTCHA
 */

return $config = array(
    "disabled"              => false,                   // Disable CAPTCHA
    "mode"                  => 'image',                 // default mode
    "name"                  => 'xoopscaptcha',          // captcha name
    "skipmember"            => true,                    // Skip CAPTCHA check for members
    "maxattempt"            => 10,                      // Maximum attempts for each session
    "num_chars"             => 4,                       // Maximum characters
    "rule_text"             => _CAPTCHA_RULE_TEXT,
    "maxattempt_text"       => _CAPTCHA_MAXATTEMPTS,
    );
?>