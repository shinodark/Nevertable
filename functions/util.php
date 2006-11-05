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
function is_a_best_record($record, $best, $critera)
{
  global $table; /* pour accès à la base de donnée */
  
  $time      = 0 + $record['time'];
  $coins     = 0 + $record['coins'];
  $levelset  = 0 + $record['levelset'];
  $level     = 0 + $record['level'];
  $besttime  = $best[$levelset][$level]["time"];   // best time for this level
  $bestcoins = $best[$levelset][$level]["coins"];  // best coins for this level
  // all record with same time/coins will be displayed
  if(empty($critera))
    $critera == "time";

  //echo "<br/><br/>besttime = $besttime<br/>bestcoins = $bestcoins<br/><br/>";

  // goal not reached cannot be "best record"  
  if ($time >= 9999)
    return false;
  else if ($record['type'] == get_type_by_name("freestyle"))
    return false;
  else if (($critera == "time") && ($time == $besttime))
  //else if (($critera == "time") && ($time == $besttime) && ($coins == $bestcoins))
    return true;
  else  if (($critera == "coins") && ($coins == $bestcoins)) 
  {
    // ici c'est chiant, il faut comparer les temps si plusieurs records
    // ont le même nombre de pièces avec des temps différents
    // seul le record avec le plus de pièces, et le temps min est retenu

    // récupère tous les temps pour ce niveau / set
    $table->db->RequestInit("SELECT", "rec");
    $table->db->RequestGenericFilter("level", (integer)$level);
    $table->db->RequestGenericFilter("levelset", (integer)$levelset);
    $table->db->RequestGenericFilter("type", get_type_by_name("most coins"));
    $table->db->RequestCustom(" AND (folder=".get_folder_by_name("contest")." OR folder=".get_folder_by_name("oldones").")");
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

// return a string of representation of sec in min' sec''
function sec_to_friendly_display($seconds, $sign_display_always=false)
{
    if ($seconds>=9999)
        return "goal not reached";
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
    
    // trim known text args
    if(isset($args['pseudo']))
        $args['pseudo'] = trim($args['pseudo']);
    if(isset($args['replay']))
        $args['replay'] = trim($args['replay']);
    if (!isset($args['coins']))
        $args['coins']=0;

    // default value for known args
    if(!isset($args['filter']))
        $args['filter']='none';
    if(isset($args['filter']) && !isset($args['filterval']))
        $args['filter']='none';
    if(!isset($args['type']))
        $args['type'] = get_type_by_name("all");
    if(!isset($args['diffview']))
        $args['diffview'] = "off";
    if(!isset($args['bestonly']))
        $args['bestonly'] = "off";
    if(!isset($args['newonly']))
        $args['newonly'] = get_newonly_by_name("off");
    if(!isset($args['levelset_f']))  // used in main page to filter, index in list
        $args['levelset_f'] = 0;     // all by default
    if(!isset($args['level_f']))     // used in main page to filter, index in list
        $args['level_f'] = 0;        // all by default
    if(!isset($args['folder']))
        $args['folder'] = get_folder_by_name("contest");
    if(!isset($args['goalnotreached']))
        $args['goalnotreached'] = "off";
    
    // only use in admin
    if(!isset($args['overwrite']))
        $args['overwrite'] = 'off';

    return $args; // return args with all arguments
}

function getIsoDate($ts)
{
	return date('Y-m-d\\TH:i:s+00:00',$ts);
}

function getInt($FileHandle)
{
    $BinaryData = fread($FileHandle, 4);
    $UnpackedData = unpack("V", $BinaryData);
    return $UnpackedData[1];
}

function getShort($FileHandle)
{
    $BinaryData = fread($FileHandle, 2);
    $UnpackedData = unpack("S", $BinaryData);
    return $UnpackedData[1];
}

function getString($FileHandle, $length)
{
    return  fread($FileHandle, $length);
}

function button($str, $width)
{
  if (empty($str)) return;
  $button = "<div class=\"button\" style=\"width:".$width."px;\">\n";
  $button .= $str;
  $button .= "</div>\n\n";
  echo $button;
}
function button_error($str, $width)
{
  if (empty($str)) return;
  $button = "<div class=\"error\" style=\"width:".$width."px;\">\n";
  $button .= $str;
  $button .= "</div>\n\n";
  echo $button;
}
function button_back()
{
  button("<a href=\"javascript:history.go(-1)\">Back</a>", 200);
}

function replay_link($folder, $replay_file)
{
  global $config;
  return "http://".$_SERVER['SERVER_NAME'] ."/".$config['nvtbl_path'] . $config['replay_dir']. get_folder_by_number($folder) ."/".$replay_file;
}

function GetDateFromTimestamp($timestamp)
{
  global $config;
  // Fonction permettant de traduire un timestamp
  
  if ($config['bdd_mysql_version']=="4.0.x")
  {
    $s = substr($timestamp,12,2); // secondes
    $mn = substr($timestamp,10,2); // minute
    $h = substr($timestamp,8,2); // heure
    $d = substr($timestamp,6,2); // jour
    $m = substr($timestamp,4,2); // mois
    $y = substr($timestamp,0,4); // année
  }
  else
  {
    $s = substr($timestamp,17,2); // secondes
    $mn = substr($timestamp,14,2); // minute
    $h = substr($timestamp,11,2); // heure
    $d = substr($timestamp,8,2); // jour
    $m = substr($timestamp,5,2); // mois
    $y = substr($timestamp,0,4); // année
  }
   
  // TIMESTAMP mysql >> TIMESTAMP UNIX
  return mktime($h, $mn, $s, $m, $d, $y);
}

/* pour mettre dans un javascript ou une URL, la total */
function CleanContent($content)
{ 
  $content = str_replace("&lt;","<", $content);
  $content = str_replace("&gt;",">", $content);
  $content = strip_tags($content);
  $content = addslashes($content);
  $content = addcslashes($content, "\0..\37");

  return $content;
}

/* pour mettre dans un javascript, mais quand cela vient d'un formulaire (les slahs dont déjà mis) */
function CleanContentPost($content)
{
  $content = stripslashes($content);
  $content = str_replace("&lt;","<", $content);
  $content = str_replace("&gt;",">", $content);
  $content = strip_tags($content);
  $content = addslashes($content);

  return $content;
}

function GetContentFromPost($content)
{
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
  $content = LineFeed2Html($content);
  $content = strip_tags($content);

  return $content;
}

function LineFeed2Html($content)
{
  $content = str_replace("\n", "<br />", $content);

  return $content;
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
  //if (ereg("^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+$", $email))
  // same as lib.mail 
  if (ereg("^[^@  ]+@([a-zA-Z0-9\-]+\.)+([a-zA-Z0-9\-]{2}|net|com|gov|mil|org|edu|int)\$",$email))
    return true;
  else
    return false;
}

function GetFolderDescription($nb)
{
  global $folders, $config;

  $name = get_folder_by_number($nb);

  $descpath = ROOT_PATH . "/";
  $desc = $descpath . $config['folders_desc'];
 
  if (file_exists($desc))
  {
    $def = file($desc);
    foreach($def as $v)
    {
      $v = trim($v);
      if (preg_match('|^([^\t]*)[#]+(.*)$|',$v,$matches))
	  {
        if($matches[1]==$name)
            return $matches[2];
      }
    }
  }
  else
  {
    echo "No folders description file found.\n";
  }
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
