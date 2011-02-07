<?php
/**
 * Formatted textarea form
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
 * @package         kernel
 * @subpackage      form
 * @since           2.0.0
 * @author          Kazumi Ono <onokazu@xoops.org>
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * @version         $Id: dhtmltextarea.php 1786 2008-05-28 09:56:27Z phppp $
 */ 

xoops_load('XoopsEditor');

class FormDhtmlTextArea extends XoopsEditor
{
    /**
     * Hidden text
     * @var    string
     * @access    private
     */
    var $_hiddenText = "xoopsHiddenText";
    
    function __construct($options = array())
    {
        parent::__construct($options);
        $this->rootPath = "/class/xoopseditor/" . basename(dirname(__FILE__));
        $hiddenText = isset($this->configs["hiddenText"]) ? $this->configs["hiddenText"] : $this->_hiddenText;
        xoops_load('XoopsFormDhtmlTextArea');
        $this->renderer = new XoopsFormDhtmlTextArea('', $this->getName(), $this->getValue(), $this->getRows(), $this->getCols(), $hiddenText, $this->configs );
    }
    
    function FormDhtmlTextArea($options = array())
    {
        $this->__construct($options);
    }
    
    function render()
    {
        return $this->renderer->render();
    }
}
?>