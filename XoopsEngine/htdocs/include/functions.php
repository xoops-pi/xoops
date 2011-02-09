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
 * XOOPS general functions
 *
 * @copyright       The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @package         kernel
 * @since           2.3.0
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * @version         $Id: functions.php 2026 2008-08-31 08:10:34Z phppp $
 */

/**
 * XOOPS class loader wrapper
 *
 * Temporay solution for XOOPS 2.3
 *
 * @param   string  $name   Name of class to be loaded
 * @param   string  $type   domain of the class, potential values: core - locaded in /class/; framework - located in /Frameworks/; other - module class, located in /modules/[$type]/class/
 * @return  boolean
 */
function xoops_load($name, $type = "core")
{
    if (!class_exists('XoopsLoad', false)) {
        require_once XOOPS_ROOT_PATH . "/class/xoopsload.php";
    }

    return XoopsLoad::load($name, $type);
}

/**
 * XOOPS language loader wrapper
 *
 * Temporay solution, not encouraged to use
 *
 * @param   string  $name       Name of language file to be loaded, without extension
 * @param   string  $domain     Module dirname; global language file will be loaded if $domain is set to 'global' or not specified
 * @param   string  $language   Language to be loaded, current language content will be loaded if not specified
 * @return  boolean
 * @todo    expand domain to multiple categories, e.g. module:system, framework:filter, etc.
 *
 */
function xoops_loadLanguage( $name, $domain = '', $language = null )
{
    //$language = empty($language) ? $GLOBALS['xoopsConfig']['language'] : $language;
    $language = $language ?: Xoops::config('language');
    $path = XOOPS_ROOT_PATH . '/' . ( (empty($domain) || 'global' == $domain) ? '' : "modules/{$domain}/" ) . 'language';
    if ( !( $ret = @include_once "{$path}/{$language}/{$name}.php" ) ) {
        $ret = include_once "{$path}/english/{$name}.php";
    }
    return $ret;
}

function xoops_header($closehead = true)
{
    global $xoopsConfig, $xoopsTheme, $xoopsConfigMetaFooter;
    $myts =& MyTextSanitizer::getInstance();

    if (!headers_sent()) {
        header('Content-Type:text/html; charset='._CHARSET);
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header('Cache-Control: no-store, no-cache, max-age=1, s-maxage=1, must-revalidate, post-check=0, pre-check=0');
        header("Pragma: no-cache");
    }
    echo "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>";
    echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="' . _LANGCODE . '" lang="' . _LANGCODE . '">
    <head>
    <meta http-equiv="content-type" content="text/html; charset=' . _CHARSET . '" />
    <meta http-equiv="content-language" content="' . _LANGCODE . '" />
    <meta name="robots" content="' . htmlspecialchars($xoopsConfigMetaFooter['meta_robots']) . '" />
    <meta name="keywords" content="' . htmlspecialchars($xoopsConfigMetaFooter['meta_keywords']) . '" />
    <meta name="description" content="' . htmlspecialchars($xoopsConfigMetaFooter['meta_desc']) . '" />
    <meta name="rating" content="' . htmlspecialchars($xoopsConfigMetaFooter['meta_rating']) . '" />
    <meta name="author" content="' . htmlspecialchars($xoopsConfigMetaFooter['meta_author']) . '" />
    <meta name="copyright" content="' . htmlspecialchars($xoopsConfigMetaFooter['meta_copyright']) . '" />
    <meta name="generator" content="XOOPS" />
    <title>' . htmlspecialchars($xoopsConfig['sitename']) . '</title>
    <script type="text/javascript" src="' . XOOPS_URL . '/include/xoops.js"></script>
    ';
    $themecss = xoops_getcss($xoopsConfig['theme_set']);
    echo '<link rel="stylesheet" type="text/css" media="all" href="' . XOOPS_URL . '/xoops.css" />';
    if ($themecss) {
        echo '<link rel="stylesheet" type="text/css" media="all" href="' . $themecss . '" />';
    }
    if ($closehead) {
        echo '</head><body>';
    }
}

function xoops_footer()
{
    echo '</body></html>';
    ob_end_flush();
}

