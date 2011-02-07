<?php
// $Id: block.php 2902 2009-03-05 07:13:07Z phppp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author: Kazumi Ono (AKA onokazu)                                          //
// URL: http://www.myweb.ne.jp/, http://www.xoops.org/, http://jp.xoops.org/ //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}
define("XOOPS_SIDEBLOCK_LEFT",0);
define("XOOPS_SIDEBLOCK_RIGHT",1);
define("XOOPS_SIDEBLOCK_BOTH",2);
define("XOOPS_CENTERBLOCK_LEFT",3);
define("XOOPS_CENTERBLOCK_RIGHT",4);
define("XOOPS_CENTERBLOCK_CENTER",5);
define("XOOPS_CENTERBLOCK_ALL",6);
define("XOOPS_CENTERBLOCK_BOTTOMLEFT",7);
define("XOOPS_CENTERBLOCK_BOTTOMRIGHT",8);
define("XOOPS_CENTERBLOCK_BOTTOM",9);
define("XOOPS_BLOCK_INVISIBLE",0);
define("XOOPS_BLOCK_VISIBLE",1);

/**
 * @author  Kazumi Ono <onokazu@xoops.org>
 * @copyright copyright (c) 2000 XOOPS.org
 **/

/**
 * A block
 *
 * @author Kazumi Ono <onokazu@xoops.org>
 * @copyright copyright (c) 2000 XOOPS.org
 *
 * @package kernel
 **/
class XoopsBlock extends XoopsObject
{

