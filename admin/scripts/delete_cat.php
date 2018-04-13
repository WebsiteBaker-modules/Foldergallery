<?php

    $sAddonPath = dirname(dirname(__DIR__));
    if (is_readable($sAddonPath.'/init.php')) {require ($sAddonPath.'/init.php');}
    // to print with or without header, default is with header
    $admin_header=true;
    // Workout if the developer wants to show the info banner
    $print_info_banner = false; // true/false
    // Tells script to update when this page was last updated
    $update_when_modified = false;
    // Include WB admin wrapper script to sanitize page_id and section_id, print SectionInfoLine
    require(WB_PATH.'/modules/admin.php');
    // An associative array that by default contains the contents of $aRequestVars, $aRequestVars and $_COOKIE.
//    $aRequestVars = $_REQUEST;
    $sSectionIdPrefix = (defined( 'SEC_ANCHOR' ) && ( SEC_ANCHOR != '' )  ? SEC_ANCHOR : 'Sec' );
    $sBacklink = ADMIN_URL.'/pages/modify.php?page_id='.$page_id.'#'.$sSectionIdPrefix.$section_id;
    $sSynclink = '/admin/sync.php?page_id='.$page_id.'&section_id='.$section_id;

    if(isset($aRequestVars['cat_id']) && is_numeric($aRequestVars['cat_id'])) {
        $cat_id = intval($aRequestVars['cat_id']);
        $sql  = 'SELECT * FROM `'.TABLE_PREFIX.'mod_foldergallery_categories` '
              . 'WHERE `id`='.$cat_id.';';
/*
        if ($sCategorie = $database->get_one($sql)){
*/
        $query = $database->query($sql);
        if($result = $query->fetchRow(MYSQLI_ASSOC)){
            // Dateien löschen
            $settings = getFGSettings($section_id);
//            $delete_path = $path.$settings['root_dir'].$result['parent'].'/'.$result['categorie'];
            // DB Einträge löschen  .'&section_id='.$section_id
            rek_db_delete($cat_id,$path.$settings['root_dir']);

            $admin->print_success(sprintf($MOD_FOLDERGALLERY['DELETE_CAT'], $sCategorie), $sAddonUrl.$sSynclink);
        } else {
            $admin->print_error($MOD_FOLDERGALLERY['FS_ERROR'], $sBacklink);
        }

    }
    $admin->print_footer();
