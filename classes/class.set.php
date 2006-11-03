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

    function Update($conservative=false,$id="")
    {
      if (!$this->isload)
        return false;
      if(empty($id))
        $id = $this->fields['id'];

      $this->_CleanFields();
      $this->db->RequestInit("UPDATE", "sets");
      $this->db->RequestUpdateSet($this->fields, $conservative);
      $this->db->RequestGenericFilter("id", $id);
      $this->db->RequestLimit(1);
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
      
      $this->SetError("Set deletion deactivated. Need to implement map deletion too.");
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
