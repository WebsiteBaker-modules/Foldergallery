<?php


if (!function_exists('sanitizeListOfValues')){
    function sanitizeListOfValues($mList, $bQuote = false, $sSeperator ='|', $sDefault = '')
    {
        if (!is_array($mList)) {
            $mList = preg_split('/[\s,=+\-\;\:\.\|]+/', $mList, -1, PREG_SPLIT_NO_EMPTY);
        }
        $mList = str_replace(',', $sSeperator, $bQuote ? preg_quote(implode(',', $mList)) : implode(',', $mList));
        return $mList = $mList ?: $sDefault;
    }
}
/*--------------------------------------------------------------------------------------------------------*/
/**
 * Durchsucht einen Ordner rekursiv mit einigen Optionen
 * @return array
 * @param string $rootDir
 * @param array $allowedExtensions[optional]
 * @param array $invisibleFileNames[optional
 * @param integer $modus[optional]  0 = Files, 1 = Files/Folders, 2 = Folders
 * @param bool $rekursiv[optional] default = true
 * @param array $allData[optional]
 */

    function scanDirectoriesOld(
                  $rootDir,
                  $allowedExtensions = array(),
                  $invisibleFileNames = array(),
                  $modus = 1,
                  $rekursiv = true,
                  $allData = array()
                  ) {
    //    run through content of root directory
    //    $dirContent = scandir(DirectoryHandler::DecodePath($rootDir));
        $bScanOnlyDir = sizeof($allowedExtensions)==0;
        $aDirContent = glob(DirectoryHandler::DecodePath($rootDir).'/*', GLOB_MARK|GLOB_NOSORT);
        if (is_array($aDirContent))
        {
            $aDirContent = array_unique($aDirContent);
            foreach ($aDirContent as $content)
            {
                // UTF-8 Codierung erzwingen
                $content = DirectoryHandler::EncodePath($content);
                // filter all files not accessible
                $sKey = str_replace(WB_PATH, '', $content);
                if (!in_array(basename($content), $invisibleFileNames)) {
    //echo $content.'<br />';
                    $path = $content;
                    // if content is file & readable, add to array
                    if (is_file(DirectoryHandler::DecodePath($path)) && is_readable(DirectoryHandler::DecodePath($path))) {
                        $content_chunks = explode(".", $content);
                        $ext = $content_chunks[count($content_chunks) - 1];
                        $ext = strtolower($ext);
                        if (empty($path)){continue;}
                        // only include files with desired extensions
                        if (in_array($ext, $allowedExtensions)) {
                            // save file name with path
                            if ($modus < 2) {
                                $allData[] = $path;//dirname($sKey)
                            }
                        }
                        // if content is a directory and readable, add path and name
                    } elseif (is_dir(DirectoryHandler::DecodePath($path)) && is_readable(DirectoryHandler::DecodePath($path))) {
                        if ($modus > 0) {
                            $allData[] = $path;
                        }
                        // recursive callback to open new directory
                        if ($rekursiv) {
                            $allData = scanDirectoriesOld($path, $allowedExtensions, $invisibleFileNames, $modus, $rekursiv, $allData);
                        }
                    }
                }
            }
        } else {
          $allData = array();
        }
        return $allData;
    }

    function scanDirectories(
                  $rootDir,
                  $aAllowedExtensions = array(),
                  $aInvisibleFileNames = array(),
                  $modus = 1,
                  $rekursiv = true
                  ) {
        $aOptionList = array();
        $sNotAllowedFolders = sanitizeListOfValues($aInvisibleFileNames, true);
        $sFolderPattern = '/.*?[\/\\\\]('.$sNotAllowedFolders.')[\/\\\\]?/is';
        $sAllowedExtension   = sanitizeListOfValues($aAllowedExtensions);
        $bScanOnlyDir        = ($sAllowedExtension!=''?false:true);
        $RENAME_FILES_ON_UPLOAD = '(ph.*?|cgi|pl|pm|exe|com|bat|pif|cmd|src|asp|aspx|js|gz|zip)';
        // Check for potentially malicious files
        $sForbiddenFileTypes  = sanitizeListOfValues($RENAME_FILES_ON_UPLOAD);
//        $sAllowedExtension   = (!$bScanOnlyDir?$sAllowedExtension:$sForbiddenFileTypes);
        $sSearchPattern = '/^.+\.('.$sAllowedExtension.')(.*)?$/is';
//        echo $sFolderPattern.' call by '.$_SERVER["SCRIPT_NAME"].'<br />';
//        echo (!$bScanOnlyDir?$sSearchPattern:'').'<br /><br />';
        $oDirIterator = scanDirTree($rootDir, $sSearchPattern);
        foreach($oDirIterator as $aFileInfo) {
            $sFilename = basename($aFileInfo->getFilename());
            $sAbsPath  = $aFileInfo->getPathName();
            $result = preg_match($sFolderPattern, $sAbsPath, $aMatch);
            if ($result) { continue; }
            if ($aFileInfo->isReadable()) {
                if (!$bScanOnlyDir){
                    if ($aFileInfo->isFile ()) {
                        $sExtension = $aFileInfo->getExtension();
                        if (!preg_match($sSearchPattern, $sAbsPath, $aMatch)) { continue; }
                            switch ($modus):
                                case 0:
                                    $aOptionList[] =  basename($aFileInfo->getFilename());
                                    break;
                                default:
                                    $aOptionList[] = $sAbsPath;
                            endswitch;
                    }
              }
              if ($aFileInfo->isDir ()) {
                  if ($modus > 0) {
                      $aOptionList[] = $sAbsPath;
                  }
              }
          }
        } // end of foreach
        return $aOptionList;
    }


    /**
     * scanDirTree()
     *
     * @param string $sAbsPath
     * @param string $regPattern
     * @return
     */
    function scanDirTree(
                  $sAbsPath,
                  $regPattern='/^.+\.(.*)?$/i'
    ){
        $Directory  = new RecursiveDirectoryIterator(
                          $sAbsPath,
                          FilesystemIterator::CURRENT_AS_SELF|
                          FilesystemIterator::FOLLOW_SYMLINKS|
                          FilesystemIterator::KEY_AS_PATHNAME|
                          FilesystemIterator::SKIP_DOTS|
                          FilesystemIterator::UNIX_PATHS
                      );
        $Iterator   = new RecursiveIteratorIterator(
                          $Directory,
                          RecursiveIteratorIterator::SELF_FIRST,
                          RecursiveIteratorIterator::CATCH_GET_CHILD
                      );
/*
        $regexIterator = new RegexIterator(
                          $Iterator, $regPattern,
                          RecursiveRegexIterator::GET_MATCH
                        );
*/
        return $Iterator;
    }


    /**
     * getFolderStructure()
     *
     * @param mixed $rootDir
     * @param mixed $aAllowedExtensions
     * @param mixed $aInvisibleFileNames
     * @param integer $modus
     * @param bool $rekursiv
     * @return
     */
    function getFolderStructure(
                  $rootDir,
                  $aAllowedExtensions = array(),
                  $aExcludedFiles = array(),
                  $modus = 1,
                  $rekursiv = true
        ){
        global $oAddonReg;

        $oReg = $oAddonReg->oReg;
        $aRecords = $oAddonReg->Records;

        $sBaseMediaUrl  = $oReg->AppUrl.$oReg->MediaDir;
        $sBaseMediaPath = $oReg->AppPath.$oReg->MediaDir;

        $iterator = scanDirTree($sBaseMediaPath);

        $sNotAllowedFolders = sanitizeListOfValues($aExcludedFiles, true);
        $sFolderPattern = '/.*?[\/\\\\]('.$sNotAllowedFolders.')[\/\\\\]?/is';

        foreach ($iterator as $key =>$aFileInfo) {
            // Only consume files of interest.
            $sFilename = basename($aFileInfo->getFilename());
            if ($aFileInfo->isFile()){continue;}
            $sAbsPath  = $aFileInfo->getPathName();
            $result = preg_match($sFolderPattern, $sAbsPath, $aMatch);
            if ($result) { continue; }
            $key = '/'.trim(str_replace($sBaseMediaPath,'',$key),'/');
            $pathFile = $aFileInfo->getFilename();
            $level = substr_count($key, '/');
            switch ($level):
              case 0:
                      $sIndexFile = str_repeat('', $level).$pathFile;
                break;
              default:
                      $sIndexFile = str_repeat(' - - ', $level-1).$pathFile;
                break;
            endswitch;
            $aOptionList[$key]['level'] = (($level <= 10 ) ? $level : 0 );
            $aOptionList[$key]['name'] = trim($sIndexFile,'/');
            $aOptionList[$key]['selected'] = ( ( $aRecords['root_dir'] == $key) ? true : false);
        }

        $aDirs[trim($oReg->MediaDir, '/')] = $aOptionList;
        $i = 1;
        $aDirs['Settings']  = $aRecords;

        $iterator = new DirectoryIterator( $oAddonReg->AddonThemePath.'' );
        foreach ($iterator as $fileinfo) {
            if ( $fileinfo->isDot() || basename($fileinfo->getPathname()) == $aRecords['thumbPath']  ) { continue; }
            if ( $fileinfo->isDir() ) {
                $galleryStyle = basename($fileinfo->getPathname());
                $aDirs['galleryStyle'][] = array ('value'=>$i, 'name'=>$galleryStyle, 'selected'=>$aRecords['galleryStyle']);
                $i++;
            }
        }

        return $aDirs;
    }

