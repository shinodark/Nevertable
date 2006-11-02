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

define('SQL_DEBUG', 0);
class DB
{
   var $db_server;
   var $db_name;
   var $db_prefix;
   var $db_user;
   var $db_passwd;
   var $prefix;
   var $error;
   var $errno;
   var $con_id;
   var $last_result;
   var $request_count;
   
   /******************************************************/
   /*------------ CONSTRUCTOR AND MAIN FUNCTIONS---------*/
   /******************************************************/

   function DB($server, $name, $prefix, $user, $passwd)
   {
        $this->db_server = $server;
        $this->db_name   = $name;
        $this->db_prefix = $prefix;
        $this->db_user   = $user;
        $this->db_passwd = $passwd;
        $this->request_count = 0;
   }
   
   function Connect()
   {
      $this->con_id = @mysql_connect($this->db_server, $this->db_user, $this->db_passwd);
      if ($this->con_id == false)
      {
        $this->SetError();
        return false;
      }
      mysql_select_db($this->db_name);
      return true;
   }
   
   
   function Disconnect()
   {
      if ($this->con_id)
      {
		mysql_close($this->con_id);
		return true;
	  }
      else
      {
		return false;
	  }
   }
   
   function Query()
   {
      // on élimine le flag
      if ($this->request{0} == "%")
        $this->request = substr($this->request,1);
     
      $this->last_result = @mysql_query($this->request, $this->con_id);
      //echo "req: ".$this->request . "<br />\n";
      //echo "res: ".$this->last_result . "<br />\n";

      if ($this->last_result != false)
      {
        $this->request_count++;
        return $this->last_result;
	  }
      else
      {
        $this->SetError();
        return false;
      }
   }
   
   function FetchArray($result=null)
   {
     if($result==null)
        $result=$this->last_result;
     return @mysql_fetch_array($result, MYSQL_ASSOC);
   }
   
   function NumRows($result=null)
   {
     if($result==null)
        $result=$this->last_result;
     return @mysql_num_rows($result);
   }
    
   function GetReqCount()
   {
     return $this->request_count;
   }
   
   function SetError()
   {
     if (SQL_DEBUG) 
     {
       if ($this->con_id)
       {
      	 $this->error = mysql_error($this->con_id);
	     $this->errno = mysql_errno($this->con_id);
       }
       else
       {
	     $this->error = (mysql_error() !== false) ? mysql_error() : 'Unknown error';
	     $this->errno = (mysql_errno() !== false) ? mysql_errno() : 0;
       }
     }
     else
     {
        $this->error = "SQL Error. Debug mode is not set.";
     }
   }
   
   function GetError()
   {
		if ($this->error != '') 
			return $this->errno.' ~ '.$this->error;
		else 
			return false;
   }

   function GetRequestString()
   {
        return $this->request;
   }
   
   /******************************************************/
   /*------------ PROCESS REQUEST FUNCTIONS -------------*/
   /******************************************************/
   
   function RequestInit($type, $table_name="", $select_field="*")
   {
       $this->request = '';
       $table = $this->db_prefix . $table_name;

       switch($type)
       {
         default:
         case "SELECT": $this->request = "SELECT ".$select_field." FROM " . $table . " "; break;
         case "UPDATE": $this->request = "UPDATE " . $table . " "; break;
         case "INSERT": $this->request = "INSERT INTO " . $table; break;
         case "DELETE": $this->request = "DELETE FROM " . $table . " "; break;
         case "CUSTOM": break;
       }
   }
   
   function RequestSelectInit($table, $fields_arr)
   {
     while (($f=array_pop($fields_arr)) != NULL)
     {
       if (!empty($flist))  
           $flist .= ",";
       $flist .= $f;
     }
   
     $this->request = "SELECT " . $flist . " FROM " . $this->db_prefix . $table . " ";
   }
   
