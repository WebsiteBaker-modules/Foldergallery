<?php

    $sAddonPath = dirname(dirname(__DIR__));
    if (is_readable($sAddonPath.'/init.php'))     {require ($sAddonPath.'/init.php');}
    // to print with or without header, default is with header
    $admin_header=true;
    // Workout if the developer wants to show the info banner
    $print_info_banner = false; // true/false
    // Tells script to update when this page was last updated
    $update_when_modified = false;
    // Include WB admin wrapper script to sanitize page_id and section_id, print SectionInfoLine
    require(WB_PATH.'/modules/admin.php');
    // An associative array that by default contains the contents of $aRequestVars, $aRequestVars and $_COOKIE.
    $aRequestVars = $_REQUEST;

    if(isset($aRequestVars['cat_id']) && is_numeric($aRequestVars['cat_id'])) {
        $cat_id = intval($aRequestVars['cat_id']);
        $sql  = 'SELECT `categorie`, `parent`, `has_child` FROM `'.TABLE_PREFIX.'mod_foldergallery_categories` '
              . 'WHERE `id`='.$cat_id.';';
        $query = $database->query($sql);
        if($result = $query->fetchRow(MYSQLI_ASSOC)){
            // Dateien löschen
            $settings = getSettings($section_id);
            $sCategorie = $MediaRel.$settings['root_dir'];
//            $sCategorie = $MediaRel.'/'.$sAddonName;
            $delete_path = $path.$settings['root_dir'].$result['parent'].'/'.$result['categorie'];
            // DB Einträge löschen
            rek_db_delete($cat_id);
            $admin->print_success($result['categorie'].' '.$TEXT['SUCCESS'].' '.$TEXT['DELETED'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id.'&section_id='.$section_id);
        } else {
            $admin->print_error($MOD_FOLDERGALLERY['ERROR_MESSAGE'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id.'&section_id='.$section_id);
        }

    }
    $admin->print_footer();
