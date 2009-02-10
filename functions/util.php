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

/******************************************************/
/*------------ OTHERS  -------------------------------*/
/******************************************************/
if (!defined('NVRTBL'))
	exit;
	
function is_a_best_record($record, $best, $critera)
{
  global $table; /* pour accès à la base de donnée */
  
  $time      = 0 + $record['time'];
  $coins     = 0 + $record['coins'];
  $levelset  = 0 + $record['levelset'];
  $level     = 0 + $record['level'];
  $besttime  = isset($best[$levelset][$level]["time"]) ? $best[$levelset][$level]["time"] : 9999;   // best time for this level
  $bestcoins = isset($best[$levelset][$level]["coins"]) ? $best[$levelset][$level]["coins"] : 0;  // best coins for this level
  // all record with same time/coins will be displayed
  if(empty($critera))
    $critera == "time";

  // goal not reached cannot be "best record"  
  if ($time >= 9999)
    return false;
  else if ($record['type'] == get_type_by_name("freestyle"))
    return false;
  else if (($critera == "time") && ($time <= $besttime))
    return true;
  else  if (($critera == "coins") && ($coins >= $bestcoins)) 
  {
    // ici c'est chiant, il faut comparer les temps si plusieurs records
    // ont le même nombre de pièces avec des temps différents
    // seul le record avec le plus de pièces, et le temps min est retenu

    // récupère tous les temps pour ce niveau / set
    $table->db->NewQuery("SELECT", "rec");
    $table->db->Where("level", (integer)$level);
    $table->db->Where("levelset", (integer)$levelset);
    $table->db->Where("type", get_type_by_name("most coins"));
    $table->db->AppendCustom(" AND (folder=".get_folder_by_name("contest")." OR folder=".get_folder_by_name("oldones").")");
    $table->db->Query();
    // recherche du temps le plus faible, pour ce niveau
    $tmptime = 99999; $i=0;
    while ($val = $table->db->FetchArray())
    { 
      /* si ce record a le nb de pièces max */
      if ($val['coins'] == $bestcoins)
      {
        /* on cherche le tps le plus faible pour le maximum de pièces */
        if ($val['time'] < $tmptime)
           $tmptime = $val['time'];
      }
    }
    /* tmptime contient le temps min pour le max de pièce */
    if ($time == $tmptime)
      return true;
    else
      return false;
  }
  else
    return false;
}

function is_best_record_by_type($record)
{
	global $table; /* pour accès à la base de donnée */
	  
	$ret = false;
    switch ($record['type'])
    {
     case get_type_by_name("best time") : $critera = "time"; $check=true; break;
     case get_type_by_name("most coins"): $critera = "coins";$check=true; break;
     case get_type_by_name("fast unlock"): $critera = "time";$check=true; break;
     default : $check=false; $ret=false; break;
    }
    if($check)
    {
      $best= $table->db->helper->GetBestRecord($val['type'], get_folder_by_name("contest"));
      $ret = is_a_best_record($record, $best, $critera);
    }
    return $ret;
}

// return a string of representation of sec in min' sec''
function sec_to_friendly_display($seconds, $sign_display_always=false)
{
    if ($seconds>=9999)
        return "goal not reached";
    
    $str = "";        
    if ($seconds<0)
    {
        $str = "-";
        $seconds = -$seconds;
    }
    else if ($sign_display_always)
    {
        $str = "+";
    }
        
    $min = floor($seconds/60);
    $sec = floor($seconds-$min*60);
    $cen = round(($seconds-$min*60 - $sec)*100);

    $str .= $min.":";
    $str .= sprintf("%02d",$sec) . "''";
    $str .= sprintf("%02d",$cen); //pourquoi sprintf fait -1 de tps en tps ????

    return $str;
}

function timestamp_diff_in_days($t1,$t2)
{
 $offset = $t1-$t2; 
 return  floor($offset/60/60/24);
}

function timestamp_diff_in_secs($u_t1,$u_t2)
{
 return $u_t1-$u_t2;
}

function get_arguments($post, $get)
{
    $args = array();
    foreach ($get as $arg => $value)
    {
      $args[$arg] = $value;
    }
    // POST is priority, so in last
    foreach ($post as $arg => $value)
    {
      $args[$arg] = $value;
    }

    return $args; // return args with all arguments
}

function getIsoDate($ts)
{
    return date('Y-m-d\\TH:i:s+00:00',$ts);
}

function gui_button($str, $width)
{
  if (empty($str)) return;
  $button = "<div class=\"button\" style=\"width:".$width."px;\">\n";
  $button .= $str;
  $button .= "</div>\n\n";
  return $button;
}

