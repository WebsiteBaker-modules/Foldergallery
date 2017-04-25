<?php


    $sAddonPath = dirname(__DIR__);
    if (is_readable($sAddonPath.'/init.php'))     {require ($sAddonPath.'/init.php');}
    if (is_readable($sAddonPath.'/presets/thumbPresets.php')){require($sAddonPath.'/presets/thumbPresets.php');}
    // to print with or without header, default is with header
    $admin_header=true;
    // Workout if the developer wants to show the info banner
    $print_info_banner = true; // true/false
    // Tells script to update when this page was last updated
    $update_when_modified = false;
    // Include WB admin wrapper script to sanitize page_id and section_id, print SectionInfoLine
    require(WB_PATH.'/modules/admin.php');
    // An associative array that by default contains the contents of $_GET, $_POST and $_COOKIE.
    $aRequestVars = $_REQUEST;

    $settings = getSettings($section_id);
    $root_dir = $settings['root_dir']; //Chio

if (isset($aRequestVars['cat_id']) && is_numeric($aRequestVars['cat_id'])) {
    $cat_id = $aRequestVars['cat_id'];
} else {
    $error['no_cat_id'] = 1;
    $admin->print_error('lost cat', ADMIN_URL . '/pages/modify.php?page_id=' . $page_id . '&section_id=' . $section_id);
    exit();
}

// Kategorie Infos aus der DB holen
    $sql = 'SELECT * FROM ' . TABLE_PREFIX . 'mod_foldergallery_categories WHERE id=' . $cat_id . ' LIMIT 1;';
    $query = $database->query($sql);
    $categorie = $query->fetchRow(MYSQLI_ASSOC);

    if (is_array($categorie)) {
        if ($categorie['parent'] != -1) {
            $cat_path = $path . $settings['root_dir'] . $categorie['parent'] . '/' . $categorie['categorie'];
            $cat_path = str_replace(WB_PATH, '', $cat_path);
            $parent = $categorie['parent'] . '/' . $categorie['categorie'];
            $uploadPath = $settings['root_dir'].$categorie['parent'] . '/' . $categorie['categorie'];
        } else {
            // Root
            $cat_path = $path . $settings['root_dir'];
            $parent = '';
            $uploadPath = $settings['root_dir'];
        }
    }
    $sCatPath = $cat_path;
    $sCategorie =  mb_strlen($cat_path) > 125 ? mb_substr($cat_path, 0, 124).'…' : $cat_path;
    $parent_id = $categorie['id'];
    if ($categorie['active'] == 1) {
        $cat_active_checked = 'checked="checked"';
    } else {
        $cat_active_checked = '';
    }

    $folder       = $root_dir.$parent;
    $pathToFolder = $path.$folder.'/';
    $pathToThumb  = $path.$folder.$thumbPath . '/';
    $urlToFolder  = $url.$MediaAddonRel.$folder.'/';
    $urlToThumb   = $url.$MediaAddonRel.$folder.$thumbPath . '/';

    $bilder = array();
    $sql = 'SELECT * FROM ' . TABLE_PREFIX . 'mod_foldergallery_files WHERE parent_id="' . $parent_id . '" ORDER BY position ASC;';
    $query = $database->query($sql);
    if ($query->numRows()) {
        while ($result = $query->fetchRow( MYSQLI_ASSOC )) {
            // Falls es das Vorschaubild noch nicht gibt:
            //Chio Start
            $bildfilename = $result['file_name'];
            $file = $pathToFolder.$bildfilename;
            if (!is_file(DirectoryHandler::DecodePath($file))) {
                $deletesql  = 'DELETE FROM '.TABLE_PREFIX.'mod_foldergallery_files '
                            . 'WHERE id='.$result['id'];
                $database->query($deletesql);
                continue;
            }
            $file  = $pathToFolder.$bildfilename;
            $thumb = $pathToThumb.$bildfilename;

            if (!is_file(DirectoryHandler::DecodePath($thumb))) {
                FG_createThumb($file, $bildfilename, $pathToThumb, $settings['tbSettings']);
            }
        //Chio Ende
            $bilder[] = array(
                'id' => $result['id'],
                'file_name'  => $bildfilename, //Chio
                'caption'    => $result['caption'], //Chio
                'img_title'  => $result['img_title'], //jacobi22
                'thumb_link' => $urlToThumb . $bildfilename
            );
        }
} else {
    // Diese Kategorie enthaelt noch keine Bilder
    $error['noimages'] = 1;
}