function xoops_error($msg, $title = '')
{
    echo '<div class="errorMsg">';
    if ($title != '') {
        echo '<strong>' . $title . '</strong><br />';
    }
    if (is_array($msg) || is_object($msg)) {
        echo "<div><pre>";
        print_r($msg);
        echo "</pre></div>";
    } else {
        echo "<div>{$msg}</div>";
    }
    echo '</div>';
}

function xoops_result($msg, $title = '')
{
    echo '<div class="resultMsg">';
    if ($title != '') {
        echo '<strong>' . $title . '</strong><br />';
    }
    if (is_array($msg) || is_object($msg)) {
        echo "<div><pre>";
        print_r($msg);
        echo "</pre></div>";
    } else {
        echo "<div>{$msg}</div>";
    }
    echo '</div>';
}

function xoops_confirm($hiddens, $action, $msg, $submit = '', $addtoken = true)
{
    $submit = ($submit != '') ? trim($submit) : _SUBMIT;
    echo '<div class="confirmMsg">' . $msg . '<br />
      <form method="post" action="' . $action . '">
    ';
    foreach ($hiddens as $name => $value) {
        if (is_array($value)) {
            foreach ($value as $caption => $newvalue) {
                echo '<input type="radio" name="' . $name . '" value="' . htmlspecialchars($newvalue) . '" /> ' . $caption;
            }
            echo '<br />';
        } else {
            echo '<input type="hidden" name="' . $name . '" value="' . htmlspecialchars($value) . '" />';
        }
    }
    if ($addtoken != false) {
        echo $GLOBALS['xoopsSecurity']->getTokenHTML();
    }
    echo '
        <input type="submit" name="confirm_submit" value="' . $submit . '" title="' . $submit . '"/> <input type="button" name="confirm_back" value="' . _CANCEL . '" onclick="javascript:history.go(-1);" title="' . _CANCEL . '" />
      </form>
    </div>
    ';
}

function xoops_getUserTimestamp($time, $timeoffset = "")
{
    global $xoopsConfig, $xoopsUser;
    if ($timeoffset == '') {
        if ($xoopsUser) {
            $timeoffset = $xoopsUser->getVar("timezone_offset");
        } else {
            $timeoffset = $xoopsConfig['default_TZ'];
        }
    }
    $usertimestamp = intval($time) + (floatval($timeoffset) - $xoopsConfig['server_TZ'])*3600;
    return $usertimestamp;
}


/*
 * Function to display formatted times in user timezone
 */
function formatTimestamp($time, $format = "l", $timeoffset = "")
{
    xoops_load('XoopsLocal');
    return XoopsLocal::formatTimestamp($time, $format, $timeoffset);
}

/*
 * Function to calculate server timestamp from user entered time (timestamp)
 */
function userTimeToServerTime($timestamp, $userTZ=null)
{
    global $xoopsConfig;
    if (!isset($userTZ)) {
        $userTZ = $xoopsConfig['default_TZ'];
    }
    $timestamp = $timestamp - (($userTZ - $xoopsConfig['server_TZ']) * 3600);
    return $timestamp;
}

function xoops_makepass()
{
    $makepass = '';
    $syllables = array("er", "in", "tia", "wol", "fe", "pre", "vet", "jo", "nes", "al", "len", "son", "cha", "ir", "ler", "bo", "ok", "tio", "nar", "sim", "ple", "bla", "ten", "toe", "cho", "co", "lat", "spe", "ak", "er", "po", "co", "lor", "pen", "cil", "li", "ght", "wh", "at", "the", "he", "ck", "is", "mam", "bo", "no", "fi", "ve", "any", "way", "pol", "iti", "cs", "ra", "dio", "sou", "rce", "sea", "rch", "pa", "per", "com", "bo", "sp", "eak", "st", "fi", "rst", "gr", "oup", "boy", "ea", "gle", "tr", "ail", "bi", "ble", "brb", "pri", "dee", "kay", "en", "be", "se");
    srand( (double)microtime() * 1000000 );
    for ($count = 1; $count <= 4; $count++) {
        if (rand() % 10 == 1) {
            $makepass .= sprintf("%0.0f", (rand() % 50) + 1);
        } else {
            $makepass .= sprintf("%s", $syllables[rand()%62]);
        }
    }
    return $makepass;
}

function checkEmail($email, $antispam = false)
{
    if (!$email || !preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+([\.][a-z0-9-]+)+$/i", $email)){
        return false;
    }
    if ($antispam) {
        $email = str_replace("@", " at ", $email);
        $email = str_replace(".", " dot ", $email);
    }
    return $email;
}

