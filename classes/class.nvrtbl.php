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

class Nvrtbl
{
  var $db;
  var $dialog;
  var $current_user;
  var $style;
  var $start_time;
  var $process_time;
  var $mode;
  var $online_users_registered;
  var $online_users_guest;
  var $online_users_list;
    
  function Nvrtbl($mode="DialogStandard")
  {
    global $config, $langs;
  
    $this->online_users_registered = 0;
    $this->online_users_guest      = 0;
    $this->online_users_list       = array();

    $time = microtime();
    $time = explode(' ', $time);
    $this->start_time = $time[1] + $time[0];
    $this->db = new DB($config['bdd_server'],
                 $config['bdd_name'],
                 $config['bdd_prefix'],
                 $config['bdd_user'],
                 $config['bdd_passwd']);
    if(!$this->db->Connect())
    {
      gui_button_error($this->db->GetError(), 500);
      exit;
    }

    /* Chargement de la configuration dans la variable globale */
    $this->db->NewQuery("SELECT", "conf");
    if(!$this->db->Query())
    {
      gui_button_error($this->db->GetError(), 500);
      exit;
    }
    while ($val = $this->db->FetchArray())
	    $config[$val['conf_name']] = $val['conf_value'];
    
    /* Chargement des objets */
    $this->style = new Style();
    $auth = new Auth($this->db);
    
    /* Initialisation de la session */

    $auth->SessionBegin();

    if (Auth::Check(get_userlevel_by_name("member")))
    {
        if ($_SESSION['options_saved'] == false)
        {
          $this->current_user = new User($this->db);
          $this->current_user->LoadFromId($_SESSION['user_id']);
          $this->current_user->LoadOptions();
          Auth::_SaveUserOptions();
        }
        else
        {
          Auth::_LoadUserOptions();
        }
	$this->style->Select($config['opt_user_theme']);
    }
    else
    {
      $this->AddOnlineGuest();
    }

    /* Chargement de la lang */
    if (!empty($config['opt_user_lang']))
       $langcode = $config['opt_user_lang'];
    else if (!empty($config['default_lang']))
       $langcode = $config['default_lang'];
    
    if (!in_array($langcode, $langs))
    {
		gui_button_error("lang is not supported.", 200);
		exit;
    }
    // anglais par défaut pour les chaines, puis écrasement avec la langue de l'utilisateur
    if ($langcode != "en")
    	include (ROOT_PATH.$config['lang_dir']."lang.en.php");
    include (ROOT_PATH.$config['lang_dir']."lang.".$langcode.".php");

    /* Compte et gËre les utilisateur en ligne */
    $this->UpdateOnlineUsers();
    
    if ($mode=="DialogStandard" || $mode=="DialogAdmin")
      $this->dialog = new $mode($this->db, $this, $this->style);
    else if ($mode == "null")
      $this->dialog = null;
    else
      $this->dialog = new DialogStandard($this->db, $this, $this->style);

    $this->mode = $mode;
  }

  function Close()
  {
    $this->db->Disconnect();
  }
  

  /*__UTILS__*/

  /* Record RSS */
  function GetEarlierDate()
  {
    $this->db->NewQuery("SELECT", "rec");
    $this->db->SortFilter("old");
    $this->db->Limit(1, 0);
    $res = $this->db->Query();

    $val = $this->db->FetchArray();
    return GetDateFromTimestamp($val['timestamp']);
  }
  
