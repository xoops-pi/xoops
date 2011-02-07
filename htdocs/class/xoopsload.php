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
 * Xoops Autoload class
 *
 * @todo        For PHP 5 compliant
 *
 * @copyright   The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license     http://www.fsf.org/copyleft/gpl.html GNU public license
 * @package     class
 * @since       2.3.0
 * @author      Taiwen Jiang <phppp@users.sourceforge.net>
 * @version     $Id: xoopsload.php 2902 2009-03-05 07:13:07Z phppp $
 */

class XoopsLoad
{
    //static  $loaded;
    //static  $configs;

    public static function load($name, $type = "core")
    {
        static $loaded;

        $name = strtolower($name);

        $type = empty($type) ? "core" : $type;

        if (isset($loaded[$type][$name])) {
            return $loaded[$type][$name];
        }
        if (class_exists($name)) {
            return true;
        }

        $isloaded = false;
        switch ($type) {
        case "framework":
            $isloaded = XoopsLoad::loadFramework($name);
            break;
        case "class":
        case "core":
            $type = "core";
            $isloaded = XoopsLoad::loadCore($name);
            break;
        default:
            $isloaded = XoopsLoad::loadModule($name, $type);
            break;
        }

        $loaded[$type][$name] = $isloaded;

        return $loaded[$type][$name];
    }

    /**
     * Load core class
     *
     * @access  private
     */
    public static function loadCore($name)
    {
        static $configs;
        if (class_exists($name)) {
            return true;
        }

        if (!isset($configs)) {
            $configs = XoopsLoad::loadCoreConfig();
        }

        if ( isset($configs[$name]) ) {
            require $configs[$name];
            if ( class_exists($name) && method_exists($name, '__autoload') ) {
                call_user_func( array($name, '__autoload') );
            }
            return true;
        } elseif (file_exists(XOOPS_ROOT_PATH . "/class/{$name}.php")) {
            include_once  XOOPS_ROOT_PATH . "/class/{$name}.php";
            return class_exists("Xoops" . ucfirst($name));
        }

        return false;
    }

    /**
     * Load Framework class
     *
     * @access  private
     */
    public static function loadFramework($name)
    {
        if (!include XOOPS_ROOT_PATH . "/Frameworks/{$name}/xoops{$name}.php") {
            return false;
        }
        return class_exists("Xoops" . ucfirst($name));
    }

    /**
     * Load module class
     *
     * @access  private
     */
    public static function loadModule($name, $dirname = null)
    {
        if (empty($dirname)) {
            return false;
        }

        if (!include XOOPS_ROOT_PATH . "/modules/{$dirname}/class/{$name}.php") {
            return false;
        }
        return class_exists(ucfirst($dirname) . ucfirst($name));
    }

