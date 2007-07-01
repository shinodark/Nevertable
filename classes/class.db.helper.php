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

class DBHelper
{
   var $db;
   
   /******************************************************/
   /*------------ CONSTRUCTOR AND MAIN FUNCTIONS---------*/
   /******************************************************/

   function DBHelper(&$db)
   {
     $this->db = &$db;
   }
   
   function RequestFilterLevels($levelset, $level)
   {
     global $config;
     
     $p = $config['bdd_prefix'];
     if ($levelset > 0)  // -1 is index in list, "all" for levelset
        $this->db->RequestGenericFilter($p."rec.levelset", $levelset);
     if ($level > 0)      // 0 is index in list, "all" for level
        $this->db->RequestGenericFilter($p."rec.level", $level);
   }
   
   function RequestFilterType($typeP)
   {
     global $config;

     if ($typeP == get_type_by_name("all"))
       return;
   
     $p = $config['bdd_prefix'];
     $this->db->RequestGenericFilter($p."rec.type", $typeP);
   }
   
   function RequestFilterNew($newonly)
   {
     global $config;
     
     if ($newonly == get_newonly_by_name("off"))
       return;
   
     else if ($newonly == get_newonly_by_name("3 days"))
       $day = 3;
     else if ($newonly == get_newonly_by_name("1 week"))
       $day = 7;
     else if ($newonly == get_newonly_by_name("2 weeks"))
       $day = 14;
     else if ($newonly == get_newonly_by_name("1 month"))
       $day = 31;
     else
       $day = 31;
  

     $res = mysql_query("SELECT DATE_SUB( CURRENT_TIMESTAMP( ) , INTERVAL ".$day." DAY ) +0 AS lim");
     $val = mysql_fetch_array($res);
     $lim = $val['lim'];
   
     $p = $config['bdd_prefix'];
     $this->db->RequestGenericFilter_ge($p."rec.timestamp", $lim);
   }
   
   function RequestFilterFolder($folder)
   {
     global $config;
     
     $p = $config['bdd_prefix'];
     if ($folder != get_folder_by_name("all"))
       $this->db->RequestGenericFilter($p."rec.folder", $folder);
     else
     { /* affichage des répertoires oldones et contest, $folder = 0 => all*/
       $this->db->RequestGenericFilter(
           array($p."rec.folder", $p."rec.folder"),
           array(get_folder_by_name("contest"), get_folder_by_name("oldones")),
           "OR"
       );
     }
   }
   
   function RequestSort($sortP)
   {
     global $config;
     
     $p = $config['bdd_prefix'];
     $sort = get_sort_by_number($sortP);
     switch($sort)
     {
         case 'pseudo' : $f = array($p."users.pseudo"); $o = "ASC";  break;
         case 'level'  : $f = array($p."rec.levelset",$p."rec.level",$p."rec.time") ; $o = "ASC"; break;
         case 'time'   : $f = array($p."rec.time",$p."rec.coins"); $o = array("ASC", "DESC"); break;
         case 'coins'  : $f = array($p."rec.coins",$p."rec.time"); $o = array("DESC","ASC"); break;
         case 'type'   : $f = array($p."rec.type"); $o = "ASC"; break;
         case 'id'     : $f = array($p."rec.id"); $o = "DESC"; break;
         default: 
         case 'old'    : $f = array($p."rec.timestamp"); $o = "DESC"; break;
     }
   
     $this->db->RequestGenericSort($f, $o);
   }
   
   function RequestMatchComments($replay_id)
   {
     $this->db->RequestInit("SELECT", "com");
     $this->db->RequestGenericFilter("replay_id", $replay_id);
     $this->db->RequestGenericSort(array("timestamp"), "ASC");
     return $this->db->Query();
   }
   
   function RequestMatchCommentById($com_id)
   {
     $this->db->RequestInit("SELECT", "com");
     $this->db->RequestGenericFilter("id", $com_id);
     return $this->db->Query();
   }
   
