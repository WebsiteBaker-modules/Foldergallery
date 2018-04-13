<?php

    $sAddonPath = dirname(dirname(__DIR__));
    if (is_readable($sAddonPath.'/init.php'))     {require ($sAddonPath.'/init.php');}
    // An associative array that by default contains the contents of $aRequestVars, $aRequestVars and $_COOKIE.
    $aRequestVars = $_REQUEST;

// Answer array wich is sent back to the backend
    $answer = [];
    $answer['message'] = $MOD_FOLDERGALLERY['CAT_TOGGLE_ACTIV_FAIL'];
    $answer['success'] = 'false';

// Check the Parameters
    if(!isset($aRequestVars['cat_id']) || !isset($aRequestVars['action'])) {
        exit(json_encode($answer));
    }

    if(!(($aRequestVars['action'] == 'enable') || ($aRequestVars['action'] == 'disable')) || !is_numeric($aRequestVars['cat_id'])) {
        exit(json_encode($answer));
    }
// OK, Parameters seem to be save

// Check if user has enough rights to do this:
    if(!class_exists('admin')){ include(WB_PATH.'/framework/class.admin.php'); }
    $admin = new admin('Modules', 'module_view', false, false);
    if (!($admin->is_authenticated() && $admin->get_permission($sAddonName, 'module'))) {
        exit(json_encode($answer));
    }

    if (isset($aRequestVars['action']) && ($aRequestVars['action'] == 'disable')) {
        $active = 0;
        $answer['message'] = $MOD_FOLDERGALLERY['CAT_INACTIVE'];
    } else {
        $active = 1;
        $answer['message'] = $MOD_FOLDERGALLERY['CAT_ACTIVE'];
    }

    $database->query("UPDATE `".TABLE_PREFIX."mod_foldergallery_categories` SET active = ".$active." WHERE `id` = ".$aRequestVars['cat_id']);

    if($database->is_error()) {
        $answer['message'] = $MOD_FOLDERGALLERY['CAT_TOGGLE_ACTIV_FAIL'];
        exit(json_encode($answer));
    }

// If the script is still running, set success to true
    $answer['success'] = 'true';
// and echo the answer as json to the ajax function
    echo json_encode($answer);

