<?php
/**
 * XOOPS cache handler
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license         BSD Licence
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * @since           3.0
 * @package         xoops_Core
 * @version         $Id$
 */

class CacheModel extends XoopsObject
{
    /**
     * constructor
     */
    public function __construct()
    {
        $this->initVar('id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('expire', XOBJ_DTYPE_INT, null, 0);
        $this->initVar('level', XOBJ_DTYPE_TXTBOX, null, false, 64);
        //$this->initVar('key', XOBJ_DTYPE_TXTBOX, null, false, 64);
        $this->initVar('title', XOBJ_DTYPE_TXTBOX, null, false, 64);
        $this->initVar('module', XOBJ_DTYPE_TXTBOX, null, false, 64);
        $this->initVar('controller', XOBJ_DTYPE_TXTBOX, null, false, 64);
        $this->initVar('action', XOBJ_DTYPE_TXTBOX, null, false, 64);
        $this->initVar('custom', XOBJ_DTYPE_INT, null, 0);
    }
}

class XoopsCacheHandler extends XoopsPersistableObjectHandler
{
    function __construct(&$db)
    {
        parent::__construct($db, "cache", 'CacheModel', "id");
    }
}
?>