  function GetLastRecords($order, $folder="contest")
  {
    global $config;

    $p = $config['bdd_prefix'];
    $this->db->Select(
        array("rec", "users", "sets" ),
        array(
            $p."rec.id AS id",
            $p."rec.type AS type",
            $p."rec.levelset AS levelset",
            $p."rec.level AS level",
            $p."users.pseudo AS pseudo",
            $p."rec.time AS time",
            $p."rec.coins AS coins",
            $p."rec.timestamp AS timestamp",
            $p."sets.set_name AS set_name",
            )
     );
     $this->db->Where(
        array($p."rec.user_id", $p."rec.levelset"),
        array($p."users.id", $p."sets.id"),
        "AND", false
    );
        
    $this->db->Where("folder", get_folder_by_name($folder));
    $this->db->SortFilter("old");
    $this->db->Limit($order, 0);
    $this->db->Query();

    $i=0;
    while($val = $this->db->FetchArray())
    {
      $Records[$i]['id'] = $val['id'];
      $Records[$i]['title'] = htmlspecialchars(get_type_by_number($val['type'])." ~ ".($val['set_name'])." ".$val['level']." by ".$val['pseudo'],ENT_NOQUOTES);
      $Records[$i]['level'] =  $val['set_name']." ".$val['level'];
      $Records[$i]['pseudo'] =  $val['pseudo'];
      $Records[$i]['time'] = sec_to_friendly_display($val['time']);
      $Records[$i]['coins'] = $val['coins'];
      $Records[$i]['type']  = get_type_by_number($val['type']);
      $Records[$i]['date'] = getIsoDate(GetDateFromTimestamp($val['timestamp']));
    
      $i++;
    }
  return $Records;
  }

  /* Comments RSS */
  function getEarlierDateComments()
  {
    $this->db->NewQuery("SELECT", "com");
    $this->db->SortFilter("old");
    $this->db->Limit(1, 0);
    $res = $this->db->Query();

    $val = $this->db->FetchArray();
    return GetDateFromTimestamp($val['timestamp']);
  }
  
  function getProcessTime()
  {
    $time = microtime();
    $time = explode(' ', $time);
    $time = $time[1] + $time[0];
    $this->process_time = timestamp_diff_in_secs($time, $this->start_time);
    /* on ne garde que 3 dÈcimales */
    return substr($this->process_time,0,5);
  }
  
  /* Statistiques */
  function GetStatsDump($folder)
  {
    global $config;

    $lst = "";
    $p = $config['bdd_prefix'];
    $this->db->Select(
            array("rec", "users", "sets", "maps"),
            array(
              $p."rec.id AS id",
              $p."rec.levelset AS levelset",
              $p."rec.level AS level",
              $p."rec.time AS time",
              $p."rec.coins AS coins",
              $p."rec.replay AS replay",
              $p."rec.type AS type",
              $p."rec.folder AS folder",
              $p."rec.timestamp AS timestamp",
              $p."rec.isbest AS isbest",
              $p."rec.user_id AS user_id",
              $p."users.pseudo AS pseudo",
              $p."sets.set_name AS set_name",
              $p."maps.map_solfile AS map_solfile",
            )
            );
     $this->db->Where(
            array($p."rec.user_id", $p."rec.levelset", $p."rec.levelset", $p."rec.level"),
            array($p."users.id", $p."sets.id", $p."maps.set_id", $p."maps.level_num"),
            "AND", false
        );
     $this->db->helper->FolderFilter($folder);
     $this->db->Sort(array("id"), "ASC");

     $res =   $this->db->Query();
     if(!$res)
        echo gui_button_error(  $this->db->GetError(), 500);

     $lst .= "id\tdate\ttype\tmember\tlevel\tset\tcoins\ttime\treplay\n";
     while ($val = $this->db->FetchArray())
     {
	  $lst .=
	       $val['id'] . "\t" .
	       $val['timestamp'] . "\t" .
	       get_type_by_number($val['type']) . "\t" .
	       $val['pseudo'] . "\t" .
	       $val['level'] . "\t" .
	       $val['set_name'] . "\t" .
	       $val['coins'] . "\t" .
	       $val['time'] . "\t" .
	       $val['replay'] . "\n" ;

     }

     return $lst;
  }

  /*__FONCTION DE GESTION__*/

  function ManageBestRecords($updated_record_fields, $type)
  {
    $val = $updated_record_fields;
    $ret = $this->db->helper->SetBestRecordByFields($val['level'], $val['levelset'], $type);
    switch ($val['type'])
    {
     case get_type_by_name("best time") : $critera = "time"; $check=true; break;
     case get_type_by_name("most coins"): $critera = "coins";$check=true; break;
     default : $check=false; $ret['isbest']=false; break;
    }
    if($check)
    {
      $best= $this->db->helper->GetBestRecord($val['type'], get_folder_by_name("contest"));
      $ret['isbest'] = is_a_best_record($val, $best, $critera);
    }
    return $ret;
  }