function gui_button_noecho($str, $width)
{
  if (empty($str)) return;
  $button = "<div class=\"button\" style=\"width:".$width."px;\">\n";
  $button .= $str;
  $button .= "</div>\n\n";
  return $button;
}

function gui_button_error($str, $width)
{
  if (empty($str)) return;
  $button = "<div class=\"error\" style=\"width:".$width."px;\">\n";
  $button .= $str;
  $button .= "</div>\n\n";
  return $button;
}

function gui_button_back()
{
  global $lang;
  return gui_button("<a href=\"javascript:history.go(-1)\">".$lang['GUI_BUTTON_BACK']."</a>", 200);
}

function gui_button_main_page()
{
  global $lang;
  return gui_button("<a href=\"".ROOT_PATH."/index.php\">".$lang['GUI_BUTTON_MAINPAGE']."</a>", 300);
}

function gui_button_main_page_admin()
{
  global $lang;
  return gui_button("<a href=\"admin.php\">".$lang['GUI_BUTTON_MAINPAGE_ADMIN']."</a>", 300);
}

function gui_button_return($name, $page)
{
  global $lang;
  return gui_button("<a href=\"".$page."\">".sprintf($lang['GUI_BUTTON_RETURN'], $name)."</a>", 300);
}

function replay_link($replay_file)
{
  global $config;
  return "http://".$_SERVER['SERVER_NAME'] ."/".$config['nvtbl_path'] . $config['replay_dir']. $replay_file;
}

function GetShot($set_path, $map_solfile)
{
    global $config;
    return "<img src=\"".ROOT_PATH.$config['shot_dir']."shot".strstr($set_path, '-')."/".str_replace("sol", "jpg", $map_solfile)."\" alt=\"\" />";
}

function GetShotMini($set_path, $map_solfile, $width="")
{
    global $config;
    if (empty($width))
      return GetShot($map_solfile);
    else
      return "<img src=\"".ROOT_PATH.$config['shot_dir']."shot".strstr($set_path, '-')."/".str_replace("sol", "jpg", $map_solfile)."\" alt=\"\" width=\"".$width."\"/>";
}

function GetDateFromTimestamp($timestamp)
{
  global $config;
  // Fonction permettant de traduire un timestamp
  
  $s = substr($timestamp,17,2); // secondes
  $mn = substr($timestamp,14,2); // minute
  $h = substr($timestamp,11,2); // heure
  $d = substr($timestamp,8,2); // jour
  $m = substr($timestamp,5,2); // mois
  $y = substr($timestamp,0,4); // année
   
  // TIMESTAMP mysql >> TIMESTAMP UNIX
  return mktime($h, $mn, $s, $m, $d, $y);
}

function GetDateFromDate($date)
{
  global $config;
  // Fonction permettant de traduire une date
  
  $d = substr($date,8,2); // jour
  $m = substr($date,5,2); // mois
  $y = substr($date,0,4); // année

  // TIMESTAMP mysql >> TIMESTAMP UNIX
  return mktime(0, 0, 0, $m, $d, $y);
}

function GetDateLang($date)
{
  global $lang_months, $lang_days;

  list($day, $month, $year, $hour) =   sscanf(date('j m Y H:i', $date), "%s %d %s %s");

  return $day." ".$lang_months[$month]." ".$year.", ".$hour;
}

function GetDateLang_mini($date)
{
  global $lang_months, $lang_days;

  list($day, $month, $year, $hour) =   sscanf(date('j m Y H:i', $date), "%s %d %s %s");
  return $day."/".$month."/".$year.", ".$hour;
}

function GetDateLang_birthday($date)
{
  global $lang_months, $lang_days;

  list($day, $month, $year, $hour) =   sscanf(date('j m Y H:i', $date), "%s %d %s %s");
  return $day." ".$lang_months[$month]." ".$year;
}

function TimestampDiffDays($t1,$t2)
{
 $offset = $t1-$t2; 
 return  floor($offset/60/60/24);
}

function TimestampDiffYears($t1,$t2)
{
 $offset = $t1-$t2; 
 return floor($offset/60/60/24/365);
}

function TimestampDiffSecs($u_t1,$u_t2)
{
 return $u_t1-$u_t2;
}


