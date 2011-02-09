<?php
/**
 * Preview of dhtml editor content
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code 
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @package         xoopsform
 * @since           2.3.0
 * @author          Vinod <smartvinu@gmail.com>
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * @version         $Id: formdhtmltextarea_preview.php 2044 2008-09-01 09:59:01Z phppp $
 */
 
include_once '../mainfile.php';
$xoopsLogger->activated = false;
$myts =& MyTextSanitizer::getInstance();
$content = $myts->stripSlashesGPC($_GET['text']);
$html = empty($_GET['html']) ? 0 : 1;
   
if (preg_match_all('/%u([[:alnum:]]{4})/', $content, $matches)) {	 
   foreach ($matches[1] as $uniord) {
       $utf = '&#x' . $uniord . ';';
       $content = str_replace('%u' . $uniord, $utf, $content);
   }	 
   $content = urldecode($content);
}
$result = $myts->displayTarea($content, $html, 1, 1, 1, 1);
echo "<div>" . $result . "</div>";
?>