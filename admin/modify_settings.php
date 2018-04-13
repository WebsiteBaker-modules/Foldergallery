<?php

    $sAddonPath = dirname(__DIR__);
    if (is_readable($sAddonPath.'/init.php')) {require ($sAddonPath.'/init.php');}
    if (is_readable($sAddonPath.'/presets/thumbPresets.php')){require($sAddonPath.'/presets/thumbPresets.php');}

    // to print with or without header, default is with header
    $admin_header=true;
    // Workout if the developer wants to show the info banner
    $print_info_banner = true; // true/false
    // Tells script to update when this page was last updated
    $update_when_modified = false;
    // Include WB admin wrapper script to sanitize page_id and section_id, print SectionInfoLine
    require(WB_PATH.'/modules/admin.php');
    // An associative array that by default contains the contents of $aRequestVars, $aRequestVars and $_COOKIE.
//    $aRequestVars = $_REQUEST;

    // --- jQueryAdmin / LibraryAdmin Integration; last edited 27.01.2011 ---
    $jqa_lightboxes = [];
    if ( file_exists( WB_PATH.'/modules/libraryadmin/foldergallery_include.php' ) ) {
        include_once WB_PATH.'/modules/libraryadmin/foldergallery_include.php';
        $jqa_lightboxes = get_lightboxes();
    }
    elseif ( file_exists( WB_PATH.'/modules/jqueryadmin/foldergallery_include.php' ) ) {
        include_once WB_PATH.'/modules/jqueryadmin/foldergallery_include.php';
        $jqa_lightboxes = get_lightboxes();
    }
    // --- end jQueryAdmin / LibraryAdmin Integration ---

    // Einstellungen zur aktuellen Foldergallery aus der DB   $aDefaults
    $settings = getFGSettings($section_id);
    // Template
    $tpl = new Template($sAddonThemePath);
    $tpl->halt_on_error = 'no';
    $tpl->set_file('modify_settings', 'modify_settings.htt');
    // clear the comment-block, if present
    $tpl->set_block('modify_settings', 'CommentDoc'); $tpl->clear_var('CommentDoc');

    $tpl->set_block('modify_settings', 'select_pagination_block', 'select_pagination');
    $aPaginationStyles = glob(DirectoryHandler::DecodePath($sAddonTemplatePath.'/css/digg/*Style.css'), GLOB_MARK|GLOB_NOSORT);
    $iSortFlags = ((version_compare(PHP_VERSION, '5.4.0', '<'))?SORT_REGULAR:SORT_NATURAL|SORT_FLAG_CASE);
    sort($aPaginationStyles, $iSortFlags);
    $settings['pagination'] = (isset($settings['pagination']) ? $settings['pagination']: '');
    foreach ($aPaginationStyles as $sStyleFilename){
        $sStyleName = basename($sStyleFilename, '.css');
        $tpl->set_var('STYLE_NAME', $sStyleName);
        if($sStyleName == $settings['pagination']) {
            $tpl->set_var('PAGI_SELECTED','selected="selected"');
        } else {
            $tpl->set_var('PAGI_SELECTED','');
        }
        $tpl->parse('select_pagination', 'select_pagination_block', true);
    }

    $tpl->set_block('modify_settings', 'select_alignment_block', 'select_alignment');
    $aAlignment = array (
    'left' => $MOD_FOLDERGALLERY['LEFT'],
    'right' => $MOD_FOLDERGALLERY['RIGHT'],
    'center' => $MOD_FOLDERGALLERY['CENTER']
    );
    foreach ($aAlignment as $key=>$sName){
        $tpl->set_var('ALIGN_NAME', $sName);
        $tpl->set_var('ALIGN_VALUE', $key);
        if($key == $settings['alignment']) {
            $tpl->set_var('ALIGN_SELECTED','selected="selected"');
        } else {
            $tpl->set_var('ALIGN_SELECTED','');
        }
        $tpl->parse('select_alignment', 'select_alignment_block', true);
    }

    $tpl->set_var('TEXT_ALIGNMENT', 'center'); // select_alignment_block