   function RequestUpdateSet($fields_array, $timestamp_conserve=false)
   {
     if (empty($fields_array))  
       return;

     $reqP = $this->request;
     $req = '';
       
     foreach ($fields_array as $f => $value)
     {
       if (!empty($req))  /* ajoute une virgule si on n'est pas au début */
           $req .= ",";
       if (ereg("(^#)(.*)",$value, $regs)) /* si dièse, il s'agit d'une fonction SQL */
           $req .= $f . "=" . $regs[2] ;
       else /* sinon normale, et on escape */
       $req .= $f . "='" . $this->Protect($value) . "'";
     }
   
     $this->request = $reqP . " SET " . $req . " ";
   
     if ($timestamp_conserve)
     {
       $this->request .= ",timestamp=timestamp ";
     }
   }
   
   function RequestInsert($fields_array)
   {
     if (empty($fields_array))  
       return;
       
     foreach($fields_array as $f => $value)
     {
       if (!empty($flist))
           $flist .= ",";
       $flist .= $f;
       if (!empty($vlist))
           $vlist .= ",";
       /* if a value begin with #, it's consired as a sql function, so no ' is added. */
       if (ereg("(^#)(.*)",$value, $regs))
           $vlist .= $regs[2];
       else    
       $vlist .= "'".$this->Protect($value)."'";
     }
       
     $this->request .= "(" . $flist .") VALUES(" . $vlist . ")";
   }
   
   function RequestGenericFilter($filter, $filterval, $logic="AND")
   {
     if(!isset($filter) || !isset($filterval))
        return;

     if ($this->Protect($filter) !== $filter) /* c'est louche ! */
        return;
        
     $logic=trim($logic);

     /* version récursive */
     if (is_array($filter) && is_array($filterval))
     {
       $first=true;
       foreach ($filter as $key => $val)
       {
         $flag = "";
         if ($this->request{0} == "%")
           if ($first) /* toujours AND par rapport au précédent */
             $command = " AND ";
           else
             $command = " ".$logic." ";
         else
         {
           $command = "WHERE ";
           $flag = "%";
         }
         if($first)
         { /* ajout parenthèse */
           $command .= "( " ; $first=false;
         }
     
         switch($val)
         {
           case 'none'   : break;
               default       : $this->request = $flag . $this->request . $command . $val . "='" . $this->Protect($filterval[$key]) . "'"; break;
         }
       }
       $this->request .= " ) ";
     }
     /* version classique */
     else
     {
       $flag = "";
       if ($this->request{0} == "%")
         $command = " ".$logic." ";
       else
       {
         $command = "WHERE ";
         $flag = "%";
       }
     
       switch($filter)
       {
         case 'none'   : break;
             default       : $this->request = $flag . $this->request . $command . $filter . "='" . $this->Protect($filterval) . "'"; break;
       }
     }
   }
   
   function RequestGenericFilter_ge($filter, $filterval)
   {
     if(empty($filter) || empty($filterval))
        return;
     $flag = "";
     // % est le flag d'indication de l'état de la requete pour le WHERE
     if ($this->request{0} == "%")
     {
       $command = " AND ";
     }
     else
     {
       $command = "WHERE ";
       $flag = "%";
     }
     
     switch($filter)
     {
       case 'none'   : break;
           default       : $this->request = $flag . $this->request . $command . $filter . ">='" . $this->Protect($filterval) . "'"; break;
     }
   }
   
   function RequestGenericFilter_lt($filter, $filterval)
   {
     if(empty($filter) || empty($filterval))
        return;
     $flag = "";
     // % est le flag d'indication de l'état de la requete pour le WHERE
     if ($this->request{0} == "%")
     {
       $command = " AND ";
     }
     else
     {
       $command = "WHERE ";
       $flag = "%";
     }
     
     switch($filter)
     {
       case 'none'   : break;
           default       : $this->request = $flag . $this->request . $command . $filter . "<='" . $this->Protect($filterval) . "'"; break;
     }
   }
   
   function RequestGenericSort($fields_array, $order)
   {
     $f="";
     $i=0;
     foreach ($fields_array as $num => $value)
     {
        if(!empty($f))
            $f .= ", ";
        if (is_array($order))
          $f .= $value . " ". $order[$i]. " ";
        else
          $f .= $value;
        $i++;
     }
   
     if (is_array($order))
       $this->request .= " ORDER BY " . $f;
     else
       $this->request .= " ORDER BY " . $f . " " . $order;
   }
   
