<?php

    $sAddonPath = dirname(__DIR__);
    if (is_readable($sAddonPath.'/init.php'))     {require ($sAddonPath.'/init.php');}
    // to print with or without header, default is with header
    $admin_header=true;
    // Workout if the developer wants to show the info banner
    $print_info_banner = false; // true/false
    // Tells script to update when this page was last updated
    $update_when_modified = false;
    // Include WB admin wrapper script to sanitize page_id and section_id, print SectionInfoLine
    require(WB_PATH.'/modules/admin.php');

    $settings = getFGSettings($section_id);
    $sSectionIdPrefix = (defined( 'SEC_ANCHOR' ) && ( SEC_ANCHOR != '' )  ? SEC_ANCHOR : 'Sec' );
    $sBacklink = ADMIN_URL.'/pages/modify.php?page_id='.$page_id.'#'.$sSectionIdPrefix.$section_id;
    $sSynclink = $sAddonUrl.'/admin/sync.php?page_id='.$page_id.'&section_id='.$section_id;

if(isset($aRequestVars['save'])) {
        if(isset($aRequestVars['cat_id'])) {
                $sCatID = $aRequestVars['cat_id'];
        } else {
                $error['no_cat_id'] = 1;
        }
        if(isset($aRequestVars['old_cat_name'])) {
            $sOldCatName = htmlspecialchars((string) isset($aRequestVars['old_cat_name']) ? $aRequestVars['old_cat_name'] : '', ENT_COMPAT);
        }
        if(isset($aRequestVars['cat_name']) && !empty($aRequestVars['cat_name'])) {
            $sCatName = htmlspecialchars((string) isset($aRequestVars['cat_name']) ? $aRequestVars['cat_name'] : $old_cat_name, ENT_COMPAT);
        } else {
            $sCatName = $sOldCatName;
        }
        if(isset($aRequestVars['cat_description'])) {
            $sCatDescription = htmlspecialchars((string) isset($aRequestVars['cat_description']) ? $aRequestVars['cat_description'] : '', ENT_COMPAT);
        }

        $active = 0;
        if(isset($aRequestVars['active'])) {
                $active = $aRequestVars['active'];
        }
        $sql = 'UPDATE `'.TABLE_PREFIX.'mod_foldergallery_categories` SET '
              .'`cat_name`= \''.$database->escapeString($sCatName).'\', '
              .'`description`= \''.$database->escapeString($sCatDescription).'\', '
              .'`active`= '.(int)$active.' '
              .'WHERE `id`='.(int)$sCatID;
        if($database->query($sql)){
                $admin->print_success($TEXT['CAT_SUCCESS'], $sSynclink);
        } else {
                $admin->print_error($MOD_FOLDERGALLERY['ERROR_MESSAGE'], $sBacklink);
        }
}

$admin->print_footer();
// end of file