/**
print '<pre  class="mod-pre rounded">function <span>'.__FUNCTION__.'( '.''.' );</span>  filename: <span>'.basename(__FILE__).'</span>  line: '.__LINE__.' -> <br />';
print_r( $aOptionList ); print '</pre>'; flush (); //  ob_flush();;sleep(10); die();
 *
 * @return
 * @param string $rootDir
 * @param array $aAllowedExtensions
 * @param array $aInvisibleFileNames
 * @param integer $modus[optional]
 * @param bool $rekursiv[optional]
 */
    function getFolderData(
                  $rootDir,
                  $aAllowedExtensions,
                  $aInvisibleFileNames,
                  $modus = 1,
                  $rekursiv = true
        ){
        global $oAddonReg;
        $aFiles = array();
        clearstatcache();
        if (!sizeof($aAllowedExtensions)){
            $aFiles = getFolderStructure(
                      $rootDir,
                      $aAllowedExtensions,
                      $aInvisibleFileNames,
                      $modus,
                      $rekursiv );
        } else {
            $aFiles = scanDirectories(
                      $rootDir,
                      $aAllowedExtensions,
                      $aInvisibleFileNames,
                      $modus,
                      $rekursiv );
            if (is_array($aFiles) && sizeof($aFiles)) {
                array_walk(
                    $aFiles,
                    function (&$sFile) use ($rootDir){
                        $sFile = str_replace($rootDir, '', $sFile);
                   }
                );
            }
        }
        return $aFiles;
    }

