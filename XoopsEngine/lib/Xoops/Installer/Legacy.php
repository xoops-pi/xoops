<?php
/**
 * XOOPS legacy module installer
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
 * @package         Xoops_Installer
 * @subpackage      Installer
 * @version         $Id$
 */

class Xoops_Installer_Legacy extends Xoops_Installer_App
{
    public function __construct($installer)
    {
        parent::__construct($installer);
        if (empty($GLOBALS['xoopsDB'])) {
            $options = array(
                "prefix"    => XOOPS::registry("db")->prefix(),
            );
            $GLOBALS['xoopsDB'] = new Xoops_Zend_Db_Legacy(XOOPS::registry("db"), $options);
        }
        if (empty($GLOBALS['xoopsConfig'])) {
            $GLOBALS['xoopsConfig'] = Xoops::config();
        }
    }

    public function install($name)
    {
        $return = array();
        $message = array();

        Xoops_Zend_Db_File_Mysql::reset();
        //XOOPS::service('translate')->loadTranslation('modinfo', $name);
        // Load configuration
        $config = $this->installer->loadConfig($name);
        $model = XOOPS::getModel("module");
        $moduleData = array(
            "name"      => $config['name'],
            "version"   => $config['version'],
            "dirname"   => $name,
        );

        $module = $model->createRow();
        $module->setFromArray($moduleData);
        // execute preInstall
        if (!empty($config['onInstall'])) {
            include Xoops::service('module')->getPath($name) . '/' . $config['onInstall'];
            $func = "xoops_module_pre_install_{$name}";
            if (function_exists($func)) {
                // Initialize module
                $module_handler = XOOPS::getHandler('module');
                $moduleObject = $module_handler->create();
                $moduleObject->setVar('name', $config['name'], true);
                $moduleObject->setVar('version', $config['version'], true);
                $moduleObject->setVar('dirname', $name, true);

                $ret = $func($moduleObject);
                if (!$ret) {
                    $message[] = "xoops_module_pre_install_{$name} failed";
                } else {
                    $message[] = "xoops_module_pre_install_{$name} executed";
                }
                $return['preinstall'] = array('status' => $ret, "message" => $message);
                if (false === $ret) {
                    return $return;
                }
            }
        }

        // save module entry into database
        if (!$moduleId = $model->insert($moduleData)) {
            $return['module']['status'] = false;
            $return['module']['message'] = array("Module insert failed");
            return $return;
        }
        $module->id = $moduleId;
        $this->updateMeta();

        // process extensions
        $extensions = array();
        if (!empty($config['extensions'])) {
            $extensions = array_keys($config['extensions']);
        }
        $extensionList = array_unique(array_merge($this->loadExtensions(), $extensions));
        foreach ($extensionList as $extension) {
            $action = __FUNCTION__;
            if (!$extensionHandler = $this->installer->loadExtension($extension, $module)) continue;
            $ret = $extensionHandler->{$action}($message);
            $return[$extension] = array('status' => $ret, "message" => $message);
            if (false === $ret) {
                $model->delete(array("id = ?" => $module->id));
                return $return;
            }
        }

        // execute postInstall
        if (!empty($config['onInstall'])) {
            $func = "xoops_module_install_{$name}";
            if (function_exists($func)) {
                if (!isset($moduleObject)) {
                    // Initialize module
                    $module_handler = XOOPS::getHandler('module');
                    $moduleObject = $module_handler->create();
                    $moduleObject->setVar('name', $config['name'], true);
                    $moduleObject->setVar('version', $config['version'], true);
                    $moduleObject->setVar('dirname', $name, true);
                }

                $moduleObject->setVar("mid", $module->id);
                $ret = $func($moduleObject);
                if (!$ret) {
                    $message[] = "xoops_module_install_{$name} failed";
                } else {
                    $message[] = "xoops_module_install_{$name} executed";
                }
                $return['postinstall'] = array('status' => $ret, "message" => $message);
            }
        }

        return $return;
    }

