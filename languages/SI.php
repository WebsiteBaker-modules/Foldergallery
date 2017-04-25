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
$MOD_FOLDERGALLERY['VIEW_TITLE']        = 'Galerija Slik';
$MOD_FOLDERGALLERY['CATEGORIES_TITLE']  = 'Kategorije';
$MOD_FOLDERGALLERY['BACK_STRING']       = 'Nazaj na pregled';
$MOD_FOLDERGALLERY['FRONT_END_ERROR']   = 'This category does not exist or does not contain Images and/or Subcategories!';
$MOD_FOLDERGALLERY['PAGE']              = 'Stran';
$MOD_FOLDERGALLERY['PAGINATION']        = 'Pagination Design';

//Variables for the Backend
$MOD_FOLDERGALLERY['PICS_PP']           = 'Slik na stran';
$MOD_FOLDERGALLERY['LIGHTBOX']          = 'Lightbox';
$MOD_FOLDERGALLERY['MODIFY_CAT_TITLE']  = 'Modify categories and image details';
$MOD_FOLDERGALLERY['MODIFY_CAT']        = 'Modify category details:';
$MOD_FOLDERGALLERY['CAT_NAME']          = 'Category name/title:';
$MOD_FOLDERGALLERY['CAT_DESCRIPTION']   = 'Category description:';
$MOD_FOLDERGALLERY['MODIFY_IMG']        = 'Uredi Slike:';
$MOD_FOLDERGALLERY['IMAGE']             = 'Slika';
$MOD_FOLDERGALLERY['IMAGE_NAME']        = 'Ime slike';
$MOD_FOLDERGALLERY['IMG_CAPTION']       = 'Opis Slike';
$MOD_FOLDERGALLERY['REDIRECT']          = 'You will have to make some <a href="{{SETTING_LINK}}">settings</a> before using the Gallery. '
                                        . 'You will be forwarded in 2 seconds. (If JavaScript is activated.)';
