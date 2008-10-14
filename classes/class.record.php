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

class Record
{
    var $db;
    var $fields;
    var $isload;

    /*__Constructeur__
    Cette fonction initialise l'objet Record.

    @param: pointeur vers la base de donnŽe
    */
    function Record(&$db)
    {
       $this->db = &$db;
       $this->isload = false;
    }

    /* Chargement des champs d'un record ˆ partir de l'id */
    function LoadFromId($id)
    {
      if (empty($id))
        return false;
      unset($this->fields);
      $this->db->NewQuery("SELECT", "rec");
      $this->db->Where("id", $id);
      if(!$this->db->Query()) {
        $this->SetError($this->db->GetError());
        return false;
      }
      else {
        if ($this->db->NumRows()<1)
        {
          $this->error = "No record match this id!";
          return false;
        }
        $this->SetFields($this->db->FetchArray());
        $this->isload=true;
        return true;
      }
    }

    /* Chargement des champs d'un record ˆ partir de l'id */
    /* @return: $hash['nb'] : Nombre de records trouvés 
                $hash['id'] : array("id1", "id2", ...) : liste des ids trouvés 
    */
    function LoadFromMatch($pseudo, $levelset, $level, $type, $folder)
    {
      unset($this->fields);
      $this->db->NewQuery("SELECT", "rec");
      $this->db->Where("pseudo", $pseudo);
      $this->db->Where("levelset", (integer)$levelset);
      $this->db->Where("level", (integer)$level);
      $this->db->Where("type", (integer)$type);
      $this->db->Where("folder", (integer)$folder);
      if(!$this->db->Query()) {
        $this->SetError($this->db->GetError());
        return false;
      }
      else {
        $ret['nb'] = $this->db->NumRows();
        if ($ret['nb'] < 1)
          $this->isload=false;
        else
          $this->isload=true;
        $this->SetFields($this->db->FetchArray());
        $ret['id'] = array();
        while ($val = $this->db->FetchArray())
            $ret['id']  = array_push ($ret['id'], $val['id']);
        return $ret;
      }
    }

    /* Mise à jour du record dans la bdd avec les champs actuellement chargŽ */
    function Update($conservative=false)
    {
      if(!$this->isload)
      {
        $this->error = "Record is not loaded!";
	return false;
      }
      if(empty($this->fields['id']))
      {
        $this->error = "Trying to update with no id specified!";
        return false;
      }

      $this->db->NewQuery("UPDATE", "rec");
      $this->db->UpdateSet($this->fields, $conservative);
      $this->db->Where("id", $this->fields['id']);
      $this->db->Limit(1);
      if(!$this->db->Query()) {
        $this->SetError($this->db->GetError());
        return false;
      }
      else {
        $this->_UpdateUserStats();
        return true;
      }
    }

    /* Insertion d'un nouveau record dans la bdd */
    function Insert()
    {
      if (!$this->isload)
        return false;
      $this->db->NewQuery("INSERT",  "rec");
      $this->db->Insert($this->fields);
      if(!$this->db->Query()) {
        $this->SetError($this->db->GetError());
        return false;
      }
      /* le but ici est de rŽcupŽrer le nouveau record pour mise ˆ jour des infos */
      /* cela permet d'avoir le bon id surtout, pour un affichage correct */
      $this->db->NewQuery("SELECT", "rec");
      $this->db->Sort(array("id"), "DESC");
      $this->db->Limit(1);
      if($this->db->Query()) 
        $this->SetFields($this->db->FetchArray());
      else
      {
        $this->SetError($this->db->GetError());
        return false;
      }
      $this->_UpdateUserStats();

      return true;
    }

    /* Deplacement de dossier */
    function Move($folder)
    {
      global $config;

      if (!$this->isload)
	      return false;
	  
      /* DŽjˆ dans le bon dossier */
      if ($this->fields['folder'] == $folder)
        return true;
      
      $ret = true;
     
      /* DŽplacement hors du contest -> Plus un best record */
      if ($folder != get_folder_by_name("contest"))
      {
        $this->SetIsBest(0);
      }
      
      $this->SetFields(array("folder" => $folder));
      if(!$this->Update())
        $ret = false;
      $this->_UpdateUserStats();
      return $ret;
    }