    public function update($name)
    {
        $return = array();
        $message = array();

        //$config =& $this->installer->config;
        $model = XOOPS::getModel("module");
        $module = $model->load($name);
        $config = $this->installer->loadConfig($module->parent ? $module->parent : $name);
        $oldVersion = $module->version;
        $moduleData = array(
            "version"   => $config['version'],
        );
        $module->version = $config['version'];

        // execute preUpdate
        if (!empty($config['onUpdate'])) {
            include Xoops::service('module')->getPath($name) . '/' . $config['onUpdate'];
            $func = "xoops_module_pre_update_{$name}";
            if (function_exists($func)) {
                // Initialize module
                $module_handler = XOOPS::getHandler('module');
                $moduleObject = $module_handler->getByDirname($name);
                $moduleObject->setVar('version', $config['version'], true);
                $ret = $func($moduleObject, $oldVersion);
                if (!$ret) {
                    $message[] = "xoops_module_pre_update_{$name} failed";
                } else {
                    $message[] = "xoops_module_pre_update_{$name} executed";
                }
                $return['preupdate'] = array('status' => $ret, "message" => $message);
                if (false === $ret) {
                    return $return;
                }
            }
        }

        // save module entry into database
        if (!$model->update($moduleData, array("id = ?" => $module->id))) {
            $return['module']['status'] = false;
            $return['module']['message'] = "Module update failed";
            return $return;
        }

        // process extensions
        $extensions = array();
        if (!empty($config['extensions'])) {
            $extensions = array_keys($config['extensions']);
        }
        $extensionList = array_unique(array_merge($this->loadExtensions(), $extensions));
        foreach ($extensionList as $extension) {
            if (!$extensionHandler = $this->installer->loadExtension($extension, $module, null, $oldVersion)) continue;
            $action = __FUNCTION__;
            $ret = $extensionHandler->{$action}($message);
            $return[$extension] = array('status' => $ret, "message" => $message);
            if (false === $ret) {
                return $return;
            }
        }

        // execute postUpdate
        if (!empty($config['onUpdate'])) {
            $return['postUpdate'] = $this->updatepre($module, $config, $instHandler);
            $func = "xoops_module_update_{$name}";
            if (function_exists($func)) {
                if (!isset($moduleObject)) {
                    // Initialize module
                    $module_handler = XOOPS::getHandler('module');
                    $moduleObject = $module_handler->getByDirname($name);
                    $moduleObject->setVar('version', $config['version'], true);
                }
                $ret = $func($moduleObject, $oldVersion);
                if (!$ret) {
                    $message[] = "xoops_module_update_{$name} failed";
                } else {
                    $message[] = "xoops_module_update_{$name} executed";
                }
                $return['postupdate'] = array('status' => $ret, "message" => $message);
            }
        }

        return $return;
    }

