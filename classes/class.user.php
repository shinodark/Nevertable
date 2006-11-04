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

class User
{
    var $db;
    var $fields;
    var $isload;

	/*__Constructeur__
	Cette fonction initialise l'objet Record.

    @param: pointeur vers la base de donnée
	*/
	function User(&$db)
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
      $this->db->RequestInit("SELECT", "users");
      $this->db->RequestGenericFilter("id", $id);
      if(!$this->db->Query()) {
        $this->SetError($this->db->GetError());
        return false;
      }
      else {
        if ($this->db->NumRows()<1)
        {
          $this->error = "No user match this id!";
          return false;
        }
        $this->SetFields($this->db->FetchArray());
        $this->isload=true;
        return true;
      }
    }

    /* Chargement des champs d'un record à partir du pseudo */
    function LoadFromPseudo($pseudo)
    {
      $this->isload = false;
      unset($this->fields);
      if (empty($pseudo))
        return false;
      $this->db->RequestInit("SELECT", "users");
      $this->db->RequestGenericFilter("pseudo", $pseudo);
      if(!$this->db->Query()) {
        $this->SetError($this->db->GetError());
        return false;
      }
      else {
        if ($this->db->NumRows()<1)
        {
          $this->error = "No user match this pseudo!";
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
      $this->db->RequestInit("UPDATE", "users");
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
      if ($this->GetTotalRecords() > 0)
      {
        $this->SetError("User deletion deactivated. Need to implement record deletion too.");
        return false;
      }

      $this->db->RequestInit("DELETE", "users");
      $this->db->RequestGenericFilter("id", $id);
      $this->db->RequestLimit(1);
      if(!$this->db->Query())
      {
        $this->SetError($this->db->GetError(), 400);
        return false;
      }
      return true;
    }

    /* Insertion d'un nouveau record dans la bdd */
    function Insert()
    { 
      if (!$this->isload)
        return false;
      $this->_CleanFields();
      $this->db->RequestInit("INSERT",  "users");
      $this->db->RequestInsert($this->fields);
      if(!$this->db->Query()) {
        $this->SetError($this->db->GetError());
        return false;
      }
      return true;
    }

    function _RecountTotalRecords()
    {
      if (!$this->isload)
        return false;
      $total_records = $this->db->RequestCountUserRecords($this->GetId());
      $this->SetFields(array("stat_total_records" => $total_records));
      $this->Update();
      return $total_records;
    }

    function _RecountBestRecords()
    {
      if (!$this->isload)
        return false;
      $best_records = $this->db->RequestCountUserBest($this->GetId());
      $this->SetFields(array("stat_best_records" => $best_records));
      $this->Update();
      return $best_records;
    }

    function _RecountComments()
    {
      if (!$this->isload)
        return false;
      $comments = $this->db->RequestCountUserComments($this->GetId());
      $this->SetFields(array("stat_comments" => $comments));
      $this->Update();
      return $comments;
    }
    
    function CountBestRecords_WithType($type)
    {
      if (!$this->isload)
        return false;
      $this->db->RequestInit("SELECT", "rec", "COUNT(id)");
      $this->db->RequestGenericFilter("user_id", $this->GetId());
      $this->db->RequestGenericFilter("folder", get_folder_by_name("contest"));
      $this->db->RequestGenericFilter("type", $type);
      if ($type != get_type_by_name("freestyle"))
        $this->db->RequestGenericFilter("isbest", 1);
      $this->db->Query();
      $res = $this->db->FetchArray();
      return $res['COUNT(id)'];
    }

    /* Programmation de champs quelconques */

    function LoadOptions()
    {
      global $config;

      if (!empty($this->fields['user_limit']))
        $config['limit'] = $this->fields['user_limit'];
      if (!empty($this->fields['user_sidebar_comments']))  
        $config['sidebar_comments'] = $this->fields['user_sidebar_comments'];
      if (!empty($this->fields['user_sidebar_comlength']))
        $config['sidebar_comlength'] = $this->fields['user_sidebar_comlength'];
      if (!empty($this->fields['user_sort']))
        $config['opt_user_sort'] = $this->fields['user_sort'];
      if (!empty($this->fields['user_theme']))
        $config['opt_user_theme'] = $this->fields['user_theme'];
    }

    function GetId()
    {
      return $this->fields['id'];
    }
    
    function GetAvatar()
    {
      return $this->fields['user_avatar'];
    }
    
    function GetPseudo()
    {
      return $this->fields['pseudo'];
    }
    
    function GetLevel()
    {
      return $this->fields['level'];
    }
    
    function GetLocalisation()
    {
      return $this->fields['user_localisation'];
    }
    function GetAvatarHtml()
    {
      global $config;

      if (!$this->isload)
        return "";
      if (!empty($this->fields['user_avatar']))
        return "<img src=\"".ROOT_PATH.$config['avatar_dir']."/".$this->fields['user_avatar']."\" alt=\"\" />";
      else
        return "<i>No Avatar</i>";
    }
    
    function GetSpeech()
    {
      return $this->fields['user_speech'];
    }
    
    function GetWeb()
    {
        return $this->fields['user_web'];
    }
    
    function GetWebHtml()
    {
        return "<a href=\"".$this->fields['user_web']."\">".$this->fields['user_web']."</a>";
    }
    
    function GetSort()
    {
      if (!empty($this->fields['user_sort']))
        return $this->fields['user_sort'];
      else
        return "old";
    }
    
    function GetTheme()
    {
      if (!empty($this->fields['user_theme']))
        return $this->fields['user_theme'];
      else
        return "default";
    }
    
    function GetLimit()
    {
      global $config;
      if (!empty($this->fields['user_limit']))
        return $this->fields['user_limit'];
      else
        return $config['limit'];
    }
    
    function GetSidebarComments()
    {
      global $config;
      if (!empty($this->fields['user_sidebar_comments']))  
        return $this->fields['user_sidebar_comments'];
      else
        return $config['sidebar_comments'];
    }
    
    function GetSidebarComLength()
    {
      global $config;
      if (!empty($this->fields['user_sidebar_comlength']))
        return $this->fields['user_sidebar_comlength'];
      else
        return $config['sidebar_comlength'];
    }

    function GetTotalRecords()
    {
      return $this->fields['stat_total_records'];
    }
    
    function GetBestRecords()
    {
      return $this->fields['stat_best_records'];
    }
    
    function GetComments()
    {
      return $this->fields['stat_comments'];
    }
    
    function GetFields()
    {
      return $this->fields;
    }

    function SetFields($fields)
    {
        foreach ($fields as $name => $value)
        {
            if (!empty($name) && (
                $name == "id" 
             || $name == "level"
             || $name == "pseudo"
             || $name == "passwd"
             || $name == "email"
             || $name == "user_limit"
             || $name == "user_sidebar_comments"
             || $name == "user_sidebar_comlength"
             || $name == "user_sort"
             || $name == "user_theme"
             || $name == "user_avatar"
             || $name == "user_speech"
             || $name == "user_localisation"
             || $name == "user_web"
             || $name == "stat_total_records"
             || $name == "stat_best_records"
             || $name == "stat_comments"))
                   $this->fields[$name] = $value;
        }
        $this->isload=true;
    }

    function _CleanFields()
    {
      $this->fields['user_speech'] = CleanContentHtml($this->fields['user_speech']);
      $this->fields['user_web'] = CleanContentHtml($this->fields['user_web']);
      $this->fields['user_localisation'] = CleanContentHtml($this->fields['user_localisation']);
      $this->fields['user_sort'] = CleanContentHtml($this->fields['user_sort']);
      $this->fields['user_theme'] = CleanContentHtml($this->fields['user_theme']);
      $this->fields['user_avatar'] = CleanContentHtml($this->fields['user_avatar']);
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