/**
 * Löscht das angegeben Verzeichnis und alle darin enthaltenen Unterverzeichnisse,
 * sowie die darin enthaltenen Files
 * @return int Fehlernummer
 * @param string $path Pfad zum Ornder
 */
function deleteFolder($path) {
    // schau' nach, ob das ueberhaupt ein Verzeichnis ist
    if (!is_dir($path)) {
        return -1;
    }
    // oeffne das Verzeichnis
    $dir = @opendir($path);

    // Fehler?
    if (!$dir) {
        return -2;
    }

    // gehe durch das Verzeichnis
    while (($entry = @readdir($dir)) !== false) {
        // wenn der Eintrag das aktuelle Verzeichnis oder das Elternverzeichnis
        // ist, ignoriere es
        if ($entry == '.' || $entry == '..')
            continue;
        // wenn der Eintrag ein Verzeichnis ist, dann
        if (is_dir($path.'/'.$entry)) {
            // rufe mich selbst auf
            $res = deleteFolder($path.'/'.$entry);
            // wenn ein Fehler aufgetreten ist
            if ($res == -1) { // dies duerfte gar nicht passieren
                @closedir($dir); // Verzeichnis schliessen
                return -2; // normalen Fehler melden
            } else if ($res == -2) { // Fehler?
                @closedir($dir); // Verzeichnis schliessen
                return -2; // Fehler weitergeben
            } else if ($res == -3) { // nicht unterstuetzer Dateityp?
                @closedir($dir); // Verzeichnis schliessen
                return -3; // Fehler weitergeben
            } else if ($res != 0) { // das duerfe auch nicht passieren...
                @closedir($dir); // Verzeichnis schliessen
                return -2; // Fehler zurueck
            }
        } else if (is_file($path.'/'.$entry) || is_link($path.'/'.$entry)) {
            // ansonsten loesche diese Datei / diesen Link
            $res = @unlink($path.'/'.$entry);
// Fehler?
            if (!$res) {
                @closedir($dir); // Verzeichnis schliessen
                return -2; // melde ihn
            }
        } else {
            // ein nicht unterstuetzer Dateityp
            @closedir($dir); // Verzeichnis schliessen
            return -3; // tut mir schrecklich leid...
        }
    }
// schliesse nun das Verzeichnis
    @closedir($dir);
// versuche nun, das Verzeichnis zu loeschen
    $res = @rmdir($path);
// gab's einen Fehler?
    if (!$res) {
        return -2; // melde ihn
    }
// alles ok
    return 0;
}

/**
 * Einfache Funktion zum ein File loeschen
 * @return bool true wenn alles gut ging, sonst false
 * @param string $path pfad zum files
 */
function deleteFile($path) {
    if (is_file(DirectoryHandler::DecodePath($path))) {
        unlink(DirectoryHandler::DecodePath($path));
        return true;
    } else {
        return false;
    }
}

