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
/* -------------------------------------------------------- */
// Must include code to prevent this file from being accessed directly
/* -------------------------------------------------------- */
if(defined('WB_PATH') == false)
{
    die('Cannot access '.basename(__DIR__).'/'.basename(__FILE__).' directly');
} else {
    $i = 0;
    $msg = null;
    $sAddonName = basename(__DIR__);
    $aDeleteTable=array();
    $sql  = 'SELECT COUNT(*) FROM `'.TABLE_PREFIX.'sections` '
          . 'WHERE `module` LIKE \''.$sAddonName.'\' ';
//    $msg[]=$sql;
    if (($iNumRow = $database->get_one($sql)) <= 1){
        $aDeleteTable[] = 'TRUNCATE TABLE `'.TABLE_PREFIX.'mod_foldergallery_settings` ';
        $aDeleteTable[] = 'TRUNCATE TABLE `'.TABLE_PREFIX.'mod_foldergallery_categories` ';
        $aDeleteTable[] = 'ALTER TABLE    `'.TABLE_PREFIX.'mod_foldergallery_categories` AUTO_INCREMENT = 1';
        $aDeleteTable[] = 'TRUNCATE TABLE `'.TABLE_PREFIX.'mod_foldergallery_files` ';
        $aDeleteTable[] = 'ALTER TABLE    `'.TABLE_PREFIX.'mod_foldergallery_files` AUTO_INCREMENT = 1';
    } else {
        // Delete DB-Entries (messages and settings)
        $sql = 'SELECT `id` FROM `'.TABLE_PREFIX.'mod_foldergallery_categories` '
             . 'WHERE `section_id` = '.$section_id.' ';
        if ($query = $database->query($sql)){
            while($cat = $query->fetchRow(MYSQLI_ASSOC)) {
                $aDeleteTable[] = 'DELETE FROM `'.TABLE_PREFIX.'mod_foldergallery_files` '
                                .'WHERE `parent_id` = '.$cat['id'];
            }
        }
        // Delete entrys from mod_fg_settings
        $aDeleteTable[] = 'DELETE FROM `'.TABLE_PREFIX.'mod_foldergallery_settings` '
                        . 'WHERE `section_id`='.$section_id;
    // Delete entrys from mod_fg_settings
        $aDeleteTable[] = 'DELETE FROM `'.TABLE_PREFIX.'mod_foldergallery_categories` '
                        . 'WHERE `section_id`='.$section_id;
    }

    foreach ($aDeleteTable AS $sDeleteTable){
        if (!$database->query($sDeleteTable))
        {
            $msg[]=$sDeleteTable;
            $msg[]=$database->get_error();
        }
    }
    if ($msg){
        print implode("<br />", $msg)."\n";
    }
}
// end of file
