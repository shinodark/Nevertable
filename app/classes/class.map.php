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
	
class Map
{
    var $db;
    var $fields;
    var $isload;

	/*__Constructeur__
	Cette fonction initialise l'objet Map.

    @param: pointeur vers la base de donnée
	*/
	function Map(&$db)
	{
        $this->db = &$db;
        $this->isload = false;
	}

    /* Chargement des champs d'une map à partir de l'id */
    function LoadFromId($id)
    {
      $this->isload = false;
      unset($this->fields);
      if (empty($id))
        return false;
      $this->db->NewQuery("SELECT", "maps");
      $this->db->Where("id", $id);
      $this->db->Query();

      if ($this->db->NumRows()<1)
      {
          $this->SetError("No map match this id!");
          return false;
      }
      $this->SetFields($this->db->FetchArray());
      $this->isload=true;
      return true;
    }

    function Update()
    {
      if (!$this->isload)
        return false;
      if(empty($id))
        $id = $this->fields['id'];

      $this->db->NewQuery("UPDATE", "maps");
      $this->db->UpdateSet($this->fields);
      $this->db->Where("id", $id);
      $this->db->Limit(1);
      if(!$this->db->Query()) {
        $this->SetError($this->db->GetError());
        return false;
      }
      else {
        return true;
      }
    }

    function Purge($id="")
    {
      if (!$this->isload)
        return false;
      if(empty($id))
        $id = $this->fields['id'];
      
      return false;

    }

    function Insert()
    { 
      if (!$this->isload)
        return false;
      return false;
    }

    function GetId()
    {
      return $this->fields['id'];
    }
    
    function GetSetId()
    {
      return $this->fields['set_id'];
    }
    
    function GetLevelNum()
    {
      return $this->fields['level_num'];
    }
    
    function GetLevelName()
    {
      return $this->fields['level_name'];
    }
    
    function GetSolFile()
    {
      return $this->fields['map_solfile'];
    }
    
    function SetFields($fields)
    {
        foreach ($fields as $name => $value)
        {
            if (!empty($name) && (
                $name == "id" 
             || $name == "set_id"
             || $name == "level_num"
             || $name == "level_name"
             || $name == "map_solfile"))
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
