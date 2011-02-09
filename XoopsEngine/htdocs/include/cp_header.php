<?php 
// $Id: cp_header.php 2648 2009-01-10 06:41:59Z phppp $
/**
 * module files can include this file for admin authorization
 * the file that will include this file must be located under xoops_url/modules/module_directory_name/admin_directory_name/
 */
//error_reporting(0);
$xoopsOption['pagetype'] = "admin";
include_once '../../../mainfile.php';
include_once XOOPS_ROOT_PATH . "/include/cp_functions.php";

$moduleperm_handler = & xoops_gethandler( 'groupperm' );
if ( $xoopsUser ) {
    $url_arr = explode('/', strstr($_SERVER['REQUEST_URI'], '/modules/'));
    $module_handler =& xoops_gethandler('module');
    $xoopsModule =& $module_handler->getByDirname($url_arr[2]);
    unset($url_arr);
    
    if ( !$moduleperm_handler->checkRight( 'module_admin', $xoopsModule->getVar( 'mid' ), $xoopsUser->getGroups() ) ) {
        redirect_header( XOOPS_URL, 1, _NOPERM );
        exit();
    }
} else {
    redirect_header( XOOPS_URL . "/user.php", 1, _NOPERM );
    exit();
}

// set config values for this module
if ( $xoopsModule->getVar( 'hasconfig' ) == 1 || $xoopsModule->getVar( 'hascomments' ) == 1 ) {
    $config_handler = & xoops_gethandler( 'config' );
    $xoopsModuleConfig =  $config_handler->getConfigsByCat( 0, $xoopsModule->getVar( 'mid' ) );
}

// include the default language file for the admin interface
xoops_loadLanguage("admin", $xoopsModule->getVar("dirname"));
?>