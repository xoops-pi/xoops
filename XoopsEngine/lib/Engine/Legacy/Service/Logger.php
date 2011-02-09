<?php
/**
 * Legacy Log service class
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
 * @package         Xoops_Service
 * @version         $Id$
 */

namespace Engine\Legacy\Service;

class Logger extends \Engine\Xoops\Service\Logger
{
    public function __construct($options = array())
    {
        parent::__construct($options);
        $GLOBALS['xoopsLogger'] = $this;
    }

    /**
     * Log a database query
     * @param   string  $sql    SQL string
     * @param   string  $error  error message (if any)
     * @param   int     $errno  error number (if any)
     */
    function addQuery($sql, $error = null, $errno = null)
    {
        //if ( $this->activated ) $this->queries[] = array('sql' => $sql, 'error' => $error, 'errno' => $errno);
    }

    /**
     * Log display of a block
     * @param   string  $name       name of the block
     * @param   bool    $cached     was the block cached?
     * @param   int     $cachetime  cachetime of the block
     */
    function addBlock($name, $cached = false, $cachetime = 0)
    {
        //if ( $this->activated ) $this->blocks[] = array('name' => $name, 'cached' => $cached, 'cachetime' => $cachetime);
    }

    /**
     * Log extra information
     * @param   string  $name       name for the entry
     * @param   int     $msg  text message for the entry
     */
    function addExtra($name, $msg)
    {
        //if ( $this->activated ) $this->extra[] = array('name' => $name, 'msg' => $msg);
    }

    /**
     * Start a timer
     * @param   string  $name   name of the timer
     */
    function startTime($name = 'XOOPS')
    {
        //$this->logstart[$name] = $this->microtime();
    }

    /**
     * Stop a timer
     * @param   string  $name   name of the timer
     */
    function stopTime($name = 'XOOPS')
    {
        //$this->logend[$name] = $this->microtime();
    }

}