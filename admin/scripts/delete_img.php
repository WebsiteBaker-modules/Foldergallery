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
//    $aRequestVars = $_REQUEST;

    if(isset($aRequestVars['id']) && is_numeric($aRequestVars['id']))
    {
        $settings = getFGSettings($section_id);
        $root_dir = $settings['root_dir']; //Chio
        $iId = 0;
        $cat_id = intval($aRequestVars['cat_id']);
        $sRedirectlink = $sAddonUrl.'/admin/modify_cat.php?page_id='.$page_id.'&section_id='.$section_id.'&cat_id='.$cat_id;
        $sql = 'SELECT * FROM '.TABLE_PREFIX.'mod_foldergallery_files WHERE id='.$aRequestVars['id'].';';
        if($query = $database->query($sql)){
                $result = $query->fetchRow(MYSQLI_ASSOC);
                $bildfilename = $result['file_name'];
                $parent_id = $result['parent_id'];
                //echo '<h2>'.$parent_id.'</h2>' ;
                $query2 = $database->query('SELECT * FROM '.TABLE_PREFIX.'mod_foldergallery_categories WHERE id='.$parent_id.' LIMIT 1;');
                $categorie = $query2->fetchRow(MYSQLI_ASSOC);
                if($categorie['parent_id'] != -1) {
                   $parent   = $categorie['parent'].'/'.$categorie['categorie'];
                } else {
                    $parent = '';
                }
                $folder = $root_dir.$parent;
                $pathToFolder = $path.$folder.'/';

                $pathToFile = $path.$folder.'/'.$bildfilename;
                $pathToThumb = $path.$folder.$thumbPath.'/'.$bildfilename;
                if(!deleteFile($pathToFile) || !deleteFile($pathToThumb) ) {
                    $admin->print_error($MOD_FOLDERGALLERY['ERROR_MESSAGE'], $sRedirectlink);
                }
                $sql = 'DELETE FROM '.TABLE_PREFIX.'mod_foldergallery_files WHERE id='.$aRequestVars['id'];
                $database->query($sql);
                $admin->print_success($bildfilename.' '.$TEXT['SUCCESS'].' '.$TEXT['DELETED'], $sRedirectlink);
        } else {
                $admin->print_error($MOD_FOLDERGALLERY['ERROR_MESSAGE'], $sRedirectlink);
        }
    } else {
        $admin->print_error($MOD_FOLDERGALLERY['ERROR_MESSAGE'], $sRedirectlink);
    }

    $admin->print_footer();
