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
    global $config;
  
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
      button_error($this->db->GetError(), 500);
      exit;
    }

    /* Chargement de la configuration dans la variable globale */
    $this->db->RequestInit("SELECT", "conf");
    if(!$this->db->Query())
    {
      button_error($this->db->GetError(), 500);
      exit;
      return false;
    }
    while ($val = $this->db->FetchArray())
      $config[$val['conf_name']] = $val['conf_value'];
    
    /* Chargement des objets */
    $this->style = new Style();
    $auth = new Auth($this->db);
    $this->current_user = new User($this->db);
    
    /* Initialisation de la session */

    $auth->SessionBegin();

    if (Auth::Check(get_userlevel_by_name("member")))
    {
        $this->current_user->LoadFromId($_SESSION['user_id']);
        $this->current_user->LoadOptions();
        $this->style->Select($this->current_user->GetTheme());
    }
    else
    {
      $this->AddOnlineGuest();
    }

    /* Compte et gère les utilisateur en ligne */
    $this->UpdateOnlineUsers();
    
    if ($mode=="DialogStandard" || $mode=="DialogAdmin")
      $this->dialog = new $mode($this->db, $this->style);
    else if ($mode == "null")
      $this->dialog = null;
    else
      $this->dialog = new DialogStandard($this->db, $this->style);

    $this->mode = $mode;
  }

  function Close()
  {
    $this->db->Disconnect();
  }
  
  function Show(&$args)
  {
    global $config;

    /* Affichage supplémentaire dans le cas de l'affichage d'un seul niveau */
    if (isset($args['level_f']) && isset($args['levelset_f'])
       && ($args['level_f'] > 0) && ($args['levelset_f'] > 0)
       && ($args['diffview'] == "off") && $this->mode == "DialogStandard")
    {
      $mode_level = true;
    }

    /* Récupération de l'option utilisateur de l'ordre de tri par défaut */
    if (empty($args['sort']) && !Auth::Check(get_userlevel_by_name("member")))
      $sort = "old";
    else if (empty($args['sort']) && Auth::Check(get_userlevel_by_name("member")))
      $sort = $this->current_user->GetSort();
    else
      $sort = $args['sort'];

    /* Affichage normal */
    /* ---------------- */
    if (!$mode_level)
    {
        /* COMPTAGE */
        /* gestion du numéro de page et de l'offset */
        $off = ($args['page']-1) * $config['limit'];

        /* hack pour faire un count en utilisant tous les filtres de base */
        $this->db->RequestInit("SELECT", "rec", "COUNT(id)");
        $this->db->RequestGenericFilter($args['filter'], $args['filterval']);
        $this->db->RequestFilterLevels($args['levelset_f'], $args['level_f']);
        $this->db->RequestFilterType($args['type']);
        $this->db->RequestFilterNew($args['newonly']);
        $this->db->RequestFilterFolder($args['folder']);
        $result0 =   $this->db->Query();
        if(!$result0)
            echo button_error(  $this->db->GetError(), 500);

        $res = $this->db->FetchArray();
        $total = $res['COUNT(id)'];
        /* FIN COMPTAGE */
        
        /* requête avec tous les champs mais limitée à "limit" */
        $p = $config['bdd_prefix'];
        $this->db->RequestSelectInit(
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
              $p."rec.comments_count AS comments_count",
              $p."rec.user_id AS user_id",
              $p."users.pseudo AS pseudo",
              $p."sets.set_name AS set_name",
              $p."sets.set_path AS set_path",
              $p."maps.map_solfile AS map_solfile",
            )
            );
        $this->db->RequestGenericFilter(
            array($p."rec.user_id", $p."rec.levelset", $p."rec.levelset", $p."rec.level"),
            array($p."users.id", $p."sets.id", $p."maps.set_id", $p."maps.level_num"),
            "AND", false
        );
        
        $this->db->RequestGenericFilter($args['filter'], $args['filterval']);
        $this->db->RequestFilterLevels($args['levelset_f'], $args['level_f']);
        $this->db->RequestFilterType($args['type']);
        $this->db->RequestFilterNew($args['newonly']);
        $this->db->RequestFilterFolder($args['folder']);
        
        if($args['bestonly'] == "on")
          $this->db->RequestGenericFilter("isbest", 1);
        
        /* dans le cas du diffview, on trie par pieces, ou par temps */
        if($args['diffview'] == "on")
        {
          /* diff impossible pour type "tous" et "freestyle" */
          if ($args['type'] == get_type_by_name("all") || $args['type'] == get_type_by_name("freestyle"))
          {
            button_error("can't select diff view with type : \"".get_type_by_number($args['type'])."\"", 400);
            $args['diffview'] = "off";
          }
          /* choix automatique de l'ordre de tri */
          if ($args['type'] == get_type_by_name("best time") )
            $sort="time";
          else if ($args['type'] == get_type_by_name("most coins") )
            $sort="coins";

          /* petit hack pas joli joli pour faire sauter la limite du nombre de record, dans le cas du diff */
          $config['limit'] = 255;
        }

        $this->db->RequestSort($sort);
        $this->db->RequestLimit($config['limit'], $off);
        //echo $this->db->GetRequestString();
        $result1 =   $this->db->Query();
        if(!$result1)
          echo button_error(  $this->db->GetError(), 500);
    }

    /* Mode fiche de niveau */
    /* -------------------- */
    else 
    {
        /* requête pour les records du contest */
        $p = $config['bdd_prefix'];
        $this->db->RequestSelectInit(
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
              $p."rec.comments_count AS comments_count",
              $p."rec.user_id AS user_id",
              $p."users.pseudo AS pseudo",
              $p."sets.set_name AS set_name",
              $p."sets.set_path AS set_path",
              $p."maps.map_solfile AS map_solfile",
            )
            );
        $this->db->RequestGenericFilter(
            array($p."rec.user_id", $p."rec.levelset", $p."rec.levelset", $p."rec.level"),
            array($p."users.id", $p."sets.id", $p."maps.set_id", $p."maps.level_num"),
            "AND", false
        );
        
        $this->db->RequestGenericFilter($args['filter'], $args['filterval']);
        $this->db->RequestFilterLevels($args['levelset_f'], $args['level_f']);
        $this->db->RequestGenericFilter("folder", get_folder_by_name("contest"));
        $this->db->RequestSort($sort);
        $result1 = $this->db->Query();
        if(!$result1)
          echo button_error(  $this->db->GetError(), 500);
        $total1 = $this->db->NumRows();
          
        /* requête pour les records anciens */
        $this->db->RequestSelectInit(
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
              $p."rec.comments_count AS comments_count",
              $p."rec.user_id AS user_id",
              $p."users.pseudo AS pseudo",
              $p."sets.set_name AS set_name",
              $p."sets.set_path AS set_path",
              $p."maps.map_solfile AS map_solfile",
            )
            );
        $this->db->RequestGenericFilter(
            array($p."rec.user_id", $p."rec.levelset", $p."rec.levelset", $p."rec.level"),
            array($p."users.id", $p."sets.id", $p."maps.set_id", $p."maps.level_num"),
            "AND", false
        );
        
        $this->db->RequestGenericFilter($args['filter'], $args['filterval']);
        $this->db->RequestFilterLevels($args['levelset_f'], $args['level_f']);
        $this->db->RequestGenericFilter("folder", get_folder_by_name("oldones"));
        $this->db->RequestSort($sort);
        $result2 = $this->db->Query();
        if(!$result2)
          echo button_error(  $this->db->GetError(), 500);
        $total2 = $this->db->NumRows();
    }

    $this->PrintSpeech();
    $this->PrintTypeForm($args);
    if (!$mode_level)
    {
      $this->dialog->NavBar($args['page'], $config['limit'], $total);
      $diff = $args['diffview']=="on" ? true : false;
      $this->dialog->Table($result1, $diff, $total);
    }
    else
    {
      $this->dialog->Level($result1, $result2, $args, $total1, $total2);
    }

    $this->dialog->SideBar( array("registered" => $this->online_users_registered,
                                  "guests"     => $this->online_users_guest,
                                  "list"       => $this->online_users_list)
                          );
    if (!$mode_level)
        $this->dialog->NavBar($args['page'], $config['limit'], $total);
  }

  function Post($replay_id, $content_memory="")
  {
    global $nextargs, $config;
    $nextargs = "record.php?to=addcomment";

    /* test si le record existe */
    $results = $this->db->RequestMatchRecords(array("id" => $replay_id));
    if ($results['nb']>0)
    {
      $p = $config['bdd_prefix'];
      $this->db->RequestSelectInit(
            array("com", "users"),
            array(
                $p."com.id AS id",
                $p."com.replay_id AS replay_id",
                $p."com.user_id AS user_id",
                $p."com.content AS content",
                $p."com.timestamp AS timestamp",
                $p."users.pseudo AS user_pseudo",
                $p."users.user_avatar AS user_avatar",
                ),
            "SELECT", "com");
      $this->db->RequestGenericFilter($p."com.user_id", $p."users.id", "AND", false);
      $this->db->RequestGenericFilter($p."com.replay_id", $replay_id);
      $this->db->RequestGenericSort(array($p."com.timestamp"), "ASC");
      $results = $this->db->Query();
      $this->PrintRecordById($replay_id, true); // with link
      $this->dialog->Comments($results, $config['date_format']);
      if (!Auth::Check(get_userlevel_by_name("member")))
      {
        button("Please <a href=\"register.php\">register</a>  or <a href=\"login.php?ready\">log in</a> to post comments !", 400);
        $_SESSION['redirect'] = "?link=".$replay_id;
      }
      else
        $this->dialog->CommentForm($replay_id, $content_memory);
    }
    else
    {
      button_error("Record doesn't exist !", 400);
    }

  }

  /*__DIALOG WRAPPERS__*/
    
  function PrintHtmlHead($title, $special="")  {
      $this->dialog->HtmlHead($title, $special);
  }
  function PrintTop()  {
    $this->dialog->Top();
  }
  function PrintPrelude()  {
    $this->dialog->Prelude();
  }
  function PrintSpeech()  {
    $this->dialog->Speech();
  }
  function PrintFooter()  {
      global $config;
      $this->dialog->Footer($config['version'], $this->getProcessTime());
  }
  function PrintRecordById($id, $with_link=false)  {
    $rec = new Record($this->db);
    if($rec->LoadFromId($id))
      $this->dialog->Record($rec->GetFields());
    if($with_link)
      $this->PrintRecordLink($rec->GetFields());
  }
  function PrintRecordLink($record_fields)  {
      $this->dialog->RecordLink($record_fields);
  }
  function PrintRecordByFields($fields)  {
    $this->dialog->Record($fields);
  }
  function PrintTypeForm($args)  {
      $this->dialog->TypeForm($args);
  }
  function PrintUploadForm()  {
      global $config;
      $this->dialog->UploadForm($config['upload_size_max']);
  }
  function PrintCommentForm($replay_id, $content_memory, $user_id=-1)  {
      $this->dialog->CommentForm($replay_id, $content_memory, $user_id);
  }
  function PrintAddFormAuto() {
      $this->dialog->AddFormAuto();
  }
  function PrintMemberList($args) {
    $this->db->RequestInit("SELECT", "users");
    switch($args['sort'])
    {
     case "pseudo": $this->db->RequestGenericSort(array("pseudo"), "ASC"); break;
     case "records": $this->db->RequestGenericSort(array("stat_total_records", "stat_best_records"), array("DESC", "DESC"));
         break;
     default: 
     case "best": $this->db->RequestGenericSort(array("stat_best_records", "stat_total_records"), array("DESC", "DESC"));
         break;
     case "comments": $this->db->RequestGenericSort(array("stat_comments"), "DESC"); break;
     case "cat": $this->db->RequestGenericSort(array("level"), "ASC"); break;
     case "id": $this->db->RequestGenericSort(array("id"), "ASC"); break;
    }
    $results1 = $this->db->Query();
    if(!$results1) {
        echo button_error(  $this->db->GetError(), 400);
    }
    else
    {
      $this->dialog->MemberList($results1);
    }
  }
  function PrintProfile() {
      $this->dialog->UserProfile($this->current_user);
  }
  function ViewProfile($id) {
      $tmp_user = new User($this->db);
      if ($tmp_user->LoadFromId($id))
        $this->dialog->UserProfile($tmp_user);
      else
        button_error($tmp_user->GetError(), 400);
  }
  
  /*__UTILS__*/

  /* Record RSS */
  function GetEarlierDate()
  {
    $this->db->RequestInit("SELECT", "rec");
    $this->db->RequestSort("old");
    $this->db->RequestLimit(1, 0);
    $res = $this->db->Query();

    $val = $this->db->FetchArray();
    return GetDateFromTimestamp($val['timestamp']);
  }
  
  function GetLastRecords($order, $folder="contest")
  {
    global $config;

    $p = $config['bdd_prefix'];
    $this->db->RequestSelectInit(
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
     $this->db->RequestGenericFilter(
        array($p."rec.user_id", $p."rec.levelset"),
        array($p."users.id", $p."sets.id"),
        "AND", false
    );
        
    $this->db->RequestGenericFilter("folder", get_folder_by_name($folder));
    $this->db->RequestSort("old");
    $this->db->RequestLimit($order, 0);
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
    $this->db->RequestInit("SELECT", "com");
    $this->db->RequestSort("old");
    $this->db->RequestLimit(1, 0);
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
    /* on ne garde que 3 décimales */
    return substr($this->process_time,0,5);
  }

  /*__FONCTION DE GESTION__*/

  function ManageBestRecords($updated_record_fields, $type)
  {
    $val = $updated_record_fields;
    $ret = $this->db->RequestSetBestRecordByFields($val['level'], $val['levelset'], $type);
    switch ($val['type'])
    {
     case get_type_by_name("best time") : $critera = "time"; $check=true; break;
     case get_type_by_name("most coins"): $critera = "coins";$check=true; break;
     default : $check=false; $ret['isbest']=false; break;
    }
    if($check)
    {
      $best= $this->db->RequestGetBestRecord($val['type'], get_folder_by_name("contest"));
      $ret['isbest'] = is_a_best_record($val, $best, $critera);
    }
    return $ret;
  }

  /* Appelé quand un utilisateur se loggue */
  function AddOnlineUser()
  {
    /* on enlève l'utilisateur de la liste des guests */
    $this->db->RequestInit("DELETE", "online");
    $this->db->RequestGenericFilter("user_id", 0);
    $this->db->RequestGenericFilter("ident", $_SERVER['REMOTE_ADDR']);
    $this->db->RequestLimit(1);
    if(!$this->db->Query()) {
      button_error("RemoveOnlineUser::".$this->db->GetError(), 300);
      return false;
    }

    /* Ajout de l'utilisateur loggué */
    /* Fait automatiquement par  UpdateOnlineUsers() */
    return true;
  }

  /* Ajout d'un utilisateur loggué dans la liste des online */
  function UpdateLoggedOnlineUser()
  {
    /* recherche de l'utilisateur déjà présent dans la liste */
    $this->db->RequestInit("SELECT", "online", "COUNT(user_id)");
    $this->db->RequestGenericFilter("user_id", $_SESSION['user_id']);
    $this->db->RequestGenericFilter("ident", $_SESSION['user_pseudo']);
    if(!$this->db->Query()) {
      button_error("RemoveOnlineUser::".$this->db->GetError(), 300);
      return false;
    }
    $res = $this->db->FetchArray();
    if ($res['COUNT(user_id)'] > 0) /* déjà présent */
    {
      $this->db->RequestInit("UPDATE", "online");
      $this->db->RequestUpdateSet(array("logged_time" => $this->start_time));
      $this->db->RequestGenericFilter("user_id", $_SESSION['user_id']);
      if(!$this->db->Query()) {
        button_error("RemoveOnlineUser::".$this->db->GetError(), 300);
        return false;
      }
    }
    else
    {
      $this->db->RequestInit("INSERT", "online");
      $this->db->RequestInsert(array(
                          "user_id"     => $_SESSION['user_id'],
                          "ident"       => $_SESSION['user_pseudo'],
                          "logged_time" => $this->start_time,
                           ));
      if(!$this->db->Query()) {
        button_error("RemoveOnlineUser::".$this->db->GetError(), 300);
        return false;
      }
    }
  }

  /* Appelé quand un utilisateur non loggué arrive */
  function AddOnlineGuest()
  {
    /* recherche de l'utilisateur déjà présent dans la liste */
    $this->db->RequestInit("SELECT", "online", "COUNT(user_id)");
    $this->db->RequestGenericFilter("user_id", $_SESSION['user_id']);
    $this->db->RequestGenericFilter("ident", $_SERVER['REMOTE_ADDR']);
    if(!$this->db->Query()) {
      button_error("RemoveOnlineUser::".$this->db->GetError(), 300);
      return false;
    }
    $res = $this->db->FetchArray();
    if ($res['COUNT(user_id)'] > 0) /* déjà présent */
    {
      $this->db->RequestInit("UPDATE", "online");
      $this->db->RequestUpdateSet(array("logged_time" => $this->start_time));
      $this->db->RequestGenericFilter("ident", $_SERVER['REMOTE_ADDR'] );
      if(!$res = $this->db->Query()) {
        button_error("RemoveOnlineUser::".$this->db->GetError(), 300);
        return false;
      }
    }
    else /* ajoute */
    {
      $this->db->RequestInit("INSERT", "online");
      $this->db->RequestInsert(array(
                          "user_id"     => $_SESSION['user_id'],
                          "ident"       => $_SERVER['REMOTE_ADDR'],
                          "logged_time" => $this->start_time,
                           ));
      if(!$res = $this->db->Query()) {
        button_error("RemoveOnlineUser::".$this->db->GetError(), 300);
        return false;
      }
    }
  }

  /* Appelé quand un utilisateur se déloggue */
  function RemoveOnlineUser()
  {
    $this->db->RequestInit("DELETE", "online");
    $this->db->RequestGenericFilter("user_id", $_SESSION['user_id']);
    $this->db->RequestLimit(1);
    if(!$this->db->Query()) {
      button_error("RemoveOnlineUser::".$this->db->GetError(), 300);
      return false;
    }
   return true;
  }

  function UpdateOnlineUsers()
  {
    global $config;
    /* Mettre à jour l'utilisateur */
    if ($_SESSION['user_logged'] == true)
    {
      $this->UpdateLoggedOnlineUser();
    }

    /* effacer les utilisateurs 'idle' */
    $this->db->RequestInit("DELETE", "online");
    $this->db->RequestGenericFilter_lt("logged_time", $this->start_time-$config['online_idletime']);
    if(!$this->db->Query()) {
      button_error("UpdateOnlineUser::" . $this->db->GetError(), 300);
      return false;
    }
    
    /* récupère la liste */
    $this->db->RequestInit("SELECT", "online");
    if(!$this->db->Query()) {
      button_error("UpdateOnlineUser::" . $this->db->GetError(), 300);
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
  
  /*__FONCTIONS DE MAINTENANCE__*/

  function CheckDatabase()
  {
    global $folders, $config;

    $replay_path = ROOT_PATH.$config['replay_dir'];
    $dir_list = array();
    $f = new FileManager();

    /* listing de tous les repertoires */
    foreach ($folders as $nb => $value)
    {
      $dir = $replay_path.$folders[$nb]["name"];
      $res = $f->DirList($dir);
      if(!$res)
      {
          button_error($f->GetError(), 500);
          return;
      }
      $dir_list[$folders[$nb]["name"]] = $res["files"] ;
      if (!$dir_list)
      {
        button_error("Error opening \"".$dir."\" directory.",500);
        return;
      }
    }

    /* base query : tous les records */
    $fields = array("replay", "id", "folder");
    $this->db->RequestInit("SELECT", "rec");
    $res = $this->db->Query();
    if(!$res)
    {
      button_error($db->GetError(), 500);
      return;
    }

    echo "<div class=\"results\" style=\"width: 100%;\">\n";
    echo "<table>\n";
    echo "<caption>Databse Check Results</caption>\n";
    echo "<tr><th>replay_id</th><th>replay_file</th><th>folder</th><th>status</th></tr>\n";
 
    $dup_arr = array(); $dup=1;
    $dir_list_found = array();
    $count = array();
    foreach ($folders as $nb => $value)
    {
      $count[$folders['nb']["name"]] = 0;
    }
    while ($val = $this->db->FetchArray())
    {
      /* Recherche du replay */
      if (!empty($val['replay']))  
      {
        $folder_name = get_folder_by_number($val['folder']);
        if (array_search($folder_name."/".$val['replay'], $dup_arr, true))
          $status = "<span style=\"color: orange;\">Duplicate</span>";
        else
        {
          if (file_exists($replay_path.$folder_name."/".$val['replay']))
          {
            $status = "<span style=\"color: green;\">OK</span>";
            /* test les fichiers dupliques, 2 records pointent vers le meme fichier */
            $dup_arr[$dup] =  $folder_name."/".$val['replay'];
            $dup++;
            /* ajoute le fichier a la liste des fichiers trouves */
            $dir_list_found[$folder_name][$count[$folder_name]] = $val['replay'];
            $count[$folder_name]++;
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
    
      echo "<tr><td><a href=\"admin.php?filter=id&amp;filterval=".$val['id']."&amp;folder=".$val['folder']."\">".$val['id']."</a></td>";
      echo "<td><a href=\"filexplorer.php?grep=".$val['replay']."\">".$val['replay']."</a></td>";
      echo "<td>".get_folder_by_number($val['folder'])."</td>";
      echo  "<td>".$status."</td></tr>\n";
    }
    echo "</table>\n";
    echo "</div>\n";

    /* Affichage des fichiers orphelins, utilises par aucun record */
    foreach ($dir_list as $key => $value)
    {
      $folder_name = $key;
      echo "<div class=\"results\" style=\"width: 100%;\">\n";
      echo "<table>\n";
      echo "<tr><th>files uploaded, but not used in ".$folder_name."</th></tr>\n";

      /* des fichiers non-orphelins ont été trouvés, affichage des différences */
      if (!empty($dir_list_found[$key]))
        $orphans = array_diff($value, $dir_list_found[$key]);
      /* ce ne sont que des fichiers orphelins, si il y en a */
      else
        $orphans = $dir_list[$key];

      while($f=array_pop($orphans))
      echo "<tr><td><a href=\"filexplorer.php?grep=".$f."\">".$f."</a></td></tr>\n";
      echo "</table>\n";
      echo "</div>\n";
    }

  }

  function CheckAllRecords()
  {
    $rec = new Record($this->db);

    /* clean up */    
    $this->db->RequestInit("UPDATE", "rec");
    $my  = array("isbest" => "0");
    $this->db->RequestUpdateSet($my, true);
    $this->db->RequestGenericFilter("type", get_type_by_name("freestyle"));
    $res = $this->db->Query();
    if(!$res)
      button_error($this->db->GetError(), 500);

    /* get best time records value */
    echo "<h2>best time </h2>";
    $best = $this->db->RequestGetBestRecord(get_type_by_name("best time"));

    /* browse all best time */
    $this->db->RequestInit("SELECT", "rec");
    $this->db->RequestGenericFilter("type", get_type_by_name("best time"));
    //$this->db->RequestGenericFilter("folder", get_folder_by_name("contest"));
    $this->db->RequestCustom("AND (folder=".get_folder_by_name("contest")." OR folder=".get_folder_by_name("oldones").")");
    $res = $this->db->Query();
    if(!$res)
      button_error($this->db->GetError(), 500);

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
          $rec->Update($true);
          echo "record id " . $val['id'] ." is a best time. Set.<br />";
          $i++;
        }
        else if(!is_a_best_record($val, $best, "time") && $val['isbest']==1)
        {
          $rec->SetIsBest(0);
          $rec->Update($true);
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
          $rec->Update($true);
          $rec->Move(get_folder_by_name("contest"));
          echo "record id " . $val['id'] ." is a best time. Moved to contest and set.<br />";
          $i++;
        }
        else if(is_a_best_record($val, $best, "time") && $val['isbest']==1)
        {
          $rec->SetIsBest(1);
          $rec->Update($true);
          $rec->Move(get_folder_by_name("contest"));
          echo "record id " . $val['id'] ." shouldn't be in oldones. Moved to contest.<br />";
          $i++;
        }
        else if(!is_a_best_record($val, $best, "time") && $val['isbest']==1)
        {
          $rec->SetIsBest(0);
          $rec->Update($true);
          echo "record id " . $val['id'] ." is NOT the \"best time\". Unset.<br />";
          $i++;
        }
      }
    }
    echo $i ." records modified.";

    /* get most coins records value */
    echo "<h2>most coins </h2>";
    $best = $this->db->RequestGetBestRecord(get_type_by_name("most coins"));

    /* browse all most coins */
    $this->db->RequestInit("SELECT", "rec");
    $this->db->RequestGenericFilter("type", get_type_by_name("most coins"));
    //$this->db->RequestGenericFilter("folder", get_folder_by_name("contest"));
    $this->db->RequestCustom("AND (folder=".get_folder_by_name("contest")." OR folder=".get_folder_by_name("oldones").")");
    $res = $this->db->Query();
    if(!$res)
      button_error($this->db->GetError(), 500);

    $i=0;
    while ($val = $this->db->FetchArray($res))
    {
      $rec->LoadFromId($val['id']);
      /* set 'isbest' field */
      if ($val['folder'] == get_folder_by_name("contest"))
      {
          //echo "is_a_best_record($val, $best, \"coins\") = " .is_a_best_record($val, $best, "coins") . "<br/>";
          //echo "val = "; print_r($val); echo "<br/>";
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
          $rec->Update($true);
          $rec->Move(get_folder_by_name("contest"));
          echo "record id " . $val['id'] ." is a most coins. Moved to contest and set.<br />";
          $i++;
        }
        else if(is_a_best_record($val, $best, "coins") && $val['isbest']==1)
        {
          $rec->SetIsBest(1);
          $rec->Update($true);
          $rec->Move(get_folder_by_name("contest"));
          echo "record id " . $val['id'] ." shouldn't be in oldones. Moved to contest.<br />";
          $i++;
        }
        else if(!is_a_best_record($val, $best, "coins") && $val['isbest']==1)
        {
          $rec->SetIsBest(0);
          $rec->Update($true);
          echo "record id " . $val['id'] ." is NOT the \"most coins\". Unset.<br />";
          $i++;
        }
      }
    }
    echo $i ." records modified.";


    /*  recalcule les statistiques utilisateurs */
    echo "<h2>user stats</h2>\n";
    
    $this->db->RequestInit("SELECT", "users");
    $res = $this->db->Query();
    if(!$res)
    {
      button_error($db->GetError(), 500);
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

