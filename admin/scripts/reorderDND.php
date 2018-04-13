<?php
    $sAddonPath = dirname(dirname(__DIR__));
    if (is_readable($sAddonPath.'/init.php'))     {require ($sAddonPath.'/init.php');}
    // An associative array that by default contains the contents of $aRequestVars, $aRequestVars and $_COOKIE.
    $aRequestVars = $_REQUEST;
    // First we prevent direct access and check for variables
    if(!isset($aRequestVars['action']) OR !isset($aRequestVars['recordsArray'])) {
        // now we redirect to index, if you are in subfolder use ../index.php
        $sMessage = $MESSAGE['RECORD_MODIFIED_FAILED'];
    } else {
        // check if user has permissions to access the  module
        if(!class_exists('admin')){ include(WB_PATH.'/framework/class.admin.php'); }
        $admin = new admin('Modules', 'module_view', false, false);
        if (!($admin->is_authenticated() && $admin->get_permission($sAddonName, 'module'))){
            $sMessage = $MESSAGE['RECORD_MODIFIED_FAILED'];
        } else {
            // Sanitized variables
            $action = $admin->add_slashes($aRequestVars['action']);
            $updateRecordsArray = isset($aRequestVars['recordsArray']) ? $aRequestVars['recordsArray'] : [];
        // This line verifies that in &action is not other text than "updateRecordsListings", if something else is inputed (to try to HACK the DB), there will be no DB access..
            if ($action == "updateRecordsListings"){
                $listingCounter = 1;
                $output = "";
                foreach ($updateRecordsArray as $recordIDValue) {
                    $database->query("UPDATE `".TABLE_PREFIX."mod_foldergallery_files` SET position = ".$listingCounter." WHERE `id` = ".$recordIDValue);
                    $listingCounter ++;
                }
                $sMessage = '<img src="'.WB_URL.'/modules/jsadmin/images/success.gif" style="vertical-align:middle;"/> <span style="font-size: 80%">'
                            .$MOD_FOLDERGALLERY['REORDER_INFO_SUCESS'].'</span>';
            }
        }
    } // this ends else statement from the top of the page
    echo $sMessage;