   function RequestCustom($str)
   {
     $this->request .= " " . $str . " ";
   }
   
   function RequestLimit($count, $off=0)
   {
       if(!empty($count))
       {
           $off = ($off < 0) ? 0 : $off;
           if (!empty($off))
              $this->request .= " LIMIT ".$off . "," . $count;
           else
              $this->request .= " LIMIT " . $count;
       }
   }

   function CountRows($table, $arr_filters=array())
   {
     $this->RequestInit("CUSTOM");
     $table = $this->db_prefix . $table;
     $this->RequestCustom("SELECT COUNT(*) FROM ".$table);
     if (!empty($arr_filters))
     {
       foreach ($arr_filters as $f => $v)
         $this->RequestGenericFilter($f, $v);
     }
     $this->Query();
     $res = $this->FetchArray();
     return $res['COUNT(*)'];
   }
   

   function Protect($string)
   {
     if (is_array($string))
     {
       return $this->ProtectArray($string);
     }

     // Stripslashes
     if (get_magic_quotes_gpc()) {
       $ret = stripslashes($string);
     }
     // Protection si ce n'est pas un entier
     if (!is_numeric($string)) {
       $ret = mysql_real_escape_string($string, $this->con_id);
     }
     if ($ret === FALSE)
     {
       echo "Error in escape_string, abort now !!<br/>\n";
       $this->Disconnect();
       exit();
     }
     return $ret;
    }

    function ProtectArray($strings_arr)
    {
      $ret = array();

      if (is_array($strings_arr))
      {
          foreach ($strings_arr as $key => $value)
            $ret[$key] = $this->Protect($value);
      }
      else return $this->Protect($strings_arr);
    }

   /******************************************************/
   /*------------ DATABASE SIMPLIFIED ACCESS ------------*/
   /******************************************************/
   
   function RequestFilterLevels($levelset, $level)
   {
     if ($levelset > 0)  // -1 is index in list, "all" for levelset
        $this->RequestGenericFilter("levelset", $levelset);
     if ($level > 0)      // 0 is index in list, "all" for level
        $this->RequestGenericFilter("level", $level);
   }
   
   function RequestFilterType($type)
   {
     if ($type == get_type_by_name("all"))
       return;
   
     $this->RequestGenericFilter("type", $type);
   }
   
   function RequestFilterNew($newonly)
   {
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
   
     $this->RequestGenericFilter_ge("timestamp", $lim);
   }
   
   function RequestSort($sortP)
   {
     switch($sortP)
     {
         case 'user' : $f = array("user"); $o = "ASC";  break;
         case 'level'  : $f = array("levelset","level","time") ; $o = "ASC"; break;
         default: 
         case 'time'   : $f = array("time","coins"); $o = array("ASC", "DESC"); break;
         case 'coins'  : $f = array("coins","time"); $o = array("DESC","ASC"); break;
         case 'type'   : $f = array("type"); $o = "ASC"; break;
         case 'id'     : $f = array("id"); $o = "DESC"; break;
         case 'old'    : $f = array("timestamp"); $o = "DESC"; break;
     }
   
     $this->RequestGenericSort($f, $o);
   }
   
   function RequestMatchComments($replay_id)
   {
     $this->RequestInit("SELECT", "com");
     $this->RequestGenericFilter("replay_id", $replay_id);
     $this->RequestGenericSort(array("timestamp"), "ASC");
     return $this->Query();
   }
   
   function RequestMatchCommentById($com_id)
   {
     $this->RequestInit("SELECT", "com");
     $this->RequestGenericFilter("id", $com_id);
     return $this->Query();
   }
   
   function RequestMatchRecords($fields_arr)
   {
     $this->RequestInit("SELECT", "rec");
     
     foreach($fields_arr as $f => $v)
     {
       $this->RequestGenericFilter($f, $v);
     }
     $res = $this->Query();
     $ret = array();
     $i=0;
     while ($val = $this->FetchArray($res))
     {
        $ret[$i]['id']=$val['id'];
        $i++;
     }
     $ret['nb'] = $i;

     return $ret;
   }

   function MatchUserByName($name)
   {
     $this->RequestInit("SELECT", "users");
     $this->RequestGenericFilter("pseudo", $name);
     return $this->Query();
   }
   
