<?php
/**
  This module is free software. You can redistribute it and/or modify it under
  the terms of the GNU General Public License - version 2 or later, as published
  by the Free Software Foundation: http://www.gnu.org/licenses/gpl.html.

  This module is distributed in the hope that it will be useful, but
  WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

 -------------------------------------------------------------------------------
  Modul: foldergallery fuer Website Baker v2.8.3 SP7 (http://websitebaker.org)
  Modulbeschreibung: Eine einfache Bildergalerie erstellen anhand der
  Ordnerstruktur auf dem Server. Im Backend kann zu jedem Bild/jeder Kategorie
  eine Beschreibung angegeben werden.

 -------------------------------------------------------------------------------
 * The Changelog of this Module can be found in the README.markdown file!
 -------------------------------------------------------------------------------
**/
// only included in init.php may be needed as global if script is called within a function
global  $sAppPath,
        $sAddonPath,
        $sAddonUrl,
        $sAddonName,
        $sAddonRel,
        $aExtraFields;
/* -------------------------------------------------------- */
// folderstructure steps to modules path
$sAppPath = dirname(dirname(__DIR__));$iSteps = 2;
// load config created for modify or save module files if WB_PATH don't exist, '
if ( !defined( 'WB_PATH' ) ){require($sAppPath.'/config.php');}
// Must include code to prevent this file from being accessed directly
if (defined('WB_PATH') == false) { die('Cannot access '.basename(__DIR__).'/'.basename(__FILE__).' directly'); }
/* -------------------------------------------------------- */
    switch ($iSteps):
        case 5:
            $sAddonPath = dirname(dirname(dirname(__DIR__)));
            break;
        case 4:
            $sAddonPath = dirname(dirname(__DIR__));
            break;
        case 3:
            $sAddonPath = dirname(__DIR__);
            break;
        case 2:
        default:
            $sAddonPath = __DIR__;
    endswitch;

    // needed for simple Dispatcher
    $sAddonName = basename($sAddonPath);
    // An associative array that by default contains the contents of $_GET, $_POST and $_COOKIE.
    $aRequestVars = $_REQUEST;
    if (is_readable($sAddonPath.'/Helper.inc')) {require($sAddonPath.'/Helper.inc');}
//    if (isset($sAddonBaseDir)) { exit; }

    $sCallingScript = $_SERVER['SCRIPT_NAME'];

/*
    if (!isset($aDefaults)&& is_readable($sAddonPath.'/presets/defaults.php')){require($sAddonPath.'/presets/defaults.php');}
print '<pre  class="mod-pre rounded">function <span>'.__FUNCTION__.'( '.''.' );</span>  filename: <span>'.basename(__FILE__).'</span>  line: '.__LINE__.' -> <br />';
print_r( $sAddonBaseDir ); print '</pre>'; flush (); //  ob_flush();;sleep(10); die();
    $AcpPath    = $sAppPath.'/'.ADMIN_DIRECTORY;
    $AppUrl     = WB_URL;
    $AcpUrl     = $AppUrl.'/'.ADMIN_DIRECTORY;
    $sAddonRel  = '/modules/'.$sAddonName;
    $sAddonUrl  = $AppUrl.$sAddonRel;
*/

/*--------------------------------------------------------------------------------------------------------*/
    $unixPath = (function ($string){
      return str_replace('\\', '/', $string);
    });
/*--------------------------------------------------------------------------------------------------------*/

/*
// include the default language, will now be set by Helper.inc
if (is_readable($sAddonPath.'/languages/EN.php')) {require($sAddonPath.'/languages/EN.php');}
// check if module language file exists for the language set by the user (e.g. DE, EN)
if (is_readable($sAddonPath.'/languages/'.LANGUAGE.'.php')) {require($sAddonPath.'/languages/'.LANGUAGE.'.php');}
*/
/**
 *
 */
if (!function_exists('index_exists')){
    function index_exists($table_name, $index_name, $number_fields = 0)
    {
        global $database;
        $number_fields = intval($number_fields);
        $keys = 0;
        $sql = 'SHOW INDEX FROM `'.$table_name.'`';
        if (($res_keys = $database->query($sql))) {
            while (($rec_key = $res_keys->fetchRow(MYSQLI_ASSOC))) {
                if ( ($rec_key['Key_name'] || ($rec_key['Column_name']) == $index_name) ) {
                    $keys++;
                }
            }
        }
        if ( $number_fields == 0 ) {
            return ($keys != $number_fields);
        } else {
            return ($keys == $number_fields);
        }
    }
}

