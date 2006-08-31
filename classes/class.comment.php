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

class Comment
{
    var $db;
    var $isload;

	/*__Constructeur__
	Cette fonction initialise l'objet Record.
	*/
	function Comment(&$db)
	{
        $this->db = &$db;
        $this->isload = true;
	}

    function LoadFromId($id)
    {
      if (empty($id))
        return false;
      unset($this->fields);
      $this->db->RequestInit("SELECT", "com");
      $this->db->RequestGenericFilter("id", $id);
      if(!$this->db->Query()) {
        $this->SetError($this->db->GetError());
        return false;
      }
      else {
        if ($this->db->NumRows()<1)
        {
          $this->error = "No comment match this id!";
          return false;
        }
        $this->SetFields($this->db->FetchArray());
        $this->isload=true;
        return true;
      }
    }

    function Insert()
    {
      if(!$this->isload)
      {
        $this->error = "Comment is empty !";
        return false;
      }
      if (empty($this->fields['user_id']))
      {
        $this->error = "Error user unknown, bad id.";
        return false;
      }

      $content = $this->fields['content'];

      $content = str_replace("&lt;","<", $content);
      $content = str_replace("&gt;",">", $content);
      $content = strip_tags($content);
      $content = str_replace("\n", "<br />", $content);

      $f = array (
        "content"     => $content,
      );
      $this->SetFields($f);
    
      $this->db->RequestInit("INSERT", "com");
      $this->db->RequestInsert($this->fields);
      if(!$this->db->Query()) {
        $this->SetError($this->db->GetError());
        return false;
      }
      $this->_RecordRecountComments();
      return true;
    }
    
    function Update($conservative=true)
    {
      if(!$this->isload)
      {
        $this->error = "Comment is empty !";
        return false;
      }
      $content = $this->fields['content'];

      $content = str_replace("&lt;","<", $content);
      $content = str_replace("&gt;",">", $content);
      $content = strip_tags($content);
      $content = str_replace("\n", "<br />", $content);

      $f = array (
        "content"     => $content,
      );

      $this->SetFields($f);
      $this->db->RequestInit("UPDATE", "com");
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
        $this->error = "Comment is empty !";
        return false;
      }
      $this->db->RequestInit("DELETE", "com");
      $this->db->RequestGenericFilter("id", $this->fields['id']);
      if(!$this->db->Query()) {
        $this->SetError($this->db->GetError());
        return false;
      }
      $this->_RecordRecountComments();
      return true;
    }

    function _RecordRecountComments()
    {
      $rec = new Record($this->db);
      $rec->LoadFromId($this->GetReplayId());
      $count = $this->db->RequestCountComments($rec->GetId());
      $rec->SetFields(array("comments_count" => $count));
      $rec->Update(true);

      /* modifié aussi les stats utilisateur */
      $u = new User($this->db);
      $u->LoadFromId($this->GetUserId());
      $u->_RecountComments();
    }
    
    function GetId()
    {
      return $this->fields['id'];
    }

    function GetReplayId()
    {
      return $this->fields['replay_id'];
    }

    function GetPseudo()
    {
      global $users_cache;
      return $users_cahce[$this->fields['user_id']];
    }

    function GetUserid()
    {
      return $this->fields['user_id'];
    }
    
    function GetContent()
    {
      return $this->fields['content'];
    }


    function SetFields($fields)
    {
        foreach ($fields as $name => $value)
        {
            if (!empty($name) && (
                $name == "id" 
             || $name == "replay_id" 
             || $name == "user_id" 
             || $name == "content"
             || $name == "timestamp"))
                   $this->fields[$name] = $value;
        }
        $this->isload=true;
    }

    function GetFields()
    {
      return $this->fields;
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