function getCategories($galerie, $searchCategorie = '', $modus = 1, $rekursiv = true)
{
    global $database;
//    global $invisibleFileNames;
    global $url;
    global $path;
    global $MediaRel;
    global $thumbPath;
/*--------------------------------------------------------------------------------------*/
    $convertToCategory = function ($sList)
    {
      if (is_array($sList)){
          return $sList;
      }
//    return preg_split('/[\s,=+\;\:\.\/\|]+/', $sList, -1, PREG_SPLIT_NO_EMPTY);
      return preg_split('/[,=+\;\:\/\|]+/', $sList, -1, PREG_SPLIT_NO_EMPTY);
    };

    $convertToArray = function ($sList)
    {
      if (is_array($sList)){
          return $sList;
      }
      return preg_split('/[\s,=+\;\:\.\|]+/', $sList, -1, PREG_SPLIT_NO_EMPTY);
    };
/*--------------------------------------------------------------------------------------*/
    // Daten Vorbereiten
    $rootDir = $path.$galerie['root_dir'];
    $searchFolder = $rootDir . $searchCategorie;
    // to allow full scan folder and files , let $aAllowedExtensions empty if you only want folders
    $aAllowedExtensions = $convertToArray($galerie['extensions']);
    $invisibleFileNames = $convertToCategory($galerie['invisible']);
    //Alle Angaben aus dem Filesystem holen
    $aFolders = getFolderData($searchFolder, $aAllowedExtensions, $invisibleFileNames);
    //Angaben auswerten
    $categories = array();
    $aFilesInDir = array();
    $files = $aFiles = array();
    foreach ($aFolders as $sFolder)
    {
        $einzelteile = explode('/', $sFolder);
        $letztesElement = count($einzelteile) - 1;
        $sFileName = $rootDir.$sFolder;
//        if (substr_count($einzelteile[$letztesElement], '.') == 0)
        if (is_dir($sFileName))
        {
            //Hier werden alle Kategorien angelegt
//            $catName = $einzelteile[$letztesElement];
//            unset($einzelteile[$letztesElement]);
            $catName = array_pop($einzelteile);
            $catParents = implode('/', $einzelteile);
            $sAllowedExtensions   = sanitizeListOfValues($aAllowedExtensions);
            $catParents = $searchCategorie.str_replace(WB_PATH.$MediaRel, '',$catParents);//$path
//            $catParents  = ($catParents==''?'Root':$catParents);
            if ($catParents!=''){
                $aFilesInDir = glob ($path.$catParents.'/*.{'.$sAllowedExtensions.'}', GLOB_BRACE);
/*
                $categories[$catParents]['is_empty'] = sizeof($aFilesInDir)>0?0:1;
                $categories[$catParents]['hasFiles'] = sizeof($aFilesInDir);
*/
//                $parent = $searchCategorie.str_replace($path, '',$catParents);
//                $fileLink = $parent."/".$fileName;
//               $fileLink = str_replace(WB_URL, '', $fileLink);
                  if (is_array($aFilesInDir) && sizeof($aFilesInDir)){
                      array_walk(
                          $aFilesInDir,
                          function (&$sFile) use($path) {
                              $sFile = basename($sFile);
                          }
                      );
                  }
//                $categories[$catParents]['Files'] = $aFilesInDir;
//                $aAllFiles[$parent][] = $aFilesInDir;
            }
            $categories[] = array(
                'categorie' => $catName,
                'parent'    => $catParents,
                'is_empty'  => (sizeof($aFilesInDir)>0?0:1),
                'hasFiles'  => sizeof($aFilesInDir),
                'Files'     => $aFilesInDir
            );
        } elseif (is_file($sFileName)) {
            //Hier gehts um die Files
            $fileName = array_pop($einzelteile);
            //if ($fileName == 'folderpreview.jpg') continue;
//            unset($einzelteile[$letztesElement]);
            $parent = implode('/', $einzelteile);
            $parent = $searchCategorie.str_replace(WB_PATH.$MediaRel, '',$parent);//$path
            $fileLink = $parent."/".$fileName;
//            $fileLink = str_replace(WB_URL, '', $fileLink);
            $files[] = array(
                'file_name' => $fileName,
                'file_link' => $fileLink,
                'parent'    => $parent,
            );
        }
    }
    return array ('cat'=>$categories, 'file'=>$files);
}

/**
 * Syncronisiert eine gesamte Bildergalerie, loescht alte Eintraege oder erstellt neu in der DB
 * Dabei wird das Dateisystem als Grundlage genommen.
 * @return bool true = sucsess
 * @param array        $galerie  Einstellungen dieser Galerie
 * @param string $categorie ab welchem ordner gescannt werden soll, relativ zum Stammordner
 * @param integer $modus[optional] 0,1,2 Modus
 * @param bool $rekursiv[optional] soll rekursiv gesucht werden
 *
 * @see scanDirectories()
 * @see deleteFolder()
 */
