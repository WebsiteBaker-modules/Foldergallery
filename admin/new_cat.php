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


// Get all Folders in this gallery
    $settings = getSettings($section_id);
    $invisibleFileNames = array_merge(explode(',', $settings['invisible']), $wbCoreFolders);
    $aOptionList = getFolderData($path.$settings['root_dir'], array(), $invisibleFileNames, 2);

// Template
    $t = new Template($sAddonThemePath, 'remove');
    $t->halt_on_error = 'no';
    $t->set_file('new_cat', 'new_cat.htt');
    // clear the comment-block, if present
    $t->set_block('new_cat', 'CommentDoc'); $t->clear_var('CommentDoc');
    $t->set_block('new_cat', 'select_option_block', 'select_option');

    $t->set_var($aTplDefaults);

// set language strings
    $t->set_var(array(
        'NEW_CAT_TITLE'         => $MOD_FOLDERGALLERY['NEW_CAT'],
        'CAT_PARENT_STRING'     => $MOD_FOLDERGALLERY['CAT_PARENT'],
        'FOLDER_NAME_STRING'    => $MOD_FOLDERGALLERY['FOLDER_NAME'],
        'CAT_TITLE_STRING'      => $MOD_FOLDERGALLERY['CAT_TITLE'],
        'CAT_DESC_STRING'       => $MOD_FOLDERGALLERY['CAT_DESC'],
        'SAVE_STRING'           => $TEXT['SAVE'],
        'CANCEL_STRING'         => $TEXT['CANCEL']
    ));
/*
// parent folder Select
    $t->set_var('ORDNER', '/');
    $t->parse('ORDNER_SELECT', 'ordner_select', true);
    foreach($folders as $folder) {
        $t->set_var('ORDNER', $folder);
        $t->parse('ORDNER_SELECT', 'ordner_select', true);
    }
    if( preg_match( '/'.'pages\/(modify)\.php$/is', $sCallingScript)) {

*/

// filter all sub folder in root dir
    foreach($aOptionList[trim($oReg->MediaDir, '/')] as $sKey=>$sValue) {
        $pattern = '/'.preg_quote($settings['root_dir'], '/').'/is';
        if (preg_match($pattern, ($sKey))){
            $aFolders[$sKey] = $sValue;
        }
    }

    foreach($aFolders as $sKey=>$sValue) {
        $t->set_var('FOLDER', $sKey);
        $t->set_var('FOLDER_NAME', ($sValue['name']));
        $t->set_var('NIVEAU', ($sValue['level']));
        if($sKey != $settings['root_dir']) {
            $t->set_var('DIR_SELECTED','');
        } else {
            $t->set_var('DIR_SELECTED','selected="selected"');
        }
        $t->parse('select_option', 'select_option_block', true);
    }

// set the links and other actions
    $t->set_var(array(
        'ROOT'              => $settings['root_dir'],
        'SECTION_ID_VALUE'  => $section_id,
        'PAGE_ID_VALUE'     => $page_id,
        'NEW_CAT_LINK'      => $sAddonUrl.'/admin/save_new_cat.php?page_id='.$page_id.'&section_id='.$section_id,
        'CANCEL_ONCLICK'    => 'javascript: window.location = \''.ADMIN_URL.'/pages/modify.php?page_id='.$page_id.'\';'
    ));

    $t->pparse('Output', 'new_cat');
    $admin->print_footer();

