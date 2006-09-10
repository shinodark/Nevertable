<?php
# ***** BEGIN LICENSE BLOCK *****
# This file is part of Nevertable .
# Copyright (c) 2004 Francois Guillet and contributors. All rights
# reserved.
#
# Nevertable is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
# 
# Nevertable is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with Nevertable; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
# ***** END LICENSE BLOCK *****

/*****************/
/* TABLE STRUCTS */
/*****************/

/* Cache pour les relation uid -> pseudo, initiailser par la classe nvrtbl */
$users_cache = array();

$levelsets = array (
    array("name" => "Easy"),
    array("name" => "Hard"),
    array("name" => "Mehdi"),
    );
$types = array (
    array("name" => "best time"),
    array("name" => "most coins"),
    array("name" => "freestyle"),
    array("name" => "all"), //use by admin
    );
$folders = array (
    array("name" => "contest"),
    array("name" => "incoming"),
    array("name" => "trash"),
    array("name" => "oldones"),
    );

$newonly = array ("off", "3 days", "1 week", "2 weeks", "1 month");

$userlevel = array (0=>"root", 1=>"admin", 10=>"moderator", 20=>"member", 30=>"guest");

$rank = array("5", "10", "20", "40");

$levels = array (
    1 => "01",
    2 => "02",
    3 => "03",
    4 => "04",
    5 => "05",
    6 => "06",
    7 => "07",
    8 => "08",
    9 => "09",
    10 => "10",
    11 => "11",
    12 => "12",
    13 => "13",
    14 => "14",
    15 => "15",
    16 => "16",
    17 => "17",
    18 => "18",
    19 => "19",
    20 => "20",
    21 => "21",
    22 => "22",
    23 => "23",
    24 => "24",
    25 => "25",
    );