  /* AppelÈ quand un utilisateur se loggue */
  function AddOnlineUser()
  {
    /* on enlËve l'utilisateur de la liste des guests */
    $this->db->NewQuery("DELETE", "online");
    $this->db->Where("user_id", 0);
    $this->db->Where("ident", $_SERVER['REMOTE_ADDR']);
    $this->db->Limit(1);
    if(!$this->db->Query()) {
      gui_button_error("RemoveOnlineUser::".$this->db->GetError(), 300);
      return false;
    }

    /* Ajout de l'utilisateur logguÈ */
    /* Fait automatiquement par  UpdateOnlineUsers() */
    return true;
  }

  /* Ajout d'un utilisateur logguÈ dans la liste des online */
  function UpdateLoggedOnlineUser()
  {
    /* recherche de l'utilisateur dÈj‡ prÈsent dans la liste */
    $this->db->NewQuery("SELECT", "online", "COUNT(user_id)");
    $this->db->Where("user_id", $_SESSION['user_id']);
    $this->db->Where("ident", $_SESSION['user_pseudo']);
    if(!$this->db->Query()) {
      gui_button_error("RemoveOnlineUser::".$this->db->GetError(), 300);
      return false;
    }
    $res = $this->db->FetchArray();
    if ($res['COUNT(user_id)'] > 0) /* dÈj‡ prÈsent */
    {
      $this->db->NewQuery("UPDATE", "online");
      $this->db->UpdateSet(array("logged_time" => $this->start_time));
      $this->db->Where("user_id", $_SESSION['user_id']);
      if(!$this->db->Query()) {
        gui_button_error("RemoveOnlineUser::".$this->db->GetError(), 300);
        return false;
      }
    }
    else
    {
      $this->db->NewQuery("INSERT", "online");
      $this->db->Insert(array(
                          "user_id"     => $_SESSION['user_id'],
                          "ident"       => $_SESSION['user_pseudo'],
                          "logged_time" => $this->start_time,
                           ));
      if(!$this->db->Query()) {
        gui_button_error("RemoveOnlineUser::".$this->db->GetError(), 300);
        return false;
      }
    }
  }

  /* AppelÈ quand un utilisateur non logguÈ arrive */
  function AddOnlineGuest()
  {
    /* recherche de l'utilisateur dÈj‡ prÈsent dans la liste */
    $this->db->NewQuery("SELECT", "online", "COUNT(user_id)");
    $this->db->Where("user_id", $_SESSION['user_id']);
    $this->db->Where("ident", $_SERVER['REMOTE_ADDR']);
    if(!$this->db->Query()) {
      gui_button_error("RemoveOnlineUser::".$this->db->GetError(), 300);
      return false;
    }
    $res = $this->db->FetchArray();
    if ($res['COUNT(user_id)'] > 0) /* dÈj‡ prÈsent */
    {
      $this->db->NewQuery("UPDATE", "online");
      $this->db->UpdateSet(array("logged_time" => $this->start_time));
      $this->db->Where("ident", $_SERVER['REMOTE_ADDR'] );
      if(!$res = $this->db->Query()) {
        gui_button_error("RemoveOnlineUser::".$this->db->GetError(), 300);
        return false;
      }
    }
    else /* ajoute */
    {
      $this->db->NewQuery("INSERT", "online");
      $this->db->Insert(array(
                          "user_id"     => $_SESSION['user_id'],
                          "ident"       => $_SERVER['REMOTE_ADDR'],
                          "logged_time" => $this->start_time,
                           ));
      if(!$res = $this->db->Query()) {
        gui_button_error("RemoveOnlineUser::".$this->db->GetError(), 300);
        return false;
      }
    }
  }

  /* AppelÈ quand un utilisateur se dÈloggue */
  function RemoveOnlineUser()
  {
    $this->db->NewQuery("DELETE", "online");
    $this->db->Where("user_id", $_SESSION['user_id']);
    $this->db->Limit(1);
    if(!$this->db->Query()) {
      gui_button_error("RemoveOnlineUser::".$this->db->GetError(), 300);
      return false;
    }
   return true;
  }