    public function unInstall($name)
    {
        $return = array();
        $message = array();

        $model = XOOPS::getModel("module");
        if (!$module = $model->load($name)) {
            $module = $model->createRow(array("dirname" => $name));
        }
        $config = $this->installer->loadConfig($module->parent ? $module->parent : $name);

        // execute preUninstall
        if (!empty($config['onUninstall'])) {
            include Xoops::service('module')->getPath($name) . '/' . $config['onUninstall'];
            $func = "xoops_module_pre_uninstall_{$name}";
            if (function_exists($func)) {
                // Initialize module
                $module_handler = XOOPS::getHandler('module');
                if (!$moduleObject = $module_handler->getByDirname($name)) {
                    $moduleObject = $name;
                }
                $ret = $func($moduleObject);
                if (!$ret) {
                    $message[] = "xoops_module_pre_uninstall_{$name} failed";
                } else {
                    $message[] = "xoops_module_pre_uninstall_{$name} executed";
                }
                $return['preuninstall'] = array('status' => $ret, "message" => $message);
                if (false === $ret) {
                    return $return;
                }
            }
        }

        // remove module entity from database
        if (is_object($module) && $module->id && !$model->delete(array("id = ?" => $module->id))) {
            $return['module']['status'] = false;
            $return['module']['message'] = "Module delete failed";
            return $return;
        }
        $this->updateMeta();

        // process extensions
        $extensions = array();
        if (!empty($config['extensions'])) {
            $extensions = array_keys($config['extensions']);
        }
        $extensionList = array_unique(array_merge($this->loadExtensions(), $extensions));
        foreach ($extensionList as $extension) {
            if (!$extensionHandler = $this->installer->loadExtension($extension, $module)) continue;
            $action = __FUNCTION__;
            $ret = $extensionHandler->{$action}($message);
            $return[$extension] = array('status' => $ret, "message" => $message);
            if (false === $ret) {
                return $return;
            }
        }

        // execute postUninstall
        if (!empty($config['onUninstall'])) {
            $func = "xoops_module_uninstall_{$name}";
            if (function_exists($func)) {
                if (!isset($moduleObject)) {
                    // Initialize module
                    $module_handler = XOOPS::getHandler('module');
                    if (!$moduleObject = $module_handler->getByDirname($name)) {
                        $moduleObject = $name;
                    }
                }
                $ret = $func($moduleObject);
                if (!$ret) {
                    $message[] = "xoops_module_uninstall_{$name} failed";
                } else {
                    $message[] = "xoops_module_uninstall_{$name} executed";
                }
                $return['postuninstall'] = array('status' => $ret, "message" => $message);
            }
        }

        return $return;
    }

    public function activate($name)
    {
        $return = array();
        $message = array();

        $model = XOOPS::getModel("module");
        $status = $model->update(array("active" => 1), array("dirname = ?" => $name));
        if (!$status) {
            $return['module']['status'] = false;
            $return['module']['message'] = "Module activation failed";
            return $return;
        }
        $this->updateMeta();
        $module = $model->load($name);
        $config = $this->installer->loadConfig($module->parent ? $module->parent : $name);

        // process extensions
        $extensions = array();
        if (!empty($config['extensions'])) {
            $extensions = array_keys($config['extensions']);
        }
        $extensionList = array_unique(array_merge($this->loadExtensions(), $extensions));
        foreach ($extensionList as $extension) {
            if (!$extensionHandler = $this->installer->loadExtension($extension, $module)) continue;
            $action = __FUNCTION__;
            $ret = $extensionHandler->{$action}($message);
            $return[$extension] = array('status' => $ret, "message" => $message);
            if (false === $ret) {
                return $return;
            }
        }
        return $return;
    }

    public function deactivate($name)
    {
        $return = array();
        $message = array();

        if ($name == "system") {
            $return['module']['status'] = false;
            $return['module']['message'] = "The module is not allowed to deactivate";
            return $return;
        }

        $model = XOOPS::getModel("module");
        $status = $model->update(array("active" => 0), array("dirname = ?" => $name));
        if (!$status) {
            $return['module']['status'] = false;
            $return['module']['message'] = "Module deactivation failed";
            return $return;
        }
        $this->updateMeta();
        $module = $model->load($name);
        $config = $this->installer->loadConfig($module->parent ? $module->parent : $name);

        // process extensions
        $extensions = array();
        if (!empty($config['extensions'])) {
            $extensions = array_keys($config['extensions']);
        }
        $extensionList = array_unique(array_merge($this->loadExtensions(), $extensions));
        foreach ($extensionList as $extension) {
            if (!$extensionHandler = $this->installer->loadExtension($extension, $module)) continue;
            $action = __FUNCTION__;
            $ret = $extensionHandler->{$action}($message);
            $return[$extension] = array('status' => $ret, "message" => $message);
            if (false === $ret) {
                return $return;
            }
        }
        return $return;
    }
}