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
      $this->db->RequestInit("SELECT", "sets");
      $this->db->RequestGenericFilter("id", $id);
      if(!$this->db->Query()) {
        $this->SetError($this->db->GetError());
        return false;
      }
      else {
        if ($this->db->NumRows()<1)
        {
          $this->error = "No set match this id!";
          return false;
        }
        $this->SetFields($this->db->FetchArray());
        $this->isload=true;
        return true;
      }
    }

    function Update($conservative=false)
    {
      if(!$this->isload)
      {
        $this->error = "Set is not loaded!";
	return false;
      }
      if(empty($this->fields['id']))
      {
        $this->error = "Trying to update with no id specified!";
        return false;
      }

      $this->db->RequestInit("UPDATE", "sets");
      $this->db->RequestUpdateSet($this->fields, $conservative);
      $this->db->RequestGenericFilter("id", $this->fields['id']);
      $this->db->RequestLimit(1);
      if(!$this->db->Query()) {
        $this->SetError($this->db->GetError());
        return false;
      }
      else {
        return true;
      }
    }

    function Purge()
    {
      if(!$this->isload)
      {
        $this->error = "Set is not loaded!";
	return false;
      }
      if(empty($this->fields['id']))
      {
        $this->error = "Trying to purge with no id specified!";
        return false;
      }

      /* efface les maps */
      $this->db->RequestInit("DELETE", "maps");
      $this->db->RequestGenericFilter("set_id", $this->fields['id']);
      if(!$this->db->Query()) {
        $this->SetError($this->db->GetError());
        return false;
      }
      
      /* efface les records */
      $this->db->RequestInit("SELECT", "rec");
      $this->db->RequestGenericFilter("levelset", $this->fields['id']);
      $res = $this->db->Query();
      if(!$res) {
        $this->SetError($this->db->GetError());
        return false;
      }

      $rec = new Record($this->db);

      /* boucle sur tous les records */
      while ($val = $this->db->FetchArray($res))
      {
        if(!$rec->LoadFromId($val['id']))
        {
          $this->SetError($rec->GetError());
          return false;
        }
	if (!$rec->Purge(true))
	{
          $this->SetError($rec->GetError());
          return false;
	}
      }
      
      /* efface le set */
      $this->db->RequestInit("DELETE", "sets");
      $this->db->RequestGenericFilter("id", $this->fields['id']);
      $this->db->RequestLimit(1);
      if(!$this->db->Query()) {
        $this->SetError($this->db->GetError());
        return false;
      }

      $this->isload = false;

      return true;
    }

    function Insert()
    { 
      if (!$this->isload)
	      return false;
      $this->db->RequestInit("INSERT",  "sets");
      $this->db->RequestInsert($this->fields);
      if(!$this->db->Query()) {
        $this->SetError($this->db->GetError());
        return false;
      }
      /* le but ici est de récupérer le nouveau set pour mise à jour des infos */
      /* cela permet d'avoir le bon id surtout, pour un affichage correct */
      $this->db->RequestInit("SELECT", "sets");
      $this->db->RequestGenericSort(array("id"), "DESC");
      $this->db->RequestLimit(1);
      if($this->db->Query()) 
         $this->SetFields($this->db->FetchArray());
      else
      {
        $this->SetError($this->db->GetError());
        return false;
      }

      return true;
    }

    function GetMapsRes()
    {
      $this->db->RequestInit("SELECT", "maps");
      $this->db->RequestGenericFilter("set_id", $this->fields['id']);
      $res = $this->db->Query();
      if(!$res) {
        $this->SetError($this->db->GetError());
        return false;
      }
      return $res;
    }
    
    function AddMap($level_num, $map_solfile)
    {
      $this->db->RequestInit("INSERT",  "maps");
      $map_fields = array(
	      "set_id"      => $this->fields['id'],
	      "level_num"   => $level_num,
	      "map_solfile" => $map_solfile,
      );
      $this->db->RequestInsert($map_fields);
      if(!$this->db->Query()) {
        $this->SetError($this->db->GetError());
        return false;
    }
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
             || $name == "set_path"
             || $name == "author"))
                   $this->fields[$name] = $value;
        }
        $this->isload=true;
    }
    
    function SetError($error)
    {
      $this->error = $error;
    }
    
    function GetError()
    {
      return $this->error;
    }
}