// TODO urgently put select in the template, bad to output with echo, fetch view templates with glob
    // find lightbox files in template folder
    $aViewFiles = [];
    $lightbox_select = '<select name="lightbox" id="lightbox" style="width: 50%;">';
    if ( $dh = opendir($sAddonTemplatePath) ) {
        while ( ($file = readdir($dh)) !== false ) {
            $aViewFiles[] = $file;
        }
        closedir($dh);
        $iSortFlags = ((version_compare(PHP_VERSION, '5.4.0', '<')) ? SORT_REGULAR : SORT_NATURAL|SORT_FLAG_CASE);
        sort($aViewFiles, $iSortFlags);

        $tpl->set_block('modify_settings', 'select_pagination_block', 'select_pagination');
        foreach ($aViewFiles as $sKey=>$sFile){
            if ( preg_match( "/^view_(\w+).htt$/", $sFile, $matches ) ) {
                $lightbox_select .= '<option value="'
                                 .  $matches[1] .'"';
                if ( $matches[1] == $settings['lightbox'] ) {
                    $lightbox_select .= ' selected="selected"';
                }
                $lightbox_select .= '>'
                                 .  $matches[1]
                                 .  '</option>';
            }
        }
    }
    // ----- jQueryAdmin / LibraryAdmin Integration; last edited 27.01.2011 -----
    if ( count( $jqa_lightboxes ) > 0 ) {
        $iSortFlags = ((version_compare(PHP_VERSION, '5.4.0', '<'))?SORT_REGULAR:SORT_NATURAL|SORT_FLAG_CASE);
        sort($jqa_lightboxes, $iSortFlags);
        foreach ( $jqa_lightboxes as $i => $lb ) {
            if ( is_array( $lb ) ) {
                foreach( $lb as $item ) {
                    $lightbox_select .= '<option value="'.$i.'/plugins/'.$item.'"';
                    if ( $i.'/plugins/'.$item == $settings['lightbox'] ) {
                        $lightbox_select .= ' selected="selected"';
                    }
                    $lightbox_select .= '> ' . $i . ': '
                                     .  $item
                                     .  '</option>';
                }
            } else {
                $lightbox_select .= '<option value="'.$lb.'"';
                if ( $lb == $settings['lightbox'] ) {
                    $lightbox_select .= ' selected="selected"';
                }
                $lightbox_select .= '> jQueryAdmin: '
                                 .  $lb
                                 .  '</option>';
            }
        }
    }
    // ----- end jQueryAdmin / LibraryAdmin Integration -----
    $lightbox_select .= '</select>';

    $tpl->set_var('NAME_CHECKED', ( intval($settings['imageName'])>0 ? $checked : ''));
    $tpl->set_var('TEXT_IMAGENAME', $MOD_FOLDERGALLERY['LIGHTBOX'].' '.$MOD_FOLDERGALLERY['IMAGE_NAME']);
    $tpl->set_var('LIGHTBOX_VALUE', $lightbox_select);

    $invisibleFileNames = [];
    if (!empty( $settings['invisible'] ) ) {
        $invisibleFileNames = array_merge($convertToCategory($settings['invisible']));
    }

    // WB Systemordner sollen nicht angezeigt werden
    $invisibleFileNames = array_merge($invisibleFileNames, $wbCoreFolders);
    // Ordnerauswahl für den Root Folder erstellen
    $aOptionList = getFolderData($path, [], $invisibleFileNames, 2);

    $tpl->set_var($aTplDefaults);

//    $tpl->set_var($MOD_FOLDERGALLERY);
    $tpl->set_var($aLangFG);

    // Rootfolder Select
    $tpl->set_block('modify_settings', 'select_option_block', 'select_option');
