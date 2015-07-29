<?php

$main_title='Weather';

$links=array();

$page_width='1240px';
$image_width='800px';
$nav_width='400px';

$allow_unknown=true;

$hour_dir=true;
$day_dir=true;

$tz_types=array("aereal","cloud","wind","rain");
$site_types=array("meteo","rose","tswind");

$topmenu='type';

$menus=array(
    "type"   => array(
        "cloud"=>"Cloud","rain"=>"Rain","wind"=>"Wind",
        "aereal"=>"Maps","meteo"=>"Meteograms",
        "rose"=>"Roses","tswind"=>"Time&nbsp;Series"
    ),
    "region" => array(
        "NZ"=>"All&nbsp;NZ",
        "NNI"=>"North&nbsp;NI","CNI"=>"Central&nbsp;NI",
        "CNZ"=>"Central&nbsp;NZ","CSI"=>"Central&nbsp;SI",
        "SSI"=>"South&nbsp;SI"
    ),
    "model"  => array(
        'NZLAM'=>'NZLAM','NZCONV'=>'NZCONV',
        'nzlam'=>'NZLAM','global'=>'Global','ncep'=>'NCEP'
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
$menu_title=array('type'=>'Product','model'=>'Model','tz'=>'Timezone','region'=>'Region','acc'=>'Accumulation period','corr'=>'Corrections');

$filename_replace=array('/^met[^_]*_/'=>'','/[-_]/'=>' ');
$filename_uc=false;

$date_format="H:i D, M d, Y T";

require 'settings.inc';

$menu_contents=array();
foreach (array_keys($menus) as $m) {
    $menu_contents[$m]=array();
}

# we need to have a default tz as it is used even if not in dir tree
$chosen=array('tz'=>'NZ');

$url_args=array();

$error=NULL;
$yearmon=NULL;
$year=NULL;
$month=NULL;
$day=NULL;
$hour=NULL;
if (isset($_REQUEST['date'])) {
    if (preg_match("/^[\d]{10}$/", $_REQUEST['date'])) {
        $date=$_REQUEST['date'];
        $yearmon=substr($date,0,6);
        $year=substr($date,0,4);
        $month=substr($date,4,2);
        $day=substr($date,6,2);
        $hour=substr($date,8,2);
        if ($hour=='25') {
            $hour=NULL;
        }
        if ($day=='00') {
            $day=NULL;
        }
    } else {
        $error="Forecast date in address bar in wrong format (YYYYMMDDHH)";
    }
}
if (isset($_REQUEST['yearmon'])) {
    if (preg_match("/^[\d]{6}$/", $_REQUEST['yearmon'])) {
        $yearmon=$_REQUEST['yearmon'];
    } else {
        $error="yearmon option in address bar in wrong format (YYYYMM)";
    }
}
if (isset($_REQUEST['year'])) {
    if (preg_match("/^[\d]{4}$/", $_REQUEST['year'])) {
        $year=$_REQUEST['year'];
    } else {
        $error="year option in address bar in wrong format (YYYY)";
    }
}
if (isset($_REQUEST['month'])) {
    if (preg_match("/^[\d]{2}$/", $_REQUEST['month'])) {
        $month=$_REQUEST['month'];
    } else {
        $error="month option in address bar in wrong format (MM)";
    }
}
if (isset($_REQUEST['day'])) {
    if (preg_match("/^[\d]{2}$/", $_REQUEST['day'])) {
        $day=$_REQUEST['day'];
    } else {
        $error="day option in address bar in wrong format (DD)";
    }
}
if (isset($_REQUEST['hour'])) {
    if (preg_match("/^[\d]{2}$/", $_REQUEST['hour'])) {
        $hour=$_REQUEST['hour'];
    } else {
        $error="hour option in address bar in wrong format (DD)";
    }
}

$image_path="images/";

$comb_ym = true;
# This is the loop to search the dir tree for meaningful
# directory names or a list of pngs
# (maximum of 10 nested directories)
for ($i = 1; $i <= 10; $i++) {
    $allimage=glob($image_path."*.{png,jpg,jpeg,webm}",GLOB_BRACE);
#    $allimage=preg_grep('/.png$/',$allfile);
    if (count($allimage)>0) {
        break;
    }
    $alldir=glob($image_path."*",GLOB_ONLYDIR);
    $found_something=false;
    $alldir=array_map("basename",$alldir);

    # first check for date structure
    if (!isset($allym) && (!isset($allyear) || !isset($allmonth))) {
        $allym=preg_grep('/^\d{6}$/',$alldir);
        if (count($allym)==0) {
            $allym = null;
        } else {
            if (is_null($yearmon) || !in_array($yearmon,$allym)) {
                $yearmon=$allym[count($allym)-1];
            }
            $ym_key=array_search($yearmon,$allym);
            $up_to_year=$image_path;
            $image_path.=$yearmon."/";
            continue;
        }
        if (!isset($allyear)) {
            $allyear=preg_grep('/^\d{4}$/',$alldir);
            if (count($allyear)==0) {
                $allyear = null;
            } else {
                $comb_ym = false;
                if (is_null($year) || !in_array($year,$allyear)) {
                    $year=$allyear[count($allyear)-1];
                }
                $year_key=array_search($year,$allyear);
                $up_to_year=$image_path;
                $image_path.=$year."/";
                continue;
            }
        } else {
            $allmonth=preg_grep('/^\d{2}$/',$alldir);
            if (count($allmonth)==0) {
                $allmonth = null;
            } else {
                if (is_null($month) || !in_array($month,$allmonth)) {
                    $month=$allmonth[count($allmonth)-1];
                }
                $month_key=array_search($month,$allmonth);
                $image_path.=$month."/";
                $yearmon=$year.$month;
                continue;
            }
        }
    } elseif ($day_dir && !isset($allday)) {
        $allday=preg_grep('/^\d{2}$/',$alldir);
        if (count($allday)==0) {
            $allday = null;
        } else {
            if (is_null($day) || !in_array($day,$allday)) {
                $day=$allday[count($allday)-1];
            }
            $day_key=array_search($day,$allday);
            $image_path.=$day."/";
            continue;
        }
    } elseif ($hour_dir && !isset($allhour)) {
        $allhour=preg_grep('/^\d{2}$/',$alldir);
        if (count($allhour)==0) {
            $allhour = null;
        } else {
            if (is_null($hour) || !in_array($hour,$allhour)) {
                $hour=$allhour[count($allhour)-1];
            }
            $hour_key=array_search($hour,$allhour);
            $image_path.=$hour."/";
            continue;
        }
    }

    # then for set menus
    foreach(array_keys($menus) as $m) {
        $temp=array_intersect(array_keys($menus[$m]),$alldir);
        if (count($temp)==count($alldir) && count($temp) > 0) {
            $menu_contents[$m]=array_values($temp);

            if (isset($_REQUEST[$m]) && in_array($_REQUEST[$m],$menu_contents[$m])) {
                $chosen[$m]=$_REQUEST[$m];
                $url_args[$m]=$m."=".$chosen[$m];
            } else {
                $chosen[$m]=$menu_contents[$m][0];
            }
            $found_something=true;
            $image_path.=$chosen[$m]."/";
            break;
        }
    }

    # then for leftovers
    if (!$found_something) {
        if ($allow_unknown && count($alldir)>0) {
            $m='menu'.$i;
            $menu_contents[$m]=$alldir;
            $menu_title[$m]="Menu $i";
            if (isset($_REQUEST[$m]) && in_array($_REQUEST[$m],$menu_contents[$m])) {
                $chosen[$m]=$_REQUEST[$m];
                $url_args[$m]=$m."=".$chosen[$m];
            } else {
                $chosen[$m]=$menu_contents[$m][0];
            }
            $image_path.=$chosen[$m]."/";
        } else {
            break;
        }
    }
}

if ($comb_ym) {
    $ymdir=$yearmon;
    $year=substr($yearmon,0,4);
    $month=substr($yearmon,4,2);
} else {
    $ymdir="$year/$month";
}

if (isset($allyear)||isset($allym)) {
    $oldhour=$hour;
    if (is_null($hour)) { $hour='25'; }
    $oldday=$day;
    if (is_null($day)) { $day='00'; }
    $url_args["date"]="date=$year$month$day$hour";

    $newhour=$hour;
    $newday=$day;
    if (!is_null($oldhour) && array_key_exists($hour_key-1,$allhour)) {
        $prevdate=$year.$month.$day.$allhour[$hour_key-1];
    } elseif (!is_null($oldday) && array_key_exists($day_key-1,$allday)) {
        $i=1;
        if (!is_null($oldhour)) {
            for ($i = 1; $i <= $day_key; $i++) {
                $newhour=array_pop(array_map("basename",glob("$up_to_year/$ymdir/".$allday[$day_key-$i]."/??")));
                if (!is_null($newhour)) { break; }
            }
        }
        $prevdate=$year.$month.$allday[$day_key-$i].$newhour;
    } elseif (!$comb_ym && array_key_exists($month_key-1,$allmonth)) {
        $i=1;
        if (!is_null($oldhour)) {
            for ($i = 1; $i <= $month_key; $i++) {
                $newhour=array_pop(array_map("basename",glob("$up_to_year/$year/".$allmonth[$month_key-$i]."/??/??")));
                if (!is_null($newhour)) { break; }
            }
            $newday=array_pop(array_map("basename",glob("$up_to_year/$year/".$allmonth[$month_key-$i]."/??")));
        } elseif (!is_null($oldday)) {
            for ($i = 1; $i <= $month_key; $i++) {
                $newday=array_pop(array_map("basename",glob("$up_to_year/$year/".$allmonth[$month_key-$i]."/??")));
                if (!is_null($newday)) { break; }
            }
        }
        $prevdate=$year.$allmonth[$month_key-$i].$newday.$newhour;
    } elseif (!$comb_ym && array_key_exists($year_key-1,$allyear)) {
        if (!is_null($oldhour)) {
            for ($i = 1; $i <= $year_key; $i++) {
                $newhour=array_pop(array_map("basename",glob("$up_to_year/".$allyear[$year_key-$i]."/??/??/??")));
                if (!is_null($newhour)) { break; }
            }
            $newday=array_pop(array_map("basename",glob("$up_to_year/".$allyear[$year_key-$i]."/??/??")));
            $newmon=array_pop(array_map("basename",glob("$up_to_year/".$allyear[$year_key-$i]."/??")));
        } elseif (!is_null($oldday)) {
            for ($i = 1; $i <= $year_key; $i++) {
                $newday=array_pop(array_map("basename",glob("$up_to_year/".$allyear[$year_key-$i]."/??/??")));
                if (!is_null($newday)) { break; }
            }
            $newmon=array_pop(array_map("basename",glob("$up_to_year/".$allyear[$year_key-$i]."/??")));
        } else {
            for ($i = 1; $i <= $year_key; $i++) {
                $newmon=array_pop(array_map("basename",glob("$up_to_year/".$allyear[$year_key-$i]."/??")));
                if (!is_null($newmon)) { break; }
            }
        }
        $prevdate=$allyear[$year_key-$i].$newmon.$newday.$newhour;
    } elseif ($comb_ym && array_key_exists($ym_key-1,$allym)) {
        $i=1;
        if (!is_null($oldhour)) {
            for ($i = 1; $i <= $ym_key; $i++) {
                $newhour=array_pop(array_map("basename",glob("$up_to_year/".$allym[$ym_key-$i]."/??/??")));
                if (!is_null($newhour)) { break; }
            }
            $newday=array_pop(array_map("basename",glob("$up_to_year/".$allym[$ym_key-$i]."/??")));
        } elseif (!is_null($oldday)) {
            for ($i = 1; $i <= $ym_key; $i++) {
                $newday=array_pop(array_map("basename",glob("$up_to_year/".$allym[$ym_key-$i]."/??")));
                if (!is_null($newday)) { break; }
            }
        }
        $prevdate=$allym[$ym_key-$i].$newday.$newhour;
    } else {
        $prevdate=NULL;
    }

    $newhour=$hour;
    $newday=$day;
    if (!is_null($oldhour) && array_key_exists($hour_key+1,$allhour)) {
        $nextdate=$yearmon.$day.$allhour[$hour_key+1];
    } elseif (!is_null($oldday) && array_key_exists($day_key+1,$allday)) {
        $i=1;
        if (!is_null($oldhour)) {
            for ($i = 1; $i < count($allday)-$day_key; $i++) {
                $newhour=array_shift(array_map("basename",glob("$up_to_year/$yearmon/".$allday[$day_key+$i]."/??")));
                if (!is_null($newhour)) { break; }
            }
        }
        $nextdate=$yearmon.$allday[$day_key+$i].$newhour;
    } elseif (!$comb_ym && array_key_exists($month_key+1,$allmonth)) {
        $i=1;
        if (!is_null($oldhour)) {
            for ($i = 1; $i < count($allmonth)-$month_key; $i++) {
                $newhour=array_shift(array_map("basename",glob("$up_to_year/$year/".$allmonth[$month_key+$i]."/??/??")));
                if (!is_null($newhour)) { break; }
            }
            $newday=array_shift(array_map("basename",glob("$up_to_year/$year/".$allmonth[$month_key+$i]."/??")));
        } elseif (!is_null($oldday)) {
            for ($i = 1; $i < count($allmonth)-$month_key; $i++) {
                $newday=array_shift(array_map("basename",glob("$up_to_year/$year/".$allmonth[$month_key+$i]."/??")));
                if (!is_null($newday)) { break; }
            }
        }
        $nextdate=$year.$allmonth[$month_key+$i].$newday.$newhour;
    } elseif (!$comb_ym && array_key_exists($year_key+1,$allyear)) {
        if (!is_null($oldhour)) {
            for ($i = 1; $i < count($allyear)-$year_key; $i++) {
                $newhour=array_shift(array_map("basename",glob("$up_to_year/".$allyear[$year_key+$i]."/??/??/??")));
                if (!is_null($newhour)) { break; }
            }
            $newday=array_shift(array_map("basename",glob("$up_to_year/".$allyear[$year_key+$i]."/??/??")));
            $newmon=array_shift(array_map("basename",glob("$up_to_year/".$allyear[$year_key+$i]."/??")));
        } elseif (!is_null($oldday)) {
            for ($i = 1; $i < count($allyear)-$year_key; $i++) {
                $newday=array_shift(array_map("basename",glob("$up_to_year/".$allyear[$year_key+$i]."/??/??")));
                if (!is_null($newday)) { break; }
            }
            $newmon=array_shift(array_map("basename",glob("$up_to_year/".$allyear[$year_key+$i]."/??")));
        } else {
            for ($i = 1; $i < count($allyear)-$year_key; $i++) {
                $newmon=array_shift(array_map("basename",glob("$up_to_year/".$allyear[$year_key+$i]."/??")));
                if (!is_null($newmon)) { break; }
            }
        }
        $nextdate=$allyear[$year_key+$i].$newmon.$newday.$newhour;
    } elseif ($comb_ym && array_key_exists($ym_key+1,$allym)) {
        $i=1;
        if (!is_null($oldhour)) {
            for ($i = 1; $i < count($allym)-$ym_key; $i++) {
                $newhour=array_shift(array_map("basename",glob("$up_to_year/".$allym[$ym_key+$i]."/??/??")));
                if (!is_null($newhour)) { break; }
            }
            $newday=array_shift(array_map("basename",glob("$up_to_year/".$allym[$ym_key+$i]."/??")));
        } elseif (!is_null($oldday)) {
            for ($i = 1; $i < count($allym)-$ym_key; $i++) {
                $newday=array_shift(array_map("basename",glob("$up_to_year/".$allym[$ym_key+$i]."/??")));
                if (!is_null($newday)) { break; }
            }
        }
        $nextdate=$allym[$ym_key+$i].$newday.$newhour;
    } else {
        $nextdate=NULL;
    }
}

$delay=256;
if (isset($_REQUEST['delay'])) {
    if (preg_match("/^\d*$/", $_REQUEST['delay'])) {
        $delay=$_REQUEST['delay'];
    } else {
        $error="delay option in address bar in wrong format";
    }
    $url_args['delay']="delay=".$delay;
}
$autostart='false';
if (isset($_REQUEST['autostart'])) {
    $autostart='true';
}

# get any $_REQUESTS for menus not in current dir tree
foreach (array_keys($menus) as $m) {
    if (count($menu_contents[$m])>0) { 
        # already done this in the while loop above
        continue;
    } elseif (isset($_REQUEST[$m])) {
        $chosen[$m]=$_REQUEST[$m];
        $url_args[$m]=$m."=".$chosen[$m];
    } else {
        $chosen[$m]=NULL;
    }
}

if ($chosen['tz']=='UT') {
    $tzone='UTC';
} else {
    $tzone='Pacific/Auckland';
}

if (is_null($chosen['type'])) {
    $chosen['type']='';
}

# Might need to add the tz menu if not in dir tree
if (in_array($chosen['type'],$tz_types) && count($menu_contents['tz'])==0) {
    $menu_contents['tz'] = array_keys($menus['tz']);
}

if (isset($allhour)) {
    $ref_time=new DateTime("$year-$month-$day $hour:00:00",new DateTimeZone("UTC"));
    $ref_time->setTimezone(new DateTimeZone($tzone));
}

?>

<html>
  <head>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8">
    <title><?php echo $main_title; ?> Products</title>
    <link rel="stylesheet" href="css/basic.css" type="text/css" />
    <link rel="stylesheet" href="css/galleriffic-1.css" type="text/css" />
    <script type="text/javascript" src="js/jquery-1.3.2.js"></script>
    <script type="text/javascript" src="js/jquery.galleriffic.js"></script>
    <script type="text/javascript" src="js/jquery.history.js"></script>
  </head>
  <body>
    <div id="page">
      <div id="container">
<?php
echo "<h1>".$main_title;
if (isset($allhour)) {
    echo ": Analysis Time ".$ref_time->format("Y-m-d H:i T");
}
echo "</h1>";

if (!is_null($error)) {
    echo "<p class='error'>$error</p>\n";
} 
echo "<form id='mainform' action='".$_SERVER['PHP_SELF']."' method='get'>";
if (!is_null($chosen[$topmenu])) {
    echo "<input type='hidden' name='".$topmenu."' value='".$chosen[$topmenu]."' />\n";
}
echo "<input type='hidden' name='delay' value='".$delay."' />\n";

if (isset($allym)) {
    echo '<p style="float:left"><span class="bold">Analysis time:</span> &nbsp;';
}
$this_url_args=$url_args;

if (!is_null($nextdate) || !is_null($prevdate)) {
    if (!is_null($prevdate)) {
        $this_url_args['date']="date=$prevdate";
        echo "<a href='".$_SERVER['PHP_SELF']."?".implode('&',$this_url_args)."'>&lsaquo; Previous</a> ";
    } else {
        echo "&lsaquo; Previous ";
    }
    if (!is_null($nextdate)) {
        $this_url_args['date']="date=$nextdate";
        echo "<a href='".$_SERVER['PHP_SELF']."?".implode('&',$this_url_args)."'>Next &rsaquo;</a> ";
    } else {
        echo "Next &rsaquo; ";
    }
    echo "&nbsp;  &nbsp;";
}

if (isset($allhour)) {
    #    $this_time=new DateTime("$yr-$mh-$dy $hr:$mn:00",new DateTimeZone("UTC"));
    #    $this_time->format("Y-m-d H:i T");

    echo " <select id='hour' name='hour'>";
    foreach ($allhour as $a) {
        $this_time=new DateTime("$year-$month-$day $a:00:00",new DateTimeZone("UTC"));
        echo "<option value='$a'";
        if ($a==$hour) { echo " SELECTED"; }
        echo ">".$this_time->format("H T")."</option>";
    }
    echo "</select> ";
}
if (isset($allday)) {
    echo " <select id='day' name='day'>";
    foreach ($allday as $a) {
        $this_time=new DateTime("$year-$month-$a 00:00:00",new DateTimeZone("UTC"));
        echo "<option value='$a'";
        if ($a==$day) { echo " SELECTED"; }
        echo ">".$this_time->format("jS")."</option>";
    }
    echo "</select> ";
}
if (isset($allmonth)) {
    echo " <select id='month' name='month'>";
    foreach ($allmonth as $a) {
        $this_time=new DateTime("$year-$a-01 00:00:00",new DateTimeZone("UTC"));
        echo "<option value='$a'";
        if ($a==$month) { echo " SELECTED"; }
        echo ">".$this_time->format("M")."</option>";
    }
    echo "</select> ";
}
if (isset($allyear)) {
    echo " <select id='year' name='year'>";
    foreach ($allyear as $a) {
        $this_time=new DateTime("$a-01-01 00:00:00",new DateTimeZone("UTC"));
        echo "<option value='$a'";
        if ($a==$year) { echo " SELECTED"; }
        echo ">".$this_time->format("Y")."</option>";
    }
    echo "</select> ";
}
if (isset($allym)) {
    echo " <select id='yearmon' name='yearmon'>";
    foreach ($allym as $a) {
        $yr=substr($a,0,4);
        $mh=substr($a,4,2);
        $this_time=new DateTime("$yr-$mh-01 00:00:00",new DateTimeZone("UTC"));
        echo "<option value='$a'";
        if ($a==$yearmon) { echo " SELECTED"; }
        echo ">".$this_time->format("M Y")."</option>";
    }
    echo "</select> ";
}
if (isset($allym) || isset($allyear)) {
    echo "&nbsp;  &nbsp;";
    unset($this_url_args['date']);
    echo "<a id='latest' href='".$_SERVER['PHP_SELF']."?".implode('&',$this_url_args)."'>Latest</a>";
    echo "</p>\n";
}

if (count($links)>0) {
    echo "<p style='float:right'>";
    foreach (array_keys($links) as $k) {
        echo "<a href='".$links[$k]."'>$k</a> &nbsp; &nbsp; ";
    }
    echo "</p>";
}
echo "<p style='clear:both'></p>";
?>
        <!-- Start Minimal Gallery Html Containers -->
        <div id="gallery" class="content">
          <div class="slideshow-container">
       <!--     <div id="loading" class="loader"></div> -->
            <div id="slideshow" class="slideshow"></div>
          </div>
        </div>
<?php
echo '<h3 class="nav">';
$this_url_args=$url_args;
foreach ($menu_contents[$topmenu] as $t) {
    $this_url_args[$topmenu]="$topmenu=$t";
    echo "<a ";
    if ($chosen[$topmenu]==$t) { echo "class='select' "; }
    echo "href='".$_SERVER['PHP_SELF']."?".implode('&',$this_url_args)."'>";
    if (in_array($t,array_keys($menus[$topmenu]))) {
        echo $menus[$topmenu][$t];
    } else {
        echo $t;
    }
    echo "</a> ";
}
#foreach (array_keys($menus[$topmenu]) as $t) {
#    if (in_array($t,$menu_contents[$topmenu])) {
#        $this_url_args[$topmenu]="$topmenu=$t";
#        echo "<a ";
#        if ($chosen[$topmenu]==$t) { echo "class='select' "; }
#        echo "href='".$_SERVER['PHP_SELF']."?".implode('&',$this_url_args)."'";
#        echo ">".$menus[$topmenu][$t]."</a> ";
#    }
#}
echo "</h3>\n";

echo "<p>";
foreach(array_keys($menu_title) as $m) {
    if (count($menu_contents[$m])==0) { continue; }
    if ($m==$topmenu) { continue; }
    echo "<span style='white-space:nowrap'><span class='bold'>".$menu_title[$m].":</span>&nbsp;";
    echo "<select id='".$m."' name='".$m."'>";
    foreach($menu_contents[$m] as $a) {
        echo "<option value='$a'";
        if ($chosen[$m]==$a) { echo " SELECTED"; }
        echo ">";
        if (in_array($a,array_keys($menus[$m]))) {
            echo $menus[$m][$a]."</option>";
        } else {
            echo $a;
        }
        echo "</option>";
    }
    echo "</select>&nbsp;</span> ";
}

echo "</p>\n";

echo "</form>\n";
echo '<div style="float:left">';
if (!is_null($chosen['type']) && $chosen['type']=='movie') {
echo "<span class='bold'>Movies:</span>";
} else {
echo "<span class='bold'>Images:</span>";
}
?>
</div>
        <div id="controls" class="controls">
        </div>
<?php
if (!in_array($chosen['type'],$site_types)) { 
    $this_url_args=$url_args;
    $this_url_args['autostart']="autostart";
    echo "<span class='bold' style='color:white'>Images:</span>&nbsp;&nbsp;Loop Speed: &nbsp;";
    $this_url_args['delay']="delay=".floor($delay/2);
    echo "<a href='".$_SERVER['PHP_SELF']."?".implode('&',$this_url_args)."' class='speed' id='speed'>Faster</a> &nbsp;";
    $this_url_args['delay']="delay=".floor($delay*2);
    echo "<a href='".$_SERVER['PHP_SELF']."?".implode('&',$this_url_args)."' class='speed' id='speed'>Slower</a>";
    echo '<p>';
    echo '</p>';
} 
?>
        <div id="thumbs" class="navigation">
          <ul class="thumbs noscript">
<?php

$no_images=true;
# Now create the "thumbnails" which in our case are just text links to the images.
# a list of all the images
foreach ($allimage as $filename) {
    $blah=preg_match('/_[\d]{12}_([\d]{12})[^\/]*$/',$filename,$matches);
    if (count($matches)>0) {
        $mn=substr($matches[1],10,2);
    } else {
        $blah=preg_match('/_[\d]{10}_([\d]{10})[^\/]*$/',$filename,$matches);
        if (count($matches)>0) {
            $mn='00';
        } else {
            $blah=preg_match('/([\d]{12})[^\/]*$/',$filename,$matches);
            if (count($matches)>0) {
                $mn=substr($matches[1],10,2);
            } else {
                $blah=preg_match('/([\d]{10})[^\/]*$/',$filename,$matches);
                $mn='00';
            }
        }
    }
    if ($blah) {
        # the filename contains date/time info
        $yr=substr($matches[1],0,4);
        $mh=substr($matches[1],4,2);
        $dy=substr($matches[1],6,2);
        $hr=substr($matches[1],8,2);
        $valid_time=new DateTime("$yr-$mh-$dy $hr:$mn:00",new DateTimeZone("UTC"));

        # we also want to know the forecast lead time
        if (isset($allhour)) {
            $lead_time=($valid_time->format('U') - $ref_time->format('U'))/60/60;
        }

        $valid_time->setTimezone(new DateTimeZone($tzone));

        if (isset($allhour)) {
            echo '<li><a class="thumb" name="'.$lead_time.'" href="'.$filename.'" title="Valid at: '.$valid_time->format("Y-m-d H:i T").'">'.$valid_time->format($date_format).' (+'.floor($lead_time).'h'.sprintf('%02d',60*fmod($lead_time,1)).'m)'."</a></li>\n";
        } else {
            $vt=$valid_time->format("YmdHi");
            echo '<li><a class="thumb" name="'.$vt.'" href="'.$filename.'" title="Valid at: '.$valid_time->format("Y-m-d H:i T").'">'.$valid_time->format($date_format)."</a></li>\n";
        }
        $no_images=false;
    } else {
        # Use the filename as the title
        $blah=preg_match('/([a-zA-Z0-9-_.]*)\.[a-zA-Z0-9]+$/',$filename,$matches);
        # make the name look prettier ($filename_replace contains reg exp from settings.inc)
        $place=preg_replace(array_keys($filename_replace),array_values($filename_replace),$matches[1]);
        if ($filename_uc) { $place=ucwords($place); }
        echo '<li><a class="thumb" name="'.$matches[1].'" href="'.$filename.'" title="'.$place.'">'.$place."</a></li>\n";
        $no_images=false;
    }
}
if ($no_images) {
    echo '<li><a class="thumb" name="No images" href="" title="No Images">No Images</a></li>'."\n";
}
?>
          </ul>
        </div>
        <!-- End Minimal Gallery Html Containers -->
        <div style="clear: both;"></div>
      </div>
    </div>
    <div id="footer">Gallery adapted from <a href='http://www.twospy.com/galleriffic/'>Galleriffic</a> &copy; 2009 Trent Foley</div>
    <script type="text/javascript">
    // We only want these styles applied when javascript is enabled
    $('div.navigation').css({'width' : '<?php echo $nav_width;?>', 'float' : 'left'});
    $('div.content').css({'display': 'block', 'width': '<?php echo $image_width;?>'});
    $('div.loader').width('<?php echo $image_width;?>');
    $('div#page').width('<?php echo $page_width;?>');
    $('div.slideshow a.advance-link').width('<?php echo $image_width;?>');

    $(document).ready(function() {				
        // Initialize Minimal Galleriffic Gallery
        $('#thumbs').galleriffic({
            imageContainerSel:      '#slideshow',
            loadingContainerSel:      '#loading',
            controlsContainerSel:   '#controls',
            numThumbs:                 25,
            preloadAhead:              3, // Set to -1 to preload all images
            enableHistory:             true,
<?php if (in_array($chosen['type'],$site_types)) { echo "renderSSControls:          false,\n";} ?>
            playLinkText:              'Play Loop',
            pauseLinkText:             'Pause Loop',
            prevLinkText:              '&lsaquo; Previous',
            nextLinkText:              'Next &rsaquo;',
            nextPageLinkText:          'Next Page &rsaquo;',
            prevPageLinkText:          '&lsaquo; Prev Page',
            delay:                    <?php echo $delay; ?>,
            autoStart:                <?php echo $autostart; ?>,
            defaultTransitionDuration: 0,
            syncTransitions:           true
        });

        /**** Functions to support integration of galleriffic with the jquery.history plugin ****/

        // PageLoad function
        // This function is called when:
        // 1. after calling $.historyInit();
        // 2. after calling $.historyLoad();
        // 3. after pushing "Go Back" button of a browser

        function pageload(hash) {
            // alert("pageload: " + hash);
            // hash doesn't contain the first # character.
            if(hash) {
                $.galleriffic.gotoImage(hash);
            } else {
                gallery.gotoIndex(0);
            }
        }

        // Initialize history plugin.
        // The callback is called at once by present location.hash. 
        $.historyInit(pageload);

        // set onlick event for buttons using the jQuery 1.3 live method
        $("a[rel='history']").live('click', function(e) {
            if (e.button != 0) return true;

            var hash = this.href;
            hash = hash.replace(/^.*#/, '');

            // moves to a new page. 
            // pageload is called at once. 
            // hash don't contain "#", "?"
            $.historyLoad(hash);

            return false;
        });

        // this function keeps the hash from the location bar if one
        // exists and there is no hash in the a.href
        $('a').click( function() { 
            var oldhash = window.location.hash;
            var newhref = this.href;
            if (newhref.search('#') < 0 && oldhash) {
                newhref = newhref+oldhash;
            }
           // document.location.href=newhref;
            if (this.id=='latest' && window.location.href==newhref) {
                location.reload(true);
            } else {
                window.location.href=newhref;
            }
            return false; 
        });

        // also when any select is changed do the hash
        // replacement and form submit
        $('select').change( function() { 
            if (window.location.hash) {
                var newhref = $('#mainform').attr('action');
                $("#mainform").attr("action",newhref+window.location.hash);
            }
            $('#mainform').submit();
            return false; 
        });
    });
    </script>
  </body>
</html>