   function RequestMatchRecords($fields_arr)
   {
     $this->db->RequestInit("SELECT", "rec");
     
     foreach($fields_arr as $f => $v)
     {
       $this->db->RequestGenericFilter($f, $v);
     }
     $res = $this->db->Query();
     $ret = array();
     $i=0;
     while ($val = $this->db->FetchArray($res))
     {
        $ret[$i]['id']=$val['id'];
        $i++;
     }
     $ret['nb'] = $i;

     return $ret;
   }

   function MatchUserByName($name)
   {
     $this->db->RequestInit("SELECT", "users");
     $this->db->RequestGenericFilter("pseudo", $name);
     return $this->db->Query();
   }
   
   function MatchUserByMail($mail)
   {
     $this->db->RequestInit("SELECT", "users");
     $this->db->RequestGenericFilter("email", $mail);
     return $this->db->Query();
   }

   function MatchUserById($user_id)
   {
     $this->db->RequestInit("SELECT", "users");
     $this->db->RequestGenericFilter("id", $user_id);
     return $this->db->Query();
   }
   
   function RequestCountComments($replay_id)
   {
       return $this->db->CountRows("com", array("replay_id" => $replay_id));
   }
   
   function RequestCountRecords($folder, $type)
   {
     if ($type==get_type_by_name("all"))
         return  $this->db->CountRows("rec", array("folder"=>$folder));
     else
         return  $this->db->CountRows("rec", array("folder"=>$folder,"type"=>$type));
   }

   function RequestCountUserRecords($user_id)
   {
     $this->db->RequestInit("SELECT", "rec");
	 /* on ne compte pas les recordes dans trash */
	 $this->db->RequestCustom("WHERE user_id=".$user_id." AND folder!=".get_folder_by_name("trash") ." AND folder!=".get_folder_by_name("incoming"));
	 $this->db->Query();
     return $this->db->NumRows();
   }
   
   function RequestCountUserBest($user_id)
   {
     return $this->db->CountRows("rec", array("user_id"=>$user_id, "isbest"=>1));
   }
   
   function RequestCountUserComments($user_id)
   {
     return $this->db->CountRows("com", array("user_id"=>$user_id));
   }
   
   function RequestGetBestRecord($type, $folder="")
   {
     global $config;

     $p = $config['bdd_prefix'];

     $this->db->RequestInit("CUSTOM");
     $this->db->RequestCustom("SELECT MIN(time) AS mintime, MAX(coins) AS maxcoins,levelset,level FROM ". $p."rec ");


     /* Si aucun paramètre précisé, on effectue une recherche à la fois dans contest et oldones */
     if(empty($folder))
     {
       if($type != get_type_by_name("all"))
         $this->db->RequestCustom("WHERE type=".$type." AND (folder=".get_folder_by_name("contest")." OR folder=".get_folder_by_name("oldones").")");
       else
         $this->db->RequestCustom("WHERE folder=".get_folder_by_name("contest")." OR folder=".get_folder_by_name("oldones"));
     }
     else
     /* Sinon, on fait dans le "folder" précisé ! */
     {
       if($type != get_type_by_name("all"))
         $this->db->RequestGenericFilter("type", $type);
       $this->db->RequestGenericFilter("folder", $folder);
     }
     
     $this->db->RequestCustom("GROUP BY levelset,level");
     $res = $this->db->Query();
     if (!$res)
     {
	     gui_button_error($this->db->GetError(), 300);
	     return false;
     }
     
     $ret = array();
     
     while ($val = $this->db->FetchArray())
     {
       $ret[$val['levelset']] [$val['level']] ["time"] = $val['mintime'];
       $ret[$val['levelset']] [$val['level']] ["coins"] = $val['maxcoins'];
     }
   
     return $ret;
   }
   
