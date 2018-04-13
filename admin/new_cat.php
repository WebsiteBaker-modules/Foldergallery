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
    $settings = getFGSettings($section_id);
    $sSectionIdPrefix = (defined( 'SEC_ANCHOR' ) && ( SEC_ANCHOR != '' )  ? SEC_ANCHOR : 'Sec' );
    $sBacklink = ADMIN_URL.'/pages/modify.php?page_id='.$page_id.'#'.$sSectionIdPrefix.$section_id;
    $sSynclink = $sAddonUrl.'/admin/sync.php?page_id='.$page_id.'&section_id='.$section_id;
    $invisibleFileNames = array_merge(explode(',', $settings['invisible']), $wbCoreFolders);
    $aOptionList = getFolderData($path.$settings['root_dir'], [], $invisibleFileNames, 2);
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
// filter all sub folder in root dir
    $aFolders = [];
    $iPrevLevel = substr_count($settings['root_dir'],'/');
    foreach($aOptionList[trim($oReg->MediaDir, '/')] as $sKey=>$aValue) {
        $aValue['key'] = $sKey = str_replace($oReg->MediaDir,'',$sKey);
        $iLevel = $aValue['level'] = substr_count($sKey, '/')-$iPrevLevel;
        $pattern = '/'.preg_quote($settings['root_dir'], '/').'/is';
        if (preg_match($pattern, ($sKey))){
            $aFolders[$sKey] = $aValue;
            $aFolders[$sKey]['level']  = $iLevel;
            $aFolders[$sKey]['select'] = ((($aValue['level'])>0) ? str_repeat(' -- ', $aValue['level']).$aValue['name'] : $aValue['name']);
        }
    }

    foreach($aFolders as $sKey=>$sValue) {
        $t->set_var('FOLDER', $sKey);
        $t->set_var('FOLDER_NAME', ($sValue['select']));
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
        'CANCEL_ONCLICK'    => 'window.location = \''.ADMIN_URL.'/pages/modify.php?page_id='.$page_id.'\';'
    ));

    $t->pparse('Output', 'new_cat');
    $admin->print_footer();

