<?php

    $sAddonPath = dirname(__DIR__);
    if (is_readable($sAddonPath.'/init.php'))     {require ($sAddonPath.'/init.php');}
    if (is_readable($sAddonPath.'/presets/thumbPresets.php')){require($sAddonPath.'/presets/thumbPresets.php');}
    // to print with or without header, default is with header
    $admin_header=true;
    // Workout if the developer wants to show the info banner
    $print_info_banner = (bool)(isset($aRequestVars['infoBanner'])?$aRequestVars['infoBanner']:true); // true/false
    // Tells script to update when this page was last updated
    $update_when_modified = false;
    // Include WB admin wrapper script to sanitize page_id and section_id, print SectionInfoLine
    require(WB_PATH.'/modules/admin.php');

if (isset($aRequestVars['id']) && is_numeric($aRequestVars['id']))
{
    $cat_id = intval($aRequestVars['cat_id']);
    $settings = getSettings($section_id);
    $root_dir = $settings['root_dir']; //Chio
    $sql = 'SELECT * FROM ' . TABLE_PREFIX . 'mod_foldergallery_files WHERE id=' . $aRequestVars['id'] . ';';
    if ($query = $database->query($sql)) {
        $result = $query->fetchRow(MYSQLI_ASSOC);
        $bildfilename = $result['file_name'];
        $parent_id = $result['parent_id'];

        $query2 = $database->query('SELECT * FROM ' . TABLE_PREFIX . 'mod_foldergallery_categories WHERE id=' . $parent_id . ' LIMIT 1;');
        $categorie = $query2->fetchRow(MYSQLI_ASSOC);
        if ($categorie['parent'] != "-1") {
            $parent = $categorie['parent'] . '/' . $categorie['categorie'];
        }
        else {
            $parent = '';
        }
        $full_file_link = $url.MEDIA_DIRECTORY.$root_dir.$parent . '/' . $bildfilename;
        $full_file   = $path . $root_dir . $parent . '/' . $bildfilename;
        $thumbFolder = $path.$root_dir.$parent.$thumbPath.'/';
        $thumb_file  = $thumbFolder.$bildfilename;
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            //Löscht das bisherige Thumbnail
            deleteFile($thumb_file);
            //Create the new Thumb
            $handle = new FgUpload(DirectoryHandler::DecodePath($full_file));
            FG_appendThumbSettings($handle, $settings['tbSettings'], DirectoryHandler::DecodePath($bildfilename));
            $topCrop    = floor($aRequestVars['y1']);
            $rightCrop  = floor($handle->image_src_x - $aRequestVars['x2']);
            $bottomCrop = floor($handle->image_src_y - $aRequestVars['y2']);
            $leftCrop   = floor($aRequestVars['x1']);
            $handle->image_precrop = "$topCrop $rightCrop $bottomCrop $leftCrop";
            $handle->process(DirectoryHandler::DecodePath($thumbFolder));
            if($handle->processed) {
                $admin->print_success($MOD_FOLDERGALLERY['UPDATED_THUMB'], $sAddonUrl.'/admin/modify_cat.php?page_id=' . $page_id . '&section_id=' . $section_id . '&cat_id=' . $cat_id);
            }
            else {
                $admin->print_error("Could not create a new thumbnail!", $sAddonUrl.'/admin/modify_cat.php?page_id=' . $page_id . '&section_id=' . $section_id . '&cat_id=' . $cat_id);
            }
        } else {
            list($width, $height, $type, $attr) = getimagesize(DirectoryHandler::DecodePath($full_file));
            $previewWidth = $settings['tbSettings']['image_x'];
            $previewHeight = $settings['tbSettings']['image_y'];

            $t = new Template($sAddonThemePath, 'remove');
            $t->set_file('modify_thumb', 'modify_thumb.htt');
            // clear the comment-block, if present
            $t->set_block('modify_thumb', 'CommentDoc');
            $t->clear_var('CommentDoc');
            $t->set_var($aTplDefaults);

            $t->set_var(array(
                // Infos for JCrop
                'REL_WIDTH'         => $width,
                'REL_HEIGHT'        => $height,
                'THUMB_SIZE'        => $previewWidth,
                'RATIO'             => $previewWidth/$previewHeight,
                // Language Strings
                'EDIT_THUMB'        => $MOD_FOLDERGALLERY['EDIT_THUMB'],
                'EDIT_THUMB_DESCR'  => $MOD_FOLDERGALLERY['EDIT_THUMB_DESCRIPTION'],
                'SAVE_NEW_THUMB'    => $MOD_FOLDERGALLERY['EDIT_THUMB_BUTTON'],
                'CANCEL'            => $TEXT['CANCEL'],
                // Data about the Image and Preview
                'FULL_FILE_LINK'    => $full_file_link,
                'PREVIEW_HEIGHT'    => $previewHeight,
                'PREVIEW_WIDTH'     => $previewWidth,
                'ADDON_URL'         => $sAddonUrl,
                'WB_URL'            => WB_URL,
                'PAGE_ID'           => $page_id,
                'SECTION_ID'        => $section_id,
                'CAT_ID'            => $cat_id,
                'IMG_ID'            => $aRequestVars['id']
            ));
            $t->pparse('output', 'modify_thumb');
        }
    }
} else {
    $admin->print_error($MOD_FOLDERGALLERY['ERROR_MESSAGE'], $sAddonUrl.'/admin/modify_cat.php?page_id=' . $page_id . '&section_id=' . $section_id . '&cat_id=' . $cat_id);
}
$admin->print_footer();