    /**
     * constructor
     *
     * @param mixed $id
     **/
    function XoopsBlock($id = null)
    {
        $this->initVar('bid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('mid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('func_num', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('options', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('name', XOBJ_DTYPE_TXTBOX, null, true, 150);
        //$this->initVar('position', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('title', XOBJ_DTYPE_TXTBOX, null, false, 150);
        $this->initVar('content', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('side', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('weight', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('visible', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('block_type', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('c_type', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('isactive', XOBJ_DTYPE_INT, null, false);
        $this->initVar('dirname', XOBJ_DTYPE_TXTBOX, null, false, 50);
        $this->initVar('func_file', XOBJ_DTYPE_TXTBOX, null, false, 50);
        $this->initVar('show_func', XOBJ_DTYPE_TXTBOX, null, false, 50);
        $this->initVar('edit_func', XOBJ_DTYPE_TXTBOX, null, false, 50);
        $this->initVar('template', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('bcachetime', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('last_modified', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('cache', XOBJ_DTYPE_TXTBOX, null, false, 50);

        // for backward compatibility
        if (isset($id)) {
            if (is_array($id)) {
                $this->assignVars($id);
            } else {
                $blkhandler =& xoops_gethandler('block');
                $obj =& $blkhandler->get($id);
                foreach (array_keys($obj->getVars()) as $i) {
                    $this->assignVar($obj->getVar($i, 'n'));
                }
            }
        }
    }

    /**
     * return the content of the block for output
     *
     * @param string $format
     * @param string $c_type type of content<br>
     * Legal value for the type of content<br>
     * <ul><li>H : custom HTML block
     * <li>P : custom PHP block
     * <li>S : use text sanitizater (smilies enabled)
     * <li>T : use text sanitizater (smilies disabled)</ul>
     * @return string content for output
     **/
    function getContent($format = 'S', $c_type = 'T')
    {
        global $xoops;

        switch ($format) {
        case 'S':
            if ($c_type == 'H') {
                return str_replace('{X_SITEURL}', $xoops->url('www'), $this->getVar('content', 'N'));
            } elseif ($c_type == 'P') {
                ob_start();
                echo eval($this->getVar('content', 'N'));
                $content = ob_get_contents();
                ob_end_clean();
                return str_replace('{X_SITEURL}', $xoops->url('www'), $content);
            } elseif ($c_type == 'S') {
                $myts =& MyTextSanitizer::getInstance();
                $content = str_replace('{X_SITEURL}', $xoops->url('www'), $this->getVar('content', 'N'));
                return $myts->displayTarea($content, 0, 1);
            } else {
                $myts =& MyTextSanitizer::getInstance();
                $content = str_replace('{X_SITEURL}', $xoops->url('www'), $this->getVar('content', 'N'));
                return $myts->displayTarea($content, 0, 0);
            }
            break;
        case 'E':
            return $this->getVar('content', 'E');
            break;
        default:
            return $this->getVar('content', 'N');
            break;
        }
    }

    /**
     * (HTML-) form for setting the options of the block
     *
     * @return string HTML for the form, FALSE if not defined for this block
     **/
    function getOptions()
    {
        if (!$this->isCustom()) {
            $edit_func = $this->getVar('edit_func');
            if (!$edit_func) {
                return false;
            }
            global $xoops;
            /*
            if (file_exists($xoops->path('www/modules/' . $this->getVar('dirname') . '/blocks/' . $this->getVar('func_file')))) {
                $location = "www/modules/" . $this->getVar('dirname');
            } elseif (file_exists($xoops->path('lib/apps/' . $this->getVar('dirname') . '/blocks/' . $this->getVar('func_file')))) {
                $location = "lib/apps/" . $this->getVar('dirname');
            } else {
                return false;
            }
            if (file_exists($file = $xoops->path($location . '/language/' . $GLOBALS['xoopsConfig']['language'] . '/blocks.php'))) {
                include_once $file;
            } elseif (file_exists($file = $xoops->path($location . '/language/english/blocks.php'))) {
                include_once $file;
            }
            include_once $xoops->path($location . '/blocks/' . $this->getVar('func_file'));
            */
            XOOPS::registry('translate')->loadTranslation('blocks', $this->getVar('dirname'));
            $info = XOOPS::service('registry')->module->read($this->getVar('dirname'));
            include_once $xoops->path($info['path'] . '/' . $this->getVar('dirname') . '/blocks/' . $this->getVar('func_file'));
            $options = explode('|', $this->getVar('options'));
            $edit_form = $edit_func($options);
            if (!$edit_form) {
                return false;
            }
            return $edit_form;
        } else {
            return false;
        }
    }

    function isCustom()
    {
        return in_array($this->getVar("block_type") , array('C' , 'E'));
    }

    function buildBlock()
    {
        global $xoopsConfig, $xoopsOption;
        $block = array();
        if (!$this->isCustom()) {
            // get block display function
            $show_func = $this->getVar('show_func');
            if (!$show_func) {
                return false;
            }
            global $xoops;
            /*
            if (file_exists($xoops->path('www/modules/' . $this->getVar('dirname') . '/blocks/' . $this->getVar('func_file')))) {
                $location = "www/modules/" . $this->getVar('dirname');
            } elseif (file_exists($xoops->path('app/' . $this->getVar('dirname') . '/blocks/' . $this->getVar('func_file')))) {
                $location = "app/" . $this->getVar('dirname');
            } else {
                return false;
            }
            if (file_exists($file = $xoops->path($location . '/language/' . $GLOBALS['xoopsConfig']['language'] . '/blocks.php'))) {
                include_once $file;
            } elseif (file_exists($file = $xoops->path($location . '/language/english/blocks.php'))) {
                include_once $file;
            }
            include_once $xoops->path($location . '/blocks/' . $this->getVar('func_file'));
            */
            XOOPS::registry('translate')->loadTranslation('blocks', $this->getVar('dirname'));
            $info = XOOPS::service('registry')->module->read($this->getVar('dirname'));
            include_once $xoops->path($info['path'] . '/' . $this->getVar('dirname') . '/blocks/' . $this->getVar('func_file'));
            $options = explode('|', $this->getVar('options'));
            $block = $show_func($options);
            if (!$block) {
                return false;
            }
        } else {
            // it is a custom block, so just return the contents
            $block['content'] = $this->getContent("S", $this->getVar("c_type"));
            if (empty($block['content'])) {
                return false;
            }
        }
        return $block;
    }
}


/**
 * XOOPS block handler class. (Singelton)
 *
 * This class is responsible for providing data access mechanisms to the data source
 * of XOOPS block class objects.
 *
 * @author  Kazumi Ono <onokazu@xoops.org>
 * @author  Taiwen Jiang <phppp@users.sourceforge.net>
 * @package kernel
 */
class XoopsBlockHandler extends XoopsPersistableObjectHandler
{
    function __construct(&$db)
    {
        parent::__construct($db, "newblocks", 'XoopsBlock', "bid", "title");
    }

    function delete(&$object, $force = false)
    {
        if (!parent::delete($object, $force)) {
            return false;
        }

        $sql = sprintf("DELETE FROM %s WHERE gperm_name = 'block_read' AND gperm_itemid = %u AND gperm_modid = 1", $this->db->prefix('group_permission'), $object->getVar('bid'));
        $this->db->query($sql);
        $sql = sprintf("DELETE FROM %s WHERE block_id = %u", $this->db->prefix('block_module_link'), $object->getVar('bid'));
        $this->db->query($sql);
        return true;
    }

    /**
    * get all the blocks that match the supplied parameters
    * @param $side   0: sideblock - left
    *        1: sideblock - right
    *        2: sideblock - left and right
    *        3: centerblock - left
    *        4: centerblock - right
    *        5: centerblock - center
    *        6: centerblock - left, right, center
    * @param $groupid   groupid (can be an array)
    * @param $visible   0: not visible 1: visible
    * @param $orderby   order of the blocks
    * @returns array of block objects
    */
    function getAllBlocksByGroup($groupid, $asobject = true, $side = null, $visible = null, $orderby = "b.weight,b.bid", $isactive = 1)
    {
        $db = $this->db;
        $ret = array();
        if (!$asobject) {
            $sql = "SELECT b.bid ";
        } else {
            $sql = "SELECT b.* ";
        }
        $sql .= "FROM ".$db->prefix("newblocks")." b LEFT JOIN ".$db->prefix("group_permission")." l ON l.gperm_itemid=b.bid WHERE gperm_name = 'block_read' AND gperm_modid = 1";
        if (is_array($groupid)) {
            $sql .= " AND (l.gperm_groupid=".$groupid[0]."";
            $size = count($groupid);
            if ($size  > 1) {
                for ($i = 1; $i < $size; $i++) {
                    $sql .= " OR l.gperm_groupid=".$groupid[$i]."";
                }
            }
            $sql .= ")";
        } else {
            $sql .= " AND l.gperm_groupid=".$groupid."";
        }
        $sql .= " AND b.isactive=".$isactive;
        if (isset($side)) {
            // get both sides in sidebox? (some themes need this)
            if ($side == XOOPS_SIDEBLOCK_BOTH) {
                $side = "(b.side=0 OR b.side=1)";
            } elseif ($side == XOOPS_CENTERBLOCK_ALL) {
                $side = "(b.side=3 OR b.side=4 OR b.side=5 OR b.side=7 OR b.side=8 OR b.side=9)";
            } else {
                $side = "b.side=".$side;
            }
            $sql .= " AND ".$side;
        }
        if (isset($visible)) {
            $sql .= " AND b.visible=$visible";
        }
        $sql .= " ORDER BY $orderby";
        $result = $db->query($sql);
        $added = array();
        while ($myrow = $db->fetchArray($result)) {
            if (!in_array($myrow['bid'], $added)) {
                if (!$asobject) {
                    $ret[] = $myrow['bid'];
                } else {
                    $ret[] = new XoopsBlock($myrow);
                }
                array_push($added, $myrow['bid']);
            }
        }
        //echo $sql;
        return $ret;
    }

    function getAllBlocks($rettype = "object", $side = null, $visible = null, $orderby = "side,weight,bid", $isactive = 1)
    {
        $db = $this->db;
        $ret = array();
        $where_query = " WHERE isactive=".$isactive;
        if (isset($side)) {
            // get both sides in sidebox? (some themes need this)
            if ($side == 2) {
                $side = "(side=0 OR side=1)";
            } elseif ($side == 6) {
                $side = "(side=3 OR side=4 OR side=5 OR side=7 OR side=8 OR side=9)";
            } else {
                $side = "side=".$side;
            }
            $where_query .= " AND ".$side;
        }
        if (isset($visible)) {
            $where_query .= " AND visible=$visible";
        }
        $where_query .= " ORDER BY $orderby";
        switch ($rettype) {
        case "object":
            $sql = "SELECT * FROM ".$db->prefix("newblocks")."".$where_query;
            $result = $db->query($sql);
            while ($myrow = $db->fetchArray($result)) {
                $ret[] = new XoopsBlock($myrow);
            }
            break;
        case "list":
            $sql = "SELECT * FROM ".$db->prefix("newblocks")."".$where_query;
            $result = $db->query($sql);
            while ($myrow = $db->fetchArray($result)) {
                $block = new XoopsBlock($myrow);
                //$title = ($block->getVar("block_type") != "C") ? $block->getVar("name") : $block->getVar("title");
                $title = $block->getVar("title");
                $title = empty($title) ? $block->getVar("name") : $title;
                $ret[$block->getVar("bid")] = $title;
            }
            break;
        case "id":
            $sql = "SELECT bid FROM ".$db->prefix("newblocks")."".$where_query;
            $result = $db->query($sql);
            while ($myrow = $db->fetchArray($result)) {
                $ret[] = $myrow['bid'];
            }
            break;
        }
        //echo $sql;
        return $ret;
    }

    function getByModule($moduleid, $asobject = true)
    {
        $moduleid = intval($moduleid);
        $db = $this->db;
        if ($asobject == true) {
            $sql = $sql = "SELECT * FROM ".$db->prefix("newblocks")." WHERE mid=".$moduleid."";
        } else {
            $sql = "SELECT bid FROM ".$db->prefix("newblocks")." WHERE mid=".$moduleid."";
        }
        $result = $db->query($sql);
        $ret = array();
        while($myrow = $db->fetchArray($result)) {
            if ($asobject) {
                $ret[] = new XoopsBlock($myrow);
            } else {
                $ret[] = $myrow['bid'];
            }
        }
        return $ret;
    }

    function getAllByGroupModule($groupid, $module_id = 0, $toponlyblock = false, $visible = null, $orderby = 'b.weight, m.block_id', $isactive = 1)
    {
        $isactive = intval($isactive);
        $db = $this->db;
        $ret = array();
        if (isset($groupid)) {
            $sql = "SELECT DISTINCT gperm_itemid FROM ".$db->prefix('group_permission')." WHERE gperm_name = 'block_read' AND gperm_modid = 1";
            if (is_array($groupid)) {
                $sql .= ' AND gperm_groupid IN ('.implode(',', $groupid).')';
            } else {
                if (intval($groupid) > 0) {
                    $sql .= ' AND gperm_groupid='.intval($groupid);
                }
            }
            $result = $db->query($sql);
            $blockids = array();
            while ($myrow = $db->fetchArray($result)) {
                $blockids[] = $myrow['gperm_itemid'];
            }
            if (empty($blockids)) {
                return $blockids;
            }
        }
        $sql = 'SELECT b.* FROM '.$db->prefix('newblocks').' b, '.$db->prefix('block_module_link').' m WHERE m.block_id=b.bid';
        $sql .= ' AND b.isactive='.$isactive;
        if (isset($visible)) {
            $sql .= ' AND b.visible='.intval($visible);
        }
        if (!isset($module_id)) {
        } elseif (!empty($module_id)) {
            $sql .= ' AND m.module_id IN (0,'. intval($module_id);
            if ($toponlyblock) {
                $sql .= ',-1';
            }
            $sql .= ')';
        } else {
            if ($toponlyblock) {
                $sql .= ' AND m.module_id IN (0,-1)';
            } else {
                $sql .= ' AND m.module_id=0';
            }
        }
        if (!empty($blockids)) {
            $sql .= ' AND b.bid IN ('.implode(',', $blockids).')';
        }
        $sql .= ' ORDER BY '.$orderby;
        $result = $db->query($sql);
        while ($myrow = $db->fetchArray($result)) {
            $block = new XoopsBlock($myrow);
            $ret[$myrow['bid']] =& $block;
            unset($block);
        }
        return $ret;
    }

    function getNonGroupedBlocks($module_id = 0, $toponlyblock = false, $visible = null, $orderby = 'b.weight, m.block_id', $isactive = 1)
    {
        $db = $this->db;
        $ret = array();
        $bids = array();
        $sql = "SELECT DISTINCT(bid) from ".$db->prefix('newblocks');
        if ($result = $db->query($sql)) {
            while ($myrow = $db->fetchArray($result)) {
                $bids[] = $myrow['bid'];
            }
        }
        $sql = "SELECT DISTINCT(p.gperm_itemid) from ".$db->prefix('group_permission')." p, ".$db->prefix('groups')." g WHERE g.groupid=p.gperm_groupid AND p.gperm_name='block_read'";
        $grouped = array();
        if ($result = $db->query($sql)) {
            while ($myrow = $db->fetchArray($result)) {
                $grouped[] = $myrow['gperm_itemid'];
            }
        }
        $non_grouped = array_diff($bids, $grouped);
        if (!empty($non_grouped)) {
            $sql = 'SELECT b.* FROM '.$db->prefix('newblocks').' b, '.$db->prefix('block_module_link').' m WHERE m.block_id=b.bid';
            $sql .= ' AND b.isactive='.intval($isactive);
            if (isset($visible)) {
                $sql .= ' AND b.visible='.intval($visible);
            }
            if (!isset($module_id)) {
            } elseif (!empty($module_id)) {
                $sql .= ' AND m.module_id IN (0,'. intval($module_id);
                if ($toponlyblock) {
                    $sql .= ',-1';
                }
                $sql .= ')';
            } else {
                if ($toponlyblock) {
                    $sql .= ' AND m.module_id IN (0,-1)';
                } else {
                    $sql .= ' AND m.module_id=0';
                }
            }
            $sql .= ' AND b.bid IN ('.implode(',', $non_grouped).')';
            $sql .= ' ORDER BY '.$orderby;
            $result = $db->query($sql);
            while ($myrow = $db->fetchArray($result)) {
                $block = new XoopsBlock($myrow);
                $ret[$myrow['bid']] =& $block;
                unset($block);
            }
        }
        return $ret;
    }

    function countSimilarBlocks($moduleId, $funcNum, $showFunc = null)
    {
        $funcNum = intval($funcNum);
        $moduleId = intval($moduleId);
        if ($funcNum < 1 || $moduleId < 1) {
            // invalid query
            return 0;
        }
        $db = $this->db;
        if (isset($showFunc)) {
            // showFunc is set for more strict comparison
            $sql = sprintf("SELECT COUNT(*) FROM %s WHERE mid = %d AND func_num = %d AND show_func = %s", $db->prefix('newblocks'), $moduleId, $funcNum, $db->quoteString(trim($showFunc)));
        } else {
            $sql = sprintf("SELECT COUNT(*) FROM %s WHERE mid = %d AND func_num = %d", $db->prefix('newblocks'), $moduleId, $funcNum);
        }
        if (!$result = $db->query($sql)) {
            return 0;
        }
        list($count) = $db->fetchRow($result);
        return $count;
    }
}
?>