$MOD_FOLDERGALLERY['TITEL_BACKEND']     = 'Foldergallery Admin';
$MOD_FOLDERGALLERY['TITEL_MODIFY']      = 'Modify categories and images:';
$MOD_FOLDERGALLERY['SETTINGS']          = 'Nastavitve';
$MOD_FOLDERGALLERY['SETTINGS_STRING']   = 'Nastavitve pogleda (NE SPREMINJAJ!)';
$MOD_FOLDERGALLERY['ROOT_DIR']          = 'Root directory';
$MOD_FOLDERGALLERY['EXTENSIONS']        = 'Allowed extensions';
$MOD_FOLDERGALLERY['INVISIBLE']         = 'Hide folders';
$MOD_FOLDERGALLERY['NEW_SCANN_INFO']    = 'This action has created the database entries. The thumbnails are created when the category is shown the first time.';
$MOD_FOLDERGALLERY['FOLDER_NAME']       = 'Ime Mape!';
$MOD_FOLDERGALLERY['DELETE']            = 'Izbrišem?';
$MOD_FOLDERGALLERY['ERROR_MESSAGE']     = 'No data!';
$MOD_FOLDERGALLERY['DB_ERROR']          = 'Database error!';
$MOD_FOLDERGALLERY['FS_ERROR']          = 'Unable to delete folder!';
$MOD_FOLDERGALLERY['NO_FILES_IN_CAT']   = 'This category does not contain any images!';
$MOD_FOLDERGALLERY['SYNC_']              = 'Osveži celotno galerijo in mape!';
$MOD_FOLDERGALLERY['EDIT_CSS']          = 'Edit CSS (NE SPREMINJAJ!!!)';
$MOD_FOLDERGALLERY['FOLDER_IN_FS']      = 'Filesystem folder:';
$MOD_FOLDERGALLERY['CAT_TITLE']         = 'Naslov Kategorije:';
$MOD_FOLDERGALLERY['ACTION']            = 'Actions:';
$MOD_FOLDERGALLERY['NO_CATEGORIES']     = 'No categories (=Subfolders) found.<br /><br />The Gallery will work, anyway, but no categories are shown.';
$MOD_FOLDERGALLERY['EDIT_THUMB']        = 'Uredi thumbnail';
$MOD_FOLDERGALLERY['EDIT_THUMB_DESCRIPTION']    = '<strong>Please select new image</strong>';
$MOD_FOLDERGALLERY['EDIT_THUMB_BUTTON']         = 'Draw up thumbnail';
$MOD_FOLDERGALLERY['THUMB_SIZE']                = 'Thumbnail size';
$MOD_FOLDERGALLERY['THUMB_RATIO']               = 'Thumbnail ratio';
$MOD_FOLDERGALLERY['THUMB_NOT_NEW']             = 'Dont recreat thumbnails';
$MOD_FOLDERGALLERY['CHANGING_INFO']             = 'Changing <strong>thumb size</strong> or <strong>thumb ratio</strong> will delete (and recreate) all thumbs.';
$MOD_FOLDERGALLERY['SYNC_DATABASE']             = 'Synchronize file system with database...';
$MOD_FOLDERGALLERY['SAVE_SETTINGS']             = 'Settings are stored...';
$MOD_FOLDERGALLERY['SORT_IMAGE']                = 'Sort images';
$MOD_FOLDERGALLERY['BACK']                      = 'Nazaj';
$MOD_FOLDERGALLERY['REORDER_INFO_STRING']       = 'Reorder result will be displayed here.';
$MOD_FOLDERGALLERY['REORDER_INFO_SUCESS']       = 'Saved new order sucessfully!';
$MOD_FOLDERGALLERY['REORDER_IMAGES']            = 'Uredi vrstni red';
$MOD_FOLDERGALLERY['SORT_BY_NAME']              = 'Sort images by filename';
$MOD_FOLDERGALLERY['SORT_BY_NAME_ASC']          = 'filename ascending';
$MOD_FOLDERGALLERY['SORT_BY_NAME_DESC']         = 'filename descending';
$MOD_FOLDERGALLERY['SORT_FREEHAND']             = 'Free sort (Drag & Drop)';
$MOD_FOLDERGALLERY['THUMB_EDIT_ALT']            = 'Uredi Thumbnail';
$MOD_FOLDERGALLERY['IMAGE_DELETE_ALT']          = 'Izbriši sliko';
$MOD_FOLDERGALLERY['EDIT_CATEGORIE']            = 'Uredi Kategorijo';
$MOD_FOLDERGALLERY['EXPAND_COLAPSE']            = 'Expand/Colapse';
$MOD_FOLDERGALLERY['MOVE_UP']                   = 'Premakni Gor';
$MOD_FOLDERGALLERY['MOVE_DOWN']                 = 'Premakni Dol';
$MOD_FOLDERGALLERY['HELP_INFORMATION']          = 'Pomoč/Info';
$MOD_FOLDERGALLERY['CAT_ACTIVE']                = 'aktivno, klikni za deaktivacijo!';
$MOD_FOLDERGALLERY['CAT_INACTIVE']              = 'ni aktivna, klikni za aktivacijo!';
$MOD_FOLDERGALLERY['CAT_TOGGLE_ACTIV_FAIL']     = 'Could not acvtivate/deactivate this categorie! Do you try to hack the system?';
$MOD_FOLDERGALLERY['DELETE_ARE_YOU_SURE']       = 'Would you like to delete this file? The file is completely deleted from the server!';
$MOD_FOLDERGALLERY['DELETE_IMG_ARE_YOU_SURE']   = 'Would you like to delete this file including title, description? The file will be completely deleted from the server!';
$MOD_FOLDERGALLERY['DELETE_CAT_ARE_YOU_SURE']   = 'Would you like to delete this categorie including images, title, descriptions? The categorie and files will be completely deleted from the server!';
$MOD_FOLDERGALLERY['ADD_MORE_PICS']             = 'Dodaj slike tej kategoriji';
$MOD_FOLDERGALLERY['CATPIC_STRINGS'][0]         = 'Random';
$MOD_FOLDERGALLERY['CATPIC_STRINGS'][1]         = 'Prva';
$MOD_FOLDERGALLERY['CATPIC_STRINGS'][2]         = 'Zadnja';
$MOD_FOLDERGALLERY['CAT_OVERVIEW_PIC']          = 'Categorie Preview';
$MOD_FOLDERGALLERY['THUMBNAIL_SETTINGS']        = 'Thumbnail Settigns';
$MOD_FOLDERGALLERY['LOAD_PRESET']               = 'Load Preset';
$MOD_FOLDERGALLERY['LOAD_PRESET_INFO']          = '<b>Attention, this functions overwrites all other fields below!</b>';
$MOD_FOLDERGALLERY['IMAGE_CROP']                = 'Image Crop';
$MOD_FOLDERGALLERY['IMAGE_DONT_CROP']           = 'No Crop';
$MOD_FOLDERGALLERY['IMAGE_DO_CROP']             = 'Crop the image';
$MOD_FOLDERGALLERY['RATIO']                     = 'Ratio';
$MOD_FOLDERGALLERY['CALCULATE_RATIO']           = 'Calculate with Max values from below';
$MOD_FOLDERGALLERY['MAX_WIDTH']                 = 'Maximum Širina';
$MOD_FOLDERGALLERY['MAX_HEIGHT']                = 'Maximum Višina';
$MOD_FOLDERGALLERY['ADVANCED_SETTINGS']         = 'Dodatne nastavitve';
$MOD_FOLDERGALLERY['BACKGROUND_COLOR']          = 'Background-color';
$MOD_FOLDERGALLERY['NEW_CAT']                   = 'Ustvari novo Galerijo';
$MOD_FOLDERGALLERY['CAT_PARENT']                = 'Nadrejena Kategorija (NI TREBA!!!)';
$MOD_FOLDERGALLERY['FOLDER_NAME']               = 'Ime Mape (kratko brez &#353;umnikov!!!)';
$MOD_FOLDERGALLERY['CAT_TITLE']                 = 'Naslov Galerije (Kategorije)';
$MOD_FOLDERGALLERY['CAT_DESC']                  = 'Opis Kategorije';