   /* Gestion des meilleurs records d'un certain level/set/type */
   /* @return :
            $ret['nb']: nb of records seen as "best" ones 
            $ret['beaten']: nb of records moved to "oldones" folder
            $ret['imports']: nb of records moved to "oldones" folder
   */
   function RequestSetBestRecordByFields($level, $levelset, $type)
   {
     $rec = new Record($this->db);

     if($type == get_type_by_name("freestyle"))
     {
        return false;
     }

     $this->db->RequestInit("SELECT", "rec");
     $this->db->RequestGenericFilter("level", (integer)$level);
     $this->db->RequestGenericFilter("levelset", (integer)$levelset);
     $this->db->RequestGenericFilter("type", (integer)$type);
     $this->db->RequestGenericFilter("folder", get_folder_by_name("contest"));
     $res = $this->db->Query();
     
     /* set all record for this level/levelset/type/folder at isbest=0 */
     $this->db->RequestInit("UPDATE", "rec");
     $my  = array("isbest" => 0);
     /* update conserving timestamp */
     $this->db->RequestUpdateSet($my, true);
     $this->db->RequestGenericFilter("level", (integer)$level);
     $this->db->RequestGenericFilter("levelset", (integer)$levelset);
     $this->db->RequestGenericFilter("type", (integer)$type);
     $this->db->RequestGenericFilter("folder", get_folder_by_name("contest"));
     $this->db->Query();
   
     switch ($type)
     {
       case get_type_by_name("best time") : $critera = "time"; break;
       case get_type_by_name("most coins"): $critera = "coins"; break;
       default: $critera = "none"; break;
     }
   
     $best = $this->RequestGetBestRecord($type, get_folder_by_name("contest"));
     
     $ret['nb'] = 0;
     $ret['beaten'] = 0;
     $ret['imports'] = 0; /* imported records from "oldones" */

     while ($val = $this->db->FetchArray($res))
     {
       $rec->LoadFromId($val['id']);
       if (is_a_best_record($val, $best, $critera))
       {
         $rec->SetIsBest(1);
         $rec->Update();
         /* a best record found, but continue, in case of equals */
         $ret['nb']++;
       }
       else /* a recent beaten record is sent to "oldones" folder */
       {
         if($rec->GetType() != get_type_by_name("freestyle")) 
         {
           $rec->Move(get_folder_by_name("oldones"));
           $ret['beaten']++;
         }
       }
     }
   
     if ($ret['nb']==0) /* no best records found, try moving up one from "oldones" */
     {
       $this->db->RequestInit("SELECT", "rec");
       $this->db->RequestGenericFilter("level", (integer)$level);
       $this->db->RequestGenericFilter("levelset", (integer)$levelset);
       $this->db->RequestGenericFilter("type", (integer)$type);
       $this->db->RequestGenericFilter("folder", get_folder_by_name("oldones"));
       $res = $this->db->Query();
       
       $best = $this->RequestGetBestRecord($type, get_folder_by_name("oldones"));
       while ($val = $this->db->FetchArray($res))
       {
         if (is_a_best_record($val, $best, $critera))
         {
           $rec->LoadFromId($val['id']);
           $rec->Move(get_folder_by_name("contest"));
           $rec->SetIsBest(1);
           $rec->Update();
           $ret['imports']++;
         }
       }
     }
   
     return $ret;
   }
   
   function RecordSetIsBest($id, $isbest)
   {
     /* set new best record */
     $this->db->RequestInit("UPDATE", "rec");
     $my  = array("isbest" => $isbest);
     /* update conserving timestamp */
     $this->db->RequestUpdateSet($my, true);
     $this->db->RequestGenericFilter("id", $id);
     $this->db->RequestLimit(1);
     $this->db->Query();
   }

   function GetSets()
   {
     $this->db->RequestInit("SELECT", "sets");
     $this->db->Query();
     $sets = array();
     while ($val = $this->db->FetchArray())
       $sets[$val['id']] = $val['set_name'];
     return $sets;
   }

   function GetSetsRes()
   {
     $this->db->RequestInit("SELECT", "sets");
     $res = $this->db->Query();
     return $res;
   }

   function RequestMatchTags()
   {
     global $config;

     $this->db->RequestInit("SELECT", "tags");
     $this->db->RequestGenericSort(array("timestamp"), "DESC");
     $this->db->RequestLimit($config['tag_limit']);
     return $this->db->Query();
   }
}
