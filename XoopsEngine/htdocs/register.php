<?php
// $Id: register.php 2825 2009-02-21 03:20:07Z phppp $
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

//$xoopsOption['pagetype'] = 'user';

include 'mainfile.php';
$module_handler = xoops_gethandler('module');
$profile_module = $module_handler->getByDirname('profile');
if ($profile_module && $profile_module->getVar('isactive')) {
    header("location: ./modules/profile/register.php" . (empty($_SERVER['QUERY_STRING']) ? "" : "?" . $_SERVER['QUERY_STRING']) );
    exit();
}

xoops_loadLanguage('user');
xoops_load("userUtility");

$myts =& MyTextSanitizer::getInstance();

$config_handler =& xoops_gethandler('config');
$xoopsConfigUser = $config_handler->getConfigsByCat(XOOPS_CONF_USER);

if (empty($xoopsConfigUser['allow_register'])) {
    redirect_header('index.php', 6, _US_NOREGISTER);
    exit();
}


function userCheck($uname, $email, $pass, $vpass)
{
    trigger_error("Function " . __FUNCTION__ . " is deprecated, use XoopsUserUtility::validate() instead", E_USER_WARNING);
    return XoopsUserUtility::validate($uname, $email, $pass, $vpass);
}

$op = isset($_POST['op']) ? $_POST['op'] : ( isset($_GET["op"]) ? $_GET["op"] : 'register' );
$uname = isset($_POST['uname']) ? $myts->stripSlashesGPC($_POST['uname']) : '';
$email = isset($_POST['email']) ? trim($myts->stripSlashesGPC($_POST['email'])) : '';
$url = isset($_POST['url']) ? trim($myts->stripSlashesGPC($_POST['url'])) : '';
$pass = isset($_POST['pass']) ? $myts->stripSlashesGPC($_POST['pass']) : '';
$vpass = isset($_POST['vpass']) ? $myts->stripSlashesGPC($_POST['vpass']) : '';
$timezone_offset = isset($_POST['timezone_offset']) ? (float)$_POST['timezone_offset'] : $xoopsConfig['default_TZ'];
$user_viewemail = (isset($_POST['user_viewemail']) && intval($_POST['user_viewemail'])) ? 1 : 0;
$user_mailok = (isset($_POST['user_mailok']) && intval($_POST['user_mailok'])) ? 1 : 0;
$agree_disc = (isset($_POST['agree_disc']) && intval($_POST['agree_disc'])) ? 1 : 0;