    /* Fusionne le replay avec target. Le replay en cours est effacŽ et la clible est modifiŽe ! */
    function Merge($target_id)
    {
      global $config;

      $target = new Record($this->db);
      $ret_target = $target->LoadFromId($target_id);
      if ($ret_target)
      {
        /* effacement du fichier replay de la cible */
        $target_folder_name = get_folder_by_number($target->GetFolder());
        $f = new FileManager($config['replay_dir']."/".$target->GetReplay());
        $ret_file = $f->Unlink();
        if ($ret_file)
        {
           // Le record actuel prend le nom de la cible        
           $this->SetFields(array("replay" => $f->GetBaseName()));       
 
           /* charge la cible avec ses nouveaux parametres */
           $fields_load = array (
                 "pseudo" => $this->fields['pseudo'],
                 "levelset" => $this->fields['levelset'],
                 "level" => $this->fields['level'],
                 "time" => $this->fields['time'],
                 "coins" => $this->fields['coins'],
                 "replay" => $this->fields['replay'],
                 "type" => $this->fields['type'],
                 );
           $target->SetFields($fields_load);
           /* effacement du record,en gardant le fichier replay qui est a la cible maintenant */
           $this->Purge(false);
           /* le record de $this est maintenant la cible */
           $this->SetFields($target->GetFields());
        }

        if (!$ret_file)
        {
          $this->SetError($f->GetError());
          return false;
        }
        
        /* ecrasement en modifiant le timestamp */
        $ret_target = $target->Update(false,$target_id);
        $target->_UpdateUserStats();
      }
      
      if (!$ret_target)
      {
        $this->SetError($target->GetError());
        return false;
      }
      
      return true;
    }

    /* Effacement dŽfinitif d'un record + fichier attachŽ si parametre est true */
    function Purge($filedelete=false)
    {
      if(!$this->isload)
      {
        $this->error = "Record is not loaded!";
	return false;
      }
      if(empty($this->fields['id']))
      {
        $this->error = "Trying to purge with no id specified!";
        return false;
      }

      $this->db->NewQuery("DELETE", "com");
      $this->db->Where("replay_id", $this->fields['id']);
      if(!$this->db->Query()) {
        $this->SetError($this->db->GetError());
        return false;
      }
      
      $this->db->NewQuery("DELETE", "rec");
      $this->db->Where("id", $this->fields['id']);
      $this->db->Limit(1);
      if(!$this->db->Query()) {
        $this->SetError($this->db->GetError());
        return false;
      }
      if($filedelete)
      {
        /* purge fichier */
        if ($this->GetReplayRelativePath())
        {
          $f = new FileManager($this->GetReplayRelativePath());
          if(!$f->Unlink())
             gui_button_error($this->SetError($f->GetError()), 400);
          else
             gui_button("replay file : ".$f->GetBaseName()." deleted from server", 500);
        }
        else
        {	
            gui_button_error("Error in GetReplayRelativePath(), replay:".$this->fields['replay']. " folder: ".$this->fields['folder'], 500);
        }
      }
      $this->_UpdateUserStats();
      $this->isload = false;
      return true;
    }

    function GetId()
    {
      return $this->fields['id'];
    }
    
    function GetUserId()
    {
      return $this->fields['user_id'];
    }
    
    function GetTime()
    {
      return $this->fields['time'];
    }
    
    function GetCoins()
    {
      return $this->fields['coins'];
    }
    
    function GetSet()
    {
      return $this->fields['levelset'];
    }
    
    function GetLevel()
    {
      return $this->fields['level'];
    }
    
    function GetReplay()
    {
      return $this->fields['replay'];
    }
    
    function GetReplayRelativePath()
    {
      global $config;

      return $config['replay_dir'].$this->GetReplay();
    }
    
    function GetType()
    {
      return $this->fields['type'];
    }

    function GetFolder()
    {
      return $this->fields['folder'];
    }

    function IsBest()
    {
      return ($this->fields['isbest']==1 ? true : false);
    }
    
    function SetIsBest($b)
    {
      $this->fields['isbest'] = ( ($b==1 || $b==true) ? 1 : 0);
    }

    function GetFields()
    {
      return $this->fields;
    }

    /* Programmation de chaps quelconques */
    function SetFields($fields)
    {
        foreach ($fields as $name => $value)
        {
            if (!empty($name) && (
                $name == "id" 
             || $name == "user_id" 
             || $name == "levelset" 
             || $name == "level" 
             || $name == "time"  
             || $name == "coins" 
             || $name == "replay" 
             || $name == "type" 
             || $name == "folder" 
             || $name == "isbest"
             || $name == "comments_count"
             || $name == "timestamp"))
                   $this->fields[$name] = $value;
        }
        $this->isload=true;
    }

    function _UpdateUserStats()
    {
      /* mise à jour stat utilisateur */
      $u = new User($this->db);
      $ret = $u->LoadFromId($this->GetUserId());
      if ($ret)
      {
        $u->_RecountTotalRecords();
        $u->_RecountBestRecords();
        $u->_RecountComments();
      }
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