    public static function loadCoreConfig()
    {
        return $configs = array(
            'XoopsLogger'               => XOOPS_ROOT_PATH . '/class/logger/xoopslogger.php',
            'xoopspagenav'              => XOOPS_ROOT_PATH . '/class/pagenav.php',
            'xoopslists'                => XOOPS_ROOT_PATH . '/class/xoopslists.php',
            'uploader'                  => XOOPS_ROOT_PATH . '/class/uploader.php',
            'utility'                   => XOOPS_ROOT_PATH . '/class/utility/xoopsutility.php',
            'captcha'                   => XOOPS_ROOT_PATH . '/class/captcha/xoopscaptcha.php',
            'cache'                     => XOOPS_ROOT_PATH . '/class/cache/xoopscache.php',
            'file'                      => XOOPS_ROOT_PATH . '/class/file/xoopsfile.php',
            'model'                     => XOOPS_ROOT_PATH . '/class/model/xoopsmodel.php',

            'xoopslocal'                => XOOPS_ROOT_PATH . '/include/xoopslocal.php',
            'xoopslocalabstract'        => XOOPS_ROOT_PATH . '/class/xoopslocal.php',
            'xoopseditor'               => XOOPS_ROOT_PATH . '/class/xoopseditor/xoopseditor.php',
            'xoopseditorhandler'        => XOOPS_ROOT_PATH . '/class/xoopseditor/xoopseditor.php',

            'xoopsformloader'           => XOOPS_ROOT_PATH . '/class/xoopsformloader.php',
            'xoopsformelement'          => XOOPS_ROOT_PATH . '/class/xoopsform/formelement.php',
            'xoopsform'                 => XOOPS_ROOT_PATH . '/class/xoopsform/form.php',
            'xoopsformlabel'            => XOOPS_ROOT_PATH . '/class/xoopsform/formlabel.php',
            'xoopsformselect'           => XOOPS_ROOT_PATH . '/class/xoopsform/formselect.php',
            'xoopsformpassword'         => XOOPS_ROOT_PATH . '/class/xoopsform/formpassword.php',
            'xoopsformbutton'           => XOOPS_ROOT_PATH . '/class/xoopsform/formbutton.php',
            'xoopsformcheckBox'         => XOOPS_ROOT_PATH . '/class/xoopsform/formcheckbox.php',
            'xoopsformhidden'           => XOOPS_ROOT_PATH . '/class/xoopsform/formhidden.php',
            'xoopsformfile'             => XOOPS_ROOT_PATH . '/class/xoopsform/formfile.php',
            'xoopsformradio'            => XOOPS_ROOT_PATH . '/class/xoopsform/formradio.php',
            'xoopsformradioyn'          => XOOPS_ROOT_PATH . '/class/xoopsform/formradioyn.php',
            'xoopsformselectcountry'    => XOOPS_ROOT_PATH . '/class/xoopsform/formselectcountry.php',
            'xoopsformselecttimezone'   => XOOPS_ROOT_PATH . '/class/xoopsform/formselecttimezone.php',
            'xoopsformselectlang'       => XOOPS_ROOT_PATH . '/class/xoopsform/formselectlang.php',
            'xoopsformselectgroup'      => XOOPS_ROOT_PATH . '/class/xoopsform/formselectgroup.php',
            'xoopsformselectuser'       => XOOPS_ROOT_PATH . '/class/xoopsform/formselectuser.php',
            'xoopsformselecttheme'      => XOOPS_ROOT_PATH . '/class/xoopsform/formselecttheme.php',
            'xoopsformselectmatchoption'    => XOOPS_ROOT_PATH . '/class/xoopsform/formselectmatchoption.php',
            'xoopsformtext'             => XOOPS_ROOT_PATH . '/class/xoopsform/formtext.php',
            'xoopsformtextarea'         => XOOPS_ROOT_PATH . '/class/xoopsform/formtextarea.php',
            'xoopsformdhtmltextarea'    => XOOPS_ROOT_PATH . '/class/xoopsform/formdhtmltextarea.php',
            'xoopsformelementtray'      => XOOPS_ROOT_PATH . '/class/xoopsform/formelementtray.php',
            'Xoopsthemeform'            => XOOPS_ROOT_PATH . '/class/xoopsform/themeform.php',
            'Xoopssimpleform'           => XOOPS_ROOT_PATH . '/class/xoopsform/simpleform.php',
            'xoopsformtextdateselect'   => XOOPS_ROOT_PATH . '/class/xoopsform/formtextdateselect.php',
            'xoopsformdatetime'         => XOOPS_ROOT_PATH . '/class/xoopsform/formdatetime.php',
            'xoopsformhiddentoken'      => XOOPS_ROOT_PATH . '/class/xoopsform/formhiddentoken.php',
            'xoopsformcolorpicker'      => XOOPS_ROOT_PATH . '/class/xoopsform/formcolorpicker.php',
            'xoopsformcaptcha'          => XOOPS_ROOT_PATH . '/class/xoopsform/formcaptcha.php',
            'xoopsformeditor'           => XOOPS_ROOT_PATH . '/class/xoopsform/formeditor.php',
            'xoopsformselecteditor'     => XOOPS_ROOT_PATH . '/class/xoopsform/formselecteditor.php',
        );
    }

    public static function loadConfig($data = null)
    {
        if (is_array($data)) {
            $configs = $data;
        } else {
            if (!empty($data)) {
                $dirname = $data;
            } elseif (is_object($GLOBALS['xoopsModule'])) {
                $dirname = $GLOBALS['xoopsModule']->getVar('dirname', 'n');
            } else {
                return false;
            }
            if ( !$configs = @include XOOPS_ROOT_PATH."/modules/".$dirname."/include/autoload.php" ) {
                return false;
            }
        }
        return $configs = array_merge(XoopsLoad::loadCoreConfig(), $configs);
    }
}

// To be enabled in XOOPS 3.0
// spl_autoload_register(array('XoopsLoad', 'load'));

?>