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
    $sExtendedPrint = preg_match( '/'.'admin\/save_settings\.php$/is', $sCallingScript);
    if (!$sExtendedPrint) {
        // to print with or without header, default is with header
        $admin_header=true;
        // Workout if the developer wants to show the info banner
        $print_info_banner = false; // true/false
        // Tells script to update when this page was last updated
        $update_when_modified = false;
        // Include WB admin wrapper script to sanitize page_id and section_id, print SectionInfoLine
        require(WB_PATH.'/modules/admin.php');
}
    // An associative array that by default contains the contents of $_GET, $_POST and $_COOKIE.
    $aRequestVars = $_REQUEST;

    $settings = getFGSettings($section_id);

    $flag = false;
    $aMessageError = [];
    $aMessageSuccess = [];
/* syncDB($galerie) ist kompletter updatealgorithmus */
if (syncDB($settings)) {
?> <div class="w3-panel w3-leftbar w3-pale-green w3-border-green w3-round">
  <h5 ><?php echo $MOD_FOLDERGALLERY['SYNC_DATABASE'];?></h5>
</div>
<?php
    $sBackLink = $sAddonUrl.'/admin/modify_settings.php?page_id='.$page_id.'&section_id='.$section_id;
//          $aMessageSuccess[]= $MOD_FOLDERGALLERY['SYNC_DATABASE'];
        // Wieder alle Angaben aus der DB holen um Sortierung festzulegen
        $results = [];
        $sql = "SELECT * FROM ".TABLE_PREFIX."mod_foldergallery_categories WHERE `section_id` =".$section_id;
        $query = $database->query($sql);
        if ($query->numRows() > 0) {
            while($result = $query->fetchRow(MYSQLI_ASSOC)) {
            if($result['parent'] != -1) {
                $folder = $settings['root_dir'].'/'.$result['parent'].'/'.$result['categorie'];
                $pathToFolder = $path.$folder;
                if (!is_dir(DirectoryHandler::DecodePath($pathToFolder))) {
                    $delete_sql = 'DELETE FROM `'.TABLE_PREFIX.'mod_foldergallery_categories` '
                                . 'WHERE `id`='.$result['id'].' ';
                    $database->query($delete_sql);
                    continue;
                }
            }
            $results[] = $result;
            }

        $niveau = 0;
        // Alle Kategorien durchlaufen zum Kinder und Parents und Level zuzuordnen
        foreach($results as &$cat) {
            $cat['niveau'] = substr_count($cat['parent'],'/');
            if($cat['niveau'] > $niveau){
                $niveau = $cat['niveau'];
            }
            // String bilden für Parentvergleich
            $ast = $cat['parent']."/".$cat['categorie'];
            $cat['ast'] = $ast;
            $cat['childs'] = '';
            $cat['has_child'] = 0;
            // Alle Kategorien durchlaufen und auf gleichheit untersuchen
            foreach($results as &$searchcat){
                if($ast == $searchcat['parent']) {
                    // Falls gleich, kann bestimmt werden wer Kind und welcher Parent ist
                    $cat['has_child'] = 1;
                    $searchcat['parent_id'] = $cat['id'];
                }
            }
        }

//Das ginge sicher besser:
//Childs finden
        foreach($results as &$cat) {
            if ($cat['has_child'] != 0){
                foreach($results as $others) {
                    if ($cat['id'] != $others['id']) {
                        if  (strpos($others['ast'], $cat['ast']) !== false) {
                            //others ist also ein Child von $cat
                            $cat['childs'].= ','.$others['id'];
                        }
                    }
                }
            }
        }

//-------------------------

// Sortierung festlegen
        foreach($results as &$cat) {
            if($cat['position'] == 0) {
                $last = 0;
                foreach($results as $vergleich) {
                    if($cat['parent'] == $vergleich['parent']){
                        if($last <= $vergleich['position']) {
                                $last = $vergleich['position'];
                        }
                    }
                }
                $cat['position'] = $last+1;
            }
        }

// Datenkank Update
        $updatesql = 'UPDATE '.TABLE_PREFIX.'mod_foldergallery_categories SET ';
        for($i = 0; $i<count($results); $i++){
            $childs = ltrim($results[$i]['childs'], ',');
            //$childs=substr($childs,1,strlen($childs-1)); //Führenden Beistrich belassen, der wird in view wieder benotigt
            $sql = $updatesql." `niveau`=".intval($results[$i]['niveau']).", `parent_id`=".intval($results[$i]['parent_id']).", `has_child`=".intval($results[$i]['has_child']).", `position`=".intval($results[$i]['position']).", `childs`='".$childs."' WHERE `id`=".intval($results[$i]['id']).";";
            if($database->query($sql)){
                $flag = true;
            } else {
                break;
            }
        }

// Fehler/Lücken in der Sortierung beheben
        for($i = 0; $i<=$niveau; $i++) {
            $last_parent = 0;
            $counter = 1;
            $sql = "SELECT `position`,`id`, `parent_id` FROM `".TABLE_PREFIX."mod_foldergallery_categories` WHERE `section_id` =".$section_id." AND `niveau`=".$i." ORDER BY `position` ASC, `parent_id` ASC;";
            $query = $database->query($sql);
            while($result = $query->fetchRow(MYSQLI_ASSOC)){
                if($last_parent == $result['parent_id']) {
                    if($counter != $result['position']){
                        $sql = $updatesql." `position`=".$counter." WHERE `id`=".$result['id'].";";
                        $database->query($sql);
                    }
                    $counter++;
                } else {
                    $last_parent = $result['parent_id'];
                    $counter = 1;
                    if($counter != $result['position']){
                        $sql = $updatesql." `position`=".$counter." WHERE `id`=".$result['id'].";";
                        $database->query($sql);
                    }
                    $counter++;
                }
            }
        }

        $sSectionIdPrefix = (defined( 'SEC_ANCHOR' ) && ( SEC_ANCHOR != '' )  ? SEC_ANCHOR : '' );
        $sBackLink =  ( $flag ?
                      ADMIN_URL.'/pages/modify.php?page_id='.$page_id.'#'.$sSectionIdPrefix.$section_id :
                      $sBackLink
                      );

        if($flag) {
            $admin->print_success(sprintf(' [%d] %s ', __LINE__, $MOD_FOLDERGALLERY['SYNC_SUCCESS']),$sBackLink);
        } else {
            $admin->print_error(sprintf(' [%d] %s ', __LINE__, $MOD_FOLDERGALLERY['SYNC_FAILED']), $sBackLink);
        }
    } else {   // keine Kategorien vorhanden
            $admin->print_error( $MOD_FOLDERGALLERY['NO_CATEGORIES'], $sBackLink);
        }
    }
// Print admin footer
    if (!$sExtendedPrint) {$admin->print_footer();}
