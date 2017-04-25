<?php

// folderstructure steps to WB_PATH
$sAppPath = dirname(dirname(__DIR__));$iSteps = 2;
/* ------------------------------------------------------------------------ */
// load config created for modify or save module files if WB_PATH don't exist, '
if ( !defined( 'WB_PATH' ) ){require($sAppPath.'/config.php');}
/* ------------------------------------------------------------------------ */
    // folderstructure steps to modules path
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

    if (is_readable($sAddonPath.'/info.php'))     {require ($sAddonPath.'/info.php');}
    if (is_readable($sAddonPath.'/init.php'))     {require ($sAddonPath.'/init.php');}
    // to print with or without header, default is with header
    $admin_header=true;
    // Workout if the developer wants to show the info banner
    $print_info_banner = true; // true/false
    // Tells script to update when this page was last updated
    $update_when_modified = false;
    // Include WB admin wrapper script
    require(WB_PATH.'/modules/admin.php');

// include the default language
if (is_readable($sAddonPath.'/help/EN.php')) {require_once($sAddonPath.'/help/EN.php');}
// check if module language file exists for the language set by the user (e.g. DE, EN)
if (is_readable($sAddonPath.'/help/'.LANGUAGE.'.php')) {require($sAddonPath.'/help/'.LANGUAGE.'.php');}

//Template
$t = new Template(dirname(__FILE__).'/help', 'remove');
$t->set_file('help', 'help.htt');
// clear the comment-block, if present
$t->set_block('help', 'CommentDoc'); $t->clear_var('CommentDoc');

$t->set_var(array(
    'TITLE'                 => $FG_HELP['TITLE'],
    'VERSION'               => $FG_HELP['VERSION'],
    'YOUR_VERSION_TEXT'     => sprintf($FG_HELP['YOUR_VERSION'],$module_version),
    'VERSION_TEXT'          => $FG_HELP['VERSION_TEXT'],
    'HOMEPAGE_TEXT'         => $FG_HELP['HOMEPAGE_TEXT'],
    'HELP_TITLE'            => $FG_HELP['HELP_TITLE'],
    'HELP_TEXT'             => $FG_HELP['HELP_TEXT'],
    'BUG_TITLE'             => $FG_HELP['BUG_TITLE'],
    'BUG_TEXT'              => $FG_HELP['BUG_TEXT'],
    'BACK_STRING'           => $FG_HELP['BACK_STRING'],
    'BACK_ONCLICK'          => 'window.location = \''.ADMIN_URL.'/pages/modify.php?page_id='.$page_id.'\';'
));

$t->pparse('output', 'help');

$admin->print_footer();

// end of file