function syncDB($galerie, $searchCategorie = '', $modus = 1, $rekursiv = true) {

    // Auf diese Variablen muss zugegriffen werden
    global $database;
//    global $invisibleFileNames;
    global $url;
    global $path;
    global $thumbPath;
    global $section_id;

/*--------------------------------------------------------------------------------------*/
    $convertToCategory = function ($sList)
    {
      if (is_array($sList)){
          return $sList;
      }
//    return preg_split('/[\s,=+\;\:\.\/\|]+/', $sList, -1, PREG_SPLIT_NO_EMPTY);
      return preg_split('/[,=+\;\:\/\|]+/', $sList, -1, PREG_SPLIT_NO_EMPTY);
    };

    $convertToArray = function ($sList)
    {
      if (is_array($sList)){
          return $sList;
      }
      return preg_split('/[\s,=+\;\:\.\|]+/', $sList, -1, PREG_SPLIT_NO_EMPTY);
    };
/*--------------------------------------------------------------------------------------*/

    // Daten Vorbereiten
    $rootDir = $path.$galerie['root_dir'];
    $searchFolder = $rootDir.$searchCategorie;
    $extensions = explode(',', $galerie['extensions']);
    $invisibleFileNames = $convertToCategory($galerie['invisible']);

    $iSectionId = $galerie['section_id'];
    $invisible = array_merge($invisibleFileNames);

    //natsort($allData); # ! Bringt es das?
    //Angaben auswerten
    $categories = array();
    $files = array();
    $aFilesDir = getCategories($galerie, $searchCategorie , $modus, $rekursiv);
    $files = $aFilesDir['file'];
    $categories = $aFilesDir['cat'];

/*
print '<pre  class="mod-pre rounded">function <span>'.__FUNCTION__.'( '.''.' );</span>  filename: <span>'.basename(__FILE__).'</span>  line: '.__LINE__.' -> <br />';
print_r( $categories ); print '</pre>'; flush (); //  ob_flush();;sleep(10); die();
    //Alle Angaben aus dem Filesystem holen
    $allData = getFolderData($searchFolder, $extensions, $invisible);
    foreach ($allData as $data) {
        $einzelteile = explode('/', $data);
        $letztesElement = count($einzelteile) - 1;
//        if (substr_count($einzelteile[$letztesElement], '.') == 0)
        if (is_dir(WB_PATH.MEDIA_DIRECTORY.$data))
        {
*/
/*
            //Hier werden alle Kategorien angelegt
            $catName = $einzelteile[$letztesElement];
            unset($einzelteile[$letztesElement]);
*/
/*
            $catName = array_pop($einzelteile);
            $catParents = implode('/', $einzelteile);
            $catParents = $searchCategorie.$catParents;
            $categories[] = array(
                'categorie' => $catName,
                'parent' => $catParents,
                'is_empty' => 1
            );
        } else {
*/
/*
            //Hier gehts um die Files
            $fileName = $einzelteile[$letztesElement];
            //if ($fileName == 'folderpreview.jpg') continue;
            unset($einzelteile[$letztesElement]);
*/
/*
            $fileName = array_pop($einzelteile);
            $parent = implode('/', $einzelteile);
            $parent = $searchCategorie.$parent;
            $fileLink = $url.MEDIA_DIRECTORY.$galerie['root_dir'].$parent."/".$fileName;
            $fileLink = str_replace(WB_URL, '', $fileLink);
            $files[] = array(
                'section_id' => $iSectionId,
                'file_name' => $fileName,
                'file_link' => $fileLink,
                'parent' => $parent
            );
        }
    }
*/

    // Kategorien mit Bildern finden
    foreach ($categories as & $nameCat) {
        $catString = $nameCat['parent']."/".$nameCat['categorie'];
//        echo $catString.'<br />';
        foreach ($files as $file) {
            if ($file['parent'] == $catString) {
                $nameCat['is_empty'] = 0;
                break;
            }
        }
    }

    // Falls Parents, diese finden
    foreach ($categories as & $nameCat) {
        $catName = $nameCat['categorie'];
        foreach ($categories as $searchCat) {
            if ((strpos($searchCat['parent'], $catName) !== false) && (!$searchCat['is_empty'])) {
                $nameCat['is_empty'] = 0;
                break;
            }
        }
    }

    // Kategorien mit DB synchronisieren
    // Neuer SQL vorbereiten
    $notDeleteArray = array();
    $insertSQL = '';
    $insertLaenge = strlen($insertSQL);
    $deleteSQL = 'DELETE FROM `'.TABLE_PREFIX.'mod_foldergallery_categories` WHERE `parent_id` > 0 AND `section_id` = '.$galerie['section_id'];
    $deleteLaenge = strlen($deleteSQL);
    foreach ($categories as $cat)
    {
        $whereSQL  = ''
                   .'WHERE `section_id` = '.$galerie['section_id'].' '
                   .  'AND `parent` = \''.$cat['parent'].'\' '
                   .  'AND `categorie` = \''.$cat['categorie'].'\' ';
        $sql  = 'SELECT * FROM `'.TABLE_PREFIX.'mod_foldergallery_categories` ';
//               .'WHERE `section_id` = '.$galerie['section_id'].' '
//               .  'AND `parent` = \''.$cat['parent'].'\' '
//               .  'AND `categorie` = \''.$cat['categorie'].'\' ';
        if ($query = $database->query($sql.$whereSQL)){
            if ($query->numRows())
            {
                $result = $query->fetchRow(MYSQLI_ASSOC);
                $sDescription = $result['description'];
                $cat_name = str_replace('_', ' ', $result['categorie']);
                $cat_name = str_replace('-', ' ', $cat_name);
                if ($result['is_empty'] == $cat['is_empty']) {
                    $notDeleteArray[] = $result['id'];
                } //else {
                    // Falls die Kategorie schon existierte nehmen wir fuer die neuen Eintraege diejenigen von der DB
                    // Diese Datensaetze muessen aber zuerst geloescht werden, da sie sonst doppelt vorkommen wuerden!
    //                $insertSQL .= " (".$result['section_id'].", '".$result['categorie']."', '".$result['parent']."', '".$result['cat_name']."', ".$cat['is_empty'].", '".$result['description']."'),";
                $insertSQL = 'UPDATE `'.TABLE_PREFIX.'mod_foldergallery_categories` SET '
                           . '`section_id` = '.intval($galerie['section_id']).', '
                           . '`categorie` = \''.$database->escapeString($result['categorie']).'\', '
                           . '`parent` = \''.$database->escapeString($result['parent']).'\', '
                           . '`cat_name` = \''.$database->escapeString($cat_name).'\', '
                           . '`is_empty` = '.intval($result['is_empty']).', '
                           . '`description` = \''.$database->escapeString($sDescription).'\' ';
//                }
            } else {
            // Sonst erstellen wir einfach einen neuen Standarddatensatz
                $whereSQL  = '';
                $sDescription = '';
                $cat_name = str_replace('_', ' ', $cat['categorie']);
                $cat_name = str_replace('-', ' ', $cat_name);
//                $insertSQL .= " (".$galerie['section_id'].", '".$cat['categorie']."', '".$cat['parent']."', '".$cat_name."', ".$cat['is_empty'].", '".$sDescription."'),";
                $insertSQL = 'INSERT INTO `'.TABLE_PREFIX.'mod_foldergallery_categories` SET '
                           . '`section_id` = '.intval($galerie['section_id']).', '
                           . '`categorie` = \''.$database->escapeString($cat['categorie']).'\', '
                           . '`parent` = \''.$database->escapeString($cat['parent']).'\', '
                           . '`cat_name` = \''.$database->escapeString($cat_name).'\', '
                           . '`is_empty` = '.intval($cat['is_empty']).', '
                           . '`description` = \''.$database->escapeString($sDescription).'\' ';
            }
            if (!$database->query($insertSQL.$whereSQL)){
              echo __LINE__.') '.$database->get_error().'<br />'.$insertSQL.$whereSQL;
            }
        }
    }// end of foreach

    // SQL zum loeschen der alten Eintraege
    if (!empty($notDeleteArray)) {
        $deleteSQL .= ' AND (`id` NOT IN( '.implode(',', $notDeleteArray).'))';
    }
    if ($searchCategorie != '') {
        $deleteSQL.= ' AND (`parent` REGEXP("'.$searchCategorie.'"))';
    }

    if (strlen($deleteSQL) != $deleteLaenge) {
        $deleteSQL .= ';';
        $database->query($deleteSQL);
    }
/*
    if (strlen($insertSQL) != $insertLaenge) {
        // Jetzt fuegen wir die neuen Eintraege hinzu
        $insertSQL = substr($insertSQL, 0, -1).";";
    }
*/
    // So, dass waren die Kategorien, nun sind die Bilder an der Reihe
    //Die Felder "file_link" und "thumb_link" sind obsolet
    //Jetzt noch die Parents zu Ziffern umwandeln:
    //Wieder aus der Datenbank laden:
    $catpathArray = array();
    $sql  = 'SELECT `id`, `categorie`, `parent` FROM `'.TABLE_PREFIX.'mod_foldergallery_categories` '
          . 'WHERE `section_id` ='.$galerie['section_id'];
    $query = $database->query($sql);
    while ($result = $query->fetchRow(MYSQLI_ASSOC)) {
        $p = $result['parent'].'/'.$result['categorie'];
        if ($result['parent'] == -1) {
            $p = '';
        }
        $catpathArray[$p] = $result['id'];
    }
    if($database->is_error()) {
        $admin->print_error($database->get_error());
    }

    $notDeleteArray = array();
//    $insertSQL = "INSERT INTO `".TABLE_PREFIX."mod_foldergallery_files` (`file_name`, `parent_id`, `caption`) VALUES";
//    $insertSQL = "INSERT INTO `".TABLE_PREFIX."mod_foldergallery_files` (`section_id`, `file_name`, `parent_id`, `caption`) VALUES";
//    $updateSQL = "UPDATE INTO `".TABLE_PREFIX."mod_foldergallery_files` (`section_id`, `file_name`, `parent_id`, `caption`) VALUES";

    //-------------------------------------------------------------------------------------------------------
    //Das Macht ein Problem, sobald es mehrere Seiten mit FG gibt.
    //Das es keine Section_id mehr gibt, werden alle Eintraege von anderen Sections ebenfalls geloescht.
    //Das muss anders geloest werden, ich weiss aber nicht wie.
    $deleteSQL = "DELETE FROM `".TABLE_PREFIX."mod_foldergallery_files` WHERE (`id` NOT IN";
    //Siehe unten, wird derzeit nicht ausgeführt
    //-------------------------------------------------------------------------------------------------------

    $count = 0;
    foreach ($files as $file)
    {
        if (!isset($catpathArray[$file['parent']])) {
            $parent_id = 0;
//            echo $file['parent'].'<br />';
        } else {
            $parent_id = $catpathArray[$file['parent']];
        }

        $whereSQL   = 'WHERE `parent_id` = \''.$parent_id.'\' '
                    .   'AND `file_name` = \''.$file['file_name'].'\'';
        $sql  = 'SELECT * FROM `'.TABLE_PREFIX.'mod_foldergallery_files` '
                . $whereSQL;
//              . 'WHERE `parent_id` ="'.$parent_id.'" AND `file_name` ="'.$file['file_name'].'" LIMIT 1;';

        $query = $database->query($sql);
        if ($query->numRows()>0)
        {
            $result = $query->fetchRow(MYSQLI_ASSOC);
            //$notDeleteArray[] = $result['id'];
            $insertSQL  = 'UPDATE `'.TABLE_PREFIX.'mod_foldergallery_files` SET '
             . '`section_id` = '.intval($section_id).', '
             . '`file_name` = \''.$database->escapeString($file['file_name']).'\', '
             . '`parent_id` = '.intval($parent_id).', '
             . '`caption` = \''.$database->escapeString($result['caption']).'\', '
             . '`img_title` = \''.$database->escapeString($result['img_title']).'\' ';
            $laenge     = strlen($insertSQL);
            $sType      = 'UPDATE';
//            $insertSQL .= " ('".$section_id. "', '".$file['file_name']."', '".$parent_id."', ''),";
        } else {
//            $insertSQL .= " ('".$file['file_name']."', '".$parent_id."', ''),";
//            $insertSQL = "INSERT INTO `".TABLE_PREFIX."mod_foldergallery_files` (`section_id`, `file_name`, `parent_id`, `caption`) VALUES";
            $whereSQL   = '';
            $insertSQL  = 'INSERT INTO `'.TABLE_PREFIX.'mod_foldergallery_files` SET '
             . '`section_id` = '.intval($section_id).', '
             . '`file_name`  = \''.$database->escapeString($file['file_name']).'\', '
             . '`parent_id`  = '.intval($parent_id).', '
             . '`caption`    = \''.'\', '
             . '`img_title`  = \''.'\' ';
            $laenge = strlen($insertSQL);
            $sType  = 'INSERT';
//            $insertSQL .= " ('".$section_id. "', '".$file['file_name']."', '".$parent_id."', ''),";
            //if ($count == 2) echo $insertSQL;
        }
        switch ($sType):
            case 'INSERT':
                $count++;
                $sql = $insertSQL;
                break;
            case 'UPDATE':
                $sql = $insertSQL.$whereSQL;
                break;
            default:
        endswitch;
        if ($database->query($sql)){
        }
    }// end of foreach
    if ($count){
        echo '<div class="info">'.'Added '.$count.' files in '.'</div><br />';
    }

    // SQL fuer neue Eintraege
//    $insertSQL = substr($insertSQL, 0, -1).";";
//    if ($laenge != strlen($insertSQL)) {

    delete_files_with_no_cat();
    return true;
}

