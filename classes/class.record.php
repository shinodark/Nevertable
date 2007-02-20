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

    @param: pointeur vers la base de donnée
	*/
	function Record(&$db)
	{
        $this->db = &$db;
        $this->isload = false;
	}

    /* Chargement des champs d'un record à partir de l'id */
    function LoadFromId($id)
    {
      if (empty($id))
        return false;
      unset($this->fields);
      $this->db->RequestInit("SELECT", "rec");
      $this->db->RequestGenericFilter("id", $id);
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

    /* Chargement des champs d'un record à partir de l'id */
    /* @return: $hash['nb'] : Nombre de records trouvés 
                $hash['id'] : array("id1", "id2", ...) : liste des ids trouvés 
    */
    function LoadFromMatch($pseudo, $levelset, $level, $type, $folder)
    {
      unset($this->fields);
      $this->db->RequestInit("SELECT", "rec");
      $this->db->RequestGenericFilter("pseudo", $pseudo);
      $this->db->RequestGenericFilter("levelset", (integer)$levelset);
      $this->db->RequestGenericFilter("level", (integer)$level);
      $this->db->RequestGenericFilter("type", (integer)$type);
      $this->db->RequestGenericFilter("folder", (integer)$folder);
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

    /* Mise à jour du record dans la bdd avec les champs actuellement chargé */
    function Update($conservative=false,$id="")
    {
      if (!$this->isload)
        return false;
      if(empty($id))
        $id = $this->fields['id'];
      $this->db->RequestInit("UPDATE", "rec");
      $this->db->RequestUpdateSet($this->fields, $conservative);
      $this->db->RequestGenericFilter("id", $id);
      $this->db->RequestLimit(1);
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
      $this->db->RequestInit("INSERT",  "rec");
      $this->db->RequestInsert($this->fields);
      if(!$this->db->Query()) {
        $this->SetError($this->db->GetError());
        return false;
      }
      else {
        /* le but ici est de récupérer le nouveau record pour mise à jour des infos */
        /* cela permet d'avoir le bon id surtout, pour un affichage correct */
        $this->db->RequestInit("SELECT", "rec");
        $this->db->RequestGenericSort(array("timestamp"), "DESC");
        $this->db->RequestLimit(1);
        if($this->db->Query()) {
          $this->SetFields($this->db->FetchArray());
        }
        $this->_UpdateUserStats();

        /* on retourne true de toute façon, l'opération est déjà effectuée */
        return true;
      }
      return true;
    }

    /* Deplacement de dossier */
    function Move($folder)
    {
      global $config;

      $ret = true;
      if (!$this->isload)
        return false;
     
      if ($this->GetReplayRelativePath())
        $f = new FileManager($this->GetReplayRelativePath());
      else
      {
        button_error("Error in GetReplayRelativePath()", 300);
        return false;
      }

      /* Déplacement hors du contest -> Plus un best record */
      if ($folder != get_folder_by_name("contest"))
      {
        $this->SetIsBest(0);
      }
      if (!$f->Move(ROOT_PATH.$config['replay_dir'].get_folder_by_number($folder)))
      {
        $this->SetError($f->GetError()." But record is moved.");
        /* affiche l'erreur car avec le ret=true, elle ne sera pas affichée sinon */
        button_error($this->GetError(), 300);
        $ret = true;
      }
      else /* change le nom au cas où il est changer par $f->Move()... */
      {
        $this->SetFields(array("replay" => $f->GetBaseName()));
      }
      
      $this->SetFields(array("folder" => $folder));
      if(!$this->Update())
        $ret = false;
        
      $this->_UpdateUserStats();
      return $ret;
    }

    /* Fusionne le replay avec target. Le replay en cours est effacé et la clible est modifiée ! */
    function Merge($target_id)
    {
      global $config;

      $target = new Record($this->db);
      $ret_target = $target->LoadFromId($target_id);
      if ($ret_target)
      {
        /* effacement du fichier replay de la cible */
        $target_folder_name = get_folder_by_number($target->GetFolder());
        $f = new FileManager($config['replay_dir'].$target_folder_name."/".$target->GetReplay());
        $ret_file = $f->Unlink();
        if ($ret_file)
        {
           /* les deux ne sont dans le meme repertoire */
           if (!$target->GetFolder() == $this->GetFolder())
           {
             $folder_name = get_folder_by_number($this->GetFolder());
             /* déplacement du fichier replay ds le repertoire de la cible */
             $f->SetFileName($config['replay_dir'].$folder_name."/".$this->GetReplay());
             $ret_file = $f->Move(ROOT_PATH.$config['replay_dir'].$target_folder_name);
             /* mise à jour, pour le chargement des parametres juste après */
             $this->SetFields(array("replay" => $f->GetBaseName()));
           }

           if ($ret_file)
           {
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

    /* Effacement définitif d'un record + fichier attaché si parametre est true */
    function Purge($filedelete=false)
    {
      if(!$this->isload)
        return false;
      $this->db->RequestInit("DELETE", "rec");
      $this->db->RequestGenericFilter("id", $this->fields['id']);
      $this->db->RequestLimit(1);
      if(!$this->db->Query()) {
        $this->SetError($this->db->GetError());
        return false;
      }
      else {
        $this->db->RequestInit("DELETE", "com");
        $this->db->RequestGenericFilter("replay_id", $this->fields['id']);
        if(!$this->db->Query()) {
          $this->SetError($this->db->GetError());
          return false;
        }
        else if($filedelete) {
          /* purge fichier */
		  if ($this->GetReplayRelativePath())
		  {
            $f = new FileManager($this->GetReplayRelativePath());
            if(!$f->Unlink())
            {
              $this->SetError($f->GetError());
              return false;
            }
            else
            {
               button("replay file : ".$f->GetBaseName()." deleted from server", 500);
            }
          }
          $this->_UpdateUserStats();
        }
      }
      return true;
    }

    function GetId()
    {
      return $this->fields['id'];
    }
    
    function GetuserId()
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

      if ( empty($this->fields['replay']) || empty($this->fields['folder']) )
	     return false;
      else
	     return $config['replay_dir'].get_folder_by_number($this->GetFolder())."/".$this->GetReplay();
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
