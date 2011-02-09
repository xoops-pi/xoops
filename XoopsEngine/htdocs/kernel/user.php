<?php
/**
 * Xoops Engine User Object
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
 * @since           2.0
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * @author          Kazumi Ono (AKA onokazu>
 * @version         $Id$
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}
/**
 * Class for users
 * @author Kazumi Ono <onokazu@xoops.org>
 * @copyright copyright (c) 2000-2003 XOOPS.org
 * @package kernel
 */
class XoopsUser extends XoopsObject
{
    private $row;
    private $map = array(
        "uid"               => "id",
        "name"              => "name",
        "uname"             => "identity",
        "pass"              => "credential",
        "email"             => "email",
        "level"             => "active",
        
        "url"               => "homepage",
        "user_occ"          => "occupation",
        "bio"               => "bio",
        "user_intrest"      => "interest",
        "user_avatar"       => "avatar",
        "user_regdate"      => "create_time",
        "user_from"         => "location",
        "user_sig"          => "signature",
        "user_aim"          => "aim",
        "user_yim"          => "yim",
        "user_msnm"         => "msn",
        "posts"             => "posts",
        "rank"              => "rank",
        "theme"             => "theme",
        "timezone_offset"   => "timezone",
        "user_mailok"       => "accept_email",
        "umode"             => "comment_mode",
        "uorder"            => "comment_order",
        "notify_method"     => "notify_method",
        "notify_mode"       => "notify_mode",
    );
    