function delete_files_with_no_cat() {
    global $database;
    $sql = 'SELECT `id` FROM `'.TABLE_PREFIX.'mod_foldergallery_categories` ';
    $query = $database->query($sql);
    $notDeleteArray = array();
    while ($result = $query->fetchRow(MYSQLI_ASSOC)) {
        $notDeleteArray[] = $result['id'];
    }

    if (!empty($notDeleteArray)) {
        $deleteSQL = 'DELETE FROM `'.TABLE_PREFIX.'mod_foldergallery_files` '
                   . 'WHERE `parent_id` NOT IN ';
        $deleteSQL .= '('.implode(',', $notDeleteArray).');';
        $database->query($deleteSQL);
    }
}

function rek_db_delete($cat_id) {
    global $database,$sCategorie;
    $sql  = 'SELECT `section_id`, `categorie`, `section_id`, `parent`, `has_child` FROM `'.TABLE_PREFIX.'mod_foldergallery_categories` '
          . 'WHERE `id`='.$cat_id.';';
    $query = $database->query($sql);
    if ($result = $query->fetchRow(MYSQLI_ASSOC)) {
        $parent = $result['parent'].'/'.$result['categorie'];
        $delete_file_sql = 'DELETE FROM `'.TABLE_PREFIX.'mod_foldergallery_files` '
                         . 'WHERE `parent_id`="'.$cat_id.'";';
        $database->query($delete_file_sql);
        if ($result['has_child']) {
            $select_sql = 'SELECT `id` FROM `'.TABLE_PREFIX.'mod_foldergallery_categories` '
                        . 'WHERE `parent_id` = '.$cat_id.';';
            $query = $database->query($select_sql);
            while ($select_result = $query->fetchRow(MYSQLI_ASSOC)) {
                rek_db_delete($select_result['id']);
            }
        }
    }

    $deletesql  = 'DELETE FROM `'.TABLE_PREFIX.'mod_foldergallery_categories` '
                . 'WHERE `id`='.$cat_id;
    if ($database->query($deletesql)){
        $sFilenameToDelete = WB_PATH.$sCategorie.$parent;
        if (is_writable($sFilenameToDelete)){
            rm_full_dir($sFilenameToDelete);
//            echo $sFilenameToDelete.'<br />';
        }
    }
}

