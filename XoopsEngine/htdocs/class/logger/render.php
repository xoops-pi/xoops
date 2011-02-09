<?php
/**
 * Xoops Logger renderer
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
 * @subpackage      logger
 * @since           2.3.0
 * @author          Skalpa Keo <skalpa@xoops.org>
 * @author          Taiwen Jiang <phppp@users.sourceforge.net>
 * @version         $Id: render.php 2633 2009-01-10 03:09:41Z phppp $
 *
 * @todo            Not well written, just keep as it is. Refactored in 3.0
 */

defined( 'XOOPS_ROOT_PATH' ) or die();

$ret = '';

if ( $mode == 'popup' ) {
    $dump = $this->dump( '' );
    $content = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
    <meta http-equiv="content-language" content="'._LANGCODE.'" />
    <meta http-equiv="content-type" content="text/html; charset='._CHARSET.'" />
    <title>'.$xoopsConfig['sitename'].' - Debug </title>
    <meta name="generator" content="XOOPS" />
    <link rel="stylesheet" type="text/css" media="all" href="'.xoops_getcss($xoopsConfig['theme_set']).'" />
</head>
<body>' . $dump . '
    <div style="text-align:center;">
        <input class="formButton" value="'._CLOSE.'" type="button" onclick="javascript:window.close();" />
    </div>
';
    $ret .= '
<script type="text/javascript">
    debug_window = openWithSelfMain("about:blank", "popup", 680, 450, true);
    debug_window.document.clear();
';
    $lines = preg_split("/(\r\n|\r|\n)( *)/", $content);
    foreach ($lines as $line) {
        $ret .= "\n" . 'debug_window.document.writeln("'.str_replace( array( '"', '</' ), array( '\"', '<\/' ), $line).'");';
    }
    $ret .= '
    debug_window.focus();
    debug_window.document.close();
</script>
';

}

$this->addExtra( 'Included files', count ( get_included_files() ) . ' files' );
$memory = 0;
if ( function_exists( 'memory_get_usage' ) ) {
    $memory = memory_get_usage() . ' bytes';
} else {
    $os = isset( $_ENV['OS'] ) ? $_ENV['OS'] : $_SERVER['OS'];
    if ( strpos( strtolower( $os ), 'windows') !== false ) {
        $out = array();
        exec('tasklist /FI "PID eq ' . getmypid() . '" /FO LIST', $out );
        $memory = substr( $out[5], strpos( $out[5], ':') + 1) . ' [Estimated]';
    }
}
if ( $memory ) {
    $this->addExtra( 'Memory usage', $memory );
}

if ( empty( $mode ) ) {
    $views = array( 'errors', 'queries', 'blocks', 'extra' );
    $ret .= "\n<div id=\"xo-logger-output\">\n<div id='xo-logger-tabs'>\n";
    $ret .= "<a href='javascript:xoSetLoggerView(\"none\")'>None</a>\n";
    $ret .= "<a href='javascript:xoSetLoggerView(\"\")'>All</a>\n";
    foreach ( $views as $view ) {
        $count = count( $this->$view );
        $ret .= "<a href='javascript:xoSetLoggerView(\"$view\")'>$view ($count)</a>\n";
    }
    $count = count( $this->logstart );
    $ret .= "<a href='javascript:xoSetLoggerView(\"timers\")'>timers ($count)</a>\n";
    $ret .= "</div>\n";
}

if ( empty($mode) || $mode == 'errors' ) {
    $types = array(
        E_USER_NOTICE => 'Notice',
        E_USER_WARNING => 'Warning',
        E_USER_ERROR => 'Error',
        E_NOTICE => 'Notice',
        E_WARNING => 'Warning',
        E_STRICT => 'Strict',
    );
    $class = 'even';
    $ret .= '<table id="xo-logger-errors" class="outer"><tr><th>Errors</th></tr>';
    foreach ( $this->errors as $error ) {
        $ret .= "\n<tr><td class='$class'>";
        $ret .= isset( $types[ $error['errno'] ] ) ? $types[ $error['errno'] ] : 'Unknown';
        $ret .= sprintf( ": %s in file %s line %s<br />\n", $this->sanitizePath($error['errstr']), $this->sanitizePath($error['errfile']), $error['errline'] );
        $ret .= "</td></tr>";
        $class = ($class == 'odd') ? 'even' : 'odd';
    }
    $ret .= "\n</table>\n";
}

