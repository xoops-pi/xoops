<?php
/**
 * Legacy Profiler service file
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

class Profiler extends \Kernel\Service\Profiler
{
    /**
     * Output SQl Zend_Db_Profiler
     *
     */
    public function database($profiler = null)
    {
        if (!$this->active) {
            return;
        }

        if (!($profiler instanceof Zend_Db_Profiler) && empty($GLOBALS['xoopsDB'])) {
            return;
        }

        if (!($profiler instanceof Zend_Db_Profiler)) {
            if (method_exists($GLOBALS['xoopsDB'], "getProfiler")) {
                $profiler = $GLOBALS['xoopsDB']->getProfiler();
            } elseif (method_exists($GLOBALS['xoopsDB']->conn, "getProfiler")) {
                $profiler = $GLOBALS['xoopsDB']->conn->getProfiler();
            } else {
                return;
            }
        }
        if (!$profiler) {
            return;
        }

        return parent::database($profiler);
    }
}