//          $tpl->set_var('GROUP_NAME', $MediaAddonRel);

          foreach($aOptionList[trim($oReg->MediaDir, '/')] as $sKey=>$aValue) {
              $tpl->set_var('FOLDER', $sKey);
              $tpl->set_var('FOLDER_NAME', ($aValue['select']));
              $tpl->set_var('NIVEAU', ($aValue['level']));
              if($sKey != $settings['root_dir']) {
                  $tpl->set_var('DIR_SELECTED','');
              } else {
                  $tpl->set_var('DIR_SELECTED','selected="selected"');
              }
              $tpl->parse('select_option', 'select_option_block', true);
          }

    $advanced_settings = FG_getAdvancedThumbSettings($settings['tbSettings']);

    // Preset Select
    $aThumbRatio = array (
                  '1:1Croped'   => 1,
                  '3:4Cropped'  => 1.34,
                  '4:3Cropped'  => 0.75,
                  '16:9Cropped' => 1.78,
                  '9:16Cropped' => 0.56,
                  '1:1noCrop'   => 'free',
                  );
    $aRatioChecked = array(
          'RATIO1_CHECKED' => '',
          'RATIO1.34_CHECKED' => '',
          'RATIO0.75_CHECKED' => '',
          'RATIO1.79_CHECKED' => '',
          'RATIO0.56_CHECKED' => '',
          'RATIOfree_CHECKED' => '',
    );
    $tpl->set_var($aRatioChecked);

    $tpl->set_block('modify_settings', 'preset_select', 'PRESET_SELECT');
    foreach($thumbPresets as $presetName => $preset) {
        $tpl->set_var(array(
            'PRESET_NAME'           => $presetName,
            'PRESET_DESCRIPTION'    => $preset['description']
        ));
              if($presetName != $settings['loadPreset']) {
                  $tpl->set_var('PRESET_SELECTED','');
              } else {
                  $tpl->set_var('PRESET_SELECTED','selected="selected"');
                  $tpl->set_var('RATIO'.$aThumbRatio[$presetName].'_CHECKED','checked="checked"');
              }
        $tpl->parse('PRESET_SELECT', 'preset_select', true);
    }

    // Cat Overview Pic Select
    $tpl->set_block('modify_settings', 'catpic_select', 'CATPIC_SELECT');
    for($i = 0; $i <= 2; $i++) {
        $tpl->set_var('CATPIC_VALUE', $i);
        if ($i == $settings['catpic']) {
            $tpl->set_var('CATPIC_SELECTED', 'selected="selected"');
        } else {
            $tpl->set_var('CATPIC_SELECTED', '');
        }
        $tpl->set_var('CATPIC_NAME',$MOD_FOLDERGALLERY['CATPIC_STRINGS'][$i]);
        $tpl->parse('CATPIC_SELECT', 'catpic_select', true);
    }