function formatURL($url)
{
    $url = trim($url);
    if ($url != '') {
        if ((!preg_match("/^http[s]*:\/\//i", $url)) && (!preg_match("/^ftp*:\/\//i", $url)) && (!preg_match("/^ed2k*:\/\//i", $url)) ) {
            $url = 'http://' . $url;
        }
    }
    return $url;
}

/*
 * Function to get banner html tags for use in templates
 */
function xoops_getbanner()
{
    global $xoopsConfig;
    $db =& $GLOBALS["xoopsDB"];
    $bresult = $db->query("SELECT COUNT(*) FROM " . $db->prefix("banner"));
    list ($numrows) = $db->fetchRow($bresult);
    if ( $numrows > 1 ) {
        $numrows = $numrows-1;
        mt_srand((double)microtime()*1000000);
        $bannum = mt_rand(0, $numrows);
    } else {
        $bannum = 0;
    }
    if ( $numrows > 0 ) {
        $bresult = $db->query("SELECT * FROM " . $db->prefix("banner"), 1, $bannum);
        list ($bid, $cid, $imptotal, $impmade, $clicks, $imageurl, $clickurl, $date, $htmlbanner, $htmlcode) = $db->fetchRow($bresult);
        if ($xoopsConfig['my_ip'] == xoops_getenv('REMOTE_ADDR')) {
            // EMPTY
        } else {
            $db->queryF(sprintf("UPDATE %s SET impmade = impmade+1 WHERE bid = %u", $db->prefix("banner"), $bid));
        }
        /* Check if this impression is the last one and print the banner */
        if ( $imptotal == $impmade ) {
            $newid = $db->genId($db->prefix("bannerfinish")."_bid_seq");
            $sql = sprintf("INSERT INTO %s (bid, cid, impressions, clicks, datestart, dateend) VALUES (%u, %u, %u, %u, %u, %u)", $db->prefix("bannerfinish"), $newid, $cid, $impmade, $clicks, $date, time());
            $db->queryF($sql);
            $db->queryF(sprintf("DELETE FROM %s WHERE bid = %u", $db->prefix("banner"), $bid));
        }
        if ($htmlbanner){
            $bannerobject = $htmlcode;
        }else{
            $bannerobject = '<div><a href="'.XOOPS_URL.'/banners.php?op=click&amp;bid=' . $bid . '" rel="external">';
            if (stristr($imageurl, '.swf')) {
                $bannerobject = $bannerobject
                    .'<object type="application/x-shockwave-flash" width="468" height="60" data="' . $imageurl . '">'
                    .'<param name="movie" value="' . $imageurl . '" />'
                    .'<param name="quality" value="high" />'
                    .'</object>';
            } else {
                $bannerobject = $bannerobject . '<img src="' . $imageurl . '" alt="" />';
            }

            $bannerobject = $bannerobject . '</a></div>';
        }
        return $bannerobject;
    }
}