if ( empty($mode) || $mode == 'queries' ) {
    $class = 'even';
    $ret .= '<table id="xo-logger-queries" class="outer"><tr><th>Queries</th></tr>';

    $pattern = '/\b' . preg_quote($GLOBALS['xoopsDB']->prefix()) . '\_/i';

    foreach ($this->queries as $q) {
        $sql = preg_replace($pattern, '', $q['sql']);
        if (isset($q['error'])) {
            $ret .= '<tr class="'.$class.'"><td><span style="color:#ff0000;">'.htmlentities($sql).'<br /><strong>Error number:</strong> '.$q['errno'].'<br /><strong>Error message:</strong> '.$q['error'].'</span></td></tr>';
        } else {
            $ret .= '<tr class="'.$class.'"><td>'.htmlentities($sql).'</td></tr>';
        }
        $class = ($class == 'odd') ? 'even' : 'odd';
    }
    $ret .= '<tr class="foot"><td>Total: <span style="color:#ff0000;">'.count($this->queries).'</span> queries</td></tr></table>';
}
if ( empty($mode) || $mode == 'blocks' ) {
    $class = 'even';
    $ret .= '<table id="xo-logger-blocks" class="outer"><tr><th colspan="2">Blocks</th></tr>';
    foreach ($this->blocks as $b) {
        if ($b['cached']) {
            $ret .= '<tr><td class="'.$class.'"><strong>'.htmlspecialchars($b['name']).':</strong> Cached (regenerates every '.intval($b['cachetime']).' seconds)</td></tr>';
        } else {
            $ret .= '<tr><td class="'.$class.'"><strong>'.htmlspecialchars($b['name']).':</strong> No Cache</td></tr>';
        }
        $class = ($class == 'odd') ? 'even' : 'odd';
    }
    $ret .= '<tr class="foot"><td>Total: <span style="color:#ff0000;">'.count($this->blocks).'</span> blocks</td></tr></table>';
}
if ( empty($mode) || $mode == 'extra' ) {
    $class = 'even';
    $ret .= '<table id="xo-logger-extra" class="outer"><tr><th colspan="2">Extra</th></tr>';
    foreach ($this->extra as $ex) {
        $ret .= '<tr><td class="'.$class.'"><strong>'.htmlspecialchars($ex['name']).':</strong> '.htmlspecialchars($ex['msg']).'</td></tr>';
        $class = ($class == 'odd') ? 'even' : 'odd';
    }
    $ret .= '</table>';
}
if ( empty($mode) || $mode == 'timers' ) {
    $class = 'even';
    $ret .= '<table id="xo-logger-timers" class="outer"><tr><th colspan="2">Timers</th></tr>';
    foreach ( $this->logstart as $k => $v ) {
        $ret .= '<tr><td class="'.$class.'"><strong>'.htmlspecialchars($k).'</strong> took <span style="color:#ff0000;">' . sprintf( "%.03f", $this->dumpTime($k) ) . '</span> seconds to load.</td></tr>';
        $class = ($class == 'odd') ? 'even' : 'odd';
    }
    $ret .= '</table>';
}

if ( empty( $mode ) ) {
    $ret .= <<<EOT
</div>
<script type="text/javascript">
    function xoLogCreateCookie(name,value,days) {
        if (days) {
            var date = new Date();
            date.setTime(date.getTime()+(days*24*60*60*1000));
            var expires = "; expires="+date.toGMTString();
        }
        else var expires = "";
        document.cookie = name+"="+value+expires+"; path=/";
    }
    function xoLogReadCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for(var i=0;i < ca.length;i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1,c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
        }
        return null;
    }
    function xoLogEraseCookie(name) {
        createCookie(name,"",-1);
    }
    function xoSetLoggerView( name ) {
        var log = document.getElementById( "xo-logger-output" );
        if ( !log ) return;
        var i, elt;
        for ( i=0; i!=log.childNodes.length; i++ ) {
            elt = log.childNodes[i];
            if ( elt.tagName && elt.tagName.toLowerCase() != 'script' && elt.id != "xo-logger-tabs" ) {
                elt.style.display = ( !name || elt.id == "xo-logger-" + name ) ? "block" : "none";
            }
        }
        xoLogCreateCookie( 'XOLOGGERVIEW', name, 1 );
    }
    xoSetLoggerView( xoLogReadCookie( 'XOLOGGERVIEW' ) );
</script>

EOT;
}


?>