switch ( $op ) {
case 'newuser':
    $xoopsOption['xoops_pagetitle'] = $xoops_pagetitle;
    include 'header.php';
    $stop = '';
    if (!$GLOBALS['xoopsSecurity']->check()) {
        $stop .= implode('<br />', $GLOBALS['xoopsSecurity']->getErrors())."<br />";
    }
    if ($xoopsConfigUser['reg_dispdsclmr'] != 0 && $xoopsConfigUser['reg_disclaimer'] != '') {
        if (empty($agree_disc)) {
            $stop .= _US_UNEEDAGREE.'<br />';
        }
    }
    $stop .= XoopsUserUtility::validate($uname, $email, $pass, $vpass);
    if (empty($stop)) {
        echo _US_USERNAME.": ".$myts->htmlSpecialChars($uname)."<br />";
        echo _US_EMAIL.": ".$myts->htmlSpecialChars($email)."<br />";
        if ($url != '') {
            $url = formatURL($url);
            echo _US_WEBSITE.': '.$myts->htmlSpecialChars($url).'<br />';
        }
        $f_timezone = ($timezone_offset < 0) ? 'GMT '.$timezone_offset : 'GMT +'.$timezone_offset;
        echo _US_TIMEZONE.": $f_timezone<br />";
        echo "<form action='register.php' method='post'>";
        xoops_load("XoopsFormCaptcha");
        $cpatcha = new XoopsFormCaptcha();
        echo "<br />".$cpatcha->getCaption().": ".$cpatcha->render();
        echo "
        <input type='hidden' name='uname' value='".$myts->htmlSpecialChars($uname)."' />
        <input type='hidden' name='email' value='".$myts->htmlSpecialChars($email)."' />";
        echo "<input type='hidden' name='user_viewemail' value='".$user_viewemail."' />
        <input type='hidden' name='timezone_offset' value='".(float)$timezone_offset."' />
        <input type='hidden' name='url' value='".$myts->htmlSpecialChars($url)."' />
        <input type='hidden' name='pass' value='".$myts->htmlSpecialChars($pass)."' />
        <input type='hidden' name='vpass' value='".$myts->htmlSpecialChars($vpass)."' />
        <input type='hidden' name='user_mailok' value='".$user_mailok."' />
        <br /><br /><input type='hidden' name='op' value='finish' />".$GLOBALS['xoopsSecurity']->getTokenHTML()."<input type='submit' value='". _US_FINISH ."' /></form>";
    } else {
        echo "<span style='color:#ff0000;'>$stop</span>";
        include 'include/registerform.php';
        $reg_form->display();
    }
    include 'footer.php';
    break;

case 'finish':
    include 'header.php';
    $stop = XoopsUserUtility::validate($uname, $email, $pass, $vpass);
    if (!$GLOBALS['xoopsSecurity']->check()) {
        $stop .= implode('<br />', $GLOBALS['xoopsSecurity']->getErrors()) . "<br />";
    }
    xoops_load("captcha");
    $xoopsCaptcha = XoopsCaptcha::getInstance();
    if( !$xoopsCaptcha->verify() ) {
        $stop .= $xoopsCaptcha->getMessage()."<br />";
    }
    if ( empty($stop) ) {
        $member_handler =& xoops_gethandler('member');
        $newuser =& $member_handler->createUser();
        $newuser->setVar('user_viewemail',$user_viewemail, true);
        $newuser->setVar('uname', $uname, true);
        $newuser->setVar('email', $email, true);
        if ($url != '') {
            $newuser->setVar('url', formatURL($url), true);
        }
        $newuser->setVar('user_avatar','blank.gif', true);
        $actkey = substr(md5(uniqid(mt_rand(), 1)), 0, 8);
        $newuser->setVar('actkey', $actkey, true);
        $newuser->setVar('pass', md5($pass), true);
        $newuser->setVar('timezone_offset', $timezone_offset, true);
        $newuser->setVar('user_regdate', time(), true);
        $newuser->setVar('uorder',$xoopsConfig['com_order'], true);
        $newuser->setVar('umode',$xoopsConfig['com_mode'], true);
        $newuser->setVar('user_mailok',$user_mailok, true);
        if ($xoopsConfigUser['activation_type'] == 1) {
            $newuser->setVar('level', 1, true);
        } else {
            $newuser->setVar('level', 0, true);
        }
        if (!$member_handler->insertUser($newuser)) {
            echo _US_REGISTERNG;
            include 'footer.php';
            exit();
        }
        $newid = $newuser->getVar('uid');
        if (!$member_handler->addUserToGroup(XOOPS_GROUP_USERS, $newid)) {
            echo _US_REGISTERNG;
            include 'footer.php';
            exit();
        }
        if ($xoopsConfigUser['activation_type'] == 1) {
            XoopsUserUtility::sendWelcome($newuser);
            redirect_header('index.php', 4, _US_ACTLOGIN);
            exit();
        }
        // Sending notification email to user for self activation
        if ($xoopsConfigUser['activation_type'] == 0) {
            $xoopsMailer =& xoops_getMailer();
            $xoopsMailer->useMail();
            $xoopsMailer->setTemplate('register.tpl');
            $xoopsMailer->assign('SITENAME', $xoopsConfig['sitename']);
            $xoopsMailer->assign('ADMINMAIL', $xoopsConfig['adminmail']);
            $xoopsMailer->assign('SITEURL', XOOPS_URL."/");
            $xoopsMailer->setToUsers(new XoopsUser($newid));
            $xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
            $xoopsMailer->setFromName($xoopsConfig['sitename']);
            $xoopsMailer->setSubject(sprintf(_US_USERKEYFOR, $uname));
            if ( !$xoopsMailer->send() ) {
                echo _US_YOURREGMAILNG;
            } else {
                echo _US_YOURREGISTERED;
            }
        // Sending notification email to administrator for activation
        } elseif ($xoopsConfigUser['activation_type'] == 2) {
            $xoopsMailer =& xoops_getMailer();
            $xoopsMailer->useMail();
            $xoopsMailer->setTemplate('adminactivate.tpl');
            $xoopsMailer->assign('USERNAME', $uname);
            $xoopsMailer->assign('USEREMAIL', $email);
            $xoopsMailer->assign('USERACTLINK', XOOPS_URL.'/register.php?op=actv&id='.$newid.'&actkey='.$actkey);
            $xoopsMailer->assign('SITENAME', $xoopsConfig['sitename']);
            $xoopsMailer->assign('ADMINMAIL', $xoopsConfig['adminmail']);
            $xoopsMailer->assign('SITEURL', XOOPS_URL."/");
            $member_handler =& xoops_gethandler('member');
            $xoopsMailer->setToGroups($member_handler->getGroup($xoopsConfigUser['activation_group']));
            $xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
            $xoopsMailer->setFromName($xoopsConfig['sitename']);
            $xoopsMailer->setSubject(sprintf(_US_USERKEYFOR, $uname));
            if ( !$xoopsMailer->send() ) {
                echo _US_YOURREGMAILNG;
            } else {
                echo _US_YOURREGISTERED2;
            }
        }
        if ($xoopsConfigUser['new_user_notify'] == 1 && !empty($xoopsConfigUser['new_user_notify_group'])) {
            $xoopsMailer =& xoops_getMailer();
            $xoopsMailer->useMail();
            $member_handler =& xoops_gethandler('member');
            $xoopsMailer->setToGroups($member_handler->getGroup($xoopsConfigUser['new_user_notify_group']));
            $xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
            $xoopsMailer->setFromName($xoopsConfig['sitename']);
            $xoopsMailer->setSubject(sprintf(_US_NEWUSERREGAT,$xoopsConfig['sitename']));
            $xoopsMailer->setBody(sprintf(_US_HASJUSTREG, $uname));
            $xoopsMailer->send();
        }
    } else {
        echo "<span style='color:#ff0000; font-weight:bold;'>$stop</span>";
        include 'include/registerform.php';
        $reg_form->display();
    }
    include 'footer.php';
    break;

case 'actv':
case 'activate':
    $id = intval($_GET['id']);
    $actkey = trim($_GET['actkey']);
    if (empty($id)) {
        redirect_header('index.php', 1, '');
        exit();
    }
    $member_handler =& xoops_gethandler('member');
    $thisuser =& $member_handler->getUser($id);
    if (!is_object($thisuser)) {
        exit();
    }
    if ($thisuser->getVar('actkey') != $actkey) {
        redirect_header('index.php', 5, _US_ACTKEYNOT);
    } else {
        if ($thisuser->getVar('level') > 0 ) {
            redirect_header( 'user.php', 5, _US_ACONTACT, false );
        } else {
            if (false != $member_handler->activateUser($thisuser)) {
                $config_handler =& xoops_gethandler('config');
                $xoopsConfigUser = $config_handler->getConfigsByCat(XOOPS_CONF_USER);
                if ($xoopsConfigUser['activation_type'] == 2) {
                    $myts =& MyTextSanitizer::getInstance();
                    $xoopsMailer =& xoops_getMailer();
                    $xoopsMailer->useMail();
                    $xoopsMailer->setTemplate('activated.tpl');
                    $xoopsMailer->assign('SITENAME', $xoopsConfig['sitename']);
                    $xoopsMailer->assign('ADMINMAIL', $xoopsConfig['adminmail']);
                    $xoopsMailer->assign('SITEURL', XOOPS_URL."/");
                    $xoopsMailer->setToUsers($thisuser);
                    $xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
                    $xoopsMailer->setFromName($xoopsConfig['sitename']);
                    $xoopsMailer->setSubject(sprintf(_US_YOURACCOUNT,$xoopsConfig['sitename']));
                    include 'header.php';
                    if ( !$xoopsMailer->send() ) {
                        printf(_US_ACTVMAILNG, $thisuser->getVar('uname'));
                    } else {
                        printf(_US_ACTVMAILOK, $thisuser->getVar('uname'));
                    }
                    include 'footer.php';
                } else {
                    redirect_header( 'user.php', 5, _US_ACTLOGIN, false );
                }
            } else {
                redirect_header('index.php',5, _US_ACTFAILD);
            }
        }
    }
    break;

case 'register':
default:
    $xoopsOption['xoops_pagetitle'] = _US_USERREG;
    include 'header.php';
    $xoTheme->addMeta('meta', 'keywords', _US_USERREG . ", " . _US_NICKNAME); // FIXME!
    $xoTheme->addMeta('meta', 'description', strip_tags($xoopsConfigUser['reg_disclaimer']) );
    include 'include/registerform.php';
    $reg_form->display();
    include 'footer.php';
    break;
}
?>