/*
* Function to redirect a user to certain pages
*/
function redirect_header($url, $time = 3, $message = '', $addredirect = true, $allowExternalLink = false)
{
    global $xoopsConfig, $xoopsLogger, $xoopsUserIsAdmin, $xoTheme;
    if ( preg_match( "/[\\0-\\31]|about:|script:/i", $url) ) {
        if (!preg_match('/^\b(java)?script:([\s]*)history\.go\(-[0-9]*\)([\s]*[;]*[\s]*)$/si', $url) ) {
            $url = XOOPS_URL;
        }
    }
    if ( !$allowExternalLink && $pos = strpos( $url, '://' ) ) {
        $xoopsLocation = substr( XOOPS_URL, strpos( XOOPS_URL, '://' ) + 3 );
        if (strcasecmp(substr($url, $pos + 3, strlen($xoopsLocation)), $xoopsLocation)) {
            $url = XOOPS_URL;
        }
    }
    if (defined('XOOPS_CPFUNC_LOADED')) {
        $theme = 'default';
    } else {
        $theme = $xoopsConfig['theme_set'];
    }

    /*
    require_once XOOPS_ROOT_PATH . '/class/template.php';
    require_once XOOPS_ROOT_PATH . '/class/theme.php';

    $xoopsThemeFactory =& new xos_opal_ThemeFactory();
    $xoopsThemeFactory->allowedThemes = $xoopsConfig['theme_set_allowed'];
    $xoopsThemeFactory->defaultTheme = $theme;
    $xoTheme =& $xoopsThemeFactory->createInstance(array("plugins" => array()));
    $xoopsTpl =& $xoTheme->template;
    */

    $xoopsTpl = XOOPS::registry('view')->getEngine();
    $xoTheme = XOOPS::registry('view')->loadTheme(array('plugins' => array()));

    $xoopsTpl->assign( array(
        'xoops_theme' => $theme,
        'xoops_imageurl' => XOOPS_THEME_URL . '/' . $theme . '/',
        'xoops_themecss'=> xoops_getcss($theme),
        'xoops_requesturi' => htmlspecialchars( $_SERVER['REQUEST_URI'], ENT_QUOTES),
        'xoops_sitename' => htmlspecialchars($xoopsConfig['sitename'], ENT_QUOTES),
        'xoops_slogan' => htmlspecialchars($xoopsConfig['slogan'], ENT_QUOTES),
        'xoops_dirname' => isset($xoopsModule) ? $xoopsModule->getVar( 'dirname' ) : 'system',
        'xoops_banner' => $xoopsConfig['banners'] ? xoops_getbanner() : '&nbsp;',
        'xoops_pagetitle' => isset($xoopsModule) && is_object($xoopsModule) ? $xoopsModule->getVar('name') : htmlspecialchars( $xoopsConfig['slogan'], ENT_QUOTES ),
    ) );

    if ($xoopsConfig['debug_mode'] == 2 && $xoopsUserIsAdmin) {
        $xoopsTpl->assign('time', 300);
        $xoopsTpl->assign('xoops_logdump', $xoopsLogger->dump());
    } else {
        $xoopsTpl->assign('time', intval($time));
    }
    if (!empty($_SERVER['REQUEST_URI']) && $addredirect && strstr($url, 'user.php')) {
        if (!strstr($url, '?')) {
            $url .= '?xoops_redirect=' . urlencode($_SERVER['REQUEST_URI']);
        } else {
            $url .= '&amp;xoops_redirect=' . urlencode($_SERVER['REQUEST_URI']);
        }
    }
    if (defined('SID') && SID && (! isset($_COOKIE[session_name()]) || ($xoopsConfig['use_mysession'] && $xoopsConfig['session_name'] != '' && !isset($_COOKIE[$xoopsConfig['session_name']])))) {
        if (!strstr($url, '?')) {
            $url .= '?' . SID;
        } else {
            $url .= '&amp;' . SID;
        }
    }
    $url = preg_replace("/&amp;/i", '&', htmlspecialchars($url, ENT_QUOTES));
    $xoopsTpl->assign('url', $url);
    $message = trim($message) != '' ? $message : _TAKINGBACK;
    $xoopsTpl->assign('message', $message);
    $xoopsTpl->assign('lang_ifnotreload', sprintf(_IFNOTRELOAD, $url));
    $xoopsTpl->display('db:system_redirect.html');
    exit();
}

function xoops_getenv($key)
{
    $ret = '';
    if ( array_key_exists( $key, $_SERVER) && isset($_SERVER[$key]) ) {
        $ret = $_SERVER[$key];
        return $ret;
    }
    if ( array_key_exists( $key, $_ENV) && isset($_ENV[$key]) ) {
        $ret = $_ENV[$key];
        return $ret;
    }
    return $ret;
}

/*
 * Function to get css file for a certain themeset
 */
