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
 * @version         $Id: install.php 55 2016-09-29 14:10:47Z dietmar $
 * @lastmodified    $Date: 2016-09-29 16:10:47 +0200 (Do, 29. Sep 2016) $
 *
 */
/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
//if(!defined('DEBUG')) { define('DEBUG', true); }
// prevent this file from being accessed directly
/* -------------------------------------------------------- */
// Must include code to prevent this file from being accessed directly
if (!defined('SYSTEM_RUN')) { header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found'); flush(); exit; }

    // create tables from sql dump file
    if (is_readable(__DIR__.'/install-struct.sql')) {
        if (!$database->SqlImport(__DIR__.'/install-struct.sql', TABLE_PREFIX, 'install' )){
          echo $database->get_error();
        }
    }

// end of file
