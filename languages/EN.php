<?php

/*

Website Baker Project <http://www.websitebaker.org/>
Copyright (C) 2008-2011, Jürg Rast

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

//Modul Description
$module_description = 'Create an Image Gallery with folders as categories';

//Variables for the Frontend
$MOD_FOLDERGALLERY['VIEW_TITLE']        = 'Image Gallery';
$MOD_FOLDERGALLERY['CATEGORIES_TITLE']  = 'Categories';
$MOD_FOLDERGALLERY['ALL_CATEGORIES_TITLE']  = 'Alle Kategorien';
$MOD_FOLDERGALLERY['BACK_STRING']       = 'Back to overview';
$MOD_FOLDERGALLERY['FRONT_END_ERROR']   = 'This category does not exist or does not contain Images and/or Subcategories!';
$MOD_FOLDERGALLERY['PAGE']              = 'Page';
$MOD_FOLDERGALLERY['GAL']                   = 'Gallery';
$MOD_FOLDERGALLERY['IMG_TITLE_TEXT']        = 'Image Title';
$MOD_FOLDERGALLERY['PAGINATION']            = 'Page Numbering Design';
$MOD_FOLDERGALLERY['ADDED']                 = '%d Bild(er) hinzugefügt';
//Variables for the Backend
$MOD_FOLDERGALLERY['PICS_PP']           = 'Images per page';
$MOD_FOLDERGALLERY['GAL_PP']            = 'Galleries per page';
$MOD_FOLDERGALLERY['LIGHTBOX']          = 'Lightbox';
$MOD_FOLDERGALLERY['DELETE_CAT']        = 'category %s successfully deleted';
$MOD_FOLDERGALLERY['MODIFY_CAT_TITLE']  = 'Modify categories and image details';
$MOD_FOLDERGALLERY['MODIFY_CAT']        = 'Modify category details:';
$MOD_FOLDERGALLERY['CAT_NAME']          = 'Category name/title:';
$MOD_FOLDERGALLERY['CAT_DESCRIPTION']   = 'Category description:';
$MOD_FOLDERGALLERY['MODIFY_IMG']        = 'Modify images:';
$MOD_FOLDERGALLERY['IMAGE']             = 'Image';
$MOD_FOLDERGALLERY['IMAGE_NAME']        = 'Image name';
$MOD_FOLDERGALLERY['IMG_CAPTION']       = 'Image description';
$MOD_FOLDERGALLERY['REDIRECT']          = 'You will have to make some settings before using the Gallery. '
                                        . 'You will be forwarded in 2 seconds. (If JavaScript is activated.)';
$MOD_FOLDERGALLERY['TITEL_BACKEND']     = 'Foldergallery Admin';
$MOD_FOLDERGALLERY['TITEL_MODIFY']      = 'Modify categories and images:';
$MOD_FOLDERGALLERY['SETTINGS']          = 'Common settings';
$MOD_FOLDERGALLERY['SETTINGS_STRING']   = 'Common settings';
$MOD_FOLDERGALLERY['ROOT_DIR']          = 'Root directory';
$MOD_FOLDERGALLERY['EXTENSIONS']        = 'Allowed extensions';
$MOD_FOLDERGALLERY['INVISIBLE']         = 'Hide folders';
$MOD_FOLDERGALLERY['NEW_SCANN_INFO']    = 'This action has created the database entries. The thumbnails are created when the category is shown the first time.';
$MOD_FOLDERGALLERY['FOLDER_NAME']       = 'Folder name';
$MOD_FOLDERGALLERY['DELETE']            = 'Delete?';
$MOD_FOLDERGALLERY['ERROR_MESSAGE']     = 'No data!';
$MOD_FOLDERGALLERY['DB_ERROR']          = 'Database error!';
$MOD_FOLDERGALLERY['FS_ERROR']          = 'Unable to delete folder!';
$MOD_FOLDERGALLERY['NO_FILES_IN_CAT']   = 'This category does not contain any images!';
$MOD_FOLDERGALLERY['SYNC']              = 'Sync database with filesystem';
$MOD_FOLDERGALLERY['SYNC_SUCCESS']      = 'Filesystem successfully synchronized';
$MOD_FOLDERGALLERY['SYNC_FAILED']       = 'Synchronization failed';
$MOD_FOLDERGALLERY['EDIT_CSS']          = 'Edit CSS';
$MOD_FOLDERGALLERY['FOLDER_IN_FS']      = 'Filesystem folder:';
$MOD_FOLDERGALLERY['CAT_TITLE']         = 'Category title:';
$MOD_FOLDERGALLERY['ACTION']            = 'Actions:';
$MOD_FOLDERGALLERY['NO_CATEGORIES']     = 'No categories (=Subfolders) found.<br /><br />The Gallery will work, anyway, but no categories are shown.';
$MOD_FOLDERGALLERY['EDIT_THUMB']        = 'Edit thumbnail';
$MOD_FOLDERGALLERY['EDIT_THUMB_DESCRIPTION']    = '<strong>Please select new image</strong>';
$MOD_FOLDERGALLERY['EDIT_THUMB_BUTTON']         = 'Draw up thumbnail';
$MOD_FOLDERGALLERY['THUMB_SIZE']                = 'Thumbnail size';
$MOD_FOLDERGALLERY['THUMB_RATIO']               = 'Thumbnail ratio';
$MOD_FOLDERGALLERY['THUMB_NOT_NEW']             = 'Don\'t recreate thumbnails';
$MOD_FOLDERGALLERY['THUMB_NEW']                 = 'Create new thumbnails!';
$MOD_FOLDERGALLERY['CHANGING_INFO']             = 'Changing <strong>thumb size</strong> or <strong>thumb ratio</strong> will delete (and recreate) all thumbs.';
$MOD_FOLDERGALLERY['SYNC_DATABASE']             = 'Synchronize file system with database...';
$MOD_FOLDERGALLERY['SAVE_SETTINGS']             = 'Settings are stored...';
$MOD_FOLDERGALLERY['SORT_IMAGE']                = 'Sort images';
$MOD_FOLDERGALLERY['BACK']                      = 'Back';
$MOD_FOLDERGALLERY['REORDER_INFO_STRING']       = 'Reorder result will be displayed here.';
$MOD_FOLDERGALLERY['REORDER_INFO_SUCESS']       = 'Saved new order successfully!';
$MOD_FOLDERGALLERY['REORDER_IMAGES']            = 'Sort Images';
$MOD_FOLDERGALLERY['SORT_BY_NAME']              = 'Sort images by filename';
$MOD_FOLDERGALLERY['SORT_BY_NAME_ASC']          = 'filename ascending';
$MOD_FOLDERGALLERY['SORT_BY_NAME_DESC']         = 'filename descending';
$MOD_FOLDERGALLERY['SORT_FREEHAND']             = 'Free sort (Drag & Drop)';
$MOD_FOLDERGALLERY['THUMB_EDIT_ALT']            = 'Edit Thumbnail';
$MOD_FOLDERGALLERY['IMAGE_DELETE_ALT']          = 'Delete Image';
$MOD_FOLDERGALLERY['EDIT_CATEGORIE']            = 'Edit Category';
$MOD_FOLDERGALLERY['EXPAND_COLAPSE']            = 'Expand/Collapse';
$MOD_FOLDERGALLERY['MOVE_UP']                   = 'Move up';
$MOD_FOLDERGALLERY['MOVE_DOWN']                 = 'Move down';
$MOD_FOLDERGALLERY['HELP_INFORMATION']          = 'Help/Info';
$MOD_FOLDERGALLERY['CAT_ACTIVE']                = 'active, click to deactivate!';
$MOD_FOLDERGALLERY['CAT_INACTIVE']              = 'inactive, click to activate!';
$MOD_FOLDERGALLERY['CAT_TOGGLE_ACTIV_FAIL']     = 'Could not activate/deactivate this category! Do you try to hack the system?';
$MOD_FOLDERGALLERY['DELETE_IMG_ARE_YOU_SURE']   = 'Are you sure you want to delete the selected image, title, description? Files are completely deleted from the server!';
$MOD_FOLDERGALLERY['DELETE_CAT_ARE_YOU_SURE']   = 'Are you sure you want to delete the selected category, subcategories and images, titles, descriptions? Files are completely deleted from the server!';
$MOD_FOLDERGALLERY['ADD_MORE_PICS']             = 'Add more Pictures to this category';
$MOD_FOLDERGALLERY['CATPIC_STRINGS'][0]         = 'Random';
$MOD_FOLDERGALLERY['CATPIC_STRINGS'][1]         = 'First';
$MOD_FOLDERGALLERY['CATPIC_STRINGS'][2]         = 'Last';
$MOD_FOLDERGALLERY['CAT_OVERVIEW_PIC']          = 'Category Preview';
$MOD_FOLDERGALLERY['THUMBNAIL_SETTINGS']        = 'Thumbnail Settings';
$MOD_FOLDERGALLERY['LOAD_PRESET']               = 'Load Preset';
$MOD_FOLDERGALLERY['LOAD_PRESET_INFO']          = '<b>Attention, this functions overwrites all other fields below!</b>';
$MOD_FOLDERGALLERY['LOAD_ROOT_INFO']            = 'Changing the root directory deletes all settings, e. g. descriptions and titles';
$MOD_FOLDERGALLERY['IMAGE_CROP']                = 'Image Crop';
$MOD_FOLDERGALLERY['IMAGE_DONT_CROP']           = 'No Crop';
$MOD_FOLDERGALLERY['IMAGE_DO_CROP']             = 'Crop the image';
$MOD_FOLDERGALLERY['RATIO']                     = 'Ratio';
$MOD_FOLDERGALLERY['CALCULATE_RATIO']           = 'Calculate with Max values from below';
$MOD_FOLDERGALLERY['MAX_WIDTH']                 = 'Maximum width';
$MOD_FOLDERGALLERY['MAX_HEIGHT']                = 'Maximum height';
$MOD_FOLDERGALLERY['ADVANCED_SETTINGS']         = 'Advanced Settings';
$MOD_FOLDERGALLERY['BACKGROUND_COLOR']          = 'Background-color';
$MOD_FOLDERGALLERY['BACKGROUND_OPACITY']        = 'Transparency Background color';
$MOD_FOLDERGALLERY['NEW_CAT']                   = 'Create New Category';
$MOD_FOLDERGALLERY['CAT_PARENT']                = 'Parent Category';
$MOD_FOLDERGALLERY['FOLDER_NAME']               = 'Foldername';
$MOD_FOLDERGALLERY['CAT_TITLE']                 = 'Category Title';
$MOD_FOLDERGALLERY['CAT_DESC']                  = 'Category Description';
$MOD_FOLDERGALLERY['DELETE_OLD_THUMBS']         = 'Delete old thumbnails';
$MOD_FOLDERGALLERY['DELETE']                    = 'Delete';
$MOD_FOLDERGALLERY['UPDATED_THUMB']             = 'Updated thumbnail successfully';
$MOD_FOLDERGALLERY['ACTIVE']                    = 'active';
// Tooltips
$MOD_FOLDERGALLERY['ROOT_FOLDER_STRING_TT']     = 'This folder specifies the root folder in which images are searched recursively. Please change only during installation, otherwise all information about the pictures will be lost!';
$MOD_FOLDERGALLERY['EXTENSIONS_STRING_TT']      = 'Specify the permitted file extensions here. Use the comma as separator. Upper and lower case letters are ignored.';
$MOD_FOLDERGALLERY['IMAGENAME_STRING_TT']       = 'Activate or deactivate the display of the image name in the lightbox window.';
$MOD_FOLDERGALLERY['CACHE_STRING_TT']           = 'Clear the language cache once, if you change the language content.';
$MOD_FOLDERGALLERY['INVISIBLE_STRING_TT']       = 'Folders you enter here will not be searched.';
$MOD_FOLDERGALLERY['DELETE_TITLE_TT']           = 'Attention, ALL pictures and subcategories including the pictures will be deleted from the server!';
$MOD_FOLDERGALLERY['NEW_THUMB_STRING_TT']       = 'Forces the creation of all thumbnails in the categories of the root directory. This setting is not saved';

$MOD_FOLDERGALLERY['PICTURES']  = 'Images';
$MOD_FOLDERGALLERY['1PICTURE']  = 'Image';
$MOD_FOLDERGALLERY['2PICTURES'] = 'Images';
$MOD_FOLDERGALLERY['3PICTURES'] = 'Images';
$MOD_FOLDERGALLERY['CATEGORIE']     = 'Category';
$MOD_FOLDERGALLERY['CATEGORIES']    = 'Categories';
$MOD_FOLDERGALLERY['ALL_CATEGORIES']= 'All Categories';
$MOD_FOLDERGALLERY['ALL_PICTURES']  = 'All Images';
$MOD_FOLDERGALLERY['ALIGNMENT']  = 'Orientation Categories Preview';

$MOD_FOLDERGALLERY['LEFT']       = 'Left';
$MOD_FOLDERGALLERY['RIGHT']      = 'Right';
$MOD_FOLDERGALLERY['CENTER']     = 'Centered';

$TEXT['CAT_SUCCESS'] = 'Category successfully saved';
$TEXT['IMG_SUCCESS'] = 'Images successfully saved';
$TEXT['CAT_SAVE_FAIL'] = 'Category could not be saved';
$TEXT['IMG_SAVE_FAIL'] = 'No images available for saving';
