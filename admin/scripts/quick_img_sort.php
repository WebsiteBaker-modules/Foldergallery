<?php
    $sAddonPath = dirname(dirname(__DIR__));
    if (is_readable($sAddonPath.'/init.php'))     {require ($sAddonPath.'/init.php');}
    // to print with or without header, default is with header
    $admin_header=false;
    // Workout if the developer wants to show the info banner
    $print_info_banner = false; // true/false
    // Tells script to update when this page was last updated
    $update_when_modified = false;
    // Include WB admin wrapper script to sanitize page_id and section_id, print SectionInfoLine
    require(WB_PATH.'/modules/admin.php');
    // An associative array that by default contains the contents of $aRequestVars, $aRequestVars and $_COOKIE.
    $aRequestVars = $_REQUEST;

    $error = null;

    if(isset($aRequestVars['cat_id']) && is_numeric($aRequestVars['cat_id'])) {
        $cat_id = intval($aRequestVars['cat_id']);
    } else {
        $error['no_cat_id'] = 1;
    }

    if(isset($aRequestVars['sort'])) {
        switch($aRequestVars['sort']) {
            case "ASC":
                $sort = "ASC";
                break;
            case "DESC":
                $sort = "DESC";
                break;
            default:
                $error['no_sort'] = 1;
                break;
        }
    }

    if($error != null) {
        header("Location: ../../index.php");
        exit();
    }

    // Create new admin object and print admin header
    $admin = new admin('Pages', 'pages_settings');


$sql="SELECT file_name, position, id FROM `".TABLE_PREFIX."mod_foldergallery_files` WHERE parent_id =".$cat_id." ORDER BY file_name ".$sort;

$query=$database->query($sql);

if($query->numRows()) {
    $sql = "UPDATE `".TABLE_PREFIX."mod_foldergallery_files` SET position= CASE ";
    $position = 1;
    while($result = $query->fetchRow(MYSQLI_ASSOC)){
        $sql = $sql."WHEN id=".$result['id']." THEN '".$position."' ";
        $position++;
    }
    $sql = $sql." ELSE position END;";
}

$sBacklink = $sAddonUrl.'/admin/modify_cat_sort.php?page_id='.$page_id.'&section_id='.$section_id.'&cat_id='.$cat_id;
if($database->query($sql)){
    $admin->print_success($MESSAGE['PAGES_REORDERED'],$sBacklink);
} else {
    $admin->print_error($TEXT['ERROR'],$sBacklink);
}

// Print admin footer
$admin->print_footer();