/*  this function/closure is placed inside this file temporarely until a better place is found */
/*  function to update a var/value-pair(s) in table ****************************
 *  nonexisting keys are inserted
 *  @param string $table: name of table to use (without prefix)
 *  @param mixedthe firring with name of the key to update
 *  @param string $value: a sting with needed value, if $key is a string too
 *  @param array example the first entry is used as WHEE....... AND request
 *                  $aFieldsList = array(
 *                      'section_id' => $section_id,
 *                      'page_id' => $page_id,
 *                  );
 *
 *  @return bool:  true if any keys are updated, otherwise false
 */
if (!function_exists('UpdateKeyValue')){

    function UpdateKeyValue($table, $key, $value = '', array $aExtraFields)
    {
        global $database;
        if( !is_array($key))
        {
            if( trim($key) != '' )
            {
                $key = array( trim($key) => trim($value) );
            } else {
                $key = array();
            }
        }
        $retval = null;
        $iFirst = 0;
        $aIndexField  = [];
        $sExtraFields = '';
        $aTmp = array();
        $sql = 'SHOW FIELDS FROM `'.TABLE_PREFIX.$table.'` ';
        if ($oRes = $database->query($sql)){
            while($aRes = $oRes->fetchRow(MYSQLI_ASSOC)){
              if (!isset($aExtraFields[$aRes['Field']])){continue;}
              $aTmp[$aRes['Field']] = $aExtraFields[$aRes['Field']];
              if (index_exists(TABLE_PREFIX.$table, $aRes['Field'])){
                  $aIndexField[] = $aRes['Field'].' = '.$aTmp[$aRes['Field']];
                  $iFirst++;
              }
            }
            $aExtraFieldsList =  array_intersect_key($aTmp, $aExtraFields);
            foreach($aExtraFieldsList AS $sName=>$sValue){
                $sExtraFields .= '`'.$sName.'` = \''.$sValue.'\', ';
            }
        } else {
            $retval[]=$sql;
            $retval[]=$database->get_error();
        }

        foreach( $key as $index=>$val)
        {
//            $index = strtolower($index);
            $sql  = 'SELECT COUNT(*) FROM `'.TABLE_PREFIX.$table.'` '
                  . 'WHERE `s_name` = \''.$index.'\' '
                  . (($aIndexField['0']!='') ? 'AND '. $aIndexField['0'] : '');
            if($database->get_one($sql))
            {
                $sql = 'UPDATE ';
                $sql_where = 'WHERE `s_name` LIKE \''.$index.'\'';
            } else {
                $sql = 'INSERT INTO ';
                $sql_where = '';
            }
            $sql .= '`'.TABLE_PREFIX.$table.'` SET ';
            $sql .= (($sExtraFields) ? $sExtraFields : '');
            $sql .= '`s_name` = \''.$index.'\', ';
            $sql .= '`s_value` = \''.$val.'\' '.$sql_where;
            if (!$database->query($sql))
            {
                $retval[]=$sql;
                $retval[]=$database->get_error();
            }
        }
        return ((sizeof($retval)==0) ? true : $retval);
    };
}
/*--------------------------------------------------------------------------------------*/

/**
*
* Author: CodexWorld
* Author URI: http://www.codexworld.com
* Function Name: rgb2hex2rgb()
* $color => HEX or RGB
* Returns RGB or HEX color format depending on given value.
*
**/
$ColorConverter = (function ($color){
   if(!$color) return false;
   $color = trim($color);
   $result = false;
  if(preg_match("/^[0-9ABCDEFabcdef\#]+$/i", $color)){
      $hex = str_replace('#','', $color);
      if(!$hex) return false;
      if(strlen($hex) == 3):
         $result['r'] = hexdec(substr($hex,0,1).substr($hex,0,1));
         $result['g'] = hexdec(substr($hex,1,1).substr($hex,1,1));
         $result['b'] = hexdec(substr($hex,2,1).substr($hex,2,1));
      else:
         $result['r'] = hexdec(substr($hex,0,2));
         $result['g'] = hexdec(substr($hex,2,2));
         $result['b'] = hexdec(substr($hex,4,2));
      endif;
   }elseif (preg_match("/^[0-9]+(,| |.)+[0-9]+(,| |.)+[0-9]+$/i", $color)){
      $rgbstr = str_replace(array(',',' ','.'), ':', $color);
      $rgbarr = explode(":", $rgbstr);
      $result = '#';
      $result .= str_pad(dechex($rgbarr[0]), 2, "0", STR_PAD_LEFT);
      $result .= str_pad(dechex($rgbarr[1]), 2, "0", STR_PAD_LEFT);
      $result .= str_pad(dechex($rgbarr[2]), 2, "0", STR_PAD_LEFT);
      $result = strtoupper($result);
   }else{
      $result = false;
   }
   return $result;
});

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


