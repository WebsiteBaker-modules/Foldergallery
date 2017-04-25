<?php

/*

Website Baker Project <http://www.websitebaker.org/>
Copyright (C) 2004-2008, Ryan Djurovich

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
$module_description = 'Skapa ett bildgalleri med foldrar och kategorier.';

//Variables for the Frontend
$MOD_FOLDERGALLERY['VIEW_TITLE']        = 'Bildgalleri';
$MOD_FOLDERGALLERY['CATEGORIES_TITLE']  = 'Kategorier';
$MOD_FOLDERGALLERY['BACK_STRING']       = 'Tillbaka till &ouml;versikt';
$MOD_FOLDERGALLERY['FRONT_END_ERROR']   = 'Antingen finns inte kategorin eller s&aring; inneh&aring;ller den varken bilder eller subkategorier.';
$MOD_FOLDERGALLERY['PAGE']              = 'Sida';
$MOD_FOLDERGALLERY['IMG_TITLE_TEXT']    = 'Bilder Title';
$MOD_FOLDERGALLERY['GAL']              = 'Galleri';
$MOD_FOLDERGALLERY['PAGINATION']        = 'Pagination Design';

//Variables for the Backend
$MOD_FOLDERGALLERY['PICS_PP']           = 'Bilder per sida';
$MOD_FOLDERGALLERY['GAL_PP']            = 'Galleri per sida';
$MOD_FOLDERGALLERY['LIGHTBOX']          = 'Lightbox';
$MOD_FOLDERGALLERY['MODIFY_CAT_TITLE']  = '&Auml;ndra kategorier och bildinformation';
$MOD_FOLDERGALLERY['MODIFY_CAT']        = '&Auml;ndra informationen f&ouml;r kategorin:';
$MOD_FOLDERGALLERY['CAT_NAME']          = 'Kategori -namn/-titel:';
$MOD_FOLDERGALLERY['CAT_DESCRIPTION']   = 'Kategorins beskrivning:';
$MOD_FOLDERGALLERY['MODIFY_IMG']        = '&Auml;ndra bilder:';
$MOD_FOLDERGALLERY['IMAGE']             = 'Bild';
$MOD_FOLDERGALLERY['IMAGE_NAME']        = 'Bildens namn';
$MOD_FOLDERGALLERY['IMG_CAPTION']       = 'Beskrivning av bilden';
$MOD_FOLDERGALLERY['REDIRECT']          = 'Du beh&ouml;ver utf&ouml;ra n&aring;gra <a href="{{SETTING_LINK}}">inst&auml;llningar</a> innan du anv&auml;nder galleriet. '
                                        . 'Du kommer att vidarebefordras innom tv&aring; sekunder (JavaScript beh&ouml;ver vara aktiverat)';
$MOD_FOLDERGALLERY['TITEL_BACKEND']     = 'Foldergallery Admin';
$MOD_FOLDERGALLERY['TITEL_MODIFY']      = '&Auml;ndra kategorier och bilder:';
$MOD_FOLDERGALLERY['SETTINGS']          = 'Inst&auml;llningar';
$MOD_FOLDERGALLERY['SETTINGS_STRING']   = 'Basic Inst&auml;llningar';
$MOD_FOLDERGALLERY['ROOT_DIR']          = 'Rootmapp';
$MOD_FOLDERGALLERY['EXTENSIONS']        = 'Till&aring;tna fil&auml;ndelser';
$MOD_FOLDERGALLERY['INVISIBLE']         = 'G&ouml;mda foldrar';
$MOD_FOLDERGALLERY['NEW_SCANN_INFO']    = 'Databasposterna har skapats. Tumnaglarna skapas n&auml;r kategorin visas f&ouml;rsta g&aring;ngen.';
$MOD_FOLDERGALLERY['FOLDER_NAME']       = 'Folderns namn';
$MOD_FOLDERGALLERY['DELETE']            = 'Radera?';
$MOD_FOLDERGALLERY['ERROR_MESSAGE']     = 'Inga uppgifter';
$MOD_FOLDERGALLERY['DB_ERROR']          = 'Database error!';
$MOD_FOLDERGALLERY['FS_ERROR']          = 'Kan inte radera foldern.';
$MOD_FOLDERGALLERY['NO_FILES_IN_CAT']   = 'Kategorin saknar bilder.';
$MOD_FOLDERGALLERY['SYNC']              = 'Synkronisera filsystem';
$MOD_FOLDERGALLERY['EDIT_CSS']          = 'Redigera CSS';
$MOD_FOLDERGALLERY['FOLDER_IN_FS']      = 'Filesystemfolder:';
$MOD_FOLDERGALLERY['CAT_TITLE']         = 'Kategorins titel:';
$MOD_FOLDERGALLERY['ACTION']            = '&Aring;tg&auml;rder:';
$MOD_FOLDERGALLERY['NO_CATEGORIES']     = 'Kategorier (subfoldrar) saknas.<br /><br />Galleriet fungerar &auml;nd&aring;, men inga kategorier visas.';
$MOD_FOLDERGALLERY['EDIT_THUMB']        = 'Redigera tumnagel';
$MOD_FOLDERGALLERY['EDIT_THUMB_DESCRIPTION']    = '<strong>V&auml;lj en ny bild</strong>';
$MOD_FOLDERGALLERY['EDIT_THUMB_BUTTON']         = 'Dra upp tumnageln';
$MOD_FOLDERGALLERY['THUMB_SIZE']                = 'Tumnaglarnas storlek';
$MOD_FOLDERGALLERY['THUMB_RATIO']               = 'Tumnaglarnas sidof&ouml;rh&aring;llande';
$MOD_FOLDERGALLERY['THUMB_NOT_NEW']             = '&Aring;terskapa ej tumnaglarna';
$MOD_FOLDERGALLERY['CHANGING_INFO']             = '&Auml;ndrar <strong>tumnaglarnas storlek</strong> eller <strong>sidof&ouml;rh&aring;llande.</strong> Raderar och &aring;terskapar alla tumnaglar.';
$MOD_FOLDERGALLERY['SYNC_DATABASE']             = 'Synkronisera databas med filsystem...';
$MOD_FOLDERGALLERY['SAVE_SETTINGS']             = 'Inst&auml;llningarna sparas...';
$MOD_FOLDERGALLERY['SORT_IMAGE']                = 'Sortera bilder';
$MOD_FOLDERGALLERY['BACK']                      = 'Back';
$MOD_FOLDERGALLERY['REORDER_INFO_STRING']       = 'Reorder result will be displayed here.';
$MOD_FOLDERGALLERY['REORDER_INFO_SUCESS']       = 'Saved new order sucessfully!';
$MOD_FOLDERGALLERY['REORDER_IMAGES']            = 'Sort Images';
$MOD_FOLDERGALLERY['SORT_BY_NAME']              = 'Sort images by filename';
$MOD_FOLDERGALLERY['SORT_BY_NAME_ASC']          = 'filename ascending';
$MOD_FOLDERGALLERY['SORT_BY_NAME_DESC']         = 'filename descending';
$MOD_FOLDERGALLERY['SORT_FREEHAND']             = 'Free sort (Drag & Drop)';
$MOD_FOLDERGALLERY['THUMB_EDIT_ALT']            = 'Edit Thumbnail';
$MOD_FOLDERGALLERY['IMAGE_DELETE_ALT']          = 'Delete Image';
$MOD_FOLDERGALLERY['EDIT_CATEGORIE']            = 'Edit Categorie';
$MOD_FOLDERGALLERY['EXPAND_COLAPSE']            = 'Expand/Colapse';
$MOD_FOLDERGALLERY['MOVE_UP']                   = 'Move up';
$MOD_FOLDERGALLERY['MOVE_DOWN']                 = 'Move down';
$MOD_FOLDERGALLERY['HELP_INFORMATION']          = 'Help/Info';
$MOD_FOLDERGALLERY['CAT_ACTIVE']                = 'active, click to deactivate!';
$MOD_FOLDERGALLERY['CAT_INACTIVE']              = 'inactive, click to activate!';
$MOD_FOLDERGALLERY['CAT_TOGGLE_ACTIV_FAIL']     = 'Could not acvtivate/deactivate this categorie! Do you try to hack the system?';
$MOD_FOLDERGALLERY['DELETE_ARE_YOU_SURE']       = 'Would you like to delete this file? The file is completely deleted from the server!';
$MOD_FOLDERGALLERY['DELETE_IMG_ARE_YOU_SURE']   = 'Would you like to delete this file including title, description? The file will be completely deleted from the server!';
$MOD_FOLDERGALLERY['DELETE_CAT_ARE_YOU_SURE']   = 'Would you like to delete this categorie including images, title, descriptions? The categorie and files will be completely deleted from the server!';
$MOD_FOLDERGALLERY['ADD_MORE_PICS']             = 'Add more Pictures to this categorie';
$MOD_FOLDERGALLERY['CATPIC_STRINGS'][0]         = 'Random';
$MOD_FOLDERGALLERY['CATPIC_STRINGS'][1]         = 'First';
$MOD_FOLDERGALLERY['CATPIC_STRINGS'][2]         = 'Last';
$MOD_FOLDERGALLERY['CAT_OVERVIEW_PIC']          = 'Categorie Preview';
$MOD_FOLDERGALLERY['THUMBNAIL_SETTINGS']        = 'Thumbnail Settigns';
$MOD_FOLDERGALLERY['LOAD_PRESET']               = 'Load Preset';
$MOD_FOLDERGALLERY['LOAD_PRESET_INFO']          = '<b>Attention, this functions overwrites all other fields below!</b>';
$MOD_FOLDERGALLERY['IMAGE_CROP']                = 'Image Crop';
$MOD_FOLDERGALLERY['IMAGE_DONT_CROP']           = 'No Crop';
$MOD_FOLDERGALLERY['IMAGE_DO_CROP']             = 'Crop the image';
$MOD_FOLDERGALLERY['RATIO']                     = 'Ratio';
$MOD_FOLDERGALLERY['CALCULATE_RATIO']           = 'Calculate with Max values from below';
$MOD_FOLDERGALLERY['MAX_WIDTH']                 = 'Maximum width';
$MOD_FOLDERGALLERY['MAX_HEIGHT']                = 'Maximum height';
$MOD_FOLDERGALLERY['ADVANCED_SETTINGS']         = 'Advanced Settings';
$MOD_FOLDERGALLERY['BACKGROUND_COLOR']          = 'Background-color';
$MOD_FOLDERGALLERY['NEW_CAT']                   = 'Create New Categorie';
$MOD_FOLDERGALLERY['CAT_PARENT']                = 'Parent Categorie';
$MOD_FOLDERGALLERY['FOLDER_NAME']               = 'Foldername';
$MOD_FOLDERGALLERY['CAT_TITLE']                 = 'Categorie Title';
$MOD_FOLDERGALLERY['CAT_DESC']                  = 'Categorie Description';
$MOD_FOLDERGALLERY['DELETE_OLD_THUMBS']         = 'Delete old thumbnails';
$MOD_FOLDERGALLERY['DELETE']                    = 'Delete';
$MOD_FOLDERGALLERY['UPDATED_THUMB']             = 'Updated thumbnail successfully';
$MOD_FOLDERGALLERY['ACTIVE']                    = 'active';





// Tooltips
$MOD_FOLDERGALLERY['ROOT_FOLDER_STRING_TT'] = 'Rotmappen f&ouml;r att s&ouml;ka efter bilder rekursivt. '
                                            . 'Om rotmappen &Auml;ndras vid ett senare tillf&auml;lle f&ouml;rloras alla bildinst&auml;llningar!';
$MOD_FOLDERGALLERY['EXTENSIONS_STRING_TT']  = 'Definiera de fil&auml;ndelser du vill till&aring;ta. (Skiftl&auml;gesok&auml;nsligt.) Anv&auml;nd "," (komma) som avgr&auml;nsare.';
$MOD_FOLDERGALLERY['INVISIBLE_STRING_TT']   = 'Foldrar som visas h&auml;r kommer inte att skannas.';
$MOD_FOLDERGALLERY['DELETE_TITLE_TT']       = 'Varning: Alla kategorier och bilder raderas! (&Auml;ven bilderna kommer att tas bort';

$MOD_FOLDERGALLERY['PICTURES']  = 'fotos';
$MOD_FOLDERGALLERY['1PICTURE']  = 'foto';
$MOD_FOLDERGALLERY['2PICTURES'] = 'fotos';
$MOD_FOLDERGALLERY['3PICTURES'] = 'fotos';
$MOD_FOLDERGALLERY['CATEGORIE']     = 'Category';
$MOD_FOLDERGALLERY['CATEGORIES']    = 'Categories';
$MOD_FOLDERGALLERY['ALL_CATEGORIES']= 'All categories';
$MOD_FOLDERGALLERY['ALL_PICTURES']  = 'All fotos';
$MOD_FOLDERGALLERY['ALIGNMENT']  = 'Categories  Preview alignment';
$MOD_FOLDERGALLERY['LEFT']       = 'left';
$MOD_FOLDERGALLERY['RIGHT']      = 'right';
$MOD_FOLDERGALLERY['CENTER']     = 'center';
