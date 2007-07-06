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
	
define('ROOT_PATH', "../");
include_once ROOT_PATH."config.inc.php";
include_once ROOT_PATH."includes/common.php";
include_once ROOT_PATH."includes/classes.php";
include_once ROOT_PATH."classes/class.dialog_admin.php";

//args process
$args = get_arguments($_POST, $_GET);

if (isset($args['folder']))
   $args['folder'] = (integer)$args['folder'];
$args['sort'] = (integer)$args['sort'];
if (isset($args['folder']))
   $args['type'] = (integer)$args['type'];

// default value for known args
if(!isset($args['filter']))
   $args['filter']='none';
if(isset($args['filter']) && !isset($args['filterval']))
   $args['filter']='none';
if(!isset($args['type']))
   $args['type'] = get_type_by_name("all");
if(!isset($args['diffview']))
   $args['diffview'] = "off";
if(!isset($args['bestonly']))
   $args['bestonly'] = "off";
if(!isset($args['newonly']))
   $args['newonly'] = get_newonly_by_name("off");
if(!isset($args['levelset_f']))
   $args['levelset_f'] = 0;     // all by default
if(!isset($args['level_f']))
   $args['level_f'] = 0;        // all by default
if(!isset($args['folder']))
   $args['folder'] = get_folder_by_name("contest");
if(!isset($args['overwrite']))
   $args['overwrite'] = 'off';
 
$table = new Nvrtbl("DialogAdmin");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>

<?php $table->dialog->Head("Nevertable - Neverball Hall of Fame", $special); ?>

<body>
<div id="page">
<?php   $table->dialog->Top();  ?>
<div id="main">
<?php

function closepage()
{   global $table;
    gui_button_main_page_admin();
    echo "</div><!-- fin \"main\" -->\n";
    $table->Close();
    $table->dialog->Footer();
    echo "</div><!-- fin \"page\" -->\n</body>\n</html>\n";
    exit;
}

if (!Auth::Check(get_userlevel_by_name('admin')))
{
    gui_button_error($lang['NOT_ADMIN'],500);
    closepage();
}
$table->dialog->Prelude();

if (isset($args['link']))
{
   $rec = new Record($table->db);
   $ret = $rec->LoadFromId($args['link']);
   if (!$ret)
   {
	   gui_button_error($rec->GetError(), 400);
	   closepage();
   }
   $table->dialog->Record($rec->GetFields());
}

/***************************************************/
/* ------------------- MOVE   ---------------------*/
/***************************************************/

/*__TRASH__*/
if (isset($args['rectrash']))
{
  $id=$args['id'];

  $rec = new Record($table->db);
  $ret = $rec->LoadFromId($id);
  if (!$ret)
  {
	  gui_button_error($rec->GetError(), 300);
	  closepage();
  }

 /* garde en mémoire l'état du record avant déplacement */
  $wasbest = $rec->IsBest();
  if (!$rec->Move(get_folder_by_name("trash")))
  {
	  gui_button_error($rec->GetError(), 300);
	  closepage();
  }

  /* gestion des records, qqsoit le résultat, des erreurs de $rec->Move étant */
  /* non critiques, il peut y avoir modification quand même */
  if($rec->GetType()!=get_type_by_name("freestyle"))
  {
      /* Gestion */
      $ret = $table->ManageBestRecords($rec->GetFields(), $rec->GetType());
      if ($wasbest)
      {
        gui_button("This was a best record...", 400);
        gui_button($ret['nb']."&nbsp;record(s) are best records for this level/levelset/type now.", 500);
        gui_button($ret['imports']."&nbsp;record(s) imported from \"oldones\".", 500);
      }
  }
  gui_button("record trashed : ", 200);
  $table->dialog->Record($rec->GetFields());

  gui_button("<a href=\"admin.php?folder=".get_folder_by_name("trash")."\">"."Go to trash folder"."</a>", 200);

  closepage();
}

