<?php
/**
 * XOOPS global header file
 *
 * See the enclosed file license.txt for licensing information.
 * If you did not receive this file, get it at http://www.fsf.org/copyleft/gpl.html
 *
 * @copyright   The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license     http://www.fsf.org/copyleft/gpl.html GNU General Public License (GPL)
 * @package     core
 * @since       3.0
 * @author      Taiwen Jiang <phppp@users.sourceforge.net>
 * @version     $Id$
 */

class_exists("XOOPS") or die('XOOPS engine is not avilable');

$GLOBALS['xoopsTpl'] = XOOPS::registry('view')->getEngine();
$GLOBALS['xoTheme'] = XOOPS::registry('view')->loadTheme();

/*
$plugin = XOOPS::registry('layout')->plugin;
$request = $this->frontController->getRequest();
$plugin->setRequest($request);
$response = $this->frontController->getResponse();
$plugin->setResponse($response);
XOOPS::registry('viewRenderer')->setNeverRender();
ob_start();
*/