/* pour mettre dans un javascript ou une URL, la total */
function CleanContent($content, $slashes = false, $cslashes = false)
{ 
  $content = str_replace("&lt;","<", $content);
  $content = str_replace("&gt;",">", $content);
  $content = strip_tags($content);
  if ($slashes)
  	$content = addslashes($content);
  if ($cslashes)
    $content = addcslashes($content, "\0..\37");

  return $content;
}

/* pour mettre dans un javascript, mais quand cela vient d'un formulaire */

function GetContentFromPost($content)
{
  if(get_magic_quotes_gpc())
     $content = stripslashes($content);
  $content = str_replace("&lt;","<", $content);
  $content = str_replace("&gt;",">", $content);
  $content = strip_tags($content);

  return $content;
}

function CleanContentHtml($content)
{
  $content = str_replace("&lt;","<", $content);
  $content = str_replace("&gt;",">", $content);
  $content = strip_tags($content);
  $content = LineFeed2Html($content);

  return $content;
}

function LineFeed2Html($content)
{
  $content = str_replace("\n", "<br />", $content);

  return $content;
}

function HtmlLineFeed2Text($content)
{
  $content = str_replace("<br />", "\n", $content);

  return $content;
}

function layout_wrap($str, $i)
{
    $j = $i;
    while ($i < strlen($str)) {
        if (strpos($str, ' ', $i-$j+1) > $i+$j || strpos($str, ' ', $i-$j+1) === false) {
            $str = substr($str, 0, $i) . ' ' . substr($str, $i);
        }
        $i += $j;
    }
    return $str;
}

function Javascriptize($string)
{
  $ret = $string;
  $ret = str_replace("\"", "'", $ret);
  $ret = str_replace("'", "\\'", $ret);
  $ret = str_replace("\n", "\\", $ret);
  $ret = str_replace("\r", "\\", $ret);
  return $ret;
}

function CheckMail($email)
{
  // same as lib.mail 
  if (ereg("^[^@  ]+@([a-zA-Z0-9\-]+\.)+([a-zA-Z0-9\-]{2}|net|com|gov|mil|org|edu|int)\$",$email))
    return true;
  else
    return false;
}


function CheckUrl($url)
{
   $ret = $url;
   if (!empty($url) && (ereg("^http://",$url) == FALSE))
     $ret = "http://" . $url;
   return $ret;
}

function CheckLimitLength($var, $limit, $field_name="")
{
  global $lang;

  if (strlen($var) > $limit) {
    gui_button_error(sprintf($lang['CHECK_ERR_TOOLONG'], '"'.$field_name.'"', $limit), 500);
    return false;
  }
  else
    return true;
}

function CheckLimitInterval($var, $limit_min, $limit_max, $field_name="")
{
  global $lang;

  $var = (integer) $var;
  if ( ($var > $limit_max) || ($var < $limit_min) ) {
    throw new Exception(sprintf($lang['CHECK_ERR_LIMITS'], '"'.$field_name.'"', $limit_min, $limit_max));
  }
  else
    return true;
}

/*** IMPORTATION PEAR ***/
if (!function_exists('file_put_contents')) {
    function file_put_contents($filename, $content, $flags = null, $resource_context = null)
    {
        // If $content is an array, convert it to a string
        if (is_array($content)) {
            $content = implode('', $content);
        }

        // If we don't have a string, throw an error
        if (!is_scalar($content)) {
            user_error('file_put_contents() The 2nd parameter should be either a string or an array',
                E_USER_WARNING);
            return false;
        }

        // Get the length of data to write
        $length = strlen($content);

        // Check what mode we are using
        $mode = ($flags & FILE_APPEND) ?
                    'a' :
                    'w';

        // Check if we're using the include path
        $use_inc_path = ($flags & FILE_USE_INCLUDE_PATH) ?
                    true :
                    false;

        // Open the file for writing
        if (($fh = @fopen($filename, $mode, $use_inc_path)) === false) {
            user_error('file_put_contents() failed to open stream: Permission denied',
                E_USER_WARNING);
            return false;
        }

        // Write to the file
        $bytes = 0;
        if (($bytes = @fwrite($fh, $content)) === false) {
            $errormsg = sprintf('file_put_contents() Failed to write %d bytes to %s',
                            $length,
                            $filename);
            user_error($errormsg, E_USER_WARNING);
            return false;
        }

        // Close the handle
        @fclose($fh);

        // Check all the data was written
        if ($bytes != $length) {
            $errormsg = sprintf('file_put_contents() Only %d of %d bytes written, possibly out of free disk space.',
                            $bytes,
                            $length);
            user_error($errormsg, E_USER_WARNING);
            return false;
        }

        // Return length
        return $bytes;
    }
}

?>
