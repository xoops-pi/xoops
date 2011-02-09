<?php

function smarty_function_xoInboxCount( $params, &$smarty ) 
{
    global $xoopsUser;
    
    if ( !isset($xoopsUser) || !is_object($xoopsUser) ) {
        return;
    }
    $time = time();
    if ( isset( $_SESSION['xoops_inbox_count'] ) && @$_SESSION['xoops_inbox_count_expire'] > $time ) {
        $count = intval( $_SESSION['xoops_inbox_count'] );
    } else {
        $module_handler = xoops_gethandler('module');
        $pm_module = $module_handler->getByDirname('pm');
        if ($pm_module && $pm_module->getVar('isactive')) {
            $pm_handler =& xoops_getModuleHandler( 'message', 'pm' );
        } else {
            $pm_handler =& xoops_gethandler( 'privmessage' );
        }
        $criteria = new CriteriaCompo( new Criteria('read_msg', 0) );
        $criteria->add( new Criteria( 'to_userid', $xoopsUser->getVar('uid') ) );
        $count = intval( $pm_handler->getCount($criteria) );
        $_SESSION['xoops_inbox_count'] = $count;
        $_SESSION['xoops_inbox_count_expire'] = $time + 60;
    }
    if ( !@empty( $params['assign'] ) ) {
        $smarty->assign( $params['assign'], $count );
    } else {
        echo $count;
    }
}

?>