/*__PURGE__*/
if (isset($args['recpurge']))
{
  $id=$args['id'];

  $rec = new Record($table->db);
  $ret = $rec->LoadFromId($id);
  if (!$ret)
  {
	  gui_button_error($rec->GetError(), 300);
	  closepage();
  }
 
  /* effacement de l'enregistrement de la bdd, plus le fichier */
  $ret = $rec->Purge(true);
  if (!$ret)
  {
	  gui_button_error($rec->GetError(), 300);
	  closepage();
  }
  gui_button("record deleted ", 200);

  closepage();
}

/*__CONTEST__*/
if (isset($args['repcontest']))
{
  $id=$args['id'];
  $overwrite=$args['overwrite'];

  $rec = new Record($table->db);
  $ret = $rec->LoadFromId($id);
  if (!$ret)
  {
	  gui_button_error($rec->GetError(), 300);
	  closepage();
  }
  /* Si le mode remplacement est activé ... */
  if ($overwrite == "on")
  {
      /* ... On cherche les records équivalents */
      $comp_array = array(
          "user_id" => $rec->GetUserId(),
          "level" => $rec->GetLevel(),
          "levelset" => $rec->GetSet(),
          "type" => $rec->GetType(),
          "folder" => get_folder_by_name("contest"),
          );
      $matches = $table->db->helper->SelectRecords($comp_array);
      $merge=false;
      if ($matches['nb'] > 1)
      {
        gui_button_error("More than one record matches, unusual situation, aborting.", 500);
        gui_button_error("Using normal injection instead...", 500);
        $merge=false;
      }
      else if ($matches['nb'] == 1)
        $merge=true;
      
      if ($merge)
      {
        gui_button("One record matches, updating this one....", 500);
        /* Fusion avec la cible */
        $ret = $rec->Merge($matches[0]['id']);
	if(!$ret)
	{
		gui_button_error($rec->GetError(), 500);
		closepage();
	}
        gui_button("Record updated.", 200);
      }
      else
      {
        gui_button("No record already matches in contest, so inserting new one.", 400);
        /* Deplacement */
        $ret = $rec->Move(get_folder_by_name("contest"));
	if (!$ret)
	{
		button_error($rec->GetError(), 300);
		closepage();
	}
      }
  } /* fin overwrite == on */

  else
  {
    /* Deplacement */
    $ret = $rec->Move(get_folder_by_name("contest"));
    if (!$ret)
    {
	gui_button_error($rec->GetError(), 300);
    }
  
    /* gestion des records, qqsoit le résultat, des erreurs de $rec->Move étant */
    /* non critiques, il peut y avoir modification quand même */
    if($rec->GetType()!=get_type_by_name("freestyle"))
    {
        /* Gestion */
        $ret = $table->ManageBestRecords($rec->GetFields(), $rec->GetType());
        if ($ret['isbest'])
        {
          gui_button("This is a new best record !", 400);
          gui_button($ret['nb']."&nbsp;record(s) are best records for this level/levelset/type now.", 500);
          gui_button($ret['beaten']."&nbsp;record(s) are obsolete.", 500);
        }
        else
        {
          gui_button("This record is not the best one ! moved in \"oldones\" !", 500);
        }
    }
    else
    {
        gui_button("Record moved to contest.", 500);
    }
      $table->dialog->Record($rec->GetFields());
  }

  gui_button("<a href=\"admin.php?folder=".get_folder_by_name("contest")."&amp;levelset_f=".$rec->GetSet()."&amp;level_f=".$rec->GetLevel()."\">"."Go to contest"."</a>", 200);
  
  closepage();
}

/***************************************************/
/* ----------------- EDIT A RECORD ----------------*/
/***************************************************/

