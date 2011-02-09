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
 * Cache engine For XOOPS
 *
 * @copyright       The XOOPS project http://www.xoops.org/
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @since           2.3.0
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * @version         $Id: model.php 1510 2008-04-27 10:22:47Z phppp $
 * @package         class
 * @subpackage      cache
 */
/**
 * Database Storage engine for cache
 *
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2005-2008, Cake Software Foundation, Inc.
 *                                1785 E. Sahara Avenue, Suite 490-204
 *                                Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright        Copyright 2005-2008, Cake Software Foundation, Inc.
 * @link                http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package            cake
 * @subpackage        cake.cake.libs.cache
 * @since            CakePHP(tm) v 1.2.0.4933
 * @version            $Revision: 6311 $
 * @modifiedby        $LastChangedBy: phpnut $
 * @lastmodified    $Date: 2008-01-02 00:33:52 -0600 (Wed, 02 Jan 2008) $
 * @license            http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Database Storage engine for cache
 *
 * @package        cake
 * @subpackage    cake.cake.libs.cache
 */
class XoopsCacheModel extends XoopsCacheEngine
{
    /**
     * settings
     *         className = name of the model to use, default => Cache
     *         fields = database fields that hold data and ttl, default => data, expires
     *
     * @var array
     * @access public
     */
    var $settings = array();

    /**
     * Model instance.
     *
     * @var object
     * @access private
     */
    var $model = null;
    /**
     * Model instance.
     *
     * @var object
     * @access private
     */
    var $fields = array();
    /**
     * Initialize the Cache Engine
     *
     * Called automatically by the cache frontend
     * To reinitialize the settings call Cache::engine('EngineName', [optional] settings = array());
     *
     * @param array $setting array of setting for the engine
     * @return boolean True if the engine has been successfully initialized, false if not
     * @access public
     */
    function init($settings)
    {
        parent::init($settings);
        $defaults = array('fields' => array('data', 'expires'));
        $this->settings = array_merge($defaults, $this->settings);
        $this->fields = $this->settings['fields'];
        $this->model = new XoopsCacheModelHandler($GLOBALS["xoopsDB"]);
        return true;
    }
    /**
     * Garbage collection. Permanently remove all expired and deleted data
     *
     * @access public
     */
    function gc()
    {
        return $this->model->deleteAll(new Criteria($this->fields[1], time, '<= '));
    }
    
    /**
     * Write data for key into cache
     *
     * @param string $key Identifier for the data
     * @param mixed $data Data to be cached
     * @param integer $duration How long to cache the data, in seconds
     * @return boolean True if the data was succesfully cached, false on failure
     * @access public
     */
    function write($key, $data, $duration)
    {
        //if (isset($this->settings['serialize'])) {
            $data = serialize($data);
        //}

        if (!$data) {
            return false;
        }
        $cache_obj = $this->model->create();
        $cache_obj->setVar($this->model::KEYNAME, $key);
        $cache_obj->setVar($this->fields[0], $data);
        $cache_obj->setVar($this->fields[1], time() + $duration);
        return $this->model->insert($cache_obj);
    }
    /**
     * Read a key from the cache
     *
     * @param string $key Identifier for the data
     * @return mixed The cached data, or false if the data doesn't exist, has expired, or if there was an error fetching it
     * @access public
     */
    function read($key)
    {
        $criteria = new CriteriaCompo(new Criteria($this->model::KEYNAME, $key));
        $criteria->add(new Criteria($this->fields[1], time(), ">"));
        $criteria->setLimit(1);
        $data = $this->model->getAll($criteria);
        if (!$data) {
            return null;
        }
         return unserialize($data[0]);
    }
    /**
     * Delete a key from the cache
     *
     * @param string $key Identifier for the data
     * @return boolean True if the value was succesfully deleted, false if it didn't exist or couldn't be removed
     * @access public
     */
    function delete($key)
    {
        return $this->model->delete($key);
    }
    /**
     * Delete all keys from the cache
     *
     * @return boolean True if the cache was succesfully cleared, false otherwise
     * @access public
     */
    function clear()
    {
        return $this->model->deleteAll();
    }

}

class XoopsCacheModelObject extends XoopsObject
{
    function __construct()
    {
        parent::__construct();
        $this->initVar('key',             XOBJ_DTYPE_TXTBOX);
        $this->initVar('data',             XOBJ_DTYPE_SOURCE);
        $this->initVar('expires',       XOBJ_DTYPE_INT);
    }
}

class XoopsCacheModelHandler extends XoopsPersistableObjectHandler
{
    const   TABLE   = "cache_model";
    const   CLASSNAME = "XoopsCacheModelObject";
    const   KEYNAME = "key";
}
?>