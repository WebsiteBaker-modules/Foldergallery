<?php

    $sAddonPath = dirname(__DIR__);

    if (is_readable($sAddonPath.'/init.php'))     {require ($sAddonPath.'/init.php');}

    // to print with or without header, default is with header
    $admin_header=true;
    // Workout if the developer wants to show the info banner
    $print_info_banner = true; // true/false
    // Tells script to update when this page was last updated
    $update_when_modified = false;
    // Include WB admin wrapper script to sanitize page_id and section_id, print SectionInfoLine
    require(WB_PATH.'/modules/admin.php');

$settings = getSettings($section_id);

if (isset($aRequestVars['cat_id']) && is_numeric($aRequestVars['cat_id'])) {
    $cat_id = intval($aRequestVars['cat_id']);
} else {
    $error['no_cat_id'] = 1;
}

// Kategorie Infos aus der DB holen
$sql = 'SELECT * FROM ' . TABLE_PREFIX . 'mod_foldergallery_categories WHERE id=' . $cat_id . ' LIMIT 1;';
$query = $database->query($sql);
$categorie = $query->fetchRow(MYSQLI_ASSOC);

if (is_array($categorie)) {
    if ($categorie['parent'] != -1) {
        $cat_path = $path . $settings['root_dir'] . $categorie['parent'] . '/' . $categorie['categorie'];
        $parent = $categorie['parent'] . '/' . $categorie['categorie'];
    } else {
        // Root
        $cat_path = $path . $settings['root_dir'];
        $parent = '';
    }
}
$parent_id = $categorie['id'];
if ($categorie['active'] == 1) {
    $cat_active_checked = 'checked="checked"';
} else {
    $cat_active_checked = '';
}

$folder = $settings['root_dir'] . $parent;
$pathToFolder = $path . $folder . '/';
$pathToThumb = $path . $folder . $thumbPath . '/';
$urlToFolder = $url.MEDIA_DIRECTORY.$folder . '/';
$urlToThumb  = $url.MEDIA_DIRECTORY.$folder . $thumbPath . '/';


$bilder = array();
$sql = 'SELECT * FROM ' . TABLE_PREFIX . 'mod_foldergallery_files WHERE parent_id="' . $parent_id . '" ORDER BY position ASC;';
$query = $database->query($sql);


$t = new Template($sAddonThemePath, 'remove');
$t->set_file('modify_cat_sort', 'modify_cat_sort.htt');
// clear the comment-block, if present
$t->set_block('modify_cat_sort', 'CommentDoc');
$t->clear_var('CommentDoc');
// Get the Blocks
$t->set_block('modify_cat_sort', 'image_loop', 'IMAGE_LOOP');
$t->set_var($aTplDefaults);

// Replace Language Strings
$t->set_var(array(
    'REORDER_IMAGES_STRING' => $MOD_FOLDERGALLERY['REORDER_IMAGES'],
    'CANCEL_STRING' => $TEXT['CANCEL'].' & '.$TEXT['BACK'],
    'QUICK_SORT_STRING' => $MOD_FOLDERGALLERY['SORT_BY_NAME'],
    'QUICK_ASC_STRING' => $MOD_FOLDERGALLERY['SORT_BY_NAME_ASC'],
    'QUICK_DESC_STRING' => $MOD_FOLDERGALLERY['SORT_BY_NAME_DESC'],
    'MANUAL_SORT' => $MOD_FOLDERGALLERY['SORT_FREEHAND'],
    'FEEDBACK_MAN_SORT' => $MOD_FOLDERGALLERY['REORDER_INFO_STRING']
));

// Links Parsen
$t->set_var(array(
    'CANCEL_ONCLICK' => 'javascript: window.location = \''.$sAddonUrl.'/admin/modify_cat.php?page_id='.$page_id.'&section_id='.$section_id.'&cat_id='.$cat_id.'\';',
    'QUICK_ASC_ONCLICK' => 'javascript: window.location = \''.$sAddonUrl.'/admin/scripts/quick_img_sort.php?page_id='.$page_id.'&section_id='.$section_id . '&cat_id='.$cat_id.'&sort=ASC\';',
    'QUICK_DESC_ONCLICK' => 'javascript: window.location = \''.$sAddonUrl.'/admin/scripts/quick_img_sort.php?page_id='.$page_id.'&section_id='.$section_id . '&cat_id='.$cat_id.'&sort=DESC\';'
));

// JS Werte Parsen
$t->set_var(array(
    'PARENT_ID_VALUE' => $parent_id,
    'WB_URL_VALUE' => WB_URL
));

// Bilder parsen
if ($query->numRows()) {
    while ($result = $query->fetchRow(MYSQLI_ASSOC)) {
        $bildfilename = $result['file_name'];
        $thumb = $pathToThumb . $bildfilename;
        $t->set_var(array(
            'RESULT_ID_VALUE' => $result['id'],
            'THUMB_SIZE_X' => $settings['tbSettings']['image_x'],
            'THUMB_SIZE_Y' => $settings['tbSettings']['image_y'],
            'THUMB_URL' => $urlToThumb . $bildfilename,
            'TITLE_VALUE' => $result['position'] . ': ' . $bildfilename
        ));
        $t->parse('IMAGE_LOOP', 'image_loop', true);
    }
}

$t->pparse('output', 'modify_cat_sort');

$admin->print_footer();