$solfiles = array (
    "map-rlk/easy.sol"      => array("set" => 0, "level" => 1),
    "map-rlk/peasy.sol"     => array("set" => 0, "level" => 2),
    "map-rlk/coins.sol"     => array("set" => 0, "level" => 3),
    "map-rlk/goslow.sol"    => array("set" => 0, "level" => 4),
    "map-rlk/fence.sol"     => array("set" => 0, "level" => 5),
    "map-rlk/bumper.sol"    => array("set" => 0, "level" => 6),
    "map-rlk/maze.sol"      => array("set" => 0, "level" => 7),
    "map-rlk/goals.sol"     => array("set" => 0, "level" => 8),
    "map-rlk/hole.sol"      => array("set" => 0, "level" => 9),
    "map-rlk/bumps.sol"     => array("set" => 0, "level" => 10),
    "map-rlk/corners.sol"   => array("set" => 0, "level" => 11),
    "map-rlk/easytele.sol"  => array("set" => 0, "level" => 12),
    "map-rlk/zigzag.sol"    => array("set" => 0, "level" => 13),
    "map-rlk/greed.sol"     => array("set" => 0, "level" => 14),
    "map-rlk/mover.sol"     => array("set" => 0, "level" => 15),
    "map-rlk/wakka.sol"     => array("set" => 0, "level" => 16),
    "map-rlk/curbs.sol"     => array("set" => 0, "level" => 17),
    "map-rlk/curved.sol"    => array("set" => 0, "level" => 18),
    "map-rlk/stairs.sol"    => array("set" => 0, "level" => 19),
    "map-rlk/rampdn.sol"    => array("set" => 0, "level" => 20),
    "map-rlk/sync.sol"      => array("set" => 0, "level" => 21),
    "map-rlk/plinko.sol"    => array("set" => 0, "level" => 22),
    "map-rlk/drops.sol"     => array("set" => 0, "level" => 23),
    "map-rlk/locks.sol"     => array("set" => 0, "level" => 24),
    "map-rlk/spiralin.sol"  => array("set" => 0, "level" => 25),
    "map-rlk/grid.sol"      => array("set" => 1, "level" => 1),
    "map-rlk/four.sol"      => array("set" => 1, "level" => 2),
    "map-rlk/telemaze.sol"  => array("set" => 1, "level" => 3),
    "map-rlk/spiraldn.sol"  => array("set" => 1, "level" => 4),
    "map-rlk/islands.sol"   => array("set" => 1, "level" => 5),
    "map-rlk/angle.sol"     => array("set" => 1, "level" => 6),
    "map-rlk/spiralup.sol"  => array("set" => 1, "level" => 7),
    "map-rlk/rampup.sol"    => array("set" => 1, "level" => 8),
    "map-rlk/check.sol"     => array("set" => 1, "level" => 9),
    "map-rlk/risers.sol"    => array("set" => 1, "level" => 10),
    "map-rlk/tilt.sol"      => array("set" => 1, "level" => 11),
    "map-rlk/gaps.sol"      => array("set" => 1, "level" => 12),
    "map-rlk/pyramid.sol"   => array("set" => 1, "level" => 13),
    "map-rlk/quads.sol"     => array("set" => 1, "level" => 14),
    "map-rlk/frogger.sol"   => array("set" => 1, "level" => 15),
    "map-rlk/timer.sol"     => array("set" => 1, "level" => 16),
    "map-rlk/spread.sol"    => array("set" => 1, "level" => 17),
    "map-rlk/hump.sol"      => array("set" => 1, "level" => 18),
    "map-rlk/movers.sol"    => array("set" => 1, "level" => 19),
    "map-rlk/teleport.sol"  => array("set" => 1, "level" => 20),
    "map-rlk/poker.sol"     => array("set" => 1, "level" => 21),
    "map-rlk/invis.sol"     => array("set" => 1, "level" => 22),
    "map-rlk/ring.sol"      => array("set" => 1, "level" => 23),
    "map-rlk/pipe.sol"      => array("set" => 1, "level" => 24),
    "map-rlk/title.sol"     => array("set" => 1, "level" => 25),
    "map-mym/descent.sol"   => array("set" => 2, "level" => 1),
    "map-mym/dance2.sol"    => array("set" => 2, "level" => 2),   
    "map-mym/snow.sol"      => array("set" => 2, "level" => 3),
    "map-mym/drive1.sol"    => array("set" => 2, "level" => 4),
    "map-mym/glasstower.sol"   => array("set" => 2, "level" => 5),
    "map-mym/scrambling.sol"   => array("set" => 2, "level" => 6),
    "map-mym/trust.sol"     => array("set" => 2, "level" => 7),
    "map-mym/loop1.sol"     => array("set" => 2, "level" => 8),
    "map-mym/maze1.sol"     => array("set" => 2, "level" => 9),
    "map-mym/up.sol"        => array("set" => 2, "level" => 10),
    "map-mym/circuit2.sol"  => array("set" => 2, "level" => 11),
    "map-mym/comeback.sol"  => array("set" => 2, "level" => 12),
    "map-mym/maze2.sol"     => array("set" => 2, "level" => 13),
    "map-mym/earthquake.sol"=> array("set" => 2, "level" => 14),
    "map-mym/circuit1.sol"  => array("set" => 2, "level" => 15),
    "map-mym/turn.sol"      => array("set" => 2, "level" => 16),
    "map-mym/assault.sol"   => array("set" => 2, "level" => 17),
    "map-mym/narrow.sol"    => array("set" => 2, "level" => 18),
    "map-mym/loop2.sol"     => array("set" => 2, "level" => 19),
    "map-mym/drive2.sol"    => array("set" => 2, "level" => 20),
    "map-mym/running.sol"   => array("set" => 2, "level" => 21),
    "map-mym/bombman.sol"   => array("set" => 2, "level" => 22),
    "map-mym/climb.sol"     => array("set" => 2, "level" => 23),
    "map-mym/dance1.sol"    => array("set" => 2, "level" => 24),
    "map-mym/hard.sol"      => array("set" => 2, "level" => 25),
    "map-mym2/sonic.sol"        => array("set" => 3, "level" => 1),
    "map-mym2/speeddance.sol"   => array("set" => 3, "level" => 2),
    "map-mym2/movinglumps.sol"  => array("set" => 3, "level" => 3),
    "map-mym2/ghosts.sol"       => array("set" => 3, "level" => 4),
    "map-mym2/backforth.sol"    => array("set" => 3, "level" => 5),
    "map-mym2/webs.sol"         => array("set" => 3, "level" => 6),
    "map-mym2/fall.sol"         => array("set" => 3, "level" => 7),
    "map-mym2/basket.sol"       => array("set" => 3, "level" => 8),
    "map-mym2/bigball.sol"      => array("set" => 3, "level" => 9),
    "map-mym2/translation.sol"  => array("set" => 3, "level" => 10),
    "map-mym2/movingpath.sol"   => array("set" => 3, "level" => 11), 
    "map-mym2/bounces.sol"      => array("set" => 3, "level" => 12), 
    "map-mym2/runstop.sol"      => array("set" => 3, "level" => 13), 
    "map-mym2/longpipe.sol"     => array("set" => 3, "level" => 14), 
    "map-mym2/rodeo.sol"        => array("set" => 3, "level" => 15), 
    "map-mym2/rainbow.sol"      => array("set" => 3, "level" => 16), 
    "map-mym2/bigcones.sol"     => array("set" => 3, "level" => 17), 
    "map-mym2/shaker.sol"       => array("set" => 3, "level" => 18), 
    "map-mym2/littlecones.sol"  => array("set" => 3, "level" => 19), 
    "map-mym2/push.sol"	        => array("set" => 3, "level" => 20), 
    "map-mym2/updown.sol"       => array("set" => 3, "level" => 21), 
    "map-mym2/freefall.sol"     => array("set" => 3, "level" => 22), 
    "map-mym2/grinder.sol"      => array("set" => 3, "level" => 23), 
    "map-mym2/speed.sol"        => array("set" => 3, "level" => 24), 
    "map-mym2/morenarrow.sol"   => array("set" => 3, "level" => 25), 
    );

