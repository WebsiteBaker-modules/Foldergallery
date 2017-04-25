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

    $sBacklink = $sAddonUrl.'/admin/new_cat.php?page_id='.$page_id.'&amp;section_id='.$section_id;

// Validate Data
    $v = new Validator();
    $v->setData($aRequestVars);
    $v->setKeys(array(
        'section_id'    => 'integer',
        'page_id'       => 'integer',
        'root'          => 'string',
        'cat_parent'    => 'string',
        'folder_name'   => 'string',
        'cat_title'     => 'string',
        'cat_desc'      => 'string',
    ));
    $v->process();
    $request = $v->getValidData();
// This is used to prevent SQL attacks
    $request['root']        = $admin->StripCodeFromText($request['root']);
    $request['cat_parent']  = $admin->StripCodeFromText($request['cat_parent']);
    $request['folder_name'] = $v->getSaveFilename($request['folder_name']);
    $request['cat_title']   = $admin->StripCodeFromText($request['cat_title']);
    $request['cat_desc']    = $admin->StripCodeFromText($request['cat_desc']);

// Get the settings for this section
    $settings = getSettings($section_id);

    $request['cat_parent'] = str_replace($request['root'], '', $request['cat_parent']);
// Check if Parent Directory exists
    if ($request['cat_parent'] == '/') { $request['cat_parent'] = ''; }

    $parentDir = $parent_dir = $request['cat_parent'];
    $aChilds    = explode('/', trim($parent_dir, '/'));

    $parent_dir_last = '/'.end($aChilds);
    $sMessage = 'An error occured during creating a new directory!';
    if($request['folder_name'] == '') {
        $admin->print_error($MESSAGE['MOD_FORM_REQUIRED_FIELDS'].'<br />'.$MOD_FOLDERGALLERY['FOLDER_NAME'].' ', $sBacklink);
    }
    if($request['cat_title'] == '') {
        $request['cat_title'] = $aRequestVars['folder_name'];
    }

    if($request['cat_title'] == '') {
        $admin->print_error($MESSAGE['MOD_FORM_REQUIRED_FIELDS'].'<br />'.$MOD_FOLDERGALLERY['CAT_TITLE'].' ', $sBacklink);
    }
    if(!is_dir(WB_PATH.MEDIA_DIRECTORY.$settings['root_dir'].$parentDir)) {
        $admin->print_error(__LINE__.') '.$sMessage.' '.$parentDir, $sBacklink);
    }

    $new_dir = WB_PATH.MEDIA_DIRECTORY.$settings['root_dir'].$parentDir.'/'.$request['folder_name'];

     make_dir($new_dir);

// get the parent id from the database
    $sql  = 'SELECT `id`, `niveau`, `childs` FROM `'.TABLE_PREFIX.'mod_foldergallery_categories` '
          . 'WHERE section_id='.$request['section_id'].' '
          .   'AND CONCAT(`parent`,\'/\', `categorie`) = \''.$parent_dir.'\';';
    $query = $database->query($sql);
    if ($result = $query->fetchRow(MYSQLI_ASSOC)){
    }

    $parent_id = (int) $result['id'];

    $niveau = $result['niveau'] + 1;
    $childs = explode(',',$result['childs']);

// OK, prepare the Insert SQL:
    $sql = 'INSERT INTO `'.TABLE_PREFIX.'mod_foldergallery_categories` (`section_id`, `parent_id`, `parent`, `categorie`, `cat_name`, `description`, `is_empty`, `niveau`, `active`) '
         . 'VALUES ('.$request['section_id'].', '.$parent_id.', \''.$parent_dir.'\', \''.$request['folder_name'].'\', \''.$request['cat_title'].'\' ,\''.$request['cat_desc'].'\', 0, '.$niveau.', 1);';
    $database->query($sql);

// OK, now update the parent_categorie
    $sql  = 'SELECT `id` FROM `'.TABLE_PREFIX.'mod_foldergallery_categories` '
          . 'WHERE `section_id`='.$request['section_id'].' '
          .   'AND CONCAT(`parent`, \'/\', `categorie`) = \''.$request['cat_parent'].'/'.$request['folder_name'].'\';';
    $query   = $database->query($sql);
    $result  = $query->fetchRow(MYSQLI_ASSOC);
    $cat_id  = $result['id'];
    $childs  = ltrim(implode(',',$childs), ',');

    $sql = 'UPDATE `'.TABLE_PREFIX.'mod_foldergallery_categories` '
           .'SET `has_child` = 1, `childs` = \''.$childs.'\' '
           .'WHERE `id` = '.$parent_id.';';
    $database->query($sql);

    $admin->print_success($TEXT['SUCCESS'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id.'&section_id='.$section_id);

    $admin->print_footer();

