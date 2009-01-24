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
      $this->db->NewQuery("SELECT", "com");
      $this->db->Where("id", $id);
      $this->db->Query();

      if ($this->db->NumRows()<1)
         $this->SetError("No comment match this id!");

      $this->SetFields($this->db->FetchArray());
      $this->isload=true;
      return true;
    }

    function Insert()
    {
      if(!$this->isload)
        $this->SetError = "Comment is not loaded !";

      if (empty($this->fields['user_id']))
        $this->SetError = "Error user unknown, bad id.";

      $content = $this->fields['content'];

      $content = CleanContent($content);

      $f = array (
        "content"     => $content,
      );
      $this->SetFields($f);
    
      $this->db->NewQuery("INSERT", "com");
      $this->db->Insert($this->fields);
      $this->db->Query();

      $this->_RecordRecountComments();
      return true;
    }
    
    function Update($conservative=true)
    {
      if(!$this->isload)
        $this->SetError("Comment is empty !");

      if(empty($this->fields['id']))
        $this->SetError("Trying to update with no id specified!");

      $content = $this->fields['content'];

      $content = CleanContent($content);

      $f = array (
        "content"     => $content,
      );

      $this->SetFields($f);
      $this->db->NewQuery("UPDATE", "com");
      $this->db->UpdateSet($this->fields, $conservative);
      $this->db->Where("id", $this->fields['id']);
      $this->db->Limit(1);
      $this->db->Query();

      return true;
    }
    
    function Purge()
    {
      if(!$this->isload)
        $this->SetError("Comment is empty !");

      if(empty($this->fields['id']))
        $this->SetError("Trying to purge with no id specified!");

      $this->db->NewQuery("DELETE", "com");
      $this->db->Where("id", $this->fields['id']);
      $this->db->Query();
      $this->_RecordRecountComments();
      $this->isload = false;
      return true;
    }

    function _RecordRecountComments()
    {
      $rec = new Record($this->db);
      $rec->LoadFromId($this->GetReplayId());
      $count = $this->db->helper->CountComments($rec->GetId());
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
      throw Exception($this->error);
    }
    
    function GetError()
    {
      return $this->error;
    }
}