   function MatchUserByMail($mail)
   {
     $this->RequestInit("SELECT", "users");
     $this->RequestGenericFilter("email", $mail);
     return $this->Query();
   }

   function MatchUserById($user_id)
   {
     $this->RequestInit("SELECT", "users");
     $this->RequestGenericFilter("id", $user_id);
     return $this->Query();
   }
   
   function RequestCountComments($replay_id)
   {
       return $this->CountRows("com", array("replay_id" => $replay_id));
   }
   
   function RequestCountRecords($folder, $type)
   {
     if ($type==get_type_by_name("all"))
         return  $this->CountRows("rec", array("folder"=>$folder));
     else
         return  $this->CountRows("rec", array("folder"=>$folder,"type"=>$type));
   }

   function RequestCountUserRecords($user_id)
   {
     return $this->CountRows("rec", array("user_id"=>$user_id));
   }
   
   function RequestCountUserBest($user_id)
   {
     return $this->CountRows("rec", array("user_id"=>$user_id, "isbest"=>1));
   }
   
   function RequestCountUserComments($user_id)
   {
     return $this->CountRows("com", array("user_id"=>$user_id));
   }
   
   function RequestGetBestRecord($type, $folder="")
   {
     $this->RequestInit("CUSTOM");
     $this->RequestCustom("SELECT MIN(time) AS mintime, MAX(coins) AS maxcoins,levelset,level FROM ". $this->db_prefix ."rec ");
     
     /* Si aucun paramètre précisé, on effectue une recherche à la fois dans contest et oldones */
     if(empty($folder))
     {
       if($type != get_type_by_name("all"))
         $this->RequestCustom("WHERE type=".$type." AND (folder=".get_folder_by_name("contest")." OR folder=".get_folder_by_name("oldones").")");
       else
         $this->RequestCustom("WHERE folder=".get_folder_by_name("contest")." OR folder=".get_folder_by_name("oldones"));
     }
     else
     /* Sinon, on fait dans le "folder" précisé ! */
     {
       if($type != get_type_by_name("all"))
         $this->RequestGenericFilter("type", $type);
       $this->RequestGenericFilter("folder", $folder);
     }
     
     $this->RequestCustom("GROUP BY levelset,level");
     $res = $this->Query();
     
     $ret = array();
     
     while ($val = $this->FetchArray())
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
     $rec = new Record($this);

     $this->RequestInit("SELECT", "rec");
     $this->RequestGenericFilter("level", (integer)$level);
     $this->RequestGenericFilter("levelset", (integer)$levelset);
     $this->RequestGenericFilter("type", (integer)$type);
     $this->RequestGenericFilter("folder", get_folder_by_name("contest"));
     $res = $this->Query();
     
     /* set all record for this level/levelset/type/folder at isbest=0 */
     $this->RequestInit("UPDATE", "rec");
     $my  = array("isbest" => 0);
     /* update conserving timestamp */
     $this->RequestUpdateSet($my, true);
     $this->RequestGenericFilter("level", (integer)$level);
     $this->RequestGenericFilter("levelset", (integer)$levelset);
     $this->RequestGenericFilter("type", (integer)$type);
     $this->RequestGenericFilter("folder", get_folder_by_name("contest"));
     $this->Query();
   
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

     while ($val = $this->FetchArray($res))
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
       $this->RequestInit("SELECT", "rec");
       $this->RequestGenericFilter("level", (integer)$level);
       $this->RequestGenericFilter("levelset", (integer)$levelset);
       $this->RequestGenericFilter("type", (integer)$type);
       $this->RequestGenericFilter("folder", get_folder_by_name("oldones"));
       $res = $this->Query();
       
       $best = $this->RequestGetBestRecord($type, get_folder_by_name("oldones"));
       while ($val = $this->FetchArray($res))
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
     $this->RequestInit("UPDATE", "rec");
     $my  = array("isbest" => $isbest);
     /* update conserving timestamp */
     $this->RequestUpdateSet($my, true);
     $this->RequestGenericFilter("id", $id);
     $this->RequestLimit(1);
     $this->Query();
   }
}
?>