function _rek_db_delete($cat_id)
{
    global $database;
print '<pre  class="mod-pre rounded">function <span>'.__FUNCTION__.'( '.$sCategorie.' );</span>  filename: <span>'.basename(__FILE__).'</span>  line: '.__LINE__.' -> <br />';
print_r( $sFilenameToDelete ); print '</pre>'; flush (); //  ob_flush();;sleep(10); die();
    $aErrorMsg = array();
    $sql  = 'SELECT * FROM `'.TABLE_PREFIX.'mod_foldergallery_categories` '
          . 'WHERE `id`='.$cat_id.';';
    if ($oCat = $database->query($sql)) {
        if ($aCat = $oCat->fetchRow(MYSQLI_ASSOC)) {
            if ($aCat['childs']!='') {
              $aChilds = sanitizeListOfValues($aCat['childs']);
            }
        }
    } else {
      $aErrorMsg[] = $sql;
      $aErrorMsg[] = $database->get_error();
    }

}

/**
 * This function is used to get the advanced thumbsettings as a string
 * to display them in the Config view.
 *
 * @param array $tbSettings The array with all the Thumbsettings
 */
function FG_getAdvancedThumbSettings($tbSettings) {
    $s = '';
    foreach ($tbSettings as $key => $value) {
        // filter default settings
        if ($key == 'image_x'
                || $key == 'image_y'
                || $key == 'image_resize'
                || $key == 'image_ratio_fill'
                || $key == 'image_background_color'
                || $key == 'image_ratio_crop') {
            continue;
        }
        // convert booleans
        if (is_bool($value)) {
            if ($value == true) {
                $s .= $key."=true;\n";
            } else {
                $s .= $key."=false;\n";
            }
        } else if (is_int($value) || is_float($value)) {
            $s .= $key."=".$value.";\n";
        } else {
            $s .= $key."='".$value."';\n";
        }
    }
    return $s;
}

function FG_setAdvancedThumbSettings($advancedString) {
    $advancedString = preg_replace('/\r\n|\r/', "\n", trim($advancedString));
    $advancedString = preg_replace("/ |'|;/", '', $advancedString);
    $advancedArray = explode("\n", $advancedString);
    $returnArray = array();
    foreach ($advancedArray as $value) {
        $tmp = explode('=', $value);
        // skip if key/value is ''
        if ($tmp[0] == '' || $tmp[1] == '') {
            continue;
        }
        // Check if it's a bool variable
        if ($tmp[1] == 'true' || $tmp[1] == 'True' || $tmp[1] == 'TRUE') {
            $tmp[1] = true;
        } else if ($tmp[1] == 'false' || $tmp[1] == 'False' || $tmp[1] == 'FALSE') {
            $tmp[1] = false;
        } else if (is_numeric($tmp[1])) {
            // is it a integer or a float?
            if (ctype_digit($tmp[1])) {
                $tmp[1] = (int) $tmp[1];
            } else {
                $tmp[1] = (float) $tmp[1];
            }
        }
        $returnArray[$tmp[0]] = $tmp[1];
    }
    return $returnArray;
}

