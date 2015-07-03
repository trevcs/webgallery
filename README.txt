Web Based Gallery Viewer

This code can be used to automatically generate a website from a directory
structure containing images (png/jpg/webm).

The images should all go under an "images" directory at the same level as the
index.php file.

In general, each set of folders in a given directory becomes a menu. If there
are YYYY, MM, (or YYYYMM), DD and HH folders these are treated specially to
create a separate date style menu. These dates do not have to be at the top of
the tree, but they need to be in descending order (year then month etc). If
there is a two digit style folder name that is not a date it should go below
the date. (If there is no day or hour folder, but a two digit folder exists
then you can set day_dir and/or hour_dir to false).

For example:

images/nzlam/YYYY/MM/DD/HH/rain_map/NZ/image_2015060100.png
                                      /image_2015060101.png
                                      /image_2015060102.png
                                      /image_2015060103.png
                                      /...
                                   /NI/image_2015060100.png
                                      /...
                                   /SI/image_2015060100.png
                                      /...
                          /mslp_map/NZ/image_2015060100_2015060100.png
                                      /image_2015060100_2015060101.png
                                      /image_2015060100_2015060102.png
                                      /image_2015060100_2015060103.png
                                      /...
                                   /NI/image_2015060100_2015060100.png
                                      /...
                                   /SI/image_2015060100_2015060100.png
                                      /...
images/nzcsm/YYYY/MM/DD/HH/rain_map/NZ/image_2015060100.png
                                      /image_2015060101.png
                                      /image_2015060102.png
                                      /image_2015060103.png
                                      /...
                                   /NI/image_2015060100.png
                                      /...
                                   /SI/image_2015060100.png
                                      /...
                          /meteogram/met_Wellington-Aero.png
                                    /Stratford-EWS.png
                                    /met_Christchurch.png

In the above one menu would choose between "nzlam" and "nzcsm". There would
be an "Analysis Time" menu constructed from the date directories. For nzcsm
there would be an mslp_map/rain_map menu and a NZ/NI/SI menu. For rain_map,
the date stamp (10 or 12 digits) will be translated into a validity time
string for the image link. For mslp_map, the second date stamp will be used
(the first is assumed to be the analysis time, but is ignored). For the 
meteogram images the filename (without extension) is used for the image link.

To give a menu a name (e.g. "Model" for nzcsm/nzlam, or "Region" for NZ/NI/SI,
the $menus array in the settings.inc file can be used. Each key in this array
should correspond to a menu and should itself be an array where the keys
are all possible directory names for that menu and the value is the name that
should be displayed instead of the directory name, i.e.

$menus = array(
    "model"  => array(
        'NZCSM'=>'Hi&nbsp;Res','nzcsm'=>'Hi&nbsp;Res',
        'nzlam'=>'2&nbsp;Day','global'=>'6&nbsp;Day'
    ),
    "region" => array(
        'NZ' => 'New Zealand', 'NI'=>'North Island', 'SI'=>'South Island'
    )
);
    
The name of the menu should be provided in $menu_title:

$menu_title=array('model'=>'Model','region'=>'Region');

There is a variable called $topmenu, which can be set to a menu that is
displayed as buttons instead of a drop down menu: $topmenu='model';
