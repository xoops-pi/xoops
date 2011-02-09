<?php
/**
 * XOOPS admin file
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright   The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license     http://www.fsf.org/copyleft/gpl.html GNU public license
 * @version     $Id: admin.php 2755 2009-02-02 14:37:15Z phppp $
 */

$xoopsOption['pagetype'] = "admin";
include "mainfile.php";
include XOOPS_ROOT_PATH . "/include/cp_functions.php";
/*********************************************************/
/* Admin Authentication                                  */
/*********************************************************/

if ( $xoopsUser ) {
    if ( !$xoopsUser->isAdmin(-1) ) {
        redirect_header("index.php", 2, _AD_NORIGHT);
        exit();
    }
} else {
    redirect_header("index.php", 2, _AD_NORIGHT);
    exit();
}

xoops_cp_header();
// ###### Output warn messages for security ######
if (is_dir(XOOPS_ROOT_PATH . "/install/" )) {
    xoops_error(sprintf(_AD_WARNINGINSTALL, XOOPS_ROOT_PATH . '/install/'));
    echo '<br />';
}

if ( is_writable(XOOPS_ROOT_PATH . "/mainfile.php" ) ) {
    xoops_error(sprintf(_AD_WARNINGWRITEABLE, XOOPS_ROOT_PATH . '/mainfile.php'));
    echo '<br />';
}

// ###### Output warn messages for correct functionality  ######
if (!is_writable(XOOPS_CACHE_PATH))  {
    xoops_error(sprintf(_AD_WARNINGNOTWRITEABLE, XOOPS_CACHE_PATH));
    echo '<br />';
}
if (!is_writable(XOOPS_UPLOAD_PATH))     {
    xoops_error(sprintf(_AD_WARNINGNOTWRITEABLE, XOOPSS_UPLOAD_PATH));
    echo '<br />';
}
if (!is_writable(XOOPS_COMPILE_PATH))    {
    xoops_error(sprintf(_AD_WARNINGNOTWRITEABLE, XOOPS_COMPILE_PATH));
    echo '<br />';
}

if (strpos(XOOPS_PATH, XOOPS_ROOT_PATH) !== false || strpos(XOOPS_PATH, $_SERVER['DOCUMENT_ROOT']) !== false) {
    xoops_error(sprintf(_AD_WARNINGXOOPSLIBINSIDE, XOOPS_PATH));
    echo '<br />';
}

if (strpos(XOOPS_VAR_PATH, XOOPS_ROOT_PATH) !== false || strpos(XOOPS_VAR_PATH, $_SERVER['DOCUMENT_ROOT']) !== false) {
    xoops_error(sprintf(_AD_WARNINGXOOPSLIBINSIDE, XOOPS_VAR_PATH));
    echo '<br />';
}

if (!empty($_GET['xoopsorgnews'])) {
    // Multiple feeds
    $rssurl = array();
    $rssurl[] = 'http://sourceforge.net/export/rss2_projnews.php?group_id=41586&rss_fulltext=1';
    $rssurl[] = 'http://www.xoops.org/backend.php';

    if ($URLs = @include XOOPS_ROOT_PATH . '/language/' . $xoopsConfig["language"] . '/backend.php') {
        $rssurl = array_unique( array_merge($rssurl, $URLs) );
    }
    $rssfile = 'adminnews-' . $xoopsConfig["language"] ;
    xoops_load("cache");

    $items = array();
    if (!$items = XoopsCache::read($rssfile)) {
        require_once XOOPS_ROOT_PATH . '/class/snoopy.php';
        include_once XOOPS_ROOT_PATH . '/class/xml/rss/xmlrss2parser.php';
        xoops_load("xoopslocal");
        $myts =& MyTextSanitizer::getInstance();
        $snoopy = new Snoopy();
        $cnt = 0;
        foreach ($rssurl as $url) {
            if ($snoopy->fetch($url)) {
                $rssdata = $snoopy->results;
                $rss2parser = new XoopsXmlRss2Parser($rssdata);
                if (false != $rss2parser->parse()) {
                    $_items = $rss2parser->getItems();
                    $count = count($_items);
                    for ($i = 0; $i < $count; $i++) {
                        $_items[$i]['title'] = XoopsLocal::convert_encoding($_items[$i]['title'], _CHARSET, 'UTF-8');
                        $_items[$i]['description'] = XoopsLocal::convert_encoding($_items[$i]['description'], _CHARSET, 'UTF-8');
                        $items[strval(strtotime($_items[$i]['pubdate'])) . "-" . strval(++$cnt)] = $_items[$i];
                    }

                } else {
                    echo $rss2parser->getErrors();
                }
            }
        }
        krsort($items);
        XoopsCache::write($rssfile, $items, 86400);
    }

    if ($items != '') {
        echo '<table class="outer" width="100%">';
        $myts =& MyTextSanitizer::getInstance();
        foreach (array_keys($items) as $i) {
            echo '<tr class="head"><td><a href="' . htmlspecialchars($items[$i]['link']) . '" rel="external">';
            echo htmlspecialchars( $items[$i]['title'] ) . '</a> (' . htmlspecialchars($items[$i]['pubdate']) . ')</td></tr>';
            if ($items[$i]['description'] != "") {
                echo '<tr><td class="odd">' . $items[$i]['description'];
                if (!empty($items[$i]['guid'])) {
                    echo '&nbsp;&nbsp;<a href="' . htmlspecialchars($items[$i]['guid']) . '" rel="external">' . _MORE . '</a>';
                }
                echo '</td></tr>';
            } elseif ($items[$i]['guid'] != "") {
                echo '<tr><td class="even" valign="top"></td><td colspan="2" class="odd"><a href="' . htmlspecialchars($items[$i]['guid']) . '" rel="external">' . _MORE . '</a></td></tr>';
            }
        }
        echo '</table>';
    }

}
xoops_cp_footer();
?>