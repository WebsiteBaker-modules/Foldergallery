<?php
/*

 Website BakerProject <http://websitebaker.org/>
 Copyright (C) Ryan Djurovich

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
/* -------------------------------------------------------- */
// Must include code to prevent this file from being accessed directly
if (!defined('SYSTEM_RUN')) {header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found'); echo '404 File not found'; flush(); exit;}
/* -------------------------------------------------------- */
    $sAddonPath = __DIR__;

    if (is_readable($sAddonPath.'/init.php')) {require ($sAddonPath.'/init.php');}
    if (is_readable($sAddonPath.'/presets/thumbPresets.php')){require($sAddonPath.'/presets/thumbPresets.php');}
//    if (!isset($aDefaults)&& is_readable($sAddonPath.'/presets/defaults.php')){require($sAddonPath.'/presets/defaults.php');}

    $msg = null;
/*
 * Neuer Eintrag in der DB erstellen
 * $root_dir wird dabei auf 'd41d8cd98f00b204e9800998ecf8427e' gesetzt,
 * damit ueberprueft werden kann, ob bereits ein Ordner festgelegt wurde
 * (Fuer interessierte: Es ist der MD5-Hashwert einer leeren Zeichenkette)
 */
    $root_dir = '/d41d8cd98f00b204e9800998ecf8427e';
    $extensions = 'jpg,jpeg,gif,png';
    $aCfg = array(
        "page_id"      => (isset($page_id) ? $page_id : "0"),
        'cat_pp'         => '-1',
        'catpic'         => '0',
        'extensions'     => $extensions,
        'gal_pp'         => '-1',
        'invisible'      => $thumbDirName.','.$chunkDirName.','.$uploadDirName.',thumbs,data',
        'lightbox'       => 'jqueryFancybox',
        'imageName'      => '0',
        'pagination'     => 'NewYahooStyle',
        'galleryStyle'   => 'default',
        'opacity'        => 1,
        'alignment'      => 'left',
        'loadPreset'     => '1:1Croped150',
        'pics_pp'        => '20',
        "defaultQuality" => "50",
        "maxImageSize"   => "1024",
        'root_dir'       => $root_dir,
        'tbSettings'     => serialize($aThumbSettings),
        'thumbDirName'   => $thumbDirName.'',
        'thumbPath'      => $thumbPath.'',
    );

// Fill in values in existing table fieldlist, first entry is an extra index in WHERE statement
// checked for existing fields in table fieldlist
    $aFieldsList = array(
        "section_id"   => (isset($section_id) ? $section_id : '0'),
        "page_id"      => (isset($page_id) ? $page_id : '0'),
    );

    if (is_array($msg = UpdateKeyValue('mod_foldergallery_settings', $aCfg, '', $aFieldsList)))
    {
      echo implode('<br />', $msg);
    }

/*
$rawSql = 'INSERT INTO `'.TABLE_PREFIX.'mod_foldergallery_settings` SET '
        . '`section_id` = '.$section_id.', '
        . '`s_name` = \'%s\' , '
        . '`s_value` = \'%s\' ';
$database->query(sprintf($rawSql, 'page_id', $page_id));
$database->query(sprintf($rawSql, 'root_dir', $root_dir));
$database->query(sprintf($rawSql, 'extensions', $aDefaults['extensions']));
$database->query(sprintf($rawSql, 'invisible', $aDefaults['invisible']));
$database->query(sprintf($rawSql, 'pics_pp', $aDefaults['pics_pp']));
$database->query(sprintf($rawSql, 'gal_pp', $aDefaults['gal_pp']));
$database->query(sprintf($rawSql, 'catpic', $aDefaults['catpic']));
$database->query(sprintf($rawSql, 'lightbox', $aDefaults['lightbox']));
$database->query(sprintf($rawSql, 'tbSettings', serialize($aThumbSettings)));
*/

// end of file