if (isset($args['edit']))
{
  $args['coins'] = (integer)$args['coins'];
  $args['time']  = (integer)$args['time'];
  $args['type']  = (integer)$args['type'];
  $args['pseudo']  = trim($args['pseudo']);

  // champs obligatoires (coins n'est pas testé il peut être == 0)
  if (empty($args['user_id']) || empty($args['time']))
  {
      gui_button_error("Please fill all fields. Only replay is optional.", 300);
      closepage();
  }
  $rec = new Record($table->db);
  $ret = $rec->LoadFromId($args['id']);
  if (!$ret)
  {
	  gui_button_error($rec->GetError(), 300);
	  closepage();
  }
  $oldtype   = $rec->GetType();
  $oldfields = $rec->GetFields();
  /* un freestyle est forcément dans contest */
  if($args['type']==get_type_by_name("freestyle"))
  {
    if (!$rec->Move(get_folder_by_name("contest")))
    {
       button_error($rec->GetError(), 500);
       closepage();
    }
    $rec->SetIsBest(false);
  }
  
  $fields = array(
	  'levelset' => $args['levelset'],
	  'level'    => $args['level'],
	  'type'     => $args['type'],
	  'time'     => $args['time'],
	  'coins'    => $args['coins'],
	  'replay'   => $args['replay'],
	  );
  $rec->SetFields($fields);
  $table->dialog->Record($rec->GetFields());
  $ret = $rec->Update();
  if(!$ret)
  {
	  button_error($rec->GetError(), 500);
	  closepage();
  }
  /* Gestion des records dans la catégorie "sortante "*/
  $ret1 = $table->ManageBestRecords($oldfields, $oldtype);
  /* Gestion des records dans la catégorie "entrante"*/
  $ret2 = $table->ManageBestRecords($rec->GetFields(), $rec->GetType());
  /* Sommation des mouvements de "best records"*/
  /* Calcul faux !
  unset($ret);
  $ret['nb']      =$ret1['nb']        + $ret2['nb'];
  $ret['beaten']  =$ret1['beaten']    + $ret2['beaten'];
  $ret['imports'] =$ret1['imports']   + $ret2['imports'];
  $ret['isbest']  =$ret1['isbest']    + $ret2['isbest'];
  */
  if ($ret2['isbest'])
  {
     gui_button("This is a new best record !", 400);
     gui_button($ret2['nb']."&nbsp;record(s) are best records for this level/levelset/type now.", 500);
     gui_button($ret2['beaten']."&nbsp;record(s) are obsolete.", 500);
  }
  else
  {
     gui_button("This is not a best record...", 400);
     gui_button($ret2['nb']."&nbsp;record(s) are best records for this level/levelset/type now.", 500);
     gui_button($ret2['imports']."&nbsp;record(s) imported from \"oldones\".", 500);
  }

  gui_button("<a href=\"admin.php?folder=".$args['folder']."&amp;levelset_f=".$rec->GetSet()."&amp;level_f=".$rec->GetLevel()."\">".$lang['GUI_BUTTON_MAINPAGE_ADMIN']."</a>", 200);
  
  closepage();
} 

/***************************************************/
/* ----------- MAINTENANCE------- -----------------*/
/***************************************************/
if (isset($args['check']))
{
   $table->CheckDatabase();
   closepage();
}

if (isset($args['recompute']))
{
   $table->CheckAllRecords();
   closepage();
}

/***************************************************/
/* ----------------- SHOW  ------------------------*/
/***************************************************/

