<?php

// prevent this file from being accessed directly
/* -------------------------------------------------------- */
// Must include code to prevent this file from being accessed directly
if(defined('WB_PATH') == false) { die('Cannot access '.basename(__DIR__).'/'.basename(__FILE__).' directly'); }
/* -------------------------------------------------------- */
// Must include code to prevent this file from being accessed directly
if(defined('DEBUG')&& DEBUG) { die('Cannot execute '.basename(__DIR__).'/'.basename(__FILE__).' please disable DEBUG mode'); }
/* -------------------------------------------------------- */

/*******************************************************************************
 *
 * Update from Version 1.17 and higher to newest Versions
 *
 * new Settings Table
 * new Thumbnail-Settings
 * categorie text field now as text
 * delete unused files (many of them are moved to the admin folder)
 *
 ******************************************************************************/

/*
 * BEGIN: Create new settings table
 */
    // next 2 vars not needed if workout with simpledisatcher
    $sAddonPath = __DIR__;
    if (is_readable($sAddonPath.'/init.php')) {require ($sAddonPath.'/init.php');}
    if (is_writable(WB_PATH.'/temp/cache')) {
        Translate::getInstance()->clearCache();
    }

    $OK   = '<span class="ok">OK</span>';
    $FAIL = '<span class="error">FAILED</span>';
    $iErr = false;
    $sNameValPairs = '';
    $bOldStructure = false;
    $callingScript = $_SERVER["SCRIPT_NAME"];
/*
    $tmp = 'upgrade-script.php';
    $globalStarted = substr_compare($callingScript, $tmp,(0-strlen($tmp)),strlen($tmp)) === 0;
*/
    // check if upgrade startet by upgrade-script to echo a message
    $globalStarted = preg_match('/upgrade\-script\.php$/', $callingScript);

    $aSettings= array();
    if (is_readable($sAddonPath.'/presets/thumbPresets.php')){require($sAddonPath.'/presets/thumbPresets.php');}
    $aOldSettings = array();
    $dbName = DB_NAME;
    $sTableName = TABLE_PREFIX.'mod_foldergallery_settings';
    $i = (!isset($i) ? 1 : $i);
    $aMsg = array();
    $sInstallStruct   = $sAddonPath.'/install-struct.sql';
    $sInstallUpdate   = $sAddonPath.'/install-upgrade.sql';
    if ($oTrans) {$oTrans->clearCache ();} else {Translate::getInstance ()->clearCache ();}

    if ( !is_readable($sInstallUpdate)) {
        $aMsg[] = '<strong>\'missing or not readable file [install-settings.sql]\'</strong> '.$FAIL.'<br />';
        $iErr = true;
    } else {
        // get settings from table and return an array otherwise false
        $getSettings = (function() use ($database, $sTableName)
        {
            $aSettings =  array();
            $sql = 'SELECT * FROM `'.$sTableName.'`';
            if ($oRes = $database->query($sql)){
                $aSettings = $oRes->fetchAll(MYSQLI_ASSOC);
            }
            return ((sizeof($aSettings)>0)?$aSettings:false);
        });

        // try to create table if not exists
        $database->SqlImport($sInstallStruct, TABLE_PREFIX, true );
        // read settings first
        $sql = 'SELECT * FROM `'.$sTableName.'`';
        // check if table already upgraded
        if ($bOldStructure = $database->field_exists($sTableName, 'page_id'))
        {
            if (!($aOldSettings = $getSettings()))
            {
                $aMsg[] = '<strong>\'Folder Gallery backup old settings\'</strong> '.$FAIL.'<br />';
                $iErr = true;
            }
            $i=0;
            for ($i=0; $i<sizeof($aOldSettings); $i++){
                $aOldSettings[$i]['root_dir'] = str_replace($MediaRel, '', $aOldSettings[$i]['root_dir']);
            }
        } elseif ($database->field_exists($sTableName, 's_name'))
        {
            // overwrite standardsettings ($aOldSettings)
            if (!($aOldSettings = $getSettings()))
            {
                $aMsg[] = '<strong>\'FolderGallery backup old settings\'</strong> '.$FAIL.'<br />';
                $aMsg[] = '<strong>\'FolderGallery has no old settings\'</strong> '.$FAIL.'<br />';
                $iErr = true;
            } else {
                $i = -1;
                $iSectionId = 0;
                // prepare an array to create the sql statement
                $i=0;
                $iSectionId = $aOldSettings[$i]['section_id'];
                $aTmpSettings = array();
                $aTmpSettings[$i]['section_id'] = $iSectionId;
                foreach ($aOldSettings AS $aSettings) {
                    $sIndexName = $aSettings['s_name'];
                    $sValue     = $aSettings['s_value'];
                    $sValue     = ($sIndexName=='root_dir'?str_replace($MediaRel, '', $sValue):$sValue);
                    $aTmpSettings[$i][$sIndexName] = $sValue;
                    if ($iSectionId != $aSettings['section_id']){
                        $i++;
                        $iSectionId = $aSettings['section_id'];
                        $aTmpSettings[$i]['section_id'] = $iSectionId;
                    }
                }
                $aOldSettings = $aTmpSettings;
            }
        } // end handling new structure

       // delete none allowed entries in settings.s_name
        $aSettingsDenied = array( 'section_id' );
        // drop old table and create new one
        if ($database->SqlImport($sInstallUpdate, TABLE_PREFIX, false))
        {
            // restore old settings if there any
            if ($aOldSettings && !$iErr)
            {
                $iSectionId = 0;
                foreach ($aOldSettings AS $aSettings) {
                    // add new default settings
                    $aNewSettings = array_intersect_key( $aSettings, $aDefaults );
                    $aSettings = array_replace_recursive( $aDefaults, $aSettings );
                    $iSectionId = $aSettings['section_id'];
                    // update sections modulename if changed with same table structure
                    $sqlSection = 'UPDATE `'.TABLE_PREFIX.'sections` SET '
                                . '`module` = \''.$sAddonName.'\' '
                                . 'WHERE `'.TABLE_PREFIX.'sections`.`section_id` = '.$iSectionId.';';
                    if (!$database->query($sqlSection)){
                        $aMsg[] = $database->get_error().'<br />';
                        $aMsg[] = '<strong>Folder Gallery couldn\'t change sections entry ID ('.$iSectionId.')</strong> '.$FAIL.'<br />';
                    }
                    foreach ($aSettings AS $index => $val) {
                        if (in_array($index, $aSettingsDenied) ) {
                            continue;
                        }
                        $sNameValPairs .= ','."\n".' ('.$iSectionId.', \''.$index.'\', \''.$database->escapeString($val).'\')';
                    }
                }
                $sValues = ltrim($sNameValPairs, ', ');
                $sql = 'INSERT INTO `'.$sTableName.'` (`section_id`, `s_name`, `s_value`) '
                     . 'VALUES '.$sValues.';';
                if (!$database->query($sql)) {
                    $aMsg[] = $database->get_error().'<br />';
                    $aMsg[] = '<strong>\'Folder Gallery INSERT INTO (restore) old settings\'</strong> '.$FAIL.'<br />';
                    $iErr = true;
                }
/*
*/
            }
        } else {
            $aMsg[] = $database->get_error().'<br />';
            $aMsg[] = '<strong>\'Folder Gallery recreate table\'</strong> '.$FAIL.'<br />';
            $iErr = true;
        }
        if (!$iErr) {
            $aMsg[] = '<strong>\'Folder Gallery successful updated\'</strong> '.$OK.'<br />';
        } else {
            $aMsg[] = '<strong>\'Folder Gallery couldn\'t updated\'</strong> '.$FAIL.'<br />';
        }
        unset($getSettings);
    }