/* Garder pour compatibilité avec les anciens records de joueurs non inscrits */
$old_users = array(
 -1=> "Pipo",
 -2=> "Joel",
 -3=> "Rg3",
 -4=> "UFX",
 -5=> "Canuck",
 -6=> "Skquinn",
 -7=> "Wolf",
 -8=> "lad",
 -9=> "Harvey",
 -10=> "Silozius",
 -11=> "Gillux",
 -12=> "Bud",
 -13=> "Danguy",
 -14=> "DiThi",
 -15=> "Rod",
 -16=> "CRO",
 -17=> "Jam",
 -18=> "rodimus",
 );

$shots = array();
foreach ($solfiles as $sol => $arr_level)
{
  $shots[$arr_level['set']][$arr_level['level']] = dirname($sol)."/".basename($sol, ".sol") . ".jpg";
}

function GetShot($set, $level)
{
    global $config, $shots;
    return "<img src=\"".ROOT_PATH.$config['shot_dir'].$shots[$set][$level]."\" alt=\"\" />";
}

function GetShotMini($set, $level, $width="")
{
    global $config, $shots;
    if (empty($width))
      return GetShot($set, $level);
    else
      return "<img src=\"".ROOT_PATH.$config['shot_dir'].$shots[$set][$level]."\" alt=\"\" width=\"".$width."\"/>";
}

function get_levelset_by_name($name)
{
    global $levelsets;

    foreach ($levelsets as $set => $value)
    {
        if  (strcmp($levelsets[$set]["name"],$name) == 0)
            return $set;
    }
    echo "Erreur de codage dans un appel a  get_levelset_by_name().";
    exit;
}

function get_levelset_by_number($nb)
{
    global $levelsets;

    return $levelsets[$nb]["name"];
}

function get_level_by_name($name)
{
    return $name-1;
}

function get_type_by_name($name)
{
    global $types;
    
    foreach ($types as $nb => $value)
    {
        if  (strcmp($types[$nb]["name"],$name) == 0)
            return $nb;
    }

    echo "Erreur de codage dans un appel a  get_type_by_name().";
    exit;
}

function get_folder_by_name($name)
{
    global $folders;
    
    foreach ($folders as $nb => $value)
    {
        if  (strcmp($folders[$nb]["name"],$name) == 0)
            return $nb;
    }

    echo "Erreur de codage dans un appel a  get_folder_by_name().";
    exit;
}

function get_folder_by_number($nb)
{
    global $folders;
    return $folders[$nb]["name"];
}

function get_type_by_number($nb)
{
    global $types;
    return $types[$nb]["name"];
}

function get_newonly_by_name($name)
{
    global $newonly;
    return array_search($name, $newonly);
}

function get_newonly_by_number($nb)
{
    global $newonly;
    return $newonly[$nb];
}

function get_userlevel_by_name($name)
{
    global $userlevel;
    foreach ($userlevel as $nb => $levelname)
    {
        if  (strcmp($levelname,$name) == 0)
            return $nb;
    }
    echo "Erreur de codage dans un appel a  get_userlevel_by_name().";
    exit;
}

function get_userlevel_by_number($nb)
{
    global $userlevel;
    return $userlevel[$nb];
}

function CalculRank($this_record)
{
  global $rank;
  if ($this_record == 0)
    return 0;
  else if ($this_record < $rank[0])
    return 1;
  else if ($this_record < $rank[1])
    return 2;
  else if ($this_record < $rank[2])
    return 3;
  else if ($this_record < $rank[3])
    return 4;
  else 
    return 5;
}

   
/*****************/
/* GUI STRUCTS   */
/*****************/

$toolbar_el = array (
    "strong" => "tbStrong",
    "em"     => "tbEm",
    "ins"    => "tbUnderline",
    "bquote" => "tbQuote",
    "link"   => "tbLink",
    "img_link" => "tbImg",
    );

$themes = array(
    "Sulfur",
    "Oxygen",
    "Lithium",
    );

$icons = array (
    "best time"   => "besttime.png",
    "most coins"  => "mostcoins.png",
    "freestyle"   => "freestyle.png",
    "new"         => "new.png",
    "attach"      => "attach.png",
    "edit"        => "edit.png",
    "del"         => "del.png",
    "best"        => "best.png",
    "rank"        => "rank.png",
    "del"         => "del.png",
    "trash"       => "trash.png",
    "arrow"       => "arrow.gif",
    "undo"        => "undo.png",
    "tocontest+"  => "tocontest+.png",
    "tocontestx"  => "tocontestx.png",
    "top"         => "top.jpg",
    );

function get_theme_by_name($name)
{
    global $themes;
    return array_search($name, $themes);
}


/*****************/
/* OTHER         */
/*****************/

/* types for getimagesize() */
$imagetypes = array (
    1 => "GIF",
    2 => "JPG",
    3 => "PNG",
    4 => "SWF",
    5 => "PSD",
    6 => "BMP",
    7 => "TIF",
    8 => "TIF",
    9 => "JPC",
    10 => "JP2",
    11 => "JPX",
    12 => "JB2",
    13 => "SWC",
    14 => "IFF",
    15 => "WBMP",
    16 => "XBM",
    );

?>