function xoops_getcss($theme = '')
{
    if ($theme == '') {
        $theme = $GLOBALS['xoopsConfig']['theme_set'];
    }
    $uagent = xoops_getenv('HTTP_USER_AGENT');
    if (stristr($uagent, 'mac')) {
        $str_css = 'styleMAC.css';
    } elseif (preg_match("/MSIE ([0-9]\.[0-9]{1,2})/i", $uagent)) {
        $str_css = 'style.css';
    } else {
        $str_css = 'styleNN.css';
    }
    if (is_dir(XOOPS_THEME_PATH . '/' . $theme)) {
        if (file_exists(XOOPS_THEME_PATH . '/' . $theme.'/' . $str_css)) {
            return XOOPS_THEME_URL . '/' . $theme . '/' . $str_css;
        } elseif (file_exists(XOOPS_THEME_PATH . '/' . $theme . '/style.css')) {
            return XOOPS_THEME_URL . '/' . $theme . '/style.css';
        }
    }
    if (is_dir(XOOPS_THEME_PATH . '/' . $theme . '/css')) {
        if (file_exists(XOOPS_THEME_PATH . '/' . $theme . '/css/' . $str_css)) {
            return XOOPS_THEME_URL . '/' . $theme . '/css/' . $str_css;
        } elseif (file_exists(XOOPS_THEME_PATH . '/' . $theme . '/css/style.css')) {
            return XOOPS_THEME_URL . '/' . $theme . '/css/style.css';
        }
    }
    return '';
}

function &xoops_getMailer()
{
    static $mailer;
    global $xoopsConfig;

    if (is_object($mailer)) return $mailer;

    include_once XOOPS_ROOT_PATH . "/class/xoopsmailer.php";
    if ( file_exists(XOOPS_ROOT_PATH . "/language/" . $xoopsConfig['language'] . "/xoopsmailerlocal.php") ) {
        include_once XOOPS_ROOT_PATH."/language/" . $xoopsConfig['language'] . "/xoopsmailerlocal.php";
    } elseif ( file_exists(XOOPS_ROOT_PATH . "/language/english/xoopsmailerlocal.php") ) {
        include_once XOOPS_ROOT_PATH . "/language/english/xoopsmailerlocal.php";
    }

    if ( class_exists("XoopsMailerLocal") ) {
        $mailer = new XoopsMailerLocal();
    } else {
        $mailer = new XoopsMailer();
    }
    return $mailer;
}

function xoops_gethandler($name, $optional = false )
{
    return Xoops::getHandler($name, $optional);
    static $handlers;
    $name = strtolower(trim($name));
    if (!isset($handlers[$name])) {
        if ( file_exists( $hnd_file = XOOPS_ROOT_PATH . '/kernel/' . $name . '.php' ) ) {
            require_once $hnd_file;
        }
        $class = 'Xoops' . ucfirst($name) . 'Handler';
        if (class_exists($class)) {
            $handlers[$name] = new $class($GLOBALS['xoopsDB']);
        }
    }
    if ( !isset($handlers[$name]) ) {
        trigger_error('Class <strong>' . $class . '</strong> does not exist<br />Handler Name: ' . $name, $optional ? E_USER_WARNING : E_USER_ERROR);
    }
    if ( isset($handlers[$name]) ) {
        return $handlers[$name];
    }
    $inst = false;
    return $inst;
}

function &xoops_getmodulehandler($name = null, $module_dir = null, $optional = false)
{
    static $handlers;
    // if $module_dir is not specified
    if (!isset($module_dir)) {
        //if a module is loaded
        if (isset($GLOBALS['xoopsModule']) && is_object($GLOBALS['xoopsModule'])) {
            $module_dir = $GLOBALS['xoopsModule']->getVar('dirname', 'n');
        } else {
            trigger_error('No Module is loaded', E_USER_ERROR);
        }
    } else {
        $module_dir = trim($module_dir);
    }
    $name = (!isset($name)) ? $module_dir : trim($name);
    if (!isset($handlers[$module_dir][$name])) {
        if ( file_exists( $hnd_file = XOOPS_ROOT_PATH . "/modules/{$module_dir}/class/{$name}.php" ) ) {
            include_once $hnd_file;
        }
        $class = ucfirst(strtolower($module_dir)) . ucfirst($name) . 'Handler';
        if (class_exists($class)) {
            $handlers[$module_dir][$name] = new $class($GLOBALS['xoopsDB']);
        }
    }
    if (!isset($handlers[$module_dir][$name])) {
        trigger_error('Handler does not exist<br />Module: ' . $module_dir . '<br />Name: ' . $name, $optional ? E_USER_WARNING : E_USER_ERROR);
    }
    if ( isset($handlers[$module_dir][$name]) ) {
        return $handlers[$module_dir][$name];
    }
    $inst = false;
    return $inst;

}