//if (!class_exists('admin', false))            {require (WB_PATH.'/framework/class.admin.php');}
if (!function_exists('make_dir'))             {require (WB_PATH.'/framework/functions.php');}
if (!function_exists('getSettings'))          {require ($sAddonPath.'/scripts/functions.php');}
if (!function_exists('getFolderData'))        {require ($sAddonPath.'/admin/scripts/backend.functions.php');}
if (!class_exists('Validator', false))        {require ($sAddonPath.'/class/Validator.php');}
if (!class_exists('DirectoryHandler', false)) {require ($sAddonPath.'/class/DirectoryHandler.php');}
if (!class_exists('FgUpload', false))         {require ($sAddonPath.'/class/FgUpload.php');}

/**
 *  Pfad und URL zum Stammverzeichnis der Foldergallery
 *  Das Stammverzeichnis ist das hoechste Verzeichnis
 *  auf welches die Foldergallery zugriff hat.
 *  Die Werte muessen auf das gleiche Verzeichnis zeigen.
 *  Diese Verzeichnisse koennen sie natuerlich aendern!
 *  (z.B) fuer externe Ordner
**/

    if (!class_exists('WbAdaptor', false)) {
    // backward compatibility for WB less then 2.8.4
        $sAppUrl  = rtrim(str_replace('\\', '/', WB_URL), '/').'/';
        $sAppPath = rtrim(str_replace('\\', '/', WB_PATH), '/').'/';
    } else {
        $sAppUrl  = WbAdaptor::getInstance()->AppUrl;
        $sAppPath = WbAdaptor::getInstance()->AppPath;
    }

    $MediaRel       = MEDIA_DIRECTORY; //
    $MediaAddonRel  = $MediaRel; ///foldergallery/NeuerOrdner/Schuetzenfest.'/'
    $sUploadFolder  = '/uploads'; //.
    $sChunkFolder   = '/chunks'; //.
    $thumbPath      = '/fg-thumbs';
    $path           = WB_PATH.$MediaRel;  // Vorher: WB_PATH.;
    $url            = WB_URL;                  // Vorher: WB_URL.MEDIA_DIRECTORY;
    $sAddonBaseDir = $path.'/'.$sAddonName; // Future setting baseDir is the addon folder

    if (!make_dir($sAddonBaseDir)){}
    if (!make_dir($sAddonBaseDir.$sChunkFolder)){}
    if (!make_dir($sAddonBaseDir.$sUploadFolder)){}

    // Des gleiche wie oben, aber ohne Slash
    // Wird fuer die Suche benoetigt
    $thumbDirName  = trim($thumbPath, '/');
    $chunkDirName  = trim($sChunkFolder, '/');
    $uploadDirName = trim($sUploadFolder, '/');

    // bad    $pages = substr(PAGES_DIRECTORY, 1);
    // better
    $pages = trim(PAGES_DIRECTORY, '/');

    $checked  = ' checked="checked"';
    $selected = ' selected="selected"';

    $sAppRel  = preg_replace('/^https?:\/\/[^\/]*(.*)$/is', '$1', $sAppUrl);
    $sDocRoot = preg_replace('/^(.*?)'.preg_quote($sAppRel, '/').'$/', '$1', $sAppUrl);

    $aThumbSettings = array(
        'image_resize'           => true,
        'image_x'                => 150,
        'image_y'                => 150,
        'image_ratio_fill'       => false,
        'image_ratio_crop'       => true,
        'image_background_color' => '#FFFFFF',
        'description'            => '',
    );
    $aDefaults =  array(
        "section_id"   => (@$section_id?:"0"),
        "page_id"      => (@$page_id?:"0"),
        'cat_pp' => '-1',
        'catpic' => '',
        'extensions' => 'jpg,jpeg,gif,png',
        'gal_pp' => 5,
        'invisible' => $thumbDirName.','.$chunkDirName.','.$uploadDirName.',thumbs,data',
        'lightbox' => 'jqueryFancybox',
        'imageName'   => '0',
        'pagination' => 'NewYahooStyle',
        'galleryStyle' => 'default',
        'opacity' => 1,
        'alignment' => 'left',
        'pics_pp' => 20,
        "defaultQuality" => "50",
        "maxImageSize"   => "1024",
        'root_dir' => '/'.trim($MediaAddonRel,'/'),
        'tbSettings' => serialize($aThumbSettings),
        'thumbDirName' => $thumbDirName.'',
        'thumbPath' => $thumbPath.'',
    );

    $oAddonReg = new stdClass(); // wird später durch ein selbstverwaltetes AddonRegistry-Objekt ersetzt
    $oAddonReg->oReg              = $oReg;
    $oAddonReg->RequestVars       = $aRequestVars;
    $oAddonReg->AddonPath         = $sAddonPath;
    $oAddonReg->AddonBaseDir      = $sAddonBaseDir;
    $oAddonReg->AddonThemePath    = $sAddonThemePath;
    $oAddonReg->AddonTemplatePath = $sAddonTemplatePath;
    $oAddonReg->AddonRel          = str_replace($oReg->AppPath, '', $oAddonReg->AddonPath);
    $oAddonReg->AddonUrl          = $oReg->AppUrl.$oAddonReg->AddonRel;
    $oAddonReg->AddonName         = $sAddonName;
    $oAddonReg->AddonClass        = 'm_'.$sAddonName.'_Addon';
    if( preg_match( '/'.'pages\/(modify)\.php$/is', $sCallingScript)) {
        $oAddonReg->PageId            = (@$page_id?:0);
        $oAddonReg->SectionId         = (@$section_id?:0);
    }

    $oAddonReg->aDefaults         = $aDefaults;
    $oAddonReg->Records           = $aDefaults;