$MOD_FOLDERGALLERY['DELETE_OLD_THUMBS']         = 'Delete old thumbnails';
$MOD_FOLDERGALLERY['DELETE']                    = 'Izbriši';
$MOD_FOLDERGALLERY['UPDATED_THUMB']             = 'Updated thumbnail successfully';
$MOD_FOLDERGALLERY['ACTIVE']                    = 'Aktivno';


// Tooltips
$MOD_FOLDERGALLERY['ROOT_FOLDER_STRING_TT']     = 'This is the basic (root) folder to scan for images recursively. '
                                                . 'Please do not change this folder later, or all image settings will be lost!';
$MOD_FOLDERGALLERY['EXTENSIONS_STRING_TT']      = 'Define the file suffixes you wish to allow here. (Case insensitive.) Use "," (comma) as delimiter.';
$MOD_FOLDERGALLERY['INVISIBLE_STRING_TT']       = 'Folder that are listed here will not be scanned.';
$MOD_FOLDERGALLERY['DELETE_TITLE_TT']           = 'Warning: This will delete ALL categories and images! (The images will be REMOVED, too!)';


/////////////////////////////////////////////
//               new language variables
/////////////////////////////////////////////

$MOD_FOLDERGALLERY['IMG_TITLE_TEXT']    = 'Picture Title';
$MOD_FOLDERGALLERY['GAL_PP']            = 'Gallerys per page';
$MOD_FOLDERGALLERY['GAL']               = 'Gallery';
$MOD_FOLDERGALLERY['PICTURES']          = 'Slik';
$MOD_FOLDERGALLERY['1PICTURE']          = 'Slika';
$MOD_FOLDERGALLERY['2PICTURES']         = 'Sliki';
$MOD_FOLDERGALLERY['3PICTURES']         = 'Slike';
$MOD_FOLDERGALLERY['CATEGORIE']     = 'Category';
$MOD_FOLDERGALLERY['CATEGORIES']    = 'Categorys';
$MOD_FOLDERGALLERY['ALL_CATEGORIES']= 'All categories';
$MOD_FOLDERGALLERY['ALL_PICTURES']  = 'All sliki';
$MOD_FOLDERGALLERY['ALIGNMENT']  = 'Categorys  Preview alignment';
$MOD_FOLDERGALLERY['LEFT']       = 'left';
$MOD_FOLDERGALLERY['RIGHT']      = 'right';
$MOD_FOLDERGALLERY['CENTER']     = 'center';
