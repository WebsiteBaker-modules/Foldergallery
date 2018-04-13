<?php
/**
 *
 */
/* -------------------------------------------------------- */
// Must include code to prevent this file from being accessed directly
if (!defined('SYSTEM_RUN')) {header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found'); echo '404 File not found'; flush(); exit;}
/* -------------------------------------------------------- */
    if (is_readable(__DIR__.'/init.php')) {require (__DIR__.'/init.php');}
    if (is_readable($sAddonPath.'/presets/thumbPresets.php')){require($sAddonPath.'/presets/thumbPresets.php');}
    // An associative array that by default contains the contents of $aRequestVars, $aRequestVars and $_COOKIE.
    $aRequestVars = $_REQUEST;
    $generatethumbscounter = 0;
    $validator = new Validator();
#    $wb->ami_group_member($wb->get_groups_id());
    $isEmpty = false;
    $bIsAdmin = ($wb->is_authenticated() && $wb->ami_group_member('1'));
try {
// get Foldergallery settings
    $settings = getFGSettings($section_id);
//    $root_dir = MEDIA_DIRECTORY.$settings['root_dir']; //Chio
    $title = '';
    if (!isset($settings['root_dir'])) {
        throw new \Exception ( $MOD_FOLDERGALLERY['FRONT_END_ERROR']);
    }

    $root_dir = $MediaRel.$settings['root_dir'];
    $catpic = (int) $settings['catpic']; //Chio
    // Get page-link from database
    $sql = 'SELECT `link` FROM `'.TABLE_PREFIX.'pages`'
          .'WHERE `page_id` = '.$page_id;
    $sPageLink = $database->get_one($sql);
    $link = WB_URL.PAGES_DIRECTORY.$sPageLink.PAGE_EXTENSION;
    $sPaginationStyle = (isset($settings['pagination']) && ($settings['pagination']!='')?$settings['pagination']:'NewYahooStyle');
    $path = WB_PATH;

    $ergebnisse = []; // Da drin werden dann alle Ergebnisse aus der DB gespeichert
    $unterKats  = [];  // Hier rein kommen die Unterkategorien der aktuellen Kategorie
    $bilder     = [];     // hier kommen alle Bilder der aktuellen Kategorie rein
    $NoImages   = false;
    $NoCategory = false;
// Ist die angegebene Kategorie gueltig? (erlaubter String)
    $aktuelleKat = '';
    $FG_Error['CatNotValid'] = false;
    $sQueryCat = (isset($aRequestVars['cat'])?$aRequestVars['cat']:null);
    $iQuerySectionId = (isset($aRequestVars['section_id'])?$aRequestVars['section_id']:0);
    if ($sQueryCat  && $iQuerySectionId==$section_id) {
        $aktuelleKat = urldecode($sQueryCat);
        $aktuelleKat = FG_cleanCat(urldecode($sQueryCat));
    } else {
        $FG_Error['CatNotValid'] = true;
    }
// Die Kategorie ID wird erst fuer die Bilder gebraucht!
// Jedoch laesst sich so einfach feststellen ob eine Kategorie vorhanden ist
    try {
        $aktuelleKat_id = FG_getCatId($section_id, $aktuelleKat);
        $FG_Error['CatNotValid'] = false;
    } catch (Exception $e) {
//        $aktuelleKat = '';
        $aktuelleKat_id = FG_getCatId($section_id, $aktuelleKat);
        $FG_Error['CatNotValid'] = true;
    }

    if (!$aktuelleKat_id) {
        throw new \Exception ( $MOD_FOLDERGALLERY['NO_FILES_IN_CAT']);
    }

    //SQL fuer die Kategorien
    $sql = 'SELECT * FROM `'.TABLE_PREFIX.'mod_foldergallery_categories` '
        .'WHERE `section_id` ='.$section_id.' '
        .  'AND `parent` = \''.$aktuelleKat.'\' '
//        .  'AND `is_empty` = 0 '
        .  'AND `active` = 1 '
          .'ORDER BY `position`';
    if ($query = $database->query($sql)) {
        while ($ergebnis = $query->fetchRow(MYSQLI_ASSOC)) {
            if (!$ergebnis['is_empty']){
                $ergebnisse[] = $ergebnis;
            } else if ($bIsAdmin && $ergebnis['is_empty']){
                $ergebnisse[] = $ergebnis;
            }
       }
    }
/*
print '<pre  class="mod-pre rounded">function <span>'.__FUNCTION__.'( '.$aktuelleKat_id.' );</span>  filename: <span>'.basename(__FILE__).'</span>  line: '.__LINE__.' -> <br />';
print_r( $ergebnisse ); print '</pre>'; flush (); //  ob_flush();;sleep(10); die();
*/
    if (count($ergebnisse) == 0) {
        $NoCategory = $error['NoImages'] = true;
    } else {
        // Vorschaubild auswaehlen:
        switch ($catpic) {
            case 0:
                $catpicstring = 'RAND()';
                break;
            case 1:
                $catpicstring = '`position` ASC';
                break;
            case 2:
                $catpicstring = '`position` DESC';
                break;
            default :
                $catpicstring = 'RAND()';
        }
        $aErrorMsg = [];
        foreach ($ergebnisse as $key=>$ergebnis)
        {
            $catCrumb = $ergebnis['parent'].'/'.$ergebnis['categorie'];
            $catLink = $link.'?cat='.$catCrumb.'&amp;section_id='.$section_id;
            $catName = $ergebnis['cat_name'];
            $catID = $ergebnis['id'];
            $catChilds = rtrim(','.$ergebnis['childs'], ',');
            // OK, lets find a preview Image for this categorie
            #echo " Cat-ID = ".$catID."<br>";
            //search undercats of this categorie
            $sql = 'SELECT `file_name`, `id`, `parent_id` FROM '
                  .'`'.TABLE_PREFIX.'mod_foldergallery_files` '
                  .'WHERE `parent_id` ='.$catID.' '
                  .'ORDER BY '.$catpicstring.' '
                  .'';
            if (!$query = $database->query($sql)){$aErrorMsg[] = $database->get_error();}
            if ($query->numRows() == 0) {
    // Categorie itself contains no images, search for files in childs of this categorie
                $sql = 'SELECT `file_name`, `id`, `parent_id` FROM `'.TABLE_PREFIX.'mod_foldergallery_files` '
                      .'WHERE `parent_id` IN ('.$catID.$catChilds.') '
                      .'ORDER BY `parent_id` ASC, '.$catpicstring.' '
                      .'';
                if (!$query = $database->query($sql)){$aErrorMsg[] = $database->get_error();}
            }
            $imageData = $query->fetchRow(MYSQLI_ASSOC);
            $imageID = $imageData['id'];
            $imageName = $imageData['file_name'];
            $imageParentID = intval($imageData['parent_id']);
            $title = PAGE_TITLE;   // Page title of the actual page (WB Core)
            if ($imageParentID != $catID) {
                // OK, its a image of a subcat, so we need the folder of this cat
                $sql = 'SELECT `id`, `parent`, `categorie` FROM '
                      .'`'.TABLE_PREFIX.'mod_foldergallery_categories` '
                      .'WHERE `id` ='.$imageParentID.' '
                      .  'AND `active` = 1 ';
                $query = $database->query($sql);
                $result = $query->fetchRow(MYSQLI_ASSOC);
                $imageCrumb = $root_dir.$result['parent'].'/'.$result['categorie'];
            } else {
                $imageCrumb = $root_dir.$catCrumb;
            }

            if ($imageName == ''){
                $NoImages   = true;
                $image_name     = ($bIsAdmin?'unknown_img.png':'');
                $imagePath      = $sAddonThemePath.'/img/'.$image_name;
                $thumbPathDir   = ''; #$sAddonThemeUrl.'/img/';
                $thumbImagePath = $thumbPathDir.$image_name;
                $thumbImageURL  = $sAddonThemeUrl.'/img/'.$image_name;
            } else {
            // Create the thumb for a categorie
                $imagePath      = WB_PATH.$imageCrumb.'/'.$imageName;
                $thumbPathDir   = WB_PATH.$imageCrumb.$settings['thumbPath'].'/';
                $thumbImagePath = $thumbPathDir.$imageName;
                $thumbImageURL  = WB_URL.$imageCrumb.$settings['thumbPath'].'/'.$imageName;
            }

//            if (!is_file(DirectoryHandler::DecodePath($thumbImagePath))) {
            $sImagePattern = '/.*?[\/\\\\](unknown_).*?/is';
            if (!is_file(DirectoryHandler::DecodePath($thumbImagePath))&& !preg_match($sImagePattern, $thumbImagePath)) {
                FG_createThumb($imagePath, $imageName, $thumbPathDir, $settings['tbSettings']);
            }

            // Create a array for the template
            $unterKats[] = array(
                'cat_id' => $ergebnis['id'],
                'link' => $catLink,
                'thumb' => $thumbImageURL,
                'name' => $catName,
                'parent_id' => $catID
            );

        } // end of foreach $ergebnisse

            if (sizeof($aErrorMsg)){
                echo implode('<br />', $aErrorMsg);
            }
    }

// Gibt es Bilder in dieser Kategorie
    $bilder[] = [];
    $sql = 'SELECT * FROM `'.TABLE_PREFIX.'mod_foldergallery_files` '
          .'WHERE `parent_id`= '. $aktuelleKat_id.' '
          .'ORDER BY `position` ASC ';
    $query = $database->query($sql);
    while ($bild = $query->fetchRow(MYSQLI_ASSOC)) {
//        if ($bild['file_name'] == 'folderpreview.jpg'){continue;}
        $bilder[] = $bild;
    }

//  && !$NoCategory && $NoImages
    if ($bIsAdmin && !$bilder)
    {
        $aTmp = array (
              'id' => 0,
              'section_id' => $section_id,
              'active' => 1,
              'parent_id' => $aktuelleKat_id,
              'file_name' => 'unknown_img.png',
              'position' => 0,
              'caption' => '',
              'img_title' => '',
        );
        $bilder[] = $aTmp;
    }
//echo '<h1>'.$aktuelleKat_id.'</h1>';
    $title = '';
    $description = '';
    if (count($bilder) != 0)
    {
        $error = false;
        $temp = explode('/', $aktuelleKat);
        $bildkat = array_pop($temp);
        $bildparent = implode('/', $temp);
        $sql = 'SELECT * FROM `'.TABLE_PREFIX.'mod_foldergallery_categories` '
             . 'WHERE `section_id` = '.$section_id.' '
             .   'AND `categorie` = \''.$bildkat.'\' '
             .   'AND `parent` = \''.$bildparent.'\' '
             .   'AND `active` = 1 '
//             . 'LIMIT 1 '
             . '';
        $query = $database->query($sql);
        $result = $query->fetchRow(MYSQLI_ASSOC);
        $title = $result['cat_name'];
        $description = $result['description'];

        if (!empty($result['categorie'])){
            $folder = $root_dir.$result['parent'].'/'.$result['categorie'].'/';
        } else{
            $folder = $root_dir.$result['parent'].'/';
        }
        $pathToFolder = $folder;
//        $pathToThumb  = WB_PATH.$folder.$thumbDirName;
//        $urlToFolder  = $url.$folder;
        $urlToThumb   = $url.$folder.$thumbDirName.'/';
        $sql = 'SELECT `cat_name`,`description` FROM `'.TABLE_PREFIX.'mod_foldergallery_categories` '
              .'WHERE `section_id` = '.$section_id.' '
              .  'AND `id` = '.$aktuelleKat_id.' '
              .  'AND `active` = 1 ';
        $query = $database->query($sql);
        $result = $query->fetchRow(MYSQLI_ASSOC);
        $description = $result['description'];
        $title = $result['cat_name'];
    } else {
        $sql = 'SELECT `cat_name`,`description` FROM `'.TABLE_PREFIX.'mod_foldergallery_categories` '
              .'WHERE `section_id` = '.$section_id.' '
              .  'AND `id` = '.$aktuelleKat_id.' '
              .  'AND `active` = 1 ';
        $query = $database->query($sql);
        $result = $query->fetchRow(MYSQLI_ASSOC);
        $description = $result['description'];
        $title = $result['cat_name'];
    }
// Zurueck Link
    if ($aktuelleKat) {
        $temp = explode('/', $aktuelleKat);
        array_pop($temp);
        $parent = implode('/', $temp);
        $back_link = $link.'?cat='.$parent.'&amp;section_id='.$section_id;
        $hidden = '';
    } else {
        $hidden = 'style="display:none"';
        $back_link = '#';
    }

// Template
    $settings['lightbox'] = ($settings['lightbox']==''?'Colorbox':$settings['lightbox']);
    if (is_readable($sAddonTemplatePath.'/view_'.$settings['lightbox'].'.htt')) {
        $viewTemplate = 'view_'.$settings['lightbox'].'.htt';
        $t = new Template($sAddonTemplatePath, 'remove');
    } elseif (is_readable(WB_PATH.'/modules/'.$settings['lightbox'].'/foldergallery_template.htt')) {
        $viewTemplate = 'foldergallery_template.htt';
        $t = new Template(WB_PATH.'/modules/'.$settings['lightbox'], 'remove');
        $parts = explode('/', $settings['lightbox']);
        echo "[[LibInclude?lib=".$parts[0]."&amp;plugin=".$parts[2]."]]";
    } elseif (is_readable(WB_PATH.'/modules/jqueryadmin/plugins/'.$settings['lightbox'].'/foldergallery_template.htt')) {
        $viewTemplate = 'foldergallery_template.htt';
        $t = new Template(WB_PATH.'/modules/jqueryadmin/plugins/'.$settings['lightbox'], 'remove');
        echo "[[jQueryInclude?plugin=".$settings['lightbox']."]]";
    } else {
        $viewTemplate = 'view.htt';
    // --- added by WebBird, 29.07.2010 ---
        $t = new Template($sAddonTemplatePath);
    // --- end added by WebBird, 29.07.2010 ---
    }
// --- commented by WebBird, 29.07.2010 ---

    $t->halt_on_error = 'yes';
    $t->set_file('view', $viewTemplate);
    $t->set_block('view', 'CommentDoc');
    $t->clear_var('CommentDoc');
    $t->set_block('view', 'categories', 'CATEGORIES');
    $t->set_block('categories', 'show_categories', 'SHOW_CATEGORIES');
    $t->set_block('view', 'galimages', 'GALIMAGES');
    $t->set_block('galimages', 'galthumbnails', 'GALTHUMBNAILS');
    $t->set_block('galimages', 'galinvisiblePre', 'GALINVISIBLEPRE'); // Fuer weitere Bilder
    $t->set_block('galimages', 'galinvisiblePost', 'GALINVISIBLEPOST');
    $t->set_block('view', 'images', 'IMAGES');
    $t->set_block('images', 'invisiblePre', 'INVISIBLEPRE'); // Fuer weitere Bilder
    $t->set_block('images', 'invisiblePost', 'INVISIBLEPOST');
    $t->set_block('view', 'hr', 'HR');
    $t->set_block('view', 'error', 'ERROR');  // Dieser Fehler wird nicht ausgegeben, BUG
    $t->set_var('WB_URL', WB_URL);

    $t->set_var($aTplDefaults);

    $t->set_var('PAGINATIONCSS', $sPaginationStyle);

// As the error reporting is not implemented in the frontend, set error to false
    $error = false;
    if ($error) {
        $t->set_var('FRONT_END_ERROR_STRING', $MOD_FOLDERGALLERY['FRONT_END_ERROR']);
        $t->parse('ERROR', 'error');
    } else {
        $t->clear_var('error');
    }

    $t->set_var(array(
        'VIEW_TITLE' => $title,
        'BACK_LINK' => $back_link,
        'BACK_STRING' => $MOD_FOLDERGALLERY['BACK_STRING'],
        'HIDDEN' => $hidden,
    ));

    if ($aktuelleKat != '')
    {
        $separator = ' » ';
        $t->parse('SHOW_BREADCRUMB', 'show_breadcrumb', true);
        $path = explode('/', $aktuelleKat);
        $breadcrumb = '<ul class="fg_pages_nav">'
               .'<li><a href="'
               .$link
               .'"  class="catbacklink">'
               .$MOD_FOLDERGALLERY['BACK_STRING']
               .'</a>'.$separator.'</li>';
        // first element is empty as the string begins with /
        array_shift($path);
        $iLast = sizeof($path)-1;
        foreach ($path as $i => $cat_name) {
           $sql = 'SELECT `cat_name`, `id` FROM `'.TABLE_PREFIX.'mod_foldergallery_categories` '
                 .'WHERE `categorie` = \''.$cat_name.'\' '
                 .  'AND  `section_id` = '.$section_id.' '
                 .  'AND `active` = 1 ';
           if($catres = $database->query($sql)) {
            $cat = $catres->fetchRow(MYSQL_ASSOC);
            $sAktKatName = ucfirst($cat['cat_name']);
            if (($sAktKatName != '')) {
//                $cat = $catres->fetchRow(MYSQL_ASSOC);
                $breadcrumb .= '<li> '.(($i!=$iLast) ? '<a href="'
                   .$link
//                   .'?cat='.$cat['id']
                   .'?cat=/'.implode('/', array_slice($path, 0, ($i + 1))).'&amp;section_id='.$section_id
                   .'" class="catlink">'.$sAktKatName.'</a>'.$separator.'':$sAktKatName).'</li>';
            }else{
                $breadcrumb .= '';
                }
            }
        }
        $breadcrumb .= '</ul>';
        $t->set_var('CATBREAD', $breadcrumb);
        $t->set_var('CATEGORIES_TITLE', $sAktKatName);
    }

#############################################################################
##                               show categories                           ##
#############################################################################
// Kategorien anzeigen

    $t->set_block('view', 'show_galnav_block', 'show_galnav');

    $t->set_block('show_galnav_block', 'list_galnav_block', 'list_galnav');
    if ($unterKats)
    {
        $anzahlGal = count($unterKats);
        $current_gal = 1;
        $settings['gal_pp'] = (($settings['gal_pp']<=0)? $anzahlGal+1 :$settings['gal_pp']);
        if ($anzahlGal > $settings['gal_pp'])
        {
            $galleries = ceil($anzahlGal / $settings['gal_pp']);
            if (isset($aRequestVars['g']) && is_numeric($aRequestVars['g']))
            {
                if ($aRequestVars['g'] <= $galleries) {
                    $current_gal = $aRequestVars['g'];
                } else {
                    $current_gal = 1;
                }
            } else {
                $current_gal = 1;
            }
            $gal_navi = '';//
            for ($i = 1; $i <= $galleries; $i++)
            {
                $gal_navi_query = '';
                $gal_navi_current = '';
                //erzeugt nur ein ?cat wenn auch $aktuelleKat nicht leer ist verhindert notice Warunung
                if (empty($aktuelleKat)){
//                    $gal_navi .= '<li><a href='.$link.'?g='.$i.' ';
                }else{
//                    $gal_navi .= '<li><a href='.$link.'&cat='.$aktuelleKat.'&g='.$i.' ';
                    $gal_navi_query = '&cat='.$aktuelleKat;
                }
                if (isset($aRequestVars['g']) && $aRequestVars['g'] == $i) {
//                    $gal_navi .= ' class="current"';
                    $gal_navi_current = 'current';
                }
//                $gal_navi .= '>'.$i.'</a></li>';
//            }
                $t->set_var(
                        array(
                            'GAL_NAV_LINK' => $link,
                            'GAL_NAV_ID' => $i,
                            'GAL_NAV_QUERY' => $gal_navi_query,
                            'GALE_NAV_CURRENT' => $gal_navi_current,
                            )
                        );
              $t->parse('list_galnav', 'list_galnav_block', true);
            }
            $t->set_var('GAL', $MOD_FOLDERGALLERY['GAL']);
            $t->parse('show_galnav', 'show_galnav_block');
        } else {
            $t->clear_var('galnav');
        }
        $offset1 = ( $settings['gal_pp'] * $current_gal - $settings['gal_pp'] );
        for ($ix = 0; $ix < $anzahlGal; $ix++)
        {
            $sql = 'SELECT count(*) FROM `'.TABLE_PREFIX .'mod_foldergallery_categories` '
                  .'WHERE `parent_id` = '.$unterKats[$ix]['parent_id'].' '
                  .  'AND `active` = 1 ';
            $query = $database->query($sql);
                if ($result = $query->fetchRow(MYSQLI_BOTH)) {
                    $subkatcount[$ix] = $result[0];
                } else {
                    $subkatcount[$ix] = 0;
                };
            $sql = 'SELECT count(*) `count` FROM `'.TABLE_PREFIX .'mod_foldergallery_files` '
                  .'WHERE `parent_id` = '.$unterKats[$ix]['parent_id'].' '
                  .  'AND `active` = 1 ';
            $oFiles = $database->query($sql);
            if ($aFiles = $oFiles->fetchRow(MYSQLI_ASSOC)) {
                if($aFiles['count'] > 0){
                    $katcount[$ix] = $aFiles['count'];
                }else{
                    $katcount[$ix] = '';
                }
            } else {
                $katcount[$ix] = '';
            };

/*
*/
            if($katcount[$ix] == "1") {
                        $katcount[$ix].= " ".$MOD_FOLDERGALLERY['1PICTURE'];
            } else if($katcount[$ix] == "2") {
                $katcount[$ix].= " ".$MOD_FOLDERGALLERY['2PICTURES'];
            } else if($katcount[$ix] == "3") {
                $katcount[$ix].= " ".$MOD_FOLDERGALLERY['3PICTURES'];
            } else if($katcount[$ix] == "4") {
                $katcount[$ix].= " ".$MOD_FOLDERGALLERY['3PICTURES'];
            }else{
                if ($katcount[$ix] === '' && $subkatcount[$ix] > 0){
                    if($subkatcount[$ix] == 1){
                        $katcount[$ix].= $subkatcount[$ix]." ".$MOD_FOLDERGALLERY['CATEGORIE'];
                    }else{
                        $katcount[$ix].= $subkatcount[$ix]." ".$MOD_FOLDERGALLERY['CATEGORIES'];}
                }else{
                    $katcount[$ix].= " ".$MOD_FOLDERGALLERY['PICTURES'];
                }
            };
/*
print '<pre  class="mod-pre rounded">function <span>'.__FUNCTION__.'( '.$iRatio.' );</span>  filename: <span>'.basename(__FILE__).'</span>  line: '.__LINE__.' -> <br />';
print_r( $bilder ); print '</pre>'; flush (); //  ob_flush();;sleep(10); die();
*/
            $sCatGalleryLink = $unterKats[$ix]['link'];
            $sCatThumbUrl = (!is_dir($unterKats[$ix]['thumb'])? $unterKats[$ix]['thumb']:'');
            $sCatName = $unterKats[$ix]['name'];
            #$t->set_var('CATEGORIES_TITLE', $MOD_FOLDERGALLERY['CATEGORIES_TITLE']);
            $isEmpty = ((intval($katcount[$ix])==0)?true:$isEmpty);
            $bIsThumbLink = ($sCatThumbUrl);
            if ($isEmpty &&!$bIsAdmin){continue;}
            $aCategorie = array(
                'CAT_LINK' => $sCatGalleryLink,
                'THUMB_LINK' => $sCatThumbUrl.'?t='.time(),
                'CAT_CAPTION' => $sCatName,
                'CATEGORIES_TITLE' => $title,
                'CAT_DESCRIPTION' => $description,
                'CAT_COUNT' => $katcount[$ix],
                'IsEmpty' =>$isEmpty
            );
            $t->set_var($aCategorie);
/*
print '<pre  class="mod-pre rounded">function <span>'.__FUNCTION__.'( '.''.' );</span>  filename: <span>'.basename(__FILE__).'</span>  line: '.__LINE__.' -> <br />';
print_r( $aCategorie ); print '</pre>'; flush (); //  ob_flush();;sleep(10); die();
*/
            $iRatio = (float)($thumbPresets[$settings ['loadPreset']]['thumb_ratio']);
            $iRatio = (float)($iRatio>1?$iRatio:1.34);
            $catWidth  = (float)$settings['tbSettings']['image_x'];
            $catHeight = abs($catWidth * $iRatio);
            $catHeight = (float)($settings['tbSettings']['image_y']);

            $t->set_var(array(
                'CATWIDTH'  => $catWidth,
                'CATHEIGHT' => $catHeight,
                'WORDCOUNT' => str_word_count($title)*3,
            ));

            $t->parse('SHOW_CATEGORIES', 'show_categories', true);
            // Bild sichtbar oder unsichtbar?
            if ($ix < $offset1) {
                $t->parse('GALINVISIBLEPRE', 'galinvisiblePre', true);
            } elseif ($ix > ($offset1 + $settings['gal_pp'] - 1)) {
                $t->parse('GALINVISIBLEPOST', 'galinvisiblePost', true);
            } else {;
                $t->parse('GALTHUMBNAILS', 'galthumbnails', true);
            }
        } // end for
        $t->parse('GALIMAGES', 'galimages', true);
    } else {
        $t->clear_var('show_categories');
        $t->clear_var('categories');
    }

    $t->set_var('TEXT_ALIGNMENT', $settings['alignment']);

// Fertig Kategorien angezeigt
#############################################################################
##                               show images                               ##
#############################################################################

// Bilder anzeigen
    $t->set_block('images', 'thumbnails', 'THUMBNAILS');
    $t->set_block('view', 'show_pagenav_block', 'show_pagenav');
    $t->set_block('show_pagenav_block', 'list_pagenav_block', 'list_pagenav');

    if ($bilder)
    {
        $sDefaultOpacity = $settings['opacity'];
        $argba = $ColorConverter($settings['tbSettings']['image_background_color']);
        $rgba  = implode(',', $argba).','.$sDefaultOpacity;
        $aColorSettings = array(
//        'defaultOpacity' => $settings['opacity'],
        'BACKGROUND_COLOR' => $rgba,
        );
        $t->set_var($aColorSettings);

        $anzahlBilder = count($bilder);
        $settings['pics_pp'] = (($settings['pics_pp']<=0)? $anzahlBilder+1 :$settings['pics_pp']);

        if ($anzahlBilder > $settings['pics_pp']) {
            $pages = ceil($anzahlBilder / $settings['pics_pp']);
        }
        if (isset($aRequestVars['p']) && is_numeric($aRequestVars['p'])) {
            if ($aRequestVars['p'] <= $pages) {
                $current_page = $aRequestVars['p'];
            } else {
                $current_page = 1;
            }
        } else {
            $current_page = 1;
        }
        $t->set_var('CATEGORIES_TITLE', $title);
        $t->set_var('CAT_DESCRIPTION', $description);

        if (is_numeric($pages))
        {
            for ($i = 1; $i <= $pages; $i++)
            {
                $pages_navi_query = '';
                $pages_navi_current = 'pages';
                //erzeugt nur ein ?cat wenn auch $aktuelleKat nicht leer ist verhindert notice Warunung
                if (empty($aktuelleKat)){
                    $pages_navi_query = '';//  <li><a href='.$link.'?p='.$i.'
                }else{
                    $pages_navi_query = '&cat='.$aktuelleKat.'&amp;section_id='.$section_id.''; // <li><a href='.$link.'?cat='.$aktuelleKat.'&p='.$i.'
                }
                if (isset($aRequestVars['p']) && $aRequestVars['p'] == $i) {
                    $pages_navi_current = 'current';
                }
                $t->set_var(
                        array(
                            'PAGE_NAV_LINK' => $link,
                            'PAGE_NAV_ID' => $i,
                            'PAGE_NAV_QUERY' => $pages_navi_query,
                            'PAGE_NAV_CURRENT' => $pages_navi_current,
                            )
                        );
              $t->parse('list_pagenav', 'list_pagenav_block', true);
            }
            $t->set_var('PAGE', $MOD_FOLDERGALLERY['PAGE']);
            $t->parse('show_pagenav', 'show_pagenav_block');
       } else {
            $t->clear_var('pagenav');
//            $t->set_block('list_pagenav_block', '');
            $t->parse('show_pagenav_block', '');
        }
        $offset = ( $settings['pics_pp'] * $current_page - $settings['pics_pp'] );
        for ($i = 0; $i < $anzahlBilder; $i++)
        {
            if (sizeof($bilder[$i])==0) {continue;}
            $bildfilename = $bilder[$i]['file_name'];

            $urlToFolder  = $folder;
            $urlToThumb   = $url.$folder.$thumbDirName.'/';
            $pathToThumb  = WB_PATH.$folder.$thumbDirName;

            $thumb        = $pathToThumb.'/'.$bildfilename;
            $thumburl     = $urlToThumb.$bildfilename;
            $file         = WB_PATH.$pathToFolder.$bildfilename;

            if (($bilder[$i]['id']!=0) && !is_file(DirectoryHandler::DecodePath($file))) {
                $deletesql = 'DELETE FROM `'.TABLE_PREFIX.'mod_foldergallery_files` WHERE `id` ='.$bilder[$i]['id'];
                if ($database->query($deletesql)){ continue;}
            }
            $sImagePattern = '/.*?[\/\\\\](unknown_).*?/is';
            if (!is_file(DirectoryHandler::DecodePath($thumb))&& !preg_match($sImagePattern, $thumb)) {
                $file = WB_PATH.$pathToFolder.$bildfilename;
                FG_createThumb($file, $bildfilename, $pathToThumb, $settings['tbSettings']);
            }
            if ($settings['lightbox'] != 'contentFlow'){
                $timeadd = '?t='.time();
            }else{
                $timeadd = '';
            }

   // what shall we show as lightbox title/caption
            $sCaption = '';
            $sOrginalFilename = $bildfilename;
            if ($settings['imageName']){
                $bildfilename = ($bilder[$i]['img_title']!=''?$bilder[$i]['img_title'] : (intval($settings['imageName'])>0?$bildfilename:''));
                $sCaption = ($bilder[$i]['caption']!=''?$bilder[$i]['caption']:$bildfilename);
            }

            $ImgInfo = (!is_dir(WB_PATH.$pathToFolder.$sOrginalFilename) ? getimagesize(WB_PATH.$pathToFolder.$sOrginalFilename) : null);
//            'preview.php?img='.$urlToFolder.$sOrginalFilename

            $sImageFile = function ($sImage) use ($ImgInfo){
                return __DIR__.'/preview.php?img='.$sImage;  // '
            };
            $sFilename = $pathToFolder.$sOrginalFilename;
//            $sImageFilename = $sImageFile($pathToFolder.$sOrginalFilename);

            $aShowImages = [
                'ORIGINAL' => $sFilename,
                'ALT_NAME' => $sOrginalFilename,
                'THUMB'    => $thumburl.'?t='.time(),
                'CAPTION'  => $sCaption,
                'NUMBER'   => $i
            ];
            $t->set_var($aShowImages);

            $iRatio = floatval($thumbPresets[$settings ['loadPreset']]['thumb_ratio']);
            $iRatio = floatval($iRatio>0?$iRatio:1.34);
            $catWidth  = $settings['tbSettings']['image_x']; //  * 1.34
            $catHeight = abs($catWidth * $iRatio);
            $catHeight = $catHeight;
            $galWidth  = $catWidth;
            $galHeight = $galWidth * $iRatio;

            $aShowCats = [
                'CATWIDTH'  => $catWidth,
                'CATHEIGHT' => $catHeight,
                'GALWIDTH'  => $galWidth,
                'GALHEIGHT' => $galHeight,
                'WORDCOUNT' => str_word_count($title)*2,
            ];
            $t->set_var($aShowCats);

            // Bild sichtbar oder unsichtbar?
            if ($i < $offset) {
                $t->parse('INVISIBLEPRE', 'invisiblePre', true);
            } elseif ($i > ($offset + $settings['pics_pp'] - 1)) {
                $t->parse('INVISIBLEPOST', 'invisiblePost', true);
            } else {
                $t->parse('THUMBNAILS', 'thumbnails', true);
            }
        } // end for
        $t->parse('IMAGES', 'images', true);
    } else {
        $t->clear_var('thumbnails');
        $t->clear_var('images');
    }

// Kategorien
    if ($bilder && $unterKats) {
        $t->parse('HR', 'hr', true);
    } else {
        $t->clear_var('hr');
    }
//ueberschreibt die fest eingestellte Groesse von ul.categories li a auf die Thumbgroessenwerte
/*
    $catWidth  = $settings['tbSettings']['image_x'] + 10;
    $catHeight = $settings['tbSettings']['image_y'] - str_word_count($title)*3;
    $t->set_var(array(
    'CATWIDTH'  => $catWidth,
    'CATHEIGHT' => $catHeight,
    'WORDCOUNT' => str_word_count($title)*3,
    ));
*/
$t->pparse('output', 'view');

}catch (\Exception $ex) {
    $sErrMsg = xnl2br(\sprintf('<h5 class="w3-panel w3-leftbar w3-sand w3-container">%s</h5>', $ex->getMessage()));
    echo $sErrMsg;
}