/*
print '<pre  class="mod-pre rounded">function <span>'.__FUNCTION__.'( '.''.' );</span>  filename: <span>'.basename(__FILE__).'</span>  line: '.__LINE__.' -> <br />';
print_r( $settings ); print '</pre>'; flush (); //  ob_flush();;sleep(10); die();
*/
    if($settings['tbSettings']['image_ratio_fill']) {
        $tpl->set_var('CROP_SELECT_KEEP', 'checked="checked"');
        $tpl->set_var('CROP_SELECT_CUT', '');
    } else {
        $tpl->set_var('CROP_SELECT_KEEP', '');
        $tpl->set_var('CROP_SELECT_CUT', 'checked="checked"');
    }

    $defaultQuality = (isset($settings['defaultQuality']) ? $settings['defaultQuality'] :'50');
    $maxImageSize   = (isset($settings['maxImageSize']) ? $settings['maxImageSize'] : '1024');

    $aDataSettings = array(
            'SECTION_ID_VALUE'              => $section_id,
            'PAGE_ID_VALUE'                 => $page_id,
            'EXTENSIONS_VALUE'              => $settings['extensions'],
            'INVISIBLE_VALUE'               => $settings['invisible'],
            'PICS_PP_VALUE'                 => $settings['pics_pp'],
            'GAL_PP_VALUE'                  => $settings['gal_pp'],
            'defaultOpacity'                => $settings['opacity'],
            'BACKGROUND_COLOR'              => $settings['tbSettings']['image_background_color'],
            'THUMB_WIDTH'                   => $settings['thumb_width'],
            'THUMB_HEIGHT'                  => $settings['thumb_height'],
            'THUMBSIZE_X'                   => $settings['tbSettings']['image_x'],
            'THUMBSIZE_Y'                   => $settings['tbSettings']['image_y'],
            'ADVANCED_SETTINGS'             => $advanced_settings,
            'THUMBSIZE'                     => $settings['tbSettings']['image_x'],
//            'LIGHTBOX_VALUE'                => $lightbox_select,
            'THEME_URL'                     => THEME_URL,
            'defaultQuality'                => $defaultQuality,
            'maxImageSize'                  => $maxImageSize,
    );
    $tpl->set_var($aDataSettings);

    // Text einsetzten
    $aModLang = array(
            'SETTINGS_STRING'               => $MOD_FOLDERGALLERY['SETTINGS_STRING'],
            'ROOT_FOLDER_STRING'            => $MOD_FOLDERGALLERY['ROOT_DIR'],
            'EXTENSIONS_STRING'             => $MOD_FOLDERGALLERY['EXTENSIONS'],
            'INVISIBLE_STRING'              => $MOD_FOLDERGALLERY['INVISIBLE'],
            'SAVE_STRING'                   => $TEXT['SAVE'],
            'CANCEL_STRING'                 => $TEXT['CANCEL'],
            'GAL_PP_STRING'                 => $MOD_FOLDERGALLERY['GAL_PP'],
            'PICS_PP_STRING'                => $MOD_FOLDERGALLERY['PICS_PP'],
            'CAT_OVERVIEW_PIC_STRING'       => $MOD_FOLDERGALLERY['CAT_OVERVIEW_PIC'],
            'THUMBNAIL_SETTINGS_STRING'     => $MOD_FOLDERGALLERY['THUMBNAIL_SETTINGS'],
            'MOD_FOLDERGALLERY_LOAD_PRESET' => $MOD_FOLDERGALLERY['LOAD_PRESET'],
            'MOD_FOLDERGALLERY_LOAD_PRESET_INFO' => $MOD_FOLDERGALLERY['LOAD_PRESET_INFO'],
            'MOD_FOLDERGALLERY_LOAD_ROOT_INFO' => $MOD_FOLDERGALLERY['LOAD_ROOT_INFO'],
            'IMAGE_CROP_STRING'             => $MOD_FOLDERGALLERY['IMAGE_CROP'],
            'IMAGE_DONT_CROP_STRING'        => $MOD_FOLDERGALLERY['IMAGE_DONT_CROP'],
            'RATIO_STRING'                  => $MOD_FOLDERGALLERY['RATIO'],
            'CALCULATE_RATIO_STRING'        => $MOD_FOLDERGALLERY['CALCULATE_RATIO'],
            'BACKGROUND_COLOR_STRING'       => $MOD_FOLDERGALLERY['BACKGROUND_COLOR'],
            'MAX_WIDTH_STRING'              => $MOD_FOLDERGALLERY['MAX_WIDTH'],
            'MAX_HEIGHT_STRING'             => $MOD_FOLDERGALLERY['MAX_HEIGHT'],
            'ADVANCED_SETTINGS_STRING'      => $MOD_FOLDERGALLERY['ADVANCED_SETTINGS'],
            'IMAGE_DO_CROP_STRING'          => $MOD_FOLDERGALLERY['IMAGE_DO_CROP'],
            'THUMB_SIZE_STRING'             => $MOD_FOLDERGALLERY['THUMB_SIZE'],
            'THUMB_RATIO_STRING'            => $MOD_FOLDERGALLERY['THUMB_RATIO'],
            'THUMB_NOT_NEW_STRING'          => $MOD_FOLDERGALLERY['THUMB_NOT_NEW'],
            'CHANGING_INFO_STRING'          => $MOD_FOLDERGALLERY['CHANGING_INFO'],
            'LIGHTBOX_STRING'               => $MOD_FOLDERGALLERY['LIGHTBOX'],
            'PAGINATION'                    => $MOD_FOLDERGALLERY['PAGINATION'],
    );
    $tpl->set_var($aLangFG);
    $tpl->set_var($aModLang);

    // Links einsetzen
    $tpl->set_var(array(
            'CANCEL_ONCLICK'              => 'window.location = \''.ADMIN_URL.'/pages/modify.php?page_id='.$page_id.'\';',
            'MODIFY_SETTINGS_LINK'        => $sAddonUrl.'/admin/save_settings.php'
    ));

    //Tooltips einsetzen
    $aTooltips = array(
            'ROOT_FOLDER_STRING_TT'       => $MOD_FOLDERGALLERY['ROOT_FOLDER_STRING_TT'],
            'EXTENSIONS_STRING_TT'        => $MOD_FOLDERGALLERY['EXTENSIONS_STRING_TT'],
            'INVISIBLE_STRING_TT'         => $MOD_FOLDERGALLERY['INVISIBLE_STRING_TT'],
    );
    $tpl->set_var($aTooltips);

    $tpl->pparse('output', 'modify_settings');
    $admin->print_footer();
