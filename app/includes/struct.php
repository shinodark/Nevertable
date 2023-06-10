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
#
if (!defined('NVRTBL'))
	exit;
	
/*****************/
/* TABLE STRUCTS */
/*****************/

$types     = array ("all", "best time", "most coins", "fast unlock", "freestyle");

/* order of types menu in drop-down list */
$types_menu = array(0, 1, 2, 3, -1, 4);

$folders   = array (0 => "all", 1 => "contest",  2 => "oldones", 3 => "incoming", 4 => "trash");
$folders_user = array(0 => "all", 1 => "contest", 2 => "oldones");

$newonly   = array ("off", "3 days", "1 week", "2 weeks", "1 month");

$sort_type = array ("id", "old", "pseudo", "level", "type", "time", "coins");

$userlevel = array (0=>"root", 1=>"admin", 10=>"moderator", 20=>"member", 30=>"guest");

$rank      = array("5", "10", "20", "40");

$langs     = array("en", "fr");

/* Champs pour un fichier replay, défini dans level.h */

$replay_mode = array (
	1 => "challenge",
	2 => "normal",
);

$replay_state = array (
	0 => "none",
	1 => "time",
	2 => "goal",
	3 => "fall",
	4 => "spec",
);

function get_level_by_name($name)
{
    return $name-1;
}

function get_type_by_name($name)
{
    global $types;
    
    foreach ($types as $nb => $value)
    {
        if  (strcmp($types[$nb],$name) == 0)
            return $nb;
    }

    echo "Erreur de codage dans un appel a  get_type_by_name().";
    exit;
}

function get_type_by_number($nb)
{
    global $types;
    return $types[$nb];
}


function get_folder_by_name($name)
{
    global $folders;
    
    foreach ($folders as $nb => $value)
    {
        if  (strcmp($folders[$nb],$name) == 0)
            return $nb;
    }

    echo "Erreur de codage dans un appel a  get_folder_by_name().";
    exit;
}

function get_folder_by_number($nb)
{
    global $folders;
    return $folders[$nb];
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

function get_sort_by_name($name)
{
    global $sort_type;
    return array_search($name, $sort_type);
}

function get_sort_by_number($nb)
{
    global $sort_type;
    return $sort_type[$nb];
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

function get_replay_mode_by_number($nb)
{
    global $replay_mode;
    return $replay_mode[$nb];
}

function get_replay_mode_by_name($name)
{
    global $replay_mode;
    return array_search($name, $replay_mode);
}

function get_replay_state_by_number($nb)
{
    global $replay_state;
    return $replay_state[$nb];
}

function get_replay_state_by_name($name)
{
    global $replay_state;
    return array_search($name, $replay_state);
}

function get_lang_by_number($nb)
{
    global $langs;
    return $langs[$nb];
}

function get_lang_by_name($name)
{
    global $langs;
    return array_search($name, $langs);
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
    "text_bold"   => "tbStrong",
    "text_italic" => "tbEm",
    "text_underline" => "tbUnderline",
    "bquote" => "tbQuote",
    "link_add" => "tbLink",
    "image_link" => "tbImg",
    );

$themes = array(
    "default",
    "Oxygen",
    "Lithium",
    );

$icons = array (
    "best time"   => "besttime.png",
    "most coins"  => "mostcoins.png",
    "freestyle"   => "ball.png",
	"fast unlock" => "fastunlock.png",
    "new"         => "new.png",
    "attach"      => "magnifier.png",
    "edit"        => "script_edit.png",
    "del"         => "del.png",
    "best"        => "best.png",
    "rank"        => "rank.png",
	"replay"	  => "controller.png",
	"no_replay"	  => "controller_delete.png",
	"comments"	  => "comments.png",
	"comments_blank" => "comment.png",
    "del"         => "cross.png",
    "trash"       => "bin_empty.png",
	"trash_full"  => "bin.png",
    "arrow"       => "resultset_next.png",
    "undo"        => "application_side_contract.png",
    "tocontest+"  => "application_add.png",
    "tocontestx"  => "application_get.png",
    "top"         => "top.jpg",
	"menu_upload" => "folder_go.png",
	"menu_userslist"  => "vcard.png",
	"menu_profile"	  => "user.png",
	"menu_options"	  => "user_edit.png",
	"menu_login"	  => "door_in.png",
	"menu_logout"	  => "door_out.png",
	"menu_register"	  => "door.png",
	"menu_forgot"	  => "lightbulb.png",
	"menu_incoming"	  => "hourglass.png",
	"menu_management"   => "page_white_edit.png",
	"menu_config"	=> "cog.png",
	"menu_sets"	=> "application_view_detail.png",
	"menu_checkdatabase" => "application_error.png",
	"menu_recompute"	=> "arrow_rotate_clockwise.png",
	"menu_members" 		=> "group_edit.png",
	"menu_tagboardmoder" =>	"phone_sound.png",
	"navbar_l" => "resultset_previous.png",
	"navbar_r" => "resultset_next.png",
	"smilies"  => "emoticon_smile.png",
	"rank_0" => "bullet_white.png",
	"rank_1" => "medal_bronze_1.png",
	"rank_2" => "award_star_bronze_1.png",
	"rank_3" => "medal_silver_1.png",
	"rank_4" => "award_star_silver_1.png",
	"rank_5" => "award_star_gold_1.png",
	    );

function get_theme_by_name($name)
{
    global $themes;
    return array_search($name, $themes);
}

function get_theme_by_num($num)
{
    global $themes;
    return $themes[$num];
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
