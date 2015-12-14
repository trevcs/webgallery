<?php
#######
## Settings for gallery viewer
#######

# Web page title
$main_title='Web Page Title';

# additional links eg:
#$links=array(
#    "Wind Farm Catalogue"=>'http://wrh-1.niwa.co.nz/~mooresa/data/ENAE1201/catalogue/catalogue.php',
#    "Product Information"=>'http://wrh-1.niwa.co.nz/~mooresa/data/ENAE1201/wind-suite-faq.mhtml'
#);

# Full page width
$page_width='1140px';
# Maximum image width
$image_width='850px';
# Width for left hand navigation
$nav_width='250px';

# name of folder to use for 'main menu' (coloured/boxed links)
# set to false or '' for no blue menu boxes
$topmenu='type';

# Set to false if you don't want do handle date directories "specially"
# (overrides $day_dir and $hour_dir if set to false)
$date_dir=true;
# Set to false if there *aren't* '/yyyymm/dd/' subfolders
$day_dir=true;
# Set to false if there *aren't* '/yyyymm/dd/hh/' subfolders
$hour_dir=true;

# format for valid time in image list  (try "Y-m-d H:i T")
#$date_format="H:i D, M d, Y T"

# Set to false to prevent unknown directory names being used
#$allow_unknown=true;

# names of 'type's that should have a virtual tz option (just changes the tz of the image link)
#$tz_types=array("aereal","cloud","wind","rain");
# names of 'type's that are site based (changes the way image links are generated)
$site_types=array("meteo","rose","tswind","movie","rolling","archive","trends");

$filename_replace=array(
    '/^met[^_]*_/'=>'',
    '/-/'=>' & ','/_/'=>' ',
    '/(sfc|total) /'=>'',
    '/(^| )me($| )/'=>'$1bias$2',
    '/(^| )rmse($| )/'=>'$1RMSE$2',
    '/(^| )rh($| )/'=>'$1RH$2',
    '/(^| )mslp($| )/'=>'$1MSLP$2',
    '/(^| )ets($| )/'=>'$1ETS$2',
    '/(^| )roc($| )/'=>'$1ROC$2',
    '/(^| )pc($| )/'=>'$1PC$2',
    '/(^| )etsbias($| )/'=>'$1ETS/Bias$2');
$filename_uc=true;

# all possible folder names and contents (with display names)
$menus=array(
    "type"   => array(
        "aereal"=>"Maps","meteo"=>"Meteograms",
        "movie"=>"Forecast Movies",
        "cloud"=>"Cloud","rain"=>"Rain maps",
        "wind"=>"Surface Wind",
        "gust"=>"Surface Gust",
        "cloud_base"=>"Cloud Base",
        "wind_233"=>"233m Wind",
        "wind_693"=>"693m Wind",
        "wind_1533"=>"1533m Wind",
        "rh"=>"Relative Humidity",
        "rose"=>"Roses","tswind"=>"Time&nbsp;Series",
        "rolling"=>"Rolling",
        "archive"=>"Archive",
        "trends"=>"Trends",
        "apptemp"=>"Apparent&nbsp;Temperature"
    ),
    "locations" => array(
        "nz-all"=>"All&nbsp;NZ",
        "regions-all"=>"Climate&nbsp;regions",
        "nz-synop"=>"Synop&nbsp;locations",
        "regions-synop"=>"Synop&nbsp;by&nbsp;region"
    ),
    "region" => array(
        "NZ"=>"All&nbsp;NZ",
        "NNI"=>"North&nbsp;NI","CNI"=>"Central&nbsp;NI",
        "CNZ"=>"Central&nbsp;NZ","CSI"=>"Central&nbsp;SI",
        "SSI"=>"South&nbsp;SI"
    ),
    "model"  => array(
        'NZCSM'=>'Hi&nbsp;Res','nzcsm'=>'Hi&nbsp;Res',
        'NZLAM'=>'2&nbsp;Day','NZCONV'=>'NZCONV',
        'nzlam'=>'2&nbsp;Day','global'=>'6&nbsp;Day',
        'ncep' => '8&nbsp;Day','ncep-ens'=>'15&nbsp;Day'
    ),
    "period"    => array(
        '00'=>'Model&nbsp;timestep','01'=>'1hr','03'=>'3hr','06'=>'6hr',
        '12'=>'12hr','24'=>'24hr','48'=>'48hr'
    ),
    "averaging" => array(
        "monthly"=>"Monthly","seasonal"=>"Seasonal","annual"=>"Annual"
    ),
    "acc"    => array(
        '30min'=>'30min','1hr'=>'1hr','3hr'=>'3hr','6hr'=>'6hr',
        '12hr'=>'12hr','24hr'=>'24hr','48hr'=>'48hr'
    ),
    "corr"   => array(
        'mos'=>'Mos','raw'=>'Raw'
    ),
    "tz"     => array(
        'NZ'=>'NZ','UT'=>'UT'
    )
);

# Display names for folders
$menu_title=array(
    'type'=>'Product',
    'model'=>'Model',
    'tz'=>'Timezone',
    'region'=>'Region',
    'acc'=>'Accumulation period',
    'corr'=>'Corrections',
    'locations'=>'Locations',
    'averaging'=>'Averaging period',
    'period'=>'Verification grouping'
);
?>
