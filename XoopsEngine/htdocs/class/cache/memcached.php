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
 * @version         $Id: memcached.php 2311 2008-11-09 05:49:31Z phppp $
 * @package         class
 * @subpackage      cache
 */
/**
 * Memcache storage engine for cache
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
 * Memcache storage engine for cache
 *
 * @package        cake
 * @subpackage    cake.cake.libs.cache
 */
class XoopsCacheMemcached extends XoopsCacheEngine
{
    /**
     * Memcache wrapper.
     *
     * @var object
     * @access private
     */
    var $memcached = null;
    /**
     * settings
     *         servers = string or array of memcached servers, default => 127.0.0.1
     *         compress = boolean, default => false
     *
     * @var array
     * @access public
     */
    var $settings = array();
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
    function init($settings = array())
    {
        if (!class_exists('Memcached')) {
            return false;
        }
        parent::init($settings);
        $defaults = array('servers' => array('127.0.0.1'));
        $this->settings = array_merge($defaults, $this->settings);

        if (!$this->settings['compress']) {
            $this->settings['compress'] = false;
        }
        if (!is_array($this->settings['servers'])) {
            $this->settings['servers'] = array($this->settings['servers']);
        }
		if(!isset($this->settings['distribution'])){
			$this->settings['distribution'] = Memcached::DISTRIBUTION_CONSISTENT;
		}
		if(!isset($this->settings['hash'])){
			$this->settings['hash'] = Memcached::HASH_DEFAULT;
		}

        $this->memcached =& new Memcached();

        foreach ($this->settings['servers'] as $server) {
            $parts = explode(':', $server);
            $host = $parts[0];
            $port = 11211;
            if (isset($parts[1])) {
                $port = $parts[1];
            }
            if (!$this->memcached->addServer($host, $port)) {
                return false;
            }
        }
		$this->memcached->setOption(Memcached::OPT_DISTRIBUTION , $this->settings['distribution']);
		$this->memcached->setOption(Memcached::OPT_COMPRESSION, $this->settings['compress']);
		$this->memcached->setOption(Memcached::OPT_HASH, $this->settings['hash']);
		$this->memcached->setOption(Memcached::OPT_NO_BLOCK, true);
        return true;
    }
    /**
     * Write data for key into cache
     *
     * @param string $key Identifier for the data
     * @param mixed $value Data to be cached
     * @param integer $duration How long to cache the data, in seconds
     * @return boolean True if the data was succesfully cached, false on failure
     * @access public
     */
    function write($key, &$value, $duration = 0)
    {
        return $this->memcached->set($key, $value, $duration);
    }
	/**
     * Write data for keys into cache
     *
     * @param array $key Identifier for the data
     * @param mixed $value Data to be cached
     * @param integer $duration How long to cache the data, in seconds
     * @return boolean True if the data was succesfully cached, false on failure
     * @access public
     */
    function writeMulti($array, $duration = 0)
    {
        return $this->memcached->setMulti($array, $duration);
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
        return $this->memcached->get($key);
    }
	/**
     * Read  keys from the cache
     *
     * @param array $keys Identifier for the data
     * @return mixed The cached data, or false if the data doesn't exist, has expired, or if there was an error fetching it
     * @access public
     */
    function readMulti($keys)
    {
        return $this->memcached->getMulti($keys);
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
        return $this->memcached->delete($key);
    }
    /**
     * Delete all keys from the cache
     *
     * @return boolean True if the cache was succesfully cleared, false otherwise
     * @access public
     */
    function clear()
    {
        return $this->memcached->flush();
    }

	function getResultCode()
	{
		return $this->memcached->getResultCode();
	}

	function getResultMessage()
	{
		return $this->memcached->getResultMessage();
	}
    
}
?>
