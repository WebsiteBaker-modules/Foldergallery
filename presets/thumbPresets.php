<?php
/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if (!defined('SYSTEM_RUN')) {header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found'); echo '404 File not found'; flush(); exit;}
/* -------------------------------------------------------- */
?><?php
// serilize in settings
    $aThumbSettings = array(
        "image_resize"           => true,
        "image_x"                => 150,
        "image_y"                => 150,
        "image_ratio_fill"       => false,
        "image_ratio_crop"       => true,
        "image_background_color" => "#FFFFFF",
        'description'            => '',
    );

    $aDefaults =  array(
        "section_id"     => (isset($section_id) ? $section_id : "0"),
        "page_id"        => (isset($page_id) ? $page_id : "0"),
        "cat_pp"         => "-1",
        "catpic"         => "0",
        "extensions"     => "jpg,jpeg,gif,png",
        "gal_pp"         => "-1",
        'invisible'      => $thumbDirName.','.$chunkDirName.','.$uploadDirName,
        "lightbox"       => "jqueryFancybox",
        "imageName"      => "0",
        "pagination"     => "NewYahooStyle",
        'galleryStyle'   => 'default',
        'opacity'        => 0.6,
        'alignment'      => 'left',
        "loadPreset"     => "1:1noCrop",
        "pics_pp"        => "20",
        "defaultQuality" => "50",
        "maxImageSize"   => "1024",
        'thumb_width'    => 150,
        'thumb_height'   => 150,
        "root_dir"       => '/'.trim($MediaAddonRel,"/"),
        "tbSettings"     => serialize($aThumbSettings),
        "thumbDirName"   => $thumbDirName."",
        'thumbPath'      => $thumbPath.'',
    );

/*
 * Here you can create your own presets!
 * Just look at the standard presets to create your own!
 */
    $thumbPresets = array(
        "1:1noCrop"      => array(
            "thumb_ratio"   => "free",
            "description"   => "{CALCULATE_RATIO_STRING}",
            "image_x"       => 150,
            "image_y"       => 150,
            "image_ratio"   => true,
            "image_background_color" => "#FFFFFF"
        ),
        "1:1Croped"      => array(
            "thumb_ratio"   => 1,
            "description"   => "Ratio: 1:1, Cropped, width/height",
            "image_x"       => 150,
            "image_y"       => 150,
            "image_ratio"   => true,
            "image_background_color" => "#FFFFFF"
        ),
        "3:4Cropped"     => array(
            "thumb_ratio"   => 1.33,
            "description"   => "Ratio: 3:4, Cropped, width",
            "image_x"       => 112,
            "image_y"       => 150,
            "image_ratio"   => true,
            "image_background_color"    => "#FFFFFF"
        ),
        "4:3Cropped"     => array(
            "thumb_ratio"   => 0.75,
            "description"   => "Ratio: 4:3, Cropped, heigth",
            "image_x"       => 150,
            "image_y"       => 112,
            "image_ratio"   => true,
            "image_background_color" => "#FFFFFF"
        ),
        "16:9Cropped"    => array(
            "thumb_ratio"   => 0.56,
            "description"   => "Ratio: 16:9, Cropped, width",
            "image_x"       => 84,
            "image_y"       => 150,
            "image_ratio"   => true,
            "image_background_color"    => "#FFFFFF"
        ),
        "9:16Cropped"    => array(
            "thumb_ratio"   => 1.78,
            "description"   => "Ratio: 9:16, Cropped, height",
            "image_x"       => 150,
            "image_y"       => 84,
            "image_ratio"   => true,
            "image_background_color" => "#FFFFFF"
        ),
    );


