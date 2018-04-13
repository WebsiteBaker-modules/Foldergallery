<?php
/*

 Website Baker Project <http://www.websitebaker.org/>
 Copyright (C) 2004-2008, Ryan Djurovich

 Website Baker is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 Website Baker is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with Website Baker; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/

    $sAddonPath = dirname(__DIR__);
    if (is_readable($sAddonPath.'/init.php')) {require ($sAddonPath.'/init.php');}
    if (is_readable($sAddonPath.'/presets/thumbPresets.php')){require($sAddonPath.'/presets/thumbPresets.php');}
    // to print with or without header, default is with header
    $admin_header=true;
    // Workout if the developer wants to show the info banner
    $print_info_banner = false; // true/false
    // Tells script to update when this page was last updated
    $update_when_modified = false;
    // Include WB admin wrapper script to sanitize page_id and section_id, print SectionInfoLine
    require(WB_PATH.'/modules/admin.php');

    $oldSettings = getFGSettings($section_id);
    $newSettings = [];
    $aErrorMsg = [];
    $aInvisibile = explode(',',$aDefaults['invisible']);

    if (isset($page_id)) {
            $newSettings['page_id'] = $admin->StripCodeFromText($page_id);
    } else {
            $newSettings['page_id'] = $page_id;
    }
    if (isset($aRequestVars['catpic']) && is_numeric($aRequestVars['catpic']) ) {
        $newSettings['catpic'] = intval($aRequestVars['catpic']);
    } else {
        $newSettings['catpic'] = 0;
    }

    //Daten aus $aRequestVars auswerten und validieren
    if (isset($aRequestVars['root_dir'])) {
        $aRequestVars['root_dir'] = str_replace($MediaAddonRel,'',$aRequestVars['root_dir']);
        $newSettings['root_dir'] = filter_var($aRequestVars['root_dir'] ,FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
    } else {
        $newSettings['root_dir'] = '/';
    }
    if (isset($aRequestVars['extensions']) && ($aRequestVars['extensions'] != '')) {
        $extensions = strtolower($aRequestVars['extensions']);
            $extensionsarray = explode(',',str_replace(' ', '', $extensions));
            $extensionsarray = array_unique($extensionsarray);
            $newSettings['extensions'] = implode(',', $extensionsarray);
    } else {
        $newSettings['extensions'] = '';
    }
    if (isset($aRequestVars['invisible'])) {
            $newSettings['invisible'] = $admin->StripCodeFromText($aRequestVars['invisible']);
            $aForbidden['invisible'] = array_diff($aInvisibile, explode(',',$newSettings['invisible']));
            $sForbidden = ','.implode(',',$aForbidden['invisible']);
            $newSettings['invisible'] = trim($newSettings['invisible'].$sForbidden,',');
    } else {
            $newSettings['invisible'] = $aDefaults['invisible'];
    }
    if (isset($aRequestVars['pics_pp']) && is_numeric($aRequestVars['pics_pp']) ) {
            $newSettings['pics_pp'] = intval($aRequestVars['pics_pp']);
    } else {
            $newSettings['pics_pp'] = 10;
    }
    if (isset($aRequestVars['gal_pp']) && is_numeric($aRequestVars['gal_pp']) ) {
            $newSettings['gal_pp'] = intval($aRequestVars['gal_pp']);
    } else {
            $newSettings['gal_pp'] = -1;
    }
    if (isset($aRequestVars['alignment']) ) {
            $newSettings['alignment'] = $admin->StripCodeFromText($aRequestVars['alignment']);
    } else {
            $newSettings['alignment'] = 'left';
    }
    if (isset($aRequestVars['pagination']) && is_readable( $sAddonTemplatePath.'/css/digg/'.$aRequestVars['pagination'].'.css' ) ) {
            $newSettings['pagination'] = $admin->StripCodeFromText($aRequestVars['pagination']);
    } else {
            $newSettings['pagination'] = 'FlickrStyle';
    }

    if (isset($aRequestVars['lightbox']) && is_readable( $sAddonTemplatePath.'/view_'.$aRequestVars['lightbox'].'.htt' ) ) {
            $newSettings['lightbox'] = $aRequestVars['lightbox'];
// ----- jQueryAdmin / LibraryAdmin Integration; last edited 27.01.2011 -----
    } elseif( isset($aRequestVars['lightbox']) && is_readable( WB_PATH.'/modules/'.$aRequestVars['lightbox'].'/foldergallery_template.htt' ) ) {
            $newSettings['lightbox'] = $aRequestVars['lightbox'];
    } elseif( isset($aRequestVars['lightbox']) && is_readable( WB_PATH.'/modules/jqueryadmin/plugins/'.$aRequestVars['lightbox'].'/foldergallery_template.htt' ) ) {
            $newSettings['lightbox'] = $aRequestVars['lightbox'];
// ----- end jQueryAdmin / LibraryAdmin Integration -----
    } else {
            $newSettings['lightbox'] = 'Colorbox';
    }
    if (isset($aRequestVars['loadPreset'])) {
            $newSettings['loadPreset'] = $admin->StripCodeFromText($aRequestVars['loadPreset']);
    } else {
            $newSettings['loadPreset'] = '1:1Croped150';
    }

    if (isset($aRequestVars['thumbPath'])) {
            $newSettings['thumbPath'] = $admin->StripCodeFromText($aRequestVars['thumbPath']);
    } else {
            $newSettings['thumbPath'] = $thumbPath;
    }
    if (isset($aRequestVars['galleryStyle'])) {
            $newSettings['galleryStyle'] = $admin->StripCodeFromText($newSettings['galleryStyle']);
    } else {
            $newSettings['galleryStyle'] = 'default';
    }

    if (isset($aRequestVars['imageName'])) {
            $newSettings['imageName'] = intval($aRequestVars['imageName']);
    } else {
            $newSettings['imageName'] = '0';
    }

    if (isset($aRequestVars['defaultQuality'])) {
        $newSettings['defaultQuality'] = $admin->StripCodeFromText($aRequestVars['defaultQuality']);
    } else {
        $newSettings['defaultQuality'] = '60';
    }
    if (isset($aRequestVars['opacity'])) {
        $newSettings['opacity'] = $admin->StripCodeFromText($aRequestVars['opacity']);
    } else {
        $newSettings['opacity'] = '0.5';
    }

    if (isset($aRequestVars['maxImageSize'])) {
            $newSettings['maxImageSize'] = $admin->StripCodeFromText($aRequestVars['maxImageSize']);
    } else {
            $newSettings['maxImageSize'] = '1024';
    }

    if (isset($aRequestVars['clearLangCache'])&& ($aRequestVars['clearLangCache']='1')) {
        Translate::getInstance ()->clearCache ();
    }

    if (isset($aRequestVars['noNew'])) {
        $bForceThumbnails = true;
    } else {
        $bForceThumbnails = false;
    }

    if (isset($aRequestVars['forceThumbnails'])&& ($aRequestVars['forceThumbnails']='1')) {
        $bForceThumbnails = true;
    } else {
        $bForceThumbnails = false;
    }
    if (isset($aRequestVars['thumb_width']) && is_numeric($aRequestVars['thumb_width']) ) {
            $newSettings['thumb_width'] = (int) trim($aRequestVars['thumb_width']);
    } else {
            $newSettings['thumb_width'] = 150;
    }
    if (isset($aRequestVars['thumb_height']) && is_numeric($aRequestVars['thumb_height']) ) {
            $newSettings['thumb_height'] = (int) trim($aRequestVars['thumb_height']);
    } else {
            $newSettings['thumb_height'] = 150;
    }

    // Get the new Thumbsettings:
    if (isset($aRequestVars['thumb_x']) && is_numeric($aRequestVars['thumb_x']) ) {
            $newSettings['tbSettings']['image_x'] = (int) trim($aRequestVars['thumb_x']);
    } else {
            $newSettings['tbSettings']['image_x'] = 150;
    }
    if (isset($aRequestVars['thumb_y']) && is_numeric($aRequestVars['thumb_y']) ) {
            $newSettings['tbSettings']['image_y'] = (int) trim($aRequestVars['thumb_y']);
    } else {
            $newSettings['tbSettings']['image_y'] = 150;
    }
    if(isset($aRequestVars['thumb_crop']) && is_string($aRequestVars['thumb_crop']) && $aRequestVars['thumb_crop'] == 'keep') {
        $newSettings['tbSettings']['image_ratio_fill'] = true;
        $newSettings['tbSettings']['image_ratio_crop'] = false;
    } else {
        $newSettings['tbSettings']['image_ratio_fill'] = false;
        $newSettings['tbSettings']['image_ratio_crop'] = true;
    }
    if(isset($aRequestVars['background_color']) && is_string($aRequestVars['background_color'])) {
        $newSettings['tbSettings']['image_background_color'] = '#'.trim($aRequestVars['background_color'], '#');
    } else {
        $newSettings['tbSettings']['image_background_color'] = '#FFFFFF';
    }
    // Fetch the advanced settings, they need a little bit more effort...
    if(isset($aRequestVars['thumb_advanced'])
       && is_string($aRequestVars['thumb_advanced'])
       && $aRequestVars['thumb_advanced'] != '')
    {
        $advanced_settings = FG_setAdvancedThumbSettings($aRequestVars['thumb_advanced']);
        $newSettings['tbSettings'] = array_merge($newSettings['tbSettings'], $advanced_settings);
    }
    // This is set by default as we want to resize the images
    $newSettings['tbSettings']['image_resize'] = true;
?><div class="w3-panel w3-leftbar w3-pale-green w3-border-green w3-round">
  <h5 ><?php echo $MOD_FOLDERGALLERY['SAVE_SETTINGS'];?></h5>
</div><!-- SAVE_SETTINGS -->
<?php

    $newSettings['section_id'] = $section_id;
    //SQL wich is used for all updates
    $rawUpdtSQL = "UPDATE `".TABLE_PREFIX."mod_foldergallery_settings` SET `s_value` = '%s' WHERE "
        ."`section_id` = '".$section_id."' AND `s_name` = '%s';";

    // execute SQL
    $sUpdateSql =[];
    foreach ($newSettings as $s_name=>$s_value){

        if (!is_array($s_value)) {
            $sSql = sprintf($rawUpdtSQL, $database->escapeString($s_value), $s_name);
        } else {
            $sSql = sprintf($rawUpdtSQL, serialize($s_value), $s_name);
        }
        $sUpdateSql[] = $sSql;
        if (!$database->query($sSql)){
            $aErrorMsg[] = $database->get_error();
        }
    }
    if (sizeof($aErrorMsg)){
      $admin->print_error(implode('<br />', $aErrorMsg), $sAddonUrl.'/admin/modify_settings.php?page_id='.$page_id.'&section_id='.$section_id);
    }

    if(( serialize($oldSettings['tbSettings']) != serialize($newSettings['tbSettings'])) && !isset($aRequestVars['noNew']) || $bForceThumbnails )
    {
        // Ok, thumb_size hat gewechselt, also alte Thumbs lÃ¶schen
        $sql = 'SELECT `parent`, `categorie` FROM '.TABLE_PREFIX.'mod_foldergallery_categories WHERE section_id='.$oldSettings['section_id'].';';
        $query = $database->query($sql);
        if ($query->numRows()) {
?><div class="w3-section w3-panel w3-leftbar w3-pale-red w3-border-red w3-round w3-border-green">
      <h5><?php echo $MOD_FOLDERGALLERY['DELETE_OLD_THUMBS'];?></h5>
      <ul class=" w3-margin-bottom">
<?php
        $pathToFolder = $oldSettings['root_dir'].$thumbPath;
?><li><?php echo $MOD_FOLDERGALLERY['DELETE'];?>: <?php echo $pathToFolder;?></li>
<?php
        deleteFolder($path.$pathToFolder);
        while($link = $query->fetchRow(MYSQLI_ASSOC))
        {
            $pathToFolder = $oldSettings['root_dir'].$link['parent'].$link['categorie'].$thumbPath;
?><li><?php echo $MOD_FOLDERGALLERY['DELETE'];?>: <?php echo $pathToFolder;?></li>
<?php
            deleteFolder($path.$pathToFolder);
        }
?>
      </ul>
  </div>
<?php
        } else {
?>
<!-- no fg-thumbs -->
<?php
        } // numRows
    } // serialize($oldSettings['tbSettings']
    $sCatTitle = basename($newSettings['root_dir']);
    $sCatTitle = $database->escapeString($sCatTitle==''?basename($path):$sCatTitle);

    if($oldSettings['root_dir'] != $newSettings['root_dir'])
    {
        // Und jetzt noch alte DB EintrÃ¤ge
        $sql = 'SELECT `id`, `categorie` FROM `'.TABLE_PREFIX.'mod_foldergallery_categories` WHERE `section_id` = '.$oldSettings['section_id'].';';
        $query = $database->query($sql);
        while($cat = $query->fetchRow(MYSQLI_ASSOC))
        {
            $sql  = 'DELETE FROM `'.TABLE_PREFIX.'mod_foldergallery_files` '
                  . 'WHERE `parent_id` = '.$cat['id'];
            $database->query($sql);
        }
        $sql  = 'DELETE FROM `'.TABLE_PREFIX.'mod_foldergallery_categories` '
              . 'WHERE `section_id` = '.$oldSettings['section_id'].';';
        $database->query($sql);
        // Root als Kategorie eintragen
        //  $sql = 'INSERT INTO '.TABLE_PREFIX."mod_foldergallery_categories ( `section_id`,`parent_id`,`categorie`,`parent`,`cat_name`,`active`,`is_empty`,`position`,`niveau`,`has_child`,`childs`,`description` )
        //    VALUES ( '$section_id', '-1', 'Root', '-1', 'Root', '1', '0', '0', '-1', '0', '', 'Root Description' );";
        $sql  = 'INSERT INTO `'.TABLE_PREFIX."mod_foldergallery_categories` ( `section_id`,`parent_id`,`categorie`,`parent`,`cat_name`,`active`,`is_empty`,`position`,`niveau`,`has_child`,`childs`,`description` ) "
              . "VALUES ( '$section_id', '-1', 'Root', '-1', '".$sCatTitle."', '1', '0', '0', '-1', '0', '', '' );";
        $query = $database->query($sql);
        if($database->is_error()) {
            $admin->print_error($database->get_error(), $sAddonUrl.'/admin/modify_settings.php?page_id='.$page_id.'&section_id='.$section_id);
        }
    }

    // Jetzt wird die DB neu synchronisiert //Anm CHio: Wozu? Wenn ein Fehler ist, kann man nichtmal die Settings speichern.
//    syncDB($newSettings);
    // Ãœberprüfen ob ein Fehler aufgetreten ist, sonst Erfolg ausgeben
    if($database->is_error()) {
        $admin->print_error(basname(__FILE__).' - '.__LINE__.' :: '.$database->get_error(), $sAddonUrl.'/admin/modify_settings.php?page_id='.$page_id.'&section_id='.$section_id);
    } else {
        include ($sAddonPath.'/admin/sync.php');
//        $admin->print_success(__LINE__.') '.$TEXT['SUCCESS'], $sAddonUrl.'/admin/sync.php?page_id='.$page_id.'&section_id='.$section_id);
}
    // Print admin footer
    $admin->print_footer();
