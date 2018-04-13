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
if (!defined('SYSTEM_RUN')) {header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found'); echo '404 File not found'; flush(); exit;}
/* -------------------------------------------------------- */
    // folderstructure steps to modules path
    $sAddonPath = __DIR__;

    if (is_readable($sAddonPath.'/init.php')) {require ($sAddonPath.'/init.php');}

    // to print with or without header, default is with header
    $admin_header=false;
    // Workout if the developer wants to show the info banner
    $print_info_banner = false; // true/false
    // Tells script to update when this page was last updated
    $update_when_modified = false;
    // Include WB admin wrapper script to sanitize page_id and section_id, print SectionInfoLine
    require(WB_PATH.'/modules/admin.php');
    // An associative array that by default contains the contents of $_GET, $_POST and $_COOKIE.
    $aRequestVars = $_REQUEST;

// Einstellungen zur aktuellen Foldergallery aus der DB
$settings = getFGSettings($section_id);
// Falls noch keine Einstellungen gemacht wurden auf die Einstellungsseite umleiten
    if (isset($settings['root_dir']) && ($settings['root_dir'] == '/d41d8cd98f00b204e9800998ecf8427e')) {
        $sRedirectUrl = $sAddonUrl.'/admin/modify_settings.php?page_id='.$page_id.'&section_id='.$section_id.'';
?>
<script >
        function redirect() {
             window.location.replace ('<?php echo $sRedirectUrl; ?>');
        }
        window.setTimeout("redirect()", 2000); // in msecs 1000 => eine Sekunde
</script>
<?php
        echo "<div class=\"info\">".str_replace('{{SETTING_LINK}}', $sRedirectUrl, $MOD_FOLDERGALLERY['REDIRECT'])."\n</div>\n";

} else {
    // Template
    $t = new Template($sAddonThemePath, 'remove');
    $t->halt_on_error = 'no';
    $t->set_file('modify', 'modify.htt');
    // clear the comment-block, if present
    $t->set_block('modify', 'CommentDoc'); $t->clear_var('CommentDoc');
    $t->set_block('modify', 'ListElement', 'LISTELEMENT');
    $t->clear_var('ListElement'); // Loeschen, da dies ueber untenstehende Funktion erledigt wird.

    // Links im Template setzen
    $t->set_var(array(
            'SETTINGS_ONCLICK'  => 'window.location = \''.$sAddonUrl.'/admin/modify_settings.php?page_id='.$page_id.'&amp;section_id='.$section_id.'\';',
            'SYNC_ONKLICK'      => 'window.location = \''.$sAddonUrl.'/admin/sync.php?page_id='.$page_id.'&amp;section_id='.$section_id.'\';',
            'HELP_ONCLICK'      => 'window.location = \''.$sAddonUrl.'/help.php?page_id='.$page_id.'&amp;section_id='.$section_id.'\';',
            'NEW_CAT_ONCLICK'   => 'window.location = \''.$sAddonUrl.'/admin/new_cat.php?page_id='.$page_id.'&amp;section_id='.$section_id.'\';',
            'EDIT_PAGE'         => $page_id,
            'EDIT_SECTION'      => $section_id,
            'WB_URL'            => WB_URL,
            'FTAN'              => $admin->getFTAN()
    ));

    $t->set_var($aTplDefaults);
    $t->set_var($aLangFG);

    // Text im Template setzten
    $t->set_var(array(
            'TITEL_BACKEND_STRING'         => $MOD_FOLDERGALLERY['TITEL_BACKEND'],
            'TITEL_MODIFY'                 => $MOD_FOLDERGALLERY['TITEL_MODIFY'],
            'SETTINGS_STRING'              => $MOD_FOLDERGALLERY['SETTINGS_STRING'],
            'FOLDER_IN_FS_STRING'          => $MOD_FOLDERGALLERY['FOLDER_IN_FS'],
            'CAT_TITLE_STRING'             => $MOD_FOLDERGALLERY['CAT_TITLE'],
            'ACTIONS_STRING'               => $MOD_FOLDERGALLERY['ACTION'],
            'SYNC_STRING'                  => $MOD_FOLDERGALLERY['SYNC'],
            'EDIT_CSS_STRING'              => $MOD_FOLDERGALLERY['EDIT_CSS'],
            'EXPAND_COLAPSE_STRING'        => $MOD_FOLDERGALLERY['EXPAND_COLAPSE'],
            'HELP_STRING'                  => $MOD_FOLDERGALLERY['HELP_INFORMATION'],
            'NEW_CAT_STRING'               => $MOD_FOLDERGALLERY['NEW_CAT'],
            'CATEGORIE'                    => $MOD_FOLDERGALLERY['CATEGORIE'],
            'DELETE_CAT_ARE_YOU_SURE'      => $MOD_FOLDERGALLERY['DELETE_CAT_ARE_YOU_SURE'],
    ));

    // Template ausgeben
    $t->pparse('output', 'modify');

    // Kategorien von der obersten Ebene aus DB hohlen
    $sql  = 'SELECT * FROM `'.TABLE_PREFIX.'mod_foldergallery_categories` '
          . 'WHERE `section_id`='.$section_id.' '
          .   'AND `niveau`=0';
    if ($query = $database->query($sql)){
        while($result = $query->fetchRow(MYSQLI_ASSOC)){
            $results[] = $result;
        }
    }
    // Needed for display_categories()
    $url = array(
            'edit' => $sAddonUrl."/admin/modify_cat.php?page_id=".$page_id."&section_id=".$section_id."&cat_id=",
    );

    echo '
    <script>
            var the_parent_id = "0";
            var theme_url = "'.THEME_URL.'";
            var fg_url = "'.$sAddonUrl.'/";
    </script>

            <ul>
                    '.display_categories(-1, $section_id).'
            </ul>
    <div id="dragableCategorie">
            <ul>
                    '.display_categories(0, $section_id).'
            </ul>
    </div>
    <div style="display: block; width: 90%; height: 15px; padding: 5px;">
      <div id="dragableResult"> </div>
    </div>
    <hr>
    ';

    }
