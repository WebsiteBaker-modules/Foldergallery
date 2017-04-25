<?php
    $sAddonPath = dirname(dirname(__DIR__));
    if (is_readable($sAddonPath.'/init.php'))     {require ($sAddonPath.'/init.php');}
    // An associative array that by default contains the contents of $aRequestVars, $aRequestVars and $_COOKIE.
    $aRequestVars = $_REQUEST;

    $v = new Validator();
    $fileArray = array();
    foreach ($aRequestVars as $key => $value) {
        if ($key != 'folder') {
            if (file_exists(WB_PATH . $aRequestVars['folder'] . '/' . $value)) {
                $fileArray[$key] = $value;
            }
        }
    }
    echo json_encode($fileArray);

