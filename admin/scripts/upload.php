<?php

// Befor doing anything, make some safety check's:
if (!isset($_REQUEST['secCheck'])) {
    exit;
}

if (empty($_FILES)) {
    exit;
}

// Check the section, page and cat ID:
$secArray = explode('/', $_REQUEST['secCheck']);
if (count($secArray) != 3) {
    exit;
}

if (!is_numeric($secArray[0])) {
    exit;
}
$page_id = $secArray[0];

if (!is_numeric($secArray[1])) {
    exit;
}
$section_id = $secArray[1];

if (!is_numeric($secArray[2])) {
    exit;
}
$cat_id = $secArray[2];

// OK, secCheck Values seems to be ok, at least they are save to use
// Start with WB specific stuff:
    $sAddonPath = dirname(dirname(__DIR__));
    if (is_readable($sAddonPath.'/init.php'))     {require ($sAddonPath.'/init.php');}
    // to print with or without header, default is with header
    $admin_header=true;
    // Workout if the developer wants to show the info banner
    $print_info_banner = false; // true/false
    // Tells script to update when this page was last updated
    $update_when_modified = false;
    // Include WB admin wrapper script to sanitize page_id and section_id, print SectionInfoLine
    require(WB_PATH.'/modules/admin.php');
    // An associative array that by default contains the contents of $aRequestVars, $aRequestVars and $_COOKIE.
    $aRequestVars = $_REQUEST;

// Get the settings for this section
$settings = getSettings($section_id);
if ($settings['page_id'] != $page_id) {
    exit;
}

// Get some more detailes for this categorie
$sql = 'SELECT id, section_id, parent_id, categorie, parent FROM ' . TABLE_PREFIX . 'mod_foldergallery_categories WHERE `id` = ' . $cat_id . ' AND `section_id` = ' . $section_id . ';';
$query = $database->query($sql);
if ($query->numRows() != 1) {
    exit;
}
$categorie = $query->fetchRow(MYSQLI_ASSOC);

$categoriePath = WB_PATH . $settings['root_dir'];

if ($categorie['parent_id'] != -1) {
    $categoriePath .= $categorie['parent'] . '/' . $categorie['categorie'];
}

$categoriePath .= '/';

// OK, now we have all Informations we can get from the Database
// Process the new Data
$v = new Validator();
$allowedFileTypes = explode(',', $settings['extensions']);
$fileParts = pathinfo($_FILES['Filedata']['name']);
$filename = $v->getSaveFilename($fileParts['filename']);
$extension = strtolower($fileParts['extension']);

if (!in_array($fileParts['extension'], $allowedFileTypes)) {
    exit;
}

// Move the image and create the thumb:
$handle = new FgUpload($_FILES['Filedata']['tmp_name']);
if ($handle->uploaded) {
    // Save the image in the right categorie
    $handle->file_new_name_body = $filename;
    $handle->file_new_name_ext = $extension;
    $handle->process(DirectoryHandler::DecodePath($categoriePath));
    if (!$handle->processed) {
        echo DirectoryHandler::DecodePath($categoriePath);
        exit;
    }
    $handle->clean();
    // Create the thumb
    $success = FG_createThumb($categoriePath . $filename . '.' . $extension, $filename . '.' . $extension, $categoriePath . 'fg-thumbs/', $settings['tbSettings']);
    if (!$success) {
        exit;
    }
} else {
    exit;
}

// get DB infos
$sql = 'SELECT position FROM `' . TABLE_PREFIX . 'mod_foldergallery_files` WHERE `parent_id`= ' . $cat_id . ' ORDER BY `position` DESC LIMIT 1;';
$query = $database->query($sql);
$result = $query->fetchRow(MYSQLI_ASSOC);
$newPosition = $result['position'] + 1;

//Insert to db
$sql = 'INSERT INTO `' . TABLE_PREFIX . 'mod_foldergallery_files` (`parent_id`, `file_name`, `position`) VALUES ( \'' . $cat_id . '\' , \'' . $filename . '.' . $extension . '\' , \'' . $newPosition . '\');';
$sql = 'INSERT INTO `' . TABLE_PREFIX . 'mod_foldergallery_files` (`section_id`, `parent_id`, `file_name`, `position`) VALUES ( \'' . $section_id . '\' , \'' . $cat_id . '\' , \'' . $filename . '.' . $extension . '\' , \'' . $newPosition . '\');';
$query = $database->query($sql);

$sql = 'SELECT id FROM `' . TABLE_PREFIX . 'mod_foldergallery_files` WHERE `parent_id` = ' . $cat_id . ' AND `position` = ' . $newPosition . ';';
$query = $database->query($sql);
$result = $query->fetchRow(MYSQLI_ASSOC);

$newId = $result['id'];

// Very bad method to get the URL to the thumb-file...
$thumbFile = $categoriePath.$thumbPath.'/'.$filename.'.'.$extension;
$urlToThumb = str_replace(WB_PATH, WB_URL, $thumbFile);
$thumbEditLink = $sAddonUrl."/admin/modify_thumb.php?page_id=" . $page_id . "&section_id=" . $section_id . "&cat_id=" . $cat_id . "&id=" . $newId;
$thumbEditAlt = $MOD_FOLDERGALLERY['THUMB_EDIT_ALT'];
$editThumbSource = THEME_URL . '/images/resize_16.png';
$imageDeleteLink = "javascript: confirm_link(\"" . $MOD_FOLDERGALLERY['DELETE_ARE_YOU_SURE'] . "\", \"" . $sAddonUrl."/admin/scripts/delete_img.php?page_id=" . $page_id . "&section_id=" . $section_id . "&cat_id=" . $cat_id . "&id=" . $newId . "\");";
$imageDeleteAlt = $MOD_FOLDERGALLERY['IMAGE_DELETE_ALT'];
$imageDeleteSource = THEME_URL . '/images/delete_16.png';

// Create output:
echo '
    <tr>
        <td align="center">
            <a href=\'' . $thumbEditLink . '\'><img src="' . $urlToThumb . '"></a>
        </td>
        <td>
            ' . $filename . '.' . $extension . '
        </td>
        <td>
            <textarea cols="40" rows="3"  name="caption[' . $newId . ']" ></textarea>
        </td>
        <td align="center" width="20px">
            <a href=\'' . $thumbEditLink . '\' title="' . $thumbEditAlt . '"><img src=\'' . $editThumbSource . '\' border="0" alt="' . $thumbEditAlt . '"></a>
        </td>
        <td align="center" width="20px">
            <a href=\'' . $imageDeleteLink . '\' title="' . $imageDeleteAlt . '"><img src=\'' . $imageDeleteSource . '\' border="0" alt="' . $imageDeleteAlt . '"></a>
        </td>
    </tr>';
