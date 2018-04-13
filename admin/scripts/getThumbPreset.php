<?php
/**
 * This file is used to comunicate with the LoadPreset-function in the
 * general-settings view.
 *
    $answer['success'] = (sizeof($thumbPresets)>0?'true':'false');
 * Simply returns a json encoded array with all the thumb presets
print '<pre  class="mod-pre rounded">function <span>'.__FUNCTION__.'( '.''.' );</span>  filename: <span>'.basename(__FILE__).'</span>  line: '.__LINE__.' -> <br />';
print_r( $aRequestVars ); print '</pre>'; flush (); //  ob_flush();;sleep(10); die();
    if (is_readable($sAppPath.'/config.php')) {require ($sAppPath.'/config.php');}
        $admin = new admin('##skip##','start', false, false);
        if (!$admin->is_authenticated() || !$admin->ami_group_member('1')){
            $answer['message'] = ['Illegal file access!'];
            throw new RuntimeException($answer);
        }
 */
    $answer[]   = [];
    $sAppPath   = dirname(dirname(dirname(__DIR__)));
    $sAddonPath = dirname(dirname(__DIR__));
    try{
    if (is_readable($sAddonPath.'/init.php')) {require ($sAddonPath.'/init.php');}
    // An associative array that by default contains the contents of $aRequestVars, $aRequestVars and $_COOKIE.
    $aRequestVars = $_REQUEST;
    if (!isset($aRequestVars["preset"])){
        $answer["success"] = "false";
        exit(json_encode($answer));
    }
    if (!is_readable($sAddonPath.'/presets/thumbPresets.php')){
        $answer["success"] = "false";
        exit(json_encode($answer));
    }
    require($sAddonPath.'/presets/thumbPresets.php');
    $answer["preset"] = $thumbPresets[$aRequestVars["preset"]];
    $iRatio  = (($answer["preset"]['thumb_ratio']=='free') ? '1' : $answer["preset"]['thumb_ratio']);
    $iThumbX = (isset($aRequestVars["thumb_x"]) ? $aRequestVars["thumb_x"] :$answer["preset"]['image_x']);
    $iThumbY = (isset($aRequestVars["thumb_x"]) ? $aRequestVars["thumb_x"] :$answer["preset"]['image_y']);
    $iImageY = round($iThumbY * $iRatio,0);
    $answer["success"] = "true";
    $answer["request"] = $aRequestVars;
    $answer["preset"]['image_x'] = $iThumbX;
    $answer["preset"]['image_y'] = (($iImageY==0) ? $iThumbX :$iImageY);
    echo json_encode($answer);
    } catch (Exception $ex) {
        $answer['message'] = $ex->getMessage();
        $answer['success'] = false;
//        $aJsonRespond['sIdKey']  = $admin->getIDKEY($iIdKey);
//        $answer['sIdKey']  = $iIdKey;
        echo(json_encode($answer));
    }
// echo the json_respond to the ajax function