  function UpdateOnlineUsers()
  {
    global $config;
    /* Mettre ‡ jour l'utilisateur */
    if ($_SESSION['user_logged'] == true)
    {
      $this->UpdateLoggedOnlineUser();
    }

    /* effacer les utilisateurs 'idle' */
    $this->db->NewQuery("DELETE", "online");
    $this->db->Where_lt("logged_time", $this->start_time-$config['online_idletime']);
    if(!$this->db->Query()) {
      gui_button_error("UpdateOnlineUser::" . $this->db->GetError(), 300);
      return false;
    }
    
    /* rÈcupËre la liste */
    $this->db->NewQuery("SELECT", "online");
    if(!$this->db->Query()) {
      gui_button_error("UpdateOnlineUser::" . $this->db->GetError(), 300);
      return false;
    }

    while ($val = $this->db->FetchArray())
    {
      if ($val['user_id'] > 0)
        $this->online_users_list[$this->online_users_registered++] = $val['ident'];
      else
        $this->online_users_guest++;
    }
  }

  function GetStats()
  {
    $ret = array();

    $ret['registered'] = $this->online_users_registered;
    $ret['guests']     = $this->online_users_guest;
    $ret['registered_list']     = $this->online_users_list;

    return $ret;
  }
  
  /*__FONCTIONS DE MAINTENANCE__*/

  function CheckDatabase()
  {
    global $folders, $config;

    $replay_path = ROOT_PATH.$config['replay_dir'];
    $dir_list = array();
    $f = new FileManager();

    /* listing du répertoire replays  */
    $res = $f->DirList($replay_path);
    if(!$res)
    {
       gui_button_error($f->GetError(), 500);
       return;
    }
    $dir_list = $res["files"] ;
    if (!$dir_list)
    {
       gui_button_error("Error opening \"".$dir."\" directory.",500);
       return;
    }
    

    /* base query : tous les records */
    $fields = array("replay", "id", "folder");
    $this->db->NewQuery("SELECT", "rec");
    $res = $this->db->Query();
    if(!$res)
    {
      gui_button_error($db->GetError(), 500);
      return;
    }

    echo "<div class=\"results\" style=\"width: 100%;\">\n";
    echo "<table>\n";
    echo "<caption>Database Check Results</caption>\n";
    echo "<tr><th>replay_id</th><th>replay_file</th><th>folder</th><th>status</th></tr>\n";
 
    $dup_arr = array(); $dup=1;
    $dir_list_found = array();
    $count = 0;

    while ($val = $this->db->FetchArray())
    {
      /* Recherche du replay */
      if (!empty($val['replay']))  
      {
        if (array_search($val['replay'], $dup_arr, true))
           $status = "<span style=\"color: orange;\">Duplicate</span>";
        else
        {
          if (file_exists($replay_path."/".$val['replay']))
          {
            $status = "<span style=\"color: green;\">OK</span>";
            /* test les fichiers dupliques, 2 records pointent vers le meme fichier */
            $dup_arr[$dup] =  $val['replay'];
            $dup++;
            /* ajoute le fichier a la liste des fichiers trouves */
            $dir_list_found[$count] = $val['replay'];
            $count++;
          }
          else
          {
            $status = "<span style=\"color: red;\">Not Found</span>";
          }
        }
      }
      else
      {
        $status = "No replay";
      }
    
      echo "<tr><td><a href=\"admin.php?link=".$val['id']."&amp;folder=".$val['folder']."\">".$val['id']."</a></td>";
      echo "<td><a href=\"filexplorer.php?grep=".$val['replay']."\">".$val['replay']."</a></td>";
      echo "<td>".get_folder_by_number($val['folder'])."</td>";
      echo  "<td>".$status."</td></tr>\n";
    }
    echo "</table>\n";
    echo "</div>\n";

    /* Affichage des fichiers orphelins, utilises par aucun record */

    echo "<div class=\"results\" style=\"width: 100%;\">\n";
    echo "<table>\n";
    echo "<tr><th>files uploaded, but not used</th></tr>\n";

    /* des fichiers non-orphelins ont été trouvés, affichage des différences */
    if (!empty($dir_list_found))
        $orphans = array_diff($dir_list, $dir_list_found);
      /* ce ne sont que des fichiers orphelins, si il y en a */
      else
        $orphans = $dir_list;

      while($f=array_pop($orphans))
        echo "<tr><td><a href=\"filexplorer.php?grep=".$f."\">".$f."</a></td></tr>\n";
      
      echo "</table>\n";
      echo "</div>\n";
  }

