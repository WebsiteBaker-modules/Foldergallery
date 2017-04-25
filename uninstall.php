<?php
/**
 *
 * @category        modules
 * @package         katalog
 * @author          WebsiteBaker Project / Jacobi22
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://www.jacobi22.com/
 * @platform        WebsiteBaker 2.8.x
 * @requirements    PHP 5.2.2 and higher
 * @version         $Id: uninstall.php 96 2016-11-20 16:46:52Z dietmar $
 * @lastmodified    $Date: 2016-11-20 17:46:52 +0100 (So, 20. Nov 2016) $
 *
 */
/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
//if(!defined('DEBUG')) { define('DEBUG', true); }
// prevent this file from being accessed directly
/* -------------------------------------------------------- */
// Must include code to prevent this file from being accessed directly
if(defined('WB_PATH') == false) {
    die('Cannot access '.basename(__DIR__).'/'.basename(__FILE__).' directly');
} else {
    // create tables from sql dump file
    if (is_readable(__DIR__.'/install-struct.sql')) {
        $database->SqlImport(__DIR__.'/install-struct.sql', TABLE_PREFIX, __FILE__ );
    }
}

// end of file
