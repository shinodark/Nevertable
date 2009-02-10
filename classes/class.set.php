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
if (!defined('NVRTBL'))
	exit;
	
class Set
{
    var $db;
    var $fields;
    var $isload;

    /*__Constructeur__
      Cette fonction initialise l'objet Set.

      @param: pointeur vers la base de donnée
    */
    function Set(&$db)
    {
       $this->db = &$db;
       $this->isload = false;
    }

    /* Chargement des champs d'un record à partir de l'id */
    function LoadFromId($id)
    {
      $this->isload = false;
      unset($this->fields);
      if (empty($id))
        return false;
      $this->db->NewQuery("SELECT", "sets");
      $this->db->Where("id", $id);
      $this->db->Query();
      
      if ($this->db->NumRows()<1)
      {
          $this->SetError("No set match this id!");
          return false;
      }
      $this->SetFields($this->db->FetchArray());
      $this->isload=true;
      return true;
    }

    function Update($conservative=false)
    {
      if(!$this->isload)
      {
        $this->SetError("Set is not loaded!");
	    return false;
      }
      if(empty($this->fields['id']))
      {
        $this->SetError("Trying to update with no id specified!");
        return false;
      }

      $this->db->NewQuery("UPDATE", "sets");
      $this->db->UpdateSet($this->fields, $conservative);
      $this->db->Where("id", $this->fields['id']);
      $this->db->Limit(1);
      $this->db->Query();
      
      return true;
    }

    function Purge()
    {
      if(!$this->isload)
      {
        $this->SetError("Set is not loaded!");
	    return false;
      }
      if(empty($this->fields['id']))
      {
        $this->SetError("Trying to purge with no id specified!");
        return false;
      }

      /* efface les maps */
      $this->db->NewQuery("DELETE", "maps");
      $this->db->Where("set_id", $this->fields['id']);
      $this->db->Query();
        
      /* efface les records */
      $this->db->NewQuery("SELECT", "rec");
      $this->db->Where("levelset", $this->fields['id']);
      $res = $this->db->Query();
      
      $rec = new Record($this->db);

      /* boucle sur tous les records */
      while ($val = $this->db->FetchArray($res))
      {
        $rec->LoadFromId($val['id']); 
	    $rec->Purge(true);
      }
      
      /* efface le set */
      $this->db->NewQuery("DELETE", "sets");
      $this->db->Where("id", $this->fields['id']);
      $this->db->Limit(1);
      $this->db->Query();

      $this->isload = false;

      return true;
    }

    function Insert()
    { 
      if (!$this->isload)
	      return false;
      $this->db->NewQuery("INSERT",  "sets");
      $this->db->Insert($this->fields);
      $this->db->Query();
      
      /* le but ici est de récupérer le nouveau set pour mise à jour des infos */
      /* cela permet d'avoir le bon id surtout, pour un affichage correct */
      $this->db->NewQuery("SELECT", "sets");
      $this->db->Sort(array("id"), "DESC");
      $this->db->Limit(1);
      $this->db->Query(); 
      $this->SetFields($this->db->FetchArray());

      return true;
    }

    function GetMapsRes()
    {
      $this->db->NewQuery("SELECT", "maps");
      $this->db->Where("set_id", $this->fields['id']);
      $this->db->Sort(array("level_num"), "ASC");
      $res = $this->db->Query();

      return $res;
    }
    
    function AddMap($level_num, $map_solfile)
    {
    	/* Names by default */
    	$levels = array (
		    1 => "01",
		    2 => "02",
		    3 => "03",
		    4 => "04",
		    5 => "I",
		    6 => "05",
		    7 => "06",
		    8 => "07",
		    9 => "08",
		    10 => "II",
		    11 => "09",
		    12 => "10",
		    13 => "11",
		    14 => "12",
		    15 => "III",
		    16 => "13",
		    17 => "14",
		    18 => "15",
		    19 => "16",
		    20 => "IV",
		    21 => "17",
		    22 => "18",
		    23 => "19",
		    24 => "20",
		    25 => "V",
		);
      $nb_name = 
      $this->db->NewQuery("INSERT",  "maps");
      $map_fields = array(
	      "set_id"      => $this->fields['id'],
	      "level_num"   => $level_num,
          "level_name"  => $levels[$level_num],
	      "map_solfile" => $map_solfile,
      );
      $this->db->Insert($map_fields);
      $this->db->Query();

      return true;
    }

    function GetId()
    {
      return $this->fields['id'];
    }
    
    function GetName()
    {
      return $this->fields['set_name'];
    }
    
    function GetShortName()
    {
      return $this->fields['set_shortname'];
    }    
    function GetPath()
    {
      return $this->fields['set_path'];
    }
    
    function GetAuthor()
    {
      return $this->fields['author'];
    }
    
    function SetFields($fields)
    {
        foreach ($fields as $name => $value)
        {
            if (!empty($name) && (
                $name == "id" 
             || $name == "set_name"
             || $name == "set_shortname"
             || $name == "set_path"
             || $name == "author"))
                   $this->fields[$name] = $value;
        }
        $this->isload=true;
    }
    
    function SetError($error)
    {
      $this->error = $error;
      throw new Exception($this->error);
    }
    
    function GetError()
    {
      return $this->error;
    }
}