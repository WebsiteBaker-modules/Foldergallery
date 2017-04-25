<?php

    $sAddonPath = dirname(__DIR__);

    if (is_readable($sAddonPath.'/init.php'))     {require ($sAddonPath.'/init.php');}

    // to print with or without header, default is with header
    $admin_header=true;
    // Workout if the developer wants to show the info banner
    $print_info_banner = false; // true/false
    // Tells script to update when this page was last updated
    $update_when_modified = false;
    // Include WB admin wrapper script to sanitize page_id and section_id, print SectionInfoLine
    require(WB_PATH.'/modules/admin.php');

    if(!isset($aRequestVars['save']) && !is_string($aRequestVars['save'])) {
//            echo "Falsche Formulardaten!";
        $admin->print_error('Falsche Formulardaten!', ADMIN_URL.'/pages/modify.php?page_id='.$page_id.'&section_id='.$section_id);
    } else {
        // Vorhandene POST Daten auswerten
        if(isset($aRequestVars['cat_id']) && is_numeric($aRequestVars['cat_id'])) {
                $cat_id = $aRequestVars['cat_id'];
        } else {
                $error['no_cat_id'] = 1;
                $admin->print_error('lost cat', ADMIN_URL.'/pages/modify.php?page_id='.$page_id.'&section_id='.$section_id);
                die();
        }

        $aRequestVars['caption'] = (@$aRequestVars['caption']?:array());
        $bilderNeu = array();
        foreach($aRequestVars['caption'] as $key => $value) {
            if(!is_numeric($key)) {
                continue;
            }
            if(is_string($value) && $value != '') {
                $caption = $value;
            } else {
                $caption = '';
            }
            $bilderNeu[] = array(
                'id'        => $key,
                'img_title' => $aRequestVars['img_title'][$key],
                'caption'   => $caption,
                'delete'    => false
            );
        }

        // Jetzt machen wir alle Datenbank Aenderungen
        $deleteSQL = 'SELECT * FROM `'.TABLE_PREFIX.'mod_foldergallery_files` WHERE ';
        $selectSQL = 'SELECT `id`, `caption`, `img_title` FROM `'.TABLE_PREFIX.'mod_foldergallery_files` WHERE ';
        $updateSQL = 'UPDATE `'.TABLE_PREFIX.'mod_foldergallery_files` SET ';
        $updateSQL = 'UPDATE `'.TABLE_PREFIX.'mod_foldergallery_files` SET `section_id` = '.$section_id.', ';
        foreach($bilderNeu as $bild){
                $selectArray[] = $bild['id'];
        }
        if(isset($selectArray)){
            $selectSQL .= '(`id` IN('.implode(',',$selectArray).'));';
            if ($query = $database->query($selectSQL)){
                while($singleResult = $query->fetchRow(MYSQLI_ASSOC)){
                    foreach($bilderNeu as $bild) {
                        if($bild['id'] == $singleResult['id']) {
                            if(
                                ($bild['caption'] != $singleResult['caption'])||
                                ($bild['img_title'] != $singleResult['img_title'])
                              ){
                                $updateArray[] = array(
                                    'id' => $bild['id'],
                                    'img_title' => $bild['img_title'],
                                    'caption' => $bild['caption']
                                );
                            }
                        }
                    }
                }
            }
        }
        if(isset($updateArray)) {
                $anzahlUpdates = count($updateArray);
                for($i = 0; $i < $anzahlUpdates; $i++) {
                        $updateSQLNew = $updateSQL." `caption`='".$database->escapeString($updateArray[$i]['caption'])."', "
                                      . " `img_title`='".$database->escapeString($updateArray[$i]['img_title'])."' "
                                      . "WHERE `id`=".intval($updateArray[$i]['id']).";";
                        if (!$database->query($updateSQLNew)){
                        }
                }
        }
/*
        //BILDTITEL
        $aRequestVars['img_title'] = (@$aRequestVars['img_title']?:array());
        foreach($aRequestVars['img_title'] as $key => $value) {
            if(!is_numeric($key)) {
                continue;
            }
            if(is_string($value) && $value != '') {
                $img_title = $value;
            } else {
                $img_title = '';
            }
            $bilderNeu1[] = array(
                'id'        => $key,
                'img_title'   => $img_title,
                'delete'    => false
            );
        }

        // Jetzt machen wir alle Datenbank Aenderungen
        $deleteSQL1 = 'SELECT * FROM `'.TABLE_PREFIX.'mod_foldergallery_files` WHERE ';
        $selectSQL1 = 'SELECT `id`, `img_title` FROM `'.TABLE_PREFIX.'mod_foldergallery_files` WHERE ';
        $updateSQL1 = 'UPDATE `'.TABLE_PREFIX.'mod_foldergallery_files` SET ';
        $updateSQL1 = 'UPDATE `'.TABLE_PREFIX.'mod_foldergallery_files` SET `section_id` = '.$section_id.' ';
        foreach($bilderNeu as $bild){
                $selectArray[] = $bild['id'];
        }

        if(isset($selectArray)){
            $selectSQL1 .= '(`id` IN('.implode(',',$selectArray).'));';
            $query1 = $database->query($selectSQL1);
            while($singleResult1 = $query1->fetchRow(MYSQLI_ASSOC)){
                foreach($bilderNeu1 as $bild1) {
                    if($bild1['id'] == $singleResult1['id']) {
                        if($bild1['img_title'] != $singleResult1['img_title']){
                            $updateArray1[] = array(
                                'id' => $bild1['id'],
                                'img_title' => $bild1['img_title']
                            );
                        }
                    }
                }
            }
        }
        if(isset($updateArray1)) {
                $anzahlUpdates1 = count($updateArray1);
                for($ix = 0; $ix < $anzahlUpdates1; $ix++) {
                        $updateSQLNew1 = $updateSQL1." img_title='".$updateArray1[$ix]['img_title']."' WHERE id=".$updateArray1[$ix]['id'].";";
                        $database->query($updateSQLNew1);
                }
        }
*/
}
$admin->print_success($TEXT['SUCCESS'], $sAddonUrl.'/admin/modify_cat.php?page_id='.$page_id.'&section_id='.$section_id.'&cat_id='.$cat_id);

$admin->print_footer();