  function CheckAllRecords()
  {
    $rec = new Record($this->db);

    /* clean up */    
    $this->db->NewQuery("UPDATE", "rec");
    $my  = array("isbest" => "0");
    $this->db->UpdateSet($my, true);
    $this->db->Where("type", get_type_by_name("freestyle"));
    $res = $this->db->Query();
    if(!$res)
      gui_button_error($this->db->GetError(), 500);

    /* get best time records value */
    echo "<h2>best time </h2>";
    $best = $this->db->helper->GetBestRecord(get_type_by_name("best time"));

    /* browse all best time */
    $this->db->NewQuery("SELECT", "rec");
    $this->db->Where("type", get_type_by_name("best time"));
    //$this->db->Where("folder", get_folder_by_name("contest"));
    $this->db->AppendCustom("AND (folder=".get_folder_by_name("contest")." OR folder=".get_folder_by_name("oldones").")");
    $res = $this->db->Query();
    if(!$res)
      gui_button_error($this->db->GetError(), 500);

    $i=0;
    while ($val = $this->db->FetchArray($res))
    {
      $rec->LoadFromId($val['id']);
      /* set 'isbest' field */
      if($val['folder'] == get_folder_by_name("contest"))
      {
        if(is_a_best_record($val, $best, "time") && $val['isbest']==0)
        {
          $rec->SetIsBest(1);
          $rec->Update(true);
          echo "record id " . $val['id'] ." is a best time. Set.<br />";
          $i++;
        }
        else if(!is_a_best_record($val, $best, "time") && $val['isbest']==1)
        {
          $rec->SetIsBest(0);
          $rec->Update(true);
          $rec->Move(get_folder_by_name("oldones"));
          echo "record id " . $val['id'] ." is NOT the \"best time\". Moved to \"oldones\"<br />";
          $i++;
        }
        else if(!is_a_best_record($val, $best, "time") &&  $val['isbest']==0)
        {
          $rec->Move(get_folder_by_name("oldones"));
          echo "record id " . $val['id'] ." shouldn't be in contest. Moved to \"oldones\".<br />";
          $i++;
        }
      }
      else // gere les oldones
      {
        if(is_a_best_record($val, $best, "time") && $val['isbest']==0)
        {
          $rec->SetIsBest(1);
          $rec->Update(true);
          $rec->Move(get_folder_by_name("contest"));
          echo "record id " . $val['id'] ." is a best time. Moved to contest and set.<br />";
          $i++;
        }
        else if(is_a_best_record($val, $best, "time") && $val['isbest']==1)
        {
          $rec->SetIsBest(1);
          $rec->Update(true);
          $rec->Move(get_folder_by_name("contest"));
          echo "record id " . $val['id'] ." shouldn't be in oldones. Moved to contest.<br />";
          $i++;
        }
        else if(!is_a_best_record($val, $best, "time") && $val['isbest']==1)
        {
          $rec->SetIsBest(0);
          $rec->Update(true);
          echo "record id " . $val['id'] ." is NOT the \"best time\". Unset.<br />";
          $i++;
        }
      }
    }
    echo $i ." records modified.";

    /* get most coins records value */
    echo "<h2>most coins </h2>";
    $best = $this->db->helper->GetBestRecord(get_type_by_name("most coins"));

    /* browse all most coins */
    $this->db->NewQuery("SELECT", "rec");
    $this->db->Where("type", get_type_by_name("most coins"));
    //$this->db->Where("folder", get_folder_by_name("contest"));
    $this->db->AppendCustom("AND (folder=".get_folder_by_name("contest")." OR folder=".get_folder_by_name("oldones").")");
    $res = $this->db->Query();
    if(!$res)
      gui_button_error($this->db->GetError(), 500);

    $i=0;
    while ($val = $this->db->FetchArray($res))
    {
      $rec->LoadFromId($val['id']);
      /* set 'isbest' field */
      if ($val['folder'] == get_folder_by_name("contest"))
      {
        if(is_a_best_record($val, $best, "coins") && $val['isbest']==0)
        {
          $rec->SetIsBest(1);
          $rec->Update(true);
          echo "record id " . $val['id'] ." is a most coins.<br />";
          $i++;
        }
        else if(!is_a_best_record($val, $best, "coins") && $val['isbest']==1)
        {
          $rec->SetIsBest(0);
          $rec->Update(true);
          $rec->Move(get_folder_by_name("oldones"));
          echo "record id " . $val['id'] ." is NOT the best \"most coins\". Moved to \"oldones\".<br />";
          $i++;
        }
        else if(!is_a_best_record($val, $best, "coins") &&  $val['isbest']==0)
        {
          $rec->Move(get_folder_by_name("oldones"));
          echo "record id " . $val['id'] ." shouldn't be in contest. Moved to \"oldones\".<br />";
          $i++;
        }
      }
      else // gere les oldones
      {
        if(is_a_best_record($val, $best, "coins") && $val['isbest']==0)
        {
          $rec->SetIsBest(1);
          $rec->Update(true);
          $rec->Move(get_folder_by_name("contest"));
          echo "record id " . $val['id'] ." is a most coins. Moved to contest and set.<br />";
          $i++;
        }
        else if(is_a_best_record($val, $best, "coins") && $val['isbest']==1)
        {
          $rec->SetIsBest(1);
          $rec->Update(true);
          $rec->Move(get_folder_by_name("contest"));
          echo "record id " . $val['id'] ." shouldn't be in oldones. Moved to contest.<br />";
          $i++;
        }
        else if(!is_a_best_record($val, $best, "coins") && $val['isbest']==1)
        {
          $rec->SetIsBest(0);
          $rec->Update(true);
          echo "record id " . $val['id'] ." is NOT the \"most coins\". Unset.<br />";
          $i++;
        }
      }
    }
    echo $i ." records modified.";
	
    /* Parcours tous pour les stats */
    $this->db->NewQuery("SELECT", "rec");
    $res = $this->db->Query();
    if(!$res)
      gui_button_error($this->db->GetError(), 500);

    $i=0;
    while ($val = $this->db->FetchArray($res))
    { 
      $rec->LoadFromId($val['id']);
      /* remet ‡ jour les statistiques du record */
      $count = $this->db->helper->CountComments($rec->GetId());
      $rec->SetFields(array("comments_count" => $count));
      $rec->Update(true);
    }

    /*  recalcule les statistiques utilisateurs */
    echo "<h2>user stats</h2>\n";
    
    $this->db->NewQuery("SELECT", "users");
    $res = $this->db->Query();
    if(!$res)
    {
      gui_button_error($db->GetError(), 500);
      return;
    }

    $u = new User($this->db);

    while ($val = $this->db->FetchArray($res))
    {
      $u->LoadFromId($val['id']);

      $current_stats = array ($u->GetTotalRecords(), $u->GetBestRecords(), $u->GetComments());

      $u->_RecountTotalRecords();
      $u->_RecountBestRecords();
      $u->_RecountComments();
      
      $new_stats = array ($u->GetTotalRecords(), $u->GetBestRecords(), $u->GetComments());
      $comp = array_diff_assoc( $current_stats, $new_stats );
      if (!empty($comp))
      {
        echo "user #".$val['id']." (".$val['pseudo'].") is not uptodate, it's fixed now. Total: ".
        $current_stats[0]. " -> ". $new_stats[0] . " ~ Best: " . 
        $current_stats[1]. " -> ". $new_stats[1] . " ~ Comments :" .
        $current_stats[2]. " -> ". $new_stats[2] . " <br/>\n";
      }
    }
  }
}

