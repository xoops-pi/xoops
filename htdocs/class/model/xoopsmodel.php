<?php
/**
 * Xoops object data model handlers
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
 * @subpackage      model
 * @since           2.3.0
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * @version         $Id: xoopsmodel.php 1778 2008-05-25 10:57:41Z phppp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}
require_once XOOPS_ROOT_PATH.'/kernel/object.php';

/**
 * Factory for object handlers
 *
 * @author Taiwen Jiang <phppp@users.sourceforge.net>
 * @copyright copyright &copy; The XOOPS project
 * @package kernel
 **/
class XoopsModelFactory
{
    /**
     * @access private
     */
    //static $instance;

    /**
     * holds reference to object handlers {@link XoopsPersistableObjectHandler}
     *
     * var array of objects
     * @access private
     */
    /*static private*/var $handlers = array();

    public function __construct()
    {
    }

    /*
    public function XoopsModelFactory()
    {
        self::__construct();
    }
    */

    /**
     * Get singleton instance
     *
     * @access  public
     */
    function getInstance()
    {
        static $instance;
        if (!isset($instance)) {
            $class = __CLASS__;
            $instance = new $class();
        }
        return $instance;
    }

    /**
     * Load object handler
     *
     * @access  public
     *
     * @param   object  $ohandler   reference to {@link XoopsPersistableObjectHandler}
     * @param   string  $name   handler name
     * @param   mixed   $args   args
     * @return  object of handler
     */
    public static function loadHandler($ohander, $name, $args = null)
    {
        //$instance = XoopsModelFactory::getInstance();
        static $handlers;
        if (!isset($handlers[$name])) {
            if ( @include_once dirname(__FILE__) . "/{$name}.php" ) {
                $className = "XoopsModel" . ucfirst($name);
                $handler = new $className();
            } elseif (xoops_load("model", "framework")) {
                $handler = XoopsModel::loadHandler($name);
            }

            if (!is_object($handler)) {
                return null;
            }
            $handlers[$name] = $handler;
            //xoops_result('loaded handler ' . $name);
        }

        $handlers[$name]->setHandler($ohander);
        if ( !empty($args) && is_array($args) && is_a($handlers[$name], 'XoopsModelAbstract') ) {
            $handlers[$name]->setVars($args);
        }

        return $handlers[$name];
    }
}

/**
 * abstract class object handler
 *
 * @author Taiwen Jiang <phppp@users.sourceforge.net>
 * @copyright copyright &copy; The XOOPS project
 * @package kernel
 **/
class XoopsModelAbstract
{

    /**
     * holds referenced to handler object
     *
     * @var     object
     * @param   object  $ohandler   reference to {@link XoopsPersistableObjectHandler}
     * @access protected
     */
    var $handler;

    /**
     * constructor
     *
     * normally, this is called from child classes only
     * @access protected
     */
    function __construct($args = null, $handler = null)
    {
        $this->setHandler($handler);
        $this->setVars($args);
    }

    function XoopsObjectAbstract($args = null, $handler = null)
    {
        $this->__construct($args, $handler);
    }

    function setHandler($handler)
    {
        $this->handler =& $handler;
        return true;
    }

    function setVars($args)
    {
        if (!empty($args) && is_array($args)) {
            foreach ($args as $key => $value) {
                $this->$key = $value;
            }
        }
        return true;
    }

}
?>