/*--------------------------------------------------------------------------------------------------*/
/**
 * There are files which are moved or no longer needed.
 * So we need to delete the old files and directories
 */
/*--------------------------------------------------------------------------------------------------*/
    $aDirDelete = array(
             );

    $aFilesDelete = array(
                '[MODULES]/modify_cat.php',
                '[MODULES]/modify_cat_sort.php',
                '[MODULES]/modify_settings.php',
                '[MODULES]/modify_thumb.php',
                '[MODULES]/save_cat.ph',
                '[MODULES]/save_files.php',
                '[MODULES]/save_settings.php',
                '[MODULES]/sync.php',
                '[MODULES]/frontend.css.orig',

                '[MODULES]/scripts/backend.functions.ph',
                '[MODULES]/scripts/delete_cat.php',
                '[MODULES]/scripts/delete_img.php',
                '[MODULES]/scripts/move_down.php',
                '[MODULES]/scripts/move_up.php',
                '[MODULES]/scripts/quick_img_sort.php',
                '[MODULES]/scripts/reorderCNC.php',
                '[MODULES]/scripts/reorderDND.php',

                '[MODULES]/templates/Unbenannt1.html',
                '[MODULES]/templates/modify_cat.htt',
                '[MODULES]/templates/modify_cat_sort.htt',
                '[MODULES]/templates/modify_settings.htt',
                '[MODULES]/templates/modify.htt',

                '[MODULES]/templates/view_ad_gallery.htt',
                '[MODULES]/templates/view_galleryview30.htt',
                '[MODULES]/templates/view_pirobox.htt',
                '[MODULES]/templates/view_prettyphoto.htt',

                '[MODULES]/images/eck.gif',
                '[MODULES]/images/crumbs.gif',
              );

    $aSearches = array(
        '[MODULES]',
    );
    $aReplacements = array(
        '/modules/'.$sAddonName,
    );

    $aMsg = array();
    array_walk(
        $aFilesDelete,
        function (&$sFile) use($aSearches, $aReplacements) {
            $sFile = str_replace( '\\', '/', WB_PATH.str_replace($aSearches, $aReplacements, $sFile) );
        }
    );

   foreach ( $aFilesDelete as $sFileToDelete ) {
        if (false !== ($aExistingFiles = glob(dirname($sFileToDelete).'/*', GLOB_MARK)) ) {
            if ( in_array($sFileToDelete, $aExistingFiles) ) {
                if ( is_writable($sFileToDelete) && unlink($sFileToDelete) ) {
                    $aMsg[] = '<strong>Delete  '.$sFileToDelete.'</strong>'." $OK<br />";
                } else {
                    $aMsg[] = $sFileToDelete.' deleted '.$FAIL;
                }
            }
        }
    }
    unset($aExistingFiles);
/*--------------------------------------------------------------------------------------------------*/

end:

    if (!$globalStarted && sizeof($aMsg)) {print implode("\n", $aMsg)."\n";}

// end of file