function xoops_getrank($rank_id =0, $posts = 0)
{
    $db =& $GLOBALS["xoopsDB"];
    $myts =& MyTextSanitizer::getInstance();
    $rank_id = intval($rank_id);
    $posts = intval($posts);
    if ($rank_id != 0) {
        $sql = "SELECT rank_title AS title, rank_image AS image FROM " . $db->prefix('ranks') . " WHERE rank_id = " . $rank_id;
    } else {
        $sql = "SELECT rank_title AS title, rank_image AS image FROM " . $db->prefix('ranks') . " WHERE rank_min <= " . $posts . " AND rank_max >= " . $posts . " AND rank_special = 0";
    }
    $rank = $db->fetchArray($db->query($sql));
    $rank['title'] = $myts->htmlspecialchars($rank['title']);
    $rank['id'] = $rank_id;
    return $rank;
}


/**
* Returns the portion of string specified by the start and length parameters. If $trimmarker is supplied, it is appended to the return string. This function works fine with multi-byte characters if mb_* functions exist on the server.
*
* @param    string    $str
* @param    int       $start
* @param    int       $length
* @param    string    $trimmarker
*
* @return   string
*/
function xoops_substr($str, $start, $length, $trimmarker = '...')
{
    xoops_load('XoopsLocal');
    return XoopsLocal::substr($str, $start, $length, $trimmarker);
}

// RMV-NOTIFY
// ################ Notification Helper Functions ##################

// We want to be able to delete by module, by user, or by item.
// How do we specify this??

function xoops_notification_deletebymodule ($module_id)
{
    $notification_handler =& xoops_gethandler('notification');
    return $notification_handler->unsubscribeByModule ($module_id);
}

function xoops_notification_deletebyuser ($user_id)
{
    $notification_handler =& xoops_gethandler('notification');
    return $notification_handler->unsubscribeByUser ($user_id);
}

function xoops_notification_deletebyitem ($module_id, $category, $item_id)
{
    $notification_handler =& xoops_gethandler('notification');
    return $notification_handler->unsubscribeByItem ($module_id, $category, $item_id);
}

// Comment helper functions
function xoops_comment_count($module_id, $item_id = null)
{
    $comment_handler =& xoops_gethandler('comment');
    $criteria = new CriteriaCompo(new Criteria('com_modid', intval($module_id)));
    if (isset($item_id)) {
        $criteria->add(new Criteria('com_itemid', intval($item_id)));
    }
    return $comment_handler->getCount($criteria);
}

function xoops_comment_delete($module_id, $item_id)
{
    if (intval($module_id) > 0 && intval($item_id) > 0) {
        $comment_handler =& xoops_gethandler('comment');
        $comments =& $comment_handler->getByItemId($module_id, $item_id);
        if (is_array($comments)) {
            $count = count($comments);
            $deleted_num = array();
            for ($i = 0; $i < $count; $i++) {
                if (false != $comment_handler->delete($comments[$i])) {
                    // store poster ID and deleted post number into array for later use
                    $poster_id = $comments[$i]->getVar('com_uid');
                    if ($poster_id != 0) {
                        $deleted_num[$poster_id] = !isset($deleted_num[$poster_id]) ? 1 : ($deleted_num[$poster_id] + 1);
                    }
                }
            }
            $member_handler =& xoops_gethandler('member');
            foreach ($deleted_num as $user_id => $post_num) {
                // update user posts
                $com_poster = $member_handler->getUser($user_id);
                if (is_object($com_poster)) {
                    $member_handler->updateUserByField($com_poster, 'posts', $com_poster->getVar('posts') - $post_num);
                }
            }
            return true;
        }
    }
    return false;
}

// Group Permission Helper Functions
function xoops_groupperm_deletebymoditem($module_id, $perm_name, $item_id = null)
{
    // do not allow system permissions to be deleted
    if (intval($module_id) <= 1) {
        return false;
    }
    $gperm_handler =& xoops_gethandler('groupperm');
    return $gperm_handler->deleteByModule($module_id, $perm_name, $item_id);
}

function xoops_utf8_encode(&$text)
{
    xoops_load('XoopsLocal');
    return XoopsLocal::utf8_encode($text);
}

function xoops_convert_encoding(&$text)
{
    return xoops_utf8_encode($text);
}

function xoops_trim($text)
{
    xoops_load('XoopsLocal');
    return XoopsLocal::trim($text);
}

include_once dirname(__FILE__) . "/functions.legacy.php";
?>