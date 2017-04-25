<?php
/**
 * This file is used to comunicate with the LoadPreset-function in the
 * general-settings view.
 *
    $answer['success'] = (sizeof($thumbPresets)>0?'true':'false');
 * Simply returns a json encoded array with all the thumb presets
print '<pre  class="mod-pre rounded">function <span>'.__FUNCTION__.'( '.''.' );</span>  filename: <span>'.basename(__FILE__).'</span>  line: '.__LINE__.' -> <br />';
print_r( $aRequestVars ); print '</pre>'; flush (); //  ob_flush();;sleep(10); die();
 */
    $answer["success"] = "true";
    $sAddonPath = dirname(dirname(__DIR__));
    if (is_readable($sAddonPath.'/init.php')) {require ($sAddonPath.'/init.php');}
    // An associative array that by default contains the contents of $aRequestVars, $aRequestVars and $_COOKIE.
    $aRequestVars = $_REQUEST;
    if (!is_readable($sAddonPath.'/presets/thumbPresets.php')){
        $answer["success"] = "false";
        exit(json_encode($answer));
    }
    require($sAddonPath.'/presets/thumbPresets.php');
    $answer["preset"] = $thumbPresets[$aRequestVars["preset"]];
    $iRatio = (($answer["preset"]['thumb_ratio']=='free') ?'1':$answer["preset"]['thumb_ratio']);
    $iThumbX = ($aRequestVars["thumb_x"]?:$answer["preset"]['image_x']);
    $iThumbY = ($aRequestVars["thumb_x"]?:$answer["preset"]['image_y']);
    $answer["request"] = $aRequestVars;
    $answer["preset"]['image_x'] = $iThumbX;
    $iImageY = round($iThumbY * $iRatio,0);
    $answer["preset"]['image_y'] = (($iImageY==0) ? $iThumbX :$iImageY);
    echo json_encode($answer);

