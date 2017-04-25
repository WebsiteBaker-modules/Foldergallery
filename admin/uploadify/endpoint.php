<?php

/**
 * PHP Server-Side Example for Fine Uploader (traditional endpoint handler).
 * Maintained by Widen Enterprises.
 *
 * This example:
 *  - handles chunked and non-chunked requests
 *  - supports the concurrent chunking feature
 *  - assumes all upload requests are multipart encoded
 *  - supports the delete file feature
 *
 * Follow these steps to get up and running with Fine Uploader in a PHP environment:
 *
 * 1. Setup your client-side code, as documented on http://docs.fineuploader.com.
 *
 * 2. Copy this file and handler.php to your server.
 *
 * 3. Ensure your php.ini file contains appropriate values for
 *    max_input_time, upload_max_filesize and post_max_size.
 *
 * 4. Ensure your "chunks" and "files" folders exist and are writable.
 *    "chunks" is only needed if you have enabled the chunking feature client-side.
 *
 * 5. If you have chunking enabled in Fine Uploader, you MUST set a value for the `chunking.success.endpoint` option.
 *    This will be called by Fine Uploader when all chunks for a file have been successfully uploaded, triggering the
 *    PHP server to combine all parts into one file. This is particularly useful for the concurrent chunking feature,
 *    but is now required in all cases if you are making use of this PHP example.
 */

    $sAddonPath = (dirname(dirname(__DIR__)));
    if (is_readable($sAddonPath.'/init.php')) {require ($sAddonPath.'/init.php');}
    // An associative array that by default contains the contents of $aRequestVars, $aRequestVars and $_COOKIE.
    $aRequestVars = $_REQUEST;
// Include the upload handler class
    if (!class_exists('UploadHandler', false)){require "UploadHandler.php";}

    $uploader = new UploadHandler();

// Specify the list of valid extensions, ex. array("jpeg", "jpg", "png", "gif","jpeg", "xml", "bmp")
    $uploader->allowedExtensions = array(); // all files types allowed by default

// Specify max file size in bytes.
    $uploader->sizeLimit = 200*1024;

// Specify the input name set in the javascript.
    $uploader->inputName = "qqfile"; // matches Fine Uploader's default inputName value by default

//    $uploader->set('sBackStep', dirname(dirname(dirname(dirname(dirname(getcwd()))))));

    $result = $uploader->set('sBackStep', '../../../../');
// If you want to use the chunking/resume feature, specify the folder to temporarily save parts.
    $uploader->set('chunksFolder', $sChunkFolder);

    $sOpenBaseTmp = $sOpenBasedir = ini_get('open_basedir');
    $aOpenBasedir    = get_required_files();
//    $tmp = array_walk(){}
    if (is_array($aOpenBasedir) && sizeof($aOpenBasedir)) {
        array_walk(
            $aOpenBasedir,
            function (&$sFile) {
                $sFile = dirname($sFile);
           }
        );
    }
    $sOpenBasedir .= trim(','.implode(',', $aOpenBasedir),',');
    $sTargetFolder = $MediaRel.$aRequestVars['TargetDir'];
    $method = $_SERVER["REQUEST_METHOD"];

    if ($method !== "POST") {
    }
    if ($method == "POST") {
        header("Content-Type: text/plain");
        // Assumes you have a chunking.success.endpoint set to point here with a query parameter of "done".
        // For example: /myserver/handlers/endpoint.php?done
        if (isset($_GET["done"])) {
            $result = $uploader->combineChunks($sTargetFolder.'/');
        }
        // Handles upload requests
        else {
            // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
            $result = $uploader->handleUpload($sTargetFolder.'/');
            // To return a name used for uploaded file you can use the following line.
            $result["uploadName"] = $uploader->getUploadName();
        }
        echo json_encode($result);
    }
// for delete file requests
    else if ($method == "DELETE") {
        $name =  $uploader->getUploadName();
        $result = $uploader->handleDelete($sUploadFolder.'/', $name);
        echo json_encode($result."\n");
    }
    else {
        header("HTTP/1.0 405 Method Not Allowed");
    }

/*
echo ini_get('open_basedir');
    echo json_encode($uploader->get('sBackStep'));
print '<pre  class="mod-pre rounded">function <span>'.__FUNCTION__.'( '.''.' );</span>  filename: <span>'.basename(__FILE__).'</span>  line: '.__LINE__.' -> <br />';
print_r( $MediaRel.$aRequestVars['TargetDir'] ); print '</pre>'."\n"; flush (); //  ob_flush();;sleep(10); die();
print '<pre  class="mod-pre rounded">function <span>'.__FUNCTION__.'( '.''.' );</span>  filename: <span>'.basename(__FILE__).'</span>  line: '.__LINE__.' -> <br />';
print_r( ini_get('open_basedir') ); print '</pre>'; flush (); //  ob_flush();;sleep(10); die();
*/
