<?php
/**
 * Legacy XOOPS kernel
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       Xoops Engine http://www.xoopsengine.org/
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * @since           3.0
 * @package         Legacy
 * @version         $Id$
 */

class Legacy_Xoops
{
    /**
     * Convert a XOOPS path to a physical one
     *
     * @param string    $url        XOOPS path: with leading slash "/" - absolute path, do not convert; w/o "/" - relative path, relative to XOOPS root path
     * @param bool      $virtual    whether convert to full URI
     */
    public function path($url, $virtual = false)
    {
        return XOOPS::path($url, $virtual);
    }

    /**
     * Convert a XOOPS path to an URL
     */
    public function url($url, $absolute = false)
    {
        return XOOPS::url($url, $absolute);
    }

    /**
     * Build an URL with the specified request params
     */
    public function buildUrl($url, $params = array())
    {
        return XOOPS::buildUrl($url, $params);
    }
}