//Template
    $t = new Template($sAddonThemePath, 'remove');
    $t->set_file('modify_cat', 'modify_cat.htt');
    // clear the comment-block, if present
    $t->set_block('modify_cat', 'CommentDoc');
    $t->clear_var('CommentDoc');
    // set other blocks
    $t->set_block('modify_cat', 'file_loop', 'FILE_LOOP');
    $t->set_var($aTplDefaults);
    $sUploadTemplateFile = '';
/*
    $sUploadTemplateFile = $sAddonThemePath.'/FineUpload/templates/simple-thumbnails.html';
    $sUploadTemplateFile = $sAddonThemePath.'/FineUpload/templates/manual_trigger.js';
*/
    if (is_readable($sUploadTemplateFile)){
/*
        $t->set_var('QQ-TEMPLATE'. $sUploadTemplateFile);
        $sContent = (file_get_contents($sUploadTemplateFile));
        ob_start();
        require $sUploadTemplate;
        $sContent = ob_get_clean();
        echo $sUploadTemplateFile;
        echo $sContent;
*/
    }

$sScriptTemplateBlock = 'show_template_manual_trigger_block';
$sScriptTemplate      = 'show_template_manual_trigger';

    $t->set_block('modify_cat', $sScriptTemplateBlock, $sScriptTemplate);
    $t->parse($sScriptTemplate, $sScriptTemplateBlock, true);
//    $t->set_block($sScriptTemplateBlock, '');

$sScriptTemplateBlock = 'show_template_default_block';
$sScriptTemplate      = 'show_template_default';

    $t->set_block('modify_cat', $sScriptTemplateBlock, $sScriptTemplate);
//    $t->parse($sScriptTemplate, $sScriptTemplateBlock, true);
    $t->set_block($sScriptTemplateBlock, '');

    $defaultQuality = (@$settings['defaultQuality']?:'50');
    $maxImageSize   = (@$settings['maxImageSize']?:'1024');

// data parsen
$t->set_var(array(
    'SYNC_ONKLICK'      => 'window.location = \''.$sAddonUrl.'/admin/sync.php?page_id='.$page_id.'&amp;section_id='.$section_id.'\';',
    'SYNC_STRING'                  => $MOD_FOLDERGALLERY['SYNC'],
    'FOLDER_IN_FS_VALUE' => str_replace($path,'',$sCategorie),
    'FOLDER_IN_FS_TITLE' => str_replace($path,'',$cat_path),
    'CAT_ACTIVE_CHECKED' => $cat_active_checked,
    'CAT_NAME_VALUE' => $categorie['cat_name'],
    'SECTION_ID_VALUE' => $section_id,
    'PAGE_ID_VALUE' => $page_id,
    'CAT_ID_VALUE' => $cat_id,
    'EXTENSIONS' => $settings['extensions'],
    'UPLOAD_FOLDER' => $uploadPath,
    'UPLOAD_SEC_NUM' => $page_id . '/' . $section_id . '/' . $cat_id,
    'UploadThumbnailSize' => 30,
    'defaultQuality' => $defaultQuality,
    'maxImageSize'  => $maxImageSize,
));

