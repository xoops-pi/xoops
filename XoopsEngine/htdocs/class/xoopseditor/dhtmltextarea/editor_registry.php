<?php
/**
 * XOOPS editor
 *
 * @copyright       The XOOPS project http://www.xoops.org/
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author          Taiwen Jiang (phppp or D.J.) <php_pp@hotmail.com>
 * @since           2.3.0
 * @version         $Id: editor_registry.php 1435 2008-04-06 14:47:51Z phppp $
 * @package         xoopseditor
 */
return $config = array(
        "class"     =>    "FormDhtmlTextArea",
        "file"      =>    XOOPS_ROOT_PATH . "/class/xoopseditor/dhtmltextarea/dhtmltextarea.php",
        "title"     =>    _XOOPS_EDITOR_DHTMLTEXTAREA,
        "order"     =>    2,
        "nohtml"    =>    1
    );
?>