if (!isset($args['link']))
{
  if (isset($args['type'])) $nextargs .= "&amp;type=".$args['type'];
  if (isset($args['sort'])) $nextargs .= "&amp;sort=".$args['sort'];
  if (isset($args['bestonly'])) $nextargs .= "&amp;bestonly=".$args['bestonly'];
  if (isset($args['newonly'])) $nextargs .= "&amp;newonly=".$args['newonly'];
  if (isset($args['filter'])) $nextargs .= "&amp;filter=".$args['filter'];
  if (isset($args['filterval'])) $nextargs .= "&amp;filterval=".$args['filterval'];
  if (isset($args['levelset_f'])) $nextargs .= "&amp;levelset_f=".$args['levelset_f'];
  if (isset($args['level_f'])) $nextargs .= "&amp;level_f=".$args['level_f'];
  if (isset($args['folder'])) $nextargs .= "&amp;folder=".$args['folder'];

  
  /* Récupération de l'option utilisateur de l'ordre de tri par défaut */
  if (empty($args['sort']) && !Auth::Check(get_userlevel_by_name("member")))
    $sort = get_sort_by_name("old");
  else if (empty($args['sort']) && Auth::Check(get_userlevel_by_name("member")))
    $sort = $config['opt_user_sort'];
  else
    $sort = $args['sort'];

      /* COMPTAGE */
      /* gestion du numéro de page et de l'offset */
      $off = ($args['page']-1) * $config['limit'];

      /* hack pour faire un count en utilisant tous les filtres de base */
      $table->db->NewQuery("SELECT", "rec", "COUNT(id)");
      $table->db->Where($args['filter'], $args['filterval']);
      $table->db->helper->LevelsFilter($args['levelset_f'], $args['level_f']);
      $table->db->helper->TypeFilter($args['type']);
      $table->db->helper->NewFilter($args['newonly']);
      $table->db->helper->FolderFilter($args['folder']);
      $result0 =   $table->db->Query();
      if(!$result0)
          gui_button_error(  $table->db->GetError(), 500);

      $res = $table->db->FetchArray();
      $total = $res['COUNT(id)'];
      /* FIN COMPTAGE */
      
      /* requête avec tous les champs mais limitée à "limit" */
      $p = $config['bdd_prefix'];
      $table->db->Select(
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
      $table->db->Where(
          array($p."rec.user_id", $p."rec.levelset", $p."rec.levelset", $p."rec.level"),
          array($p."users.id", $p."sets.id", $p."maps.set_id", $p."maps.level_num"),
          "AND", false
      );
      
      $table->db->Where($args['filter'], $args['filterval']);
      $table->db->helper->LevelsFilter($args['levelset_f'], $args['level_f']);
      $table->db->helper->TypeFilter($args['type']);
      $table->db->helper->NewFilter($args['newonly']);
      $table->db->helper->FolderFilter($args['folder']);
      
      if($args['bestonly'] == "on")
        $table->db->Where("isbest", 1);
      
      /* dans le cas du diffview, on trie par pieces, ou par temps */
      if($args['diffview'] == "on")
      {
        /* diff impossible pour type "tous" et "freestyle" */
        if ($args['type'] == get_type_by_name("all") || $args['type'] == get_type_by_name("freestyle"))
        {
          gui_button_error("can't select diff view with type : \"".get_type_by_number($args['type'])."\"", 400);
          $args['diffview'] = "off";
        }
        /* choix automatique de l'ordre de tri */
        if ($args['type'] == get_type_by_name("best time") )
          $sort=get_sort_by_name("time");
        else if ($args['type'] == get_type_by_name("most coins") )
          $sort=get_sort_by_name("coins");

        /* petit hack pas joli joli pour faire sauter la limite du nombre de record, dans le cas du diff */
        $config['limit'] = 255;
      }

      $table->db->helper->SortFilter($sort);
      $table->db->Limit($config['limit'], $off);
      $result1 =   $table->db->Query();
      if(!$result1)
        gui_button_error(  $table->db->GetError(), 500);

  $table->dialog->TypeForm($args);
  $table->dialog->NavBar($total, $config['limit'], 'admin.php', $nextargs);
  $diff = $args['diffview']=="on" ? true : false;
  $table->dialog->Table($result1, $diff, $total);

 $table->dialog->SideBar( );
 $table->dialog->NavBar($total, $config['limit'], 'admin.php', $nextargs);

} // !isset($args['link'])
$table->dialog->EditForm();
if (isset($args['link']))
    gui_button_main_page_admin();
?>

</div><!-- fin "main" -->

<?php
/* Close avant le footer, car db inutile et pour les statistiques de temps */
$table->Close();
$table->dialog->Footer();
?>

</div><!-- fin "page" -->
</body>
</html>