    public function __construct($row = null)
    {
        $this->XoopsObject();
        if (isset($row)) {
            $this->row = $row;
        } else {
            $this->row = XOOPS::getModel("user")->createRow();
        }
        
        $this->initVar('uid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('name', XOBJ_DTYPE_TXTBOX, null, false, 60);
        $this->initVar('uname', XOBJ_DTYPE_TXTBOX, null, true, 25);
        $this->initVar('email', XOBJ_DTYPE_TXTBOX, null, true, 60);
        //$this->initVar('url', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('user_avatar', XOBJ_DTYPE_TXTBOX, null, false, 30);
        $this->initVar('user_regdate', XOBJ_DTYPE_INT, null, false);
        //$this->initVar('user_icq', XOBJ_DTYPE_TXTBOX, null, false, 15);
        //$this->initVar('user_from', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('user_sig', XOBJ_DTYPE_TXTAREA, null, false, null);
        //$this->initVar('user_viewemail', XOBJ_DTYPE_INT, 0, false);
        //$this->initVar('actkey', XOBJ_DTYPE_OTHER, null, false);
        //$this->initVar('user_aim', XOBJ_DTYPE_TXTBOX, null, false, 18);
        //$this->initVar('user_yim', XOBJ_DTYPE_TXTBOX, null, false, 25);
        //$this->initVar('user_msnm', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('pass', XOBJ_DTYPE_TXTBOX, null, false, 32);
        $this->initVar('posts', XOBJ_DTYPE_INT, null, false);
        //$this->initVar('attachsig', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('rank', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('level', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('theme', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('timezone_offset', XOBJ_DTYPE_OTHER, '0.0', false);
        //$this->initVar('last_login', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('umode', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('uorder', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('notify_method', XOBJ_DTYPE_OTHER, 1, false);
        $this->initVar('notify_mode', XOBJ_DTYPE_OTHER, 0, false);
        //$this->initVar('user_occ', XOBJ_DTYPE_TXTBOX, null, false, 100);
        //$this->initVar('bio', XOBJ_DTYPE_TXTAREA, null, false, null);
        //$this->initVar('user_intrest', XOBJ_DTYPE_TXTBOX, null, false, 150);
        $this->initVar('user_mailok', XOBJ_DTYPE_INT, 1, false);
    }
    
    public function setRow($row)
    {
        $this->row = $row;
        return $this;
    }
    
    public function save()
    {
        return $this->row->save();
    }
    
    public function delete()
    {
        return $this->row->delete();
    }
    
    public function getVar($key, $format = '')
    {
        $value = null;
        if (isset($this->map[$key])) {
            $value = $this->row->{$this->map[$key]};
        }
        return $value;
    }
    
    public function setVar($key, $value, $not_gpc = false)
    {
        if (isset($this->map[$key])) {
            $this->row->{$this->map[$key]} = $value;
        }
        return;
    }

    /**
     * initialize variables for the object
     *
     * @access public
     * @param string $key
     * @param int $data_type  set to one of XOBJ_DTYPE_XXX constants (set to XOBJ_DTYPE_OTHER if no data type ckecking nor text sanitizing is required)
     * @param mixed
     * @param bool $required  require html form input?
     * @param int $maxlength  for XOBJ_DTYPE_TXTBOX type only
     * @param string $option  does this data have any select options?
     */
    function initVar($key, $data_type, $value = null, $required = false, $maxlength = null, $options = '')
    {
        if (!isset($this->map[$key])) {
            return;
        }
        parent::initVar($key, $data_type, $value, $required, $maxlength, $options);
    }

    
    /**
     * Array of groups that user belongs to
     * @var array
     * @access private
     */
    var $_groups = array();
    /**
     * @var bool is the user admin?
     * @access private
     */
    var $_isAdmin = null;
    /**
     * @var string user's rank
     * @access private
     */
    var $_rank = null;
    /**
     * @var bool is the user online?
     * @access private
     */
    var $_isOnline = null;

    /**
     * check if the user is a guest user
     *
     * @return bool returns false
     *
     */
    function isGuest()
    {
        return false;
    }


    /**
     * Get user name
     *
     * @param int $userid ID of the user to find
     * @param int $usereal switch for usename or realname
     * @return string name of the user. name for "anonymous" if not found.
     */
    function getUnameFromId($userid, $usereal = 0)
    {
        if ($userid > 0) {
            if ($row = $this->row->getTable()->findRow($userid)) {
                if ($usereal) {
                    $name = $row->name;
                }
                if (empty($name)) {
                    $name = $row->identity;
                }
                return MyTextSanitizer::getInstance()->htmlSpecialChars($name);
            }
        }
        return XOOPS::$config['anonymous'];
    }
    
    /**
     * increase the number of posts for the user
     *
     * @deprecated
     */
    function incrementPost()
    {
        $profile = $this->row->getProfile();
        $profile->posts ++;
        return $profile->save();
    }
    
    /**
     * set the groups for the user
     *
     * @param array $groupsArr Array of groups that user belongs to
     */
    function setGroups($groupsArr)
    {
        if (is_array($groupsArr)) {
            $this->_groups =& $groupsArr;
        }
    }
    /**
     * get the groups that the user belongs to
     *
     * @return array array of groups
     */
    function &getGroups()
    {
        if (empty($this->_groups)) {
            $member_handler =& xoops_gethandler('member');
            $this->_groups = $member_handler->getGroupsByUser($this->getVar('uid'));
        }
        return $this->_groups;
    }
    
    /**
     * alias for {@link getGroups()}
     * @see getGroups()
     * @return array array of groups
     * @deprecated
     */
    function groups()
    {
        $groups = $this->getGroups();
        return $groups;
    }
    
    /**
     * Is the user admin ?
     *
     * This method will return true if this user has admin rights for the specified module.<br />
     * - If you don't specify any module ID, the current module will be checked.<br />
     * - If you set the module_id to -1, it will return true if the user has admin rights for at least one module
     *
     * @param int $module_id check if user is admin of this module
     * @return bool is the user admin of that module?
     */
    function isAdmin( $module_id = null )
    {
        if ( is_null( $module_id ) ) {
            $module_id = isset($GLOBALS['xoopsModule']) ? $GLOBALS['xoopsModule']->getVar( 'mid', 'n' ) : 1;
        } elseif ( intval($module_id) < 1 ) {
            $module_id = 0;
        }
        $moduleperm_handler =& xoops_gethandler('groupperm');
        return $moduleperm_handler->checkRight('module_admin', $module_id, $this->getGroups());
    }
    
    /**
     * get the user's rank
     * @return array array of rank ID and title
     */
    function rank()
    {
        if (!isset($this->_rank)) {
            $this->_rank = $this->row->profile()->display("rank");
        }
        return $this->_rank;
    }
    /**
     * is the user activated?
     * @return bool
     */
    function isActive()
    {
        if ($this->getVar('level') == 0) {
            return false;
        }
        return true;
    }
    /**
     * is the user currently logged in?
     * @return bool
     */
    function isOnline()
    {
        if (!isset($this->_isOnline)) {
            $onlinehandler =& xoops_gethandler('online');
            $this->_isOnline = ($onlinehandler->getCount(new Criteria('online_uid', $this->getVar('uid'))) > 0) ? true : false;
        }
        return $this->_isOnline;
    }
    /**#@+
     * specialized wrapper for {@link XoopsObject::getVar()}
     *
     * kept for compatibility reasons.
     *
     * @see XoopsObject::getVar()
     * @deprecated
     */
    /**
     * get the users UID
     * @return int
     */
    function uid()
    {
        return $this->getVar("uid");
    }

    /**
     * get the users name
     * @param string $format format for the output, see {@link XoopsObject::getVar()}
     * @return string
     */
    function name($format="S")
    {
        return $this->getVar("name", $format);
    }

    /**
     * get the user's uname
     * @param string $format format for the output, see {@link XoopsObject::getVar()}
     * @return string
     */
    function uname($format="S")
    {
        return $this->getVar("uname", $format);
    }

    /**
     * get the user's email
     *
     * @param string $format format for the output, see {@link XoopsObject::getVar()}
     * @return string
     */
    function email($format="S")
    {
        return $this->getVar("email", $format);
    }

    function url($format="S")
    {
        return $this->getVar("url", $format);
    }

    function user_avatar($format="S")
    {
        return $this->getVar("user_avatar");
    }

    function user_regdate()
    {
        return $this->getVar("user_regdate");
    }

    function user_icq($format="S")
    {
        return $this->getVar("user_icq", $format);
    }

    function user_from($format="S")
    {
        return $this->getVar("user_from", $format);
    }
    function user_sig($format="S")
    {
        return $this->getVar("user_sig", $format);
    }

    function user_viewemail()
    {
        return $this->getVar("user_viewemail");
    }

    function actkey()
    {
        return $this->getVar("actkey");
    }

    function user_aim($format="S")
    {
        return $this->getVar("user_aim", $format);
    }

    function user_yim($format="S")
    {
        return $this->getVar("user_yim", $format);
    }

    function user_msnm($format="S")
    {
        return $this->getVar("user_msnm", $format);
    }

    function pass()
    {
        return $this->getVar("pass");
    }

    function posts()
    {
        return $this->getVar("posts");
    }

    function attachsig()
    {
        return $this->getVar("attachsig");
    }

    function level()
    {
        return $this->getVar("level");
    }

    function theme()
    {
        return $this->getVar("theme");
    }

    function timezone()
    {
        return $this->getVar("timezone_offset");
    }

    function umode()
    {
        return $this->getVar("umode");
    }

    function uorder()
    {
        return $this->getVar("uorder");
    }

    // RMV-NOTIFY
    function notify_method()
    {
        return $this->getVar("notify_method");
    }

    function notify_mode()
    {
        return $this->getVar("notify_mode");
    }

    function user_occ($format="S")
    {
        return $this->getVar("user_occ", $format);
    }

    function bio($format="S")
    {
        return $this->getVar("bio", $format);
    }

    function user_intrest($format="S")
    {
        return $this->getVar("user_intrest", $format);
    }

    function last_login()
    {
        return $this->getVar("last_login");
    }
    /**#@-*/

}

/**
 * Class that represents a guest user
 * @author Kazumi Ono <onokazu@xoops.org>
 * @copyright copyright (c) 2000-2003 XOOPS.org
 * @package kernel
 */
class XoopsGuestUser extends XoopsUser
{
    /**
     * check if the user is a guest user
     *
     * @return bool returns true
     *
     */
    function isGuest()
    {
        return true;
    }
}


/**
 * XOOPS user handler class.
 * This class is responsible for providing data access mechanisms to the data source
 * of XOOPS user class objects.
 *
 * @author  Taiwen Jiang <phppp@users.sourceforge.net>
 * @package kernel
 */
class XoopsUserHandler extends XoopsPersistableObjectHandler
{
    public function __construct(&$db)
    {
        parent::__construct($db, "users", 'XoopsUser', "uid", "uname");
    }
    
    public function get($id)
    {
        $user = false;
        if (!$row = XOOPS::getModel("user")->findRow($id)) {
            return $user;
        }
        $user = new XoopsUser($row);
        return $user;
    }
    
    function insert(&$user)
    {
        trigger_error(__METHOD__ . " is deprecated, please use XOOPS::getModel('user')", E_USER_DEPRECATED);
        return $user->save();
    }
    
    function delete(&$user)
    {
        trigger_error(__METHOD__ . " is deprecated, please use XOOPS::getModel('user')", E_USER_DEPRECATED);
        return $user->delete();
    }
}
?>