// Textvariablen parsen
$t->set_var(array(
    'MODIFY_CAT_TITLE' => $MOD_FOLDERGALLERY['MODIFY_CAT_TITLE'],
    'MODIFY_CAT_STRING' => $MOD_FOLDERGALLERY['MODIFY_CAT'],
    'FOLDER_IN_FS_STRING' => $MOD_FOLDERGALLERY['FOLDER_IN_FS'],
    'CAT_ACTIVE_STRING' => $MOD_FOLDERGALLERY['ACTIVE'],
    'CAT_NAME_STRING' => $MOD_FOLDERGALLERY['CAT_NAME'],
    'CAT_DESCRIPTION_STRING' => $MOD_FOLDERGALLERY['CAT_DESCRIPTION'],
    'CAT_DESCRIPTION_VALUE' => $categorie['description'],
    'MODIFY_IMG_STRING' => $MOD_FOLDERGALLERY['MODIFY_IMG'],
    'IMAGE_STRING' => $MOD_FOLDERGALLERY['IMAGE'],
    'IMAGE_NAME_STRING' => $MOD_FOLDERGALLERY['IMAGE_NAME'],
    'IMAGE_CAPTION_STRING' => $MOD_FOLDERGALLERY['IMG_CAPTION'],
    'IMAGE_ACTION_STRING' => $MOD_FOLDERGALLERY['ACTION'],
    'SAVE_STRING' => $TEXT['SAVE'],
    'CANCEL_STRING' => $TEXT['CANCEL'],
    'SORT_IMAGES_STRING' => $MOD_FOLDERGALLERY['SORT_IMAGE'],
    'IMAGE_DELETE_ALT' => $MOD_FOLDERGALLERY['IMAGE_DELETE_ALT'],
    'THUMB_EDIT_ALT' => $MOD_FOLDERGALLERY['THUMB_EDIT_ALT'],
    'EDIT_THUMB_SOURCE' => THEME_URL . '/images/resize_16.png',
    'DELETE_IMG_SOURCE' => THEME_URL . '/images/delete_16.png',
    'ADD_MORE_PICS_TITLE' => $MOD_FOLDERGALLERY['ADD_MORE_PICS'],
    'IMG_TITLE_TEXT' => $MOD_FOLDERGALLERY['IMG_TITLE_TEXT']
));

$t->set_var($aLang);
// Links parsen
$t->set_var(array(
    'SAVE_CAT_LINK' => $sAddonUrl.'/admin/save_cat.php?page_id=' . $page_id . '&section_id=' . $section_id . '&cat_id=' . $cat_id,
    'SAVE_FILES_LINK' => $sAddonUrl.'/admin/save_files.php?page_id=' . $page_id . '&section_id=' . $section_id . '&cat_id=' . $cat_id,
    'CANCEL_ONCLICK' => 'window.location = \'' . ADMIN_URL . '/pages/modify.php?page_id=' . $page_id . '\';'
));

// parse Images
$row=0;
foreach ($bilder as $bild) {
    $t->set_var(array(
        'ROW' => $row,
        'ID_VALUE' => $bild['id'],
        'IMAGE_VALUE' => $bild['thumb_link'] . '?t=' . time(),
        'IMAGE_NAME_VALUE' => $bild['file_name'],
        'IMG_TITLE' => $bild['img_title'],
        'CAPTION_VALUE' => $bild['caption'],
        'THUMB_EDIT_LINK' => $sAddonUrl."/admin/modify_thumb.php?page_id=" . $page_id . "&section_id=" . $section_id . "&cat_id=" . $cat_id . "&id=" . $bild['id'],
        'IMAGE_DELETE_LINK' => "javascript: confirm_link(\"" .$MOD_FOLDERGALLERY['DELETE_IMG_ARE_YOU_SURE'] . "\", \"" . $sAddonUrl."/admin/scripts/delete_img.php?page_id=" . $page_id . "&section_id=" . $section_id . "&cat_id=" . $cat_id . "&id=" . $bild['id'] . "\");",
        'ONCLICK_DELETE_CONFIRM' => $MOD_FOLDERGALLERY['DELETE_IMG_ARE_YOU_SURE'],
        'ONCLICK_DELETE_URL' => $sAddonUrl."/admin/scripts/delete_img.php?page_id=".$page_id."&section_id=".$section_id."&cat_id=".$cat_id."&id=".$bild['id'],
//        'ONCLICK_DELETE_LINK' => "confirm_link(\"".$MOD_FOLDERGALLERY['DELETE_IMG_ARE_YOU_SURE']."\", \"".$sAddonUrl."/admin/scripts/delete_img.php?page_id=".$page_id."&section_id=".$section_id."&cat_id=".$cat_id."&id=".$bild['id']."\");",
        'COUNTER' => $bild['id'],
        'EDIT_THUMB_SOURCE' => THEME_URL . '/images/resize_16.png',
        'DELETE_IMG_SOURCE' => THEME_URL . '/images/delete_16.png'
    ));
    $row++;

    $t->parse('FILE_LOOP', 'file_loop', true);
}

$t->pparse('output', 'modify_cat');

$admin->print_footer();

