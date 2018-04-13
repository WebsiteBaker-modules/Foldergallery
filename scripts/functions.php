<?php
/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if (!defined('SYSTEM_RUN')) {header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found'); echo '404 File not found'; flush(); exit;}
/* -------------------------------------------------------- */
?><?PHP
/*  */
    $sAddonPath = dirname(__DIR__);

    function getFGSettings($section_id) {
        global $database, $aDefaults, $page_id;
        $settings  = [];
//        $aDefaults = $oAddonReg->aDefaults;
        $sql  = 'SELECT `s_name`, `s_value` FROM `'.TABLE_PREFIX.'mod_foldergallery_settings` '
              . 'WHERE `section_id` = ' . $section_id;
        if ($query = $database->query($sql)){
            while ($row = $query->fetchRow(MYSQLI_ASSOC)) {
                $settings[$row['s_name']] = $row['s_value'];
            }
            $settings['section_id'] = $section_id;
/* add extra handling */
            if (isset($settings['tbSettings'])){
                $settings['tbSettings'] = unserialize($settings['tbSettings']);
                $settings['page_id'] = $page_id;
            }

        }
        if (is_array($aDefaults)) {
            $aTmp = array_diff_key($aDefaults, $settings);
            if ($aTmp){
                UpdateKeyValue('mod_foldergallery_settings', $aTmp, '', ['section_id'=>$section_id]);
//                $settings = array_merge($aDefaults, $settings);
            }
        }
        return $settings;
    }

/**
 * @deprecated Use the class.upload instead!
 *
 * Generiert ein Thumbnail $thumb aus $file, falls dieses noch nicht vorhanden ist
 * @return void or true
 * @param string $file  Pfadangabe zum original File
 * @param string $thumb Pfadangabe zum Thumbfile
 * @param bool $showmessage Logging ein/aus
 * @param string $ratio Seitenverhaeltniss der Thumbs
 * @param int $fullpercent Prozentangabe, wie das Originalbild beschniten werden muss
 * @param string $bg_color Farbe des Hintergrunds, falls das Bild nicht das richtige Format hat
 * @param string $positionX Position X von jCrop ansonsten 0
 * @param string $positionY Position Y von jCrop ansonsten 0
 * @param string $positionW Position W von jCrop ansonsten 0
 * @param string $positionH Position H von jCrop ansonsten 0
 */
function generateThumb($file, $thumb, $thumb_size, $showmessage, $ratio, $fullpercent = 100, $bg_color = '999999', $positionX = 0, $positionY = 0, $positionW = 0, $positionH = 0) {

    //Von Chio eingefuegt:
    global $megapixel_limit;
    if ($megapixel_limit < 2) {
        $megapixel_limit = 5;
    }

    static $thumbscounter, $thumbsstarttime, $allthumbssizes;
    if (!$thumbscounter) {
        $thumbscounter = 0;
    }
    if (!$thumbsstarttime) {
        $thumbsstarttime = time();
    }
    if (!$allthumbssizes) {
        $allthumbssizes = 0;
    }

    if (!is_file($file)) {
        if ($showmessage == 1) {
            echo '<b>Missing file:</b> ' . $file . '<br/>';
            return -1;
        }
    }

    $thumbscounter++;
    if ($thumbscounter == 80 AND $showmessage == 1)
        echo('<h3>stopped.. press F5 to reload</h3>');
    if ($thumbscounter > 80)
        return -1;

    $tgone = time() - $thumbsstarttime;
    if (time() - $thumbsstarttime > 50) {
        die('<h3>timeout.. press F5 to reload</h3>');
    }

    //if ($showmessage==1) {echo "<br/>creating thumb: ".$file;}
    // ENDE Chio
    // Einige Variablen
    $jpg = '\.jpg$|\.jpeg$';
    $gif = '\.gif$';
    $png = '\.png$';
    $fontSize = 2;
    $bg = $bg_color;

    $thumbFolder = dirname($thumb);

    //Verzeichnis erstellen, falls noch nicht vorhanden
    if (!is_dir($thumbFolder)) {
        $u = umask(0);
        if (!@mkdir($thumbFolder, 0777)) {
            echo '<!--p style="color: red; text-align: center;">Fehler beim Verzeichniss erstellen</p-->';
        }
        umask($u);
    }

    // Thumb erstellen
    if (!is_file($thumb)) {
        //checken, ob megapixel ueber 5:
        if (function_exists('getimagesize')) {
            list($width, $height, $type, $attr) = getimagesize($file);
            $fl = ceil(($width * $height) / 1000000);
            if ($fl > $megapixel_limit) {
                if ($showmessage == 1) {
                    echo '<br/><b>' . $fl . ' Megapixel; skipped!</b>';
                }
                return -2;
            }
        }

        if ($type == 2) {
            $original = @imagecreatefromjpeg($file);
        } elseif ($type == 1) {
            $original = @imagecreatefromgif($file);
        } elseif ($type == 3) {
            $original = @imagecreatefrompng($file);
        }

        if (isset($original)) {
            if (function_exists('getimagesize')) {
                list($width, $height, $type, $attr) = getimagesize($file);
            }

            //------------------------------------------------------------//
            //Werte berechnen:

            if (!isset($fullpercent)) {
                $fullpercent = 100;
            }

            //$thumb_size ist IMMER die Breite:
            $newwidth = $thumb_size;
            $newheight = $thumb_size / $ratio;

            if ($ratio < 1) {
                //portrait format:
                $newwidth = $thumb_size * $ratio;
                $newheight = $thumb_size;
            }

            $pic_ratio = $width / $height;
            if ($pic_ratio > $ratio) {
                //Bild ist breiter als der Rahmen erlaubt
                //echo '<p>breiter: ' .$pic_ratio.' '.$file.'</p>';

                $smallheight = $newheight;
                $smallwidth = $smallheight * $pic_ratio;
                $ofx = ($newwidth - $smallwidth) / 2;
                $ofy = 0;

                //values without crop:
                $smallwidth2 = $newwidth;
                $smallheight2 = $smallwidth2 / $pic_ratio;
                $ofx2 = 0;
                $ofy2 = ($newheight - $smallheight2) / 2;
            } else {
                //Bild ist hoeher als der Rahmen erlaubt
                //echo '<p>hoeher: ' .$pic_ratio.' '.$file.'</p>';

                $smallwidth = $newwidth;
                $smallheight = $smallwidth / $pic_ratio;
                $ofx = 0;
                $ofy = ($newheight - $smallheight) / 3; //Eher oberen Teil, dh /3
                //values without crop:
                $smallheight2 = $newheight;
                $smallwidth2 = $smallheight2 * $pic_ratio;
                $ofy2 = 0;
                $ofx2 = ($newwidth - $smallwidth2) / 2;
            }

            //mix crped and non-cropped values by percent:
            $f1 = 0.01 * $fullpercent;
            $f2 = 1.0 - $f1;
            $smallwidth = floor(($f1 * $smallwidth) + ($f2 * $smallwidth2));
            $smallheight = floor(($f1 * $smallheight) + ($f2 * $smallheight2));
            $ofx = floor(($f1 * $ofx) + ($f2 * $ofx2));
            $ofy = floor(($f1 * $ofy) + ($f2 * $ofy2));

            $newwidth = floor($newwidth);
            $newheight = floor($newheight);

            //Ausnahme: Bild ist kleiner als thumb
            if ($width < $smallwidth AND $height < $smallheight) {
                echo $smallwidth;
                $ofx = 0;
                $ofy = 0;
                $smallwidth = $width;
                $smallheight = $height;
                $ofx = floor(($newwidth - $width) / 2);
                $ofy = floor(($newheight - $height) / 2);
            }

            //Ausnahme: Bild wird gecropt
            if (!empty($positionW) && !empty($positionH)) {
                $ofy = 0;
                $ofx = 0;
            }

            if (function_exists('imagecreatetruecolor')) {
                if ($ratio > 1) {
                    $small = imagecreatetruecolor($thumb_size, $thumb_size / $ratio);
                } else {
                    $small = imagecreatetruecolor($thumb_size * $ratio, $thumb_size);
                }
            } else {
                $small = imagecreate($smallwidth, $smallheight);
            }
            sscanf($bg, '%2x%2x%2x', $red, $green, $blue);
            $b = imagecolorallocate($small, $red, $green, $blue);
            imagefill($small, 0, 0, $b);
            if ($original) {
                //aenderungen der Variablen die fuer JCrop Thumberstellung anderst sein muessen
                if (!empty($positionW) && !empty($positionH)) {
                    $width = $positionW;
                    $height = $positionH;

                    //wenn ein Ratio eingestellt ist werden die small Atribute des Thumbs angepasst
                    //die ist allerdings nur bei JCrop noetig normal wird die groesse vom 0Punkt aus errechnet

                    if ($ratio > 1) {
                        $smallwidth = $thumb_size;
                        $smallheight = $thumb_size / $ratio;
                    } else {
                        $smallwidth = $thumb_size * $ratio;
                        $smallheight = $thumb_size;
                    }
                }

                if (function_exists('imagecopyresampled')) {
                    imagecopyresampled($small, $original, $ofx, $ofy, $positionX, $positionY, $smallwidth, $smallheight, $width, $height);
                } else {
                    imagecopyresized($small, $original, $ofx, $ofy, $positionX, $positionY, $smallwidth, $smallheight, $width, $height);
                }
            } else {
                $black = imagecolorallocate($small, 0, 0, 0);
                $fw = imagefontwidth($fontSize);
                $fh = imagefontheight($fontSize);
                $htw = ($fw * strlen($filename)) / 2;
                $hts = $thumb_size / 2;
                imagestring($small, $fontSize, $hts - $htw, $hts - ($fh / 2), $filename, $black);
                imagerectangle($small, $hts - $htw - $fw - 1, $hts - $fh, $hts + $htw + $fw - 1, $hts + $fh, $black);
            }
            imagejpeg($small, $thumb);
            imagedestroy($original);
            imagedestroy($small);
            return true;
        }
    }
}

/**
 * Just clean up your Categorie-string which is passed by the user to you
 *
 * @deprecated deprecated since version 1.30, use validator class instead
 * @param string $string
 * @return string clean categorie name
 */
function FG_cleanCat($string) {
    if (is_string($string)) {
        if (!preg_match('~^(/([.a-zA-Z0-9_\-\s]/{0,1})*[^/])$~', $string)) {
            return '';
        } else {
            return $string;
        }
    } else {
        return '';
    }
}
function FG_getCatId($sectionID, $kategorie) {
    global $database;
    global $MOD_FOLDERGALLERY;

    $activeTerm = 'AND active = 1 ';
    if ($kategorie == '') {
        $kategorie = '-1/Root';
        $activeTerm = '';
    }
    $sql = "SELECT `id` FROM `" . TABLE_PREFIX . "mod_foldergallery_categories` WHERE " .
            "`section_id` = " . $sectionID . " AND `is_empty` = 0 " . $activeTerm .
            "AND CONCAT(`parent`,'/',`categorie`) = '" . $kategorie . "'";
    $query = $database->query($sql);
    $ergebnis = $query->fetchRow();
    if (!$ergebnis) {
        return 0;
    }
    $katID = $ergebnis['id'];
    return $katID;
}

/**
 * Gibt die Kategorie ID der Kategorie zurueck
 *
 * @global $database
 * @param int $sectionID WB section_id
 * @param string $kategorie String wie er im URL angezeigt wird
 * @return int
 */
function _FG_getCatId($sectionID, $kategorie='') {
    global $database;
    global $MOD_FOLDERGALLERY;
    $retVal = 0;
    $activeFlag = 1;
    if ($kategorie == '') {
        $kategorie = '-1/Root';
        $activeFlag = 0;
    }
    $sql = 'SELECT `id` FROM `' . TABLE_PREFIX . 'mod_foldergallery_categories` '
    . 'WHERE '.'`section_id` = ' . $sectionID . ' '
//    .   'AND `is_empty` = 0 '
    .   'AND `active` = '.$activeFlag.' '
    .   'AND CONCAT(`parent`,'/',`categorie`) = \''. $kategorie .'\' ';

    if ($oCat = $database->query($sql)) {
        $aCategories = $oCat->fetchRow(MYSQLI_ASSOC);
        $retVal = @$aCategories['id']?:$retVal;
    }
print '<pre  class="mod-pre rounded">function <span>'.__FUNCTION__.'( '.''.' );</span>  filename: <span>'.basename(__FILE__).'</span>  line: '.__LINE__.' -> <br />';
print_r( $sql ); print '</pre>'; flush (); //  ob_flush();;sleep(10); die();
    return $retVal;
}

function display_categories($parent_id, $section_id, $level = 0) {
    global $database;
    global $url;
    global $page_id;
    global $MOD_FOLDERGALLERY;
    global $sAddonName,$sAddonUrl, $sAddonThemeUrl;
    global $settings;
    global $sCategorie;

    $padding    = (($level >= 1)? ($level*20)+20 : 20);
    $width_left =  $padding+20;

/*
 20     00            Root             width = 370      root
 40     20            folder_1         width = 370-20   level 0
 60     40            folder_1_1       width = 370-40   level 1
 80     60            folder_1_1_1     width = 370-60   level 2
100     80            folder_1_1_1_1   width = 370-80   level 3
usw
*/

    $list = "\n";
    $sql  = 'SELECT * FROM `'.TABLE_PREFIX . 'mod_foldergallery_categories` '
          . 'WHERE `parent_id` ='.$parent_id.' '
          .   'AND `section_id` ='.$section_id.' '
          . 'ORDER BY `position` ASC';
    $query = $database->query($sql);
    $zagl = $query->numRows();
    $arrup = false;
    $arrdown = true;
    if ($zagl > 1) {  }
    $counter = 0;
    while ($result = $query->fetchRow(MYSQLI_ASSOC)) {
        $counter++;
        if ($counter > 1) {
            $arrup = true;
        }
        if ($counter == $zagl) {
            $arrdown = false;
        }

        $padding    = (($parent_id==-1) ?  0 : $padding);
        $width_left = (($parent_id==-1) ? 20 : $width_left);
        $sScatName  =  mb_strlen($result['cat_name']) > 35 ? mb_substr($result['cat_name'], 0, 34).'…' : $result['cat_name'];

        if ($parent_id != "-1") {
            $cursor = ' cursor: move;';
            $result['categorie'] = $settings['root_dir'] . $result['parent'] . '/' . $result['categorie'];
        } else {
            $cursor = '';
            $result['categorie'] = $settings['root_dir'];
        }
        $sCategorie =  mb_strlen($result['categorie']) > 85 ? mb_substr($result['categorie'], 0, 84).'…' : $result['categorie'];

        if ($result['active'] == 1) {
            $activ_string = $MOD_FOLDERGALLERY['CAT_ACTIVE'];
        } else {
            $activ_string = $MOD_FOLDERGALLERY['CAT_INACTIVE'];
        }

        $list .= "<li id='recordsArray_".$result['id']."' style='padding: 1px 0px 1px 0px;".$cursor."'>\n"
                . "<table style='width: 100%;' class='cat_table'>\n"
                . '<tr onmouseover="this.style.backgroundColor = \'#F1F8DD\';" onmouseout="this.style.backgroundColor = \'#ECF3F7\';">'
                . "<td style='width: ".$width_left."px; padding-left:".$padding."px'>\n";

        if ($result['has_child']) {
            // Display Expand Sign
            $list .= '<a href="#self" onclick="toggle_visibility(\'p'.$result['id'].'\');" title="'.$MOD_FOLDERGALLERY['EXPAND_COLAPSE'] . '">'
                    . '<img class="fg" src="'.THEME_URL.'/images/plus_16.png" id="plus_minus_p'.$result['id'].'" alt="+'.$result['has_child'].'" />'
                    . '</a>';
        }
        // Categorie Name and Folder
        $list .= '</td>'
                . '<td style="width:'.(400 - $padding).'px; white-space: nowrap; overflow: hidden;" >'.PHP_EOL
                . '<a href="'.$url['edit'].$result['id'].'" >'.PHP_EOL
                . '<img class="fg" src="' . THEME_URL . '/images/visible_16.png" alt="edit" style="margin-right: 5px; text-align: left;" />'
//                . '<span class="tooltip">'.$result['cat_name'].'</span>'.PHP_EOL
                . ''.$sScatName.PHP_EOL.'</a>'.PHP_EOL
                .'</td>'.PHP_EOL
                . '<td style="float: left; white-space: nowrap; overflow: hidden; " title="">'.$sCategorie.PHP_EOL
                . '</td>'.PHP_EOL
                . '<td style="width: 20px;">'.PHP_EOL;

        // Active / Inactive Sign href=""
        if ($parent_id != "-1") {
            $list .= '<a class="FG_active_categorie" id="'.$result['id'].'" onclick="toggle_active_inactive('.$result['id'].');" >
            <img  id="i'.$result['id'].'" src="'.$sAddonThemeUrl.'/img/active' . $result['active'] . '.gif" alt="" title="' . $activ_string . '" />
            </a>';
        }

        $list .='
          </td>
          <td style="width: 20px;">
        ';

        if ($arrup == true) {
            // Move up arrow
            $list .="<a href='".$sAddonUrl."/admin/scripts/move_up.php?page_id=".$page_id . "&section_id=" . $section_id . "&id=" . $result['id'] . "' title='" . $MOD_FOLDERGALLERY['MOVE_UP'] . "'>"
                    . "<img src='" . THEME_URL . "/images/up_16.png' alt='v' /></a>";
        }

        $list .="</td>"
                . "<td style=\"width: 30px;\">";

        if ($arrdown == true) {
            // Move down arrow
            $list .="<a href='" . $sAddonUrl."/admin/scripts/move_down.php?page_id=" . $page_id . "&section_id=" . $section_id . "&id=" . $result['id'] . "' title='" . $MOD_FOLDERGALLERY['MOVE_DOWN'] . "'>"
                    . "<img src='" . THEME_URL . "/images/down_16.png' alt='u' /></a>";
        }
              $list .="</td>\n"
                    . "<td style=\"width: 20px;\">\n";
        if (($arrup == true)  || ($arrdown == true) || $parent_id>=0){
              $sDeleteLink = $sAddonUrl."/admin/scripts/delete_cat.php?page_id=" . $page_id . "&section_id=" . $section_id . "&cat_id=" . $result['id'];
              $list .="<a onclick=\"warnBeforeDelete(this)\" data-cat=\"".$result['cat_name']."\" data-source='".$sDeleteLink."' href='#' title='" . $MOD_FOLDERGALLERY['DELETE'] . "'>"
                    . "<img src='" . THEME_URL . "/images/delete_16.png' alt='u' /></a>\n";
        }
        $list .="</td>\n"
                . "</tr>\n</table>\n"
                . "<ul id='p" . $result['id'] . "' style='padding: 1px 0px 1px 0px;' class='cat_subelem'>";

        if ($result['has_child']) {
            $list .= display_categories($result['id'], $section_id, $level + 1);
        }

        $list .= "</ul></li>\n ";
    }
    return $list;
}

/**
 * Use this to append your thumb-settings to the upload class, it's just an easy way
 * to pass a array to the function and all the keys are appended to the thumbclass
 *
 * @param Upload $handle
 * @param array $settings
 * @param string $filename
 */
function FG_appendThumbSettings(&$handle, $settings, $filename) {
    foreach ($settings as $option => $value) {
        if ($option == 'description') {
            continue;
        }
        $handle->$option = $value;
        $handle->file_safe_name = false;
        $handle->file_new_name_body = $filename;
        $handle->file_new_name_ext = ''; // Else you have a filename like img.jpg.tmp
    }
}

function FG_updateFilename($catID, $path, $oldFilename, $newFilename) {
    global $database, $section_id;
    if (file_exists($path . $oldFilename)) {
        // OK, file exists in FS
    } else {
        return;
    }
    $sql = 'SELECT id FROM ' . TABLE_PREFIX . 'mod_foldergallery_files WHERE parent_id=' . $catID . ' AND file_name=\'' . $oldFilename . '\';';
    $query = $database->query($sql);
    if ($result = $query->fetchRow(MYSQLI_ASSOC)) {
        // OK, file exists in DB
    } else {
        return;
    }
    $sql = 'UPDATE ' . TABLE_PREFIX . 'mod_foldergallery_files SET file_name=\'' . $newFilename . '\' WHERE id=' . $result['id'];
    $sql = 'UPDATE ' . TABLE_PREFIX . 'mod_foldergallery_files SET section_id='.$section_id.', file_name=\'' . $newFilename . '\' WHERE id=' . $result['id'];
    $database->query($sql);
    rename($path . $oldFilename, $path . $newFilename);
}

/**
 * Creates a thumbnail out of the given File/Image
 *
 * This function creates a thumbimage out of the File/Image given in $imagePath
 * The settings which are given in $settings are applied during creation. Finaly
 * the new image is saved in the $thumbPath-Folder with the Name $imageName.
 * If it is not possible to create a thumbnail out of $imagePath, a Icon is used
 * and transformed according the $settings.
 *
 * @param string $imagePath Image/File
 * @param string $imageName Name of the Image/File which is created, inclusive extension
 * @param string $thumbPath Path to the folder in which the created file is saved
 * @param array $settings   Settings array with the configuration of the uploadclass
 * @return boolean
 */
function FG_createThumb($imagePath, $imageName, $thumbPath, $settings) {
    global $sAddonName,$sAddonUrl,$sAddonPath;
    $handle = new FgUpload(DirectoryHandler::DecodePath($imagePath));
    if (!$handle->file_is_image) {
        switch ($handle->file_src_mime) {
            case 'application/x-shockwave-flash' :
                $handle = new FgUpload($sAddonPath.'/images/swf_icon.png');
                break;
            case 'video/quicktime' :
                $handle = new FgUpload($sAddonPath.'/images/quicktime_icon.png');
                break;
            default:
                $handle = new FgUpload($sAddonPath.'/images/unknown_icon.png');
        }
    }

    if ($handle->file_is_image) {
        FG_appendThumbSettings($handle, $settings, DirectoryHandler::DecodePath($imageName));
        $handle->process(DirectoryHandler::DecodePath($thumbPath));
        if ($handle->processed) {
            return true;
        }
    }
    return false;
}
