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

define('SQL_DEBUG', 1);
class DB
{
   var $db_server;
   var $db_name;
   var $db_prefix;
   var $db_user;
   var $db_passwd;
   var $error;
   var $errno;
   var $con_id;
   var $last_result;
   var $request_count;
   var $helper;
   
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
      $this->helper = new DBHelper($this);
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

   function GetQueryString()
   {
        return $this->request;
   }
   
   /******************************************************/
   /*------------ PROCESS REQUEST FUNCTIONS -------------*/
   /******************************************************/
   
   function NewQuery($type, $table_name="", $select_field="*")
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
   
   function Select($table_arr, $fields_arr)
   {
     while (($f=array_pop($fields_arr)) != NULL)
     {
       if (!empty($flist))  
           $flist .= ",";
       $flist .= $f;
     }
     
     while (($f=array_pop($table_arr)) != NULL)
     {
       if (!empty($tlist))  
           $tlist .= ",";
       $tlist .= $this->db_prefix . $f;
     }
   
   
     $this->request = "SELECT " . $flist . " FROM " . $tlist . " ";
   }
   
   function Update($fields_array, $timestamp_conserve=false)
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
   
   function Insert($fields_array)
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
   
   function Where($filter, $filterval, $logic="AND", $val_quote=true)
   {
     if(!isset($filter) || !isset($filterval))
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
         default       : 
           if ($val_quote)
             $this->request = $flag . $this->request . $command . $val . "='" . $this->Protect($filterval[$key]) . "'";
           else
             $this->request = $flag . $this->request . $command . $val . "=" . $this->Protect($filterval[$key]);
           break;
         }
       }
       $this->request .= " ) ";
     }
     /* version classique */
     else
     {
       if ($this->Protect($filter) !== $filter) /* c'est louche ! */
        return;
        
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
         default       :
           if ($val_quote)
             $this->request = $flag . $this->request . $command . $filter . "='" . $this->Protect($filterval) . "'";
           else
             $this->request = $flag . $this->request . $command . $filter . "=" . $this->Protect($filterval);
         break;
       }
     }
   }
   
   function Where_ge($filter, $filterval)
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
   
   function Where_lt($filter, $filterval)
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
   
   function Sort($fields_array, $order)
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
   
   function AppendCustom($str)
   {
     $this->request .= " " . $str . " ";
   }
   
   function Limit($count, $off=0)
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
     $this->NewQuery("CUSTOM");
     $table = $this->db_prefix . $table;
     $this->AppendCustom("SELECT COUNT(*) FROM ".$table);
     if (!empty($arr_filters))
     {
       foreach ($arr_filters as $f => $v)
         $this->Where($f, $v);
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
}