/*
print '<pre  class="mod-pre rounded">function <span>'.__FUNCTION__.'( '.''.' );</span>  filename: <span>'.basename(__FILE__).'</span>  line: '.__LINE__.' -> <br />';
print_r( $section_id ); print '</pre>'; flush (); //  ob_flush();;sleep(10); die();
*/
    Translate::getInstance ()->enableAddon ('modules\\'.$sAddonName);
    $aLang = Translate::getInstance ()->getLangArray();

// insert needed javascript variables to template
$sModule = '
<script type="text/javascript">
<!--
    if (typeof '.strtoupper($sAddonName).' === \'undefined\')
    {
    var '.strtoupper($sAddonName).' = {
        ADMIN_DIRECTORY    : "'.ADMIN_DIRECTORY.'",
        WB_URL             : "'.$oReg->AppUrl.'",
        AppRel             : "'.$sAppRel.'",
        ADMIN_URL          : "'.$oReg->AcpUrl.'",
        AddonUrl           : "'.$sAddonUrl.'/",
        ModulesTemplateUrl : "'.($bIsBackend?$sAddonThemeUrl:$sAddonTemplateUrl).'/",
        AddonThemeUrl      : "'.$sAddonThemeUrl.'/",
        AddonTemplateUrl   : "'.$sAddonTemplateUrl.'",
        AddonName          : "'.$sAddonName.'",
        MediaRel           : "'.$MediaRel.'",
        MediaAddonRel      : "'.$MediaRel.'/'.$sAddonName.'/",
    };
}
-->
</script>
';

    $aTplDefaults = array (
          'ADDON_NAME' => strtoupper($sAddonName),
          'AppUrl' =>  $oReg->AppUrl,
          'AppRel' => $sAppRel,
          'AddonName'=> $sAddonName,
          'sAddonUrl' => $sAddonUrl,
          'AddonThemeUrl'  => $sAddonUrl.'/themes/default',
          'AddonUrl' => $sAddonUrl,
          'sAddonThemeUrl'  => $sAddonUrl.'/themes/default',
          'ModulesTemplateUrl' => ($bIsBackend?$sAddonThemeUrl:$sAddonTemplateUrl),
          'AddonTemplateUrl' => ($bIsBackend?$sAddonThemeUrl:$sAddonTemplateUrl),
          'ADDON_URL'=> $sAddonUrl,
          'ModuleScript' => $sModule,
          );

/**
 * Diese Zeilen nur aendern wenn du genau weisst was du tust!
 * '.' und '..' duerfen nicht entfernt werden!
 * Weitere invisibleFileNames koennen direkt im Backend der Foldergallery definiert werden.
 */
//Alle Ordner ausschliessen, welche zum Core von WB gehoeren
$wbCoreFolders = array('account',
     trim(ADMIN_DIRECTORY, '/'),
    'framework',
    'include',
    'install',
    'languages',
    'modules',
     trim(PAGES_DIRECTORY, '/'),
    'search',
    'temp',
//    'templates',
    'logs',
);
$megapixel_limit      = 5000*1024; //Ab dieser groesse wird kein Thumb mehr erzeugt.

// end of file