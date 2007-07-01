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

define('ROOT_PATH', "./");
include_once ROOT_PATH ."config.inc.php";
include_once ROOT_PATH ."includes/common.php";
include_once ROOT_PATH ."includes/classes.php";

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

if($args['to'] == 'showlinklist')
{
 header("Content-Type: text/plain");
 echo $_SESSION['download_list'];
 exit;
}

$table = new Nvrtbl("DialogStandard");

if(isset($args['link']))
  $special="<meta http-equiv=\"refresh\" content=\"0;URL=record.php?id=".$args['link']."\" />\n";
else
  $special="";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<?php $table->dialog->Head("Nevertable - Neverball Hall of Fame", $special); ?>

<body>
<div id="page">
<?php   $table->dialog->Top();  ?>
<div id="main">
<?php
$table->dialog->Prelude();

/***************************************************/
/* ----------- AFFICHAGE   ------------------------*/
/***************************************************/
if (isset($args['link']))
{
  gui_button("Redirecting...", 100);
}

else 
{
  $nextargs = "index.php?";
  if (isset($args['type'])) $nextargs .= "&amp;type=".$args['type'];
  if (isset($args['sort'])) $nextargs .= "&amp;sort=".$args['sort'];
  if (isset($args['diffview'])) $nextargs .= "&amp;diffview=".$args['diffview'];
  if (isset($args['newonly'])) $nextargs .= "&amp;newonly=".$args['newonly'];
  if (isset($args['filter'])) $nextargs .= "&amp;filter=".$args['filter'];
  if (isset($args['filterval'])) $nextargs .= "&amp;filterval=".$args['filterval'];
  if (isset($args['levelset_f'])) $nextargs .= "&amp;levelset_f=".$args['levelset_f'];
  if (isset($args['level_f'])) $nextargs .= "&amp;level_f=".$args['level_f'];
  if (isset($args['folder'])) $nextargs .= "&amp;folder=".$args['folder'];

  /* Affichage supplémentaire dans le cas de l'affichage d'un seul niveau */
  if (isset($args['level_f']) && isset($args['levelset_f'])
     && ($args['level_f'] > 0) && ($args['levelset_f'] > 0)
     && ($args['diffview'] == "off") && $table->mode == "DialogStandard")
  {
    $mode_level = true;
  }

  /* Récupération de l'option utilisateur de l'ordre de tri par défaut */
  if (empty($args['sort']) && !Auth::Check(get_userlevel_by_name("member")))
    $sort = get_sort_by_name("old");
  else if (empty($args['sort']) && Auth::Check(get_userlevel_by_name("member")))
    $sort = $config['opt_user_sort'];
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
      $table->db->RequestInit("SELECT", "rec", "COUNT(id)");
      $table->db->RequestGenericFilter($args['filter'], $args['filterval']);
      $table->db->helper->RequestFilterLevels($args['levelset_f'], $args['level_f']);
      $table->db->helper->RequestFilterType($args['type']);
      $table->db->helper->RequestFilterNew($args['newonly']);
      $table->db->helper->RequestFilterFolder($args['folder']);
      $result0 =   $table->db->Query();
      if(!$result0)
          gui_button_error(  $table->db->GetError(), 500);

      $res = $table->db->FetchArray();
      $total = $res['COUNT(id)'];
      /* FIN COMPTAGE */
      
      /* requête avec tous les champs mais limitée à "limit" */
      $p = $config['bdd_prefix'];
      $table->db->RequestSelectInit(
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
      $table->db->RequestGenericFilter(
          array($p."rec.user_id", $p."rec.levelset", $p."rec.levelset", $p."rec.level"),
          array($p."users.id", $p."sets.id", $p."maps.set_id", $p."maps.level_num"),
          "AND", false
      );
      
      $table->db->RequestGenericFilter($args['filter'], $args['filterval']);
      $table->db->helper->RequestFilterLevels($args['levelset_f'], $args['level_f']);
      $table->db->helper->RequestFilterType($args['type']);
      $table->db->helper->RequestFilterNew($args['newonly']);
      $table->db->helper->RequestFilterFolder($args['folder']);
      
      if($args['bestonly'] == "on")
        $table->db->RequestGenericFilter("isbest", 1);
      
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

      $table->db->helper->RequestSort($sort);
      $table->db->RequestLimit($config['limit'], $off);
      $result1 =   $table->db->Query();
      if(!$result1)
        gui_button_error(  $table->db->GetError(), 500);
  }

  /* Mode fiche de niveau */
  /* -------------------- */
  else 
  {
      /* requête pour les records du contest */
      $p = $config['bdd_prefix'];
      $table->db->RequestSelectInit(
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
      $table->db->RequestGenericFilter(
          array($p."rec.user_id", $p."rec.levelset", $p."rec.levelset", $p."rec.level"),
          array($p."users.id", $p."sets.id", $p."maps.set_id", $p."maps.level_num"),
          "AND", false
      );
      
      $table->db->RequestGenericFilter($args['filter'], $args['filterval']);
      $table->db->RequestGenericFilter("folder", get_folder_by_name("contest"));
      $table->db->helper->RequestFilterLevels($args['levelset_f'], $args['level_f']);
      $table->db->helper->RequestFilterType($args['type']);
      $table->db->helper->RequestSort($sort);
      $result1 = $table->db->Query();
      if(!$result1)
        gui_button_error(  $table->db->GetError(), 500);
      $total1 = $table->db->NumRows();
        
      /* requête pour les records anciens */
      $table->db->RequestSelectInit(
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
      $table->db->RequestGenericFilter(
          array($p."rec.user_id", $p."rec.levelset", $p."rec.levelset", $p."rec.level"),
          array($p."users.id", $p."sets.id", $p."maps.set_id", $p."maps.level_num"),
          "AND", false
      );
      
      $table->db->RequestGenericFilter($args['filter'], $args['filterval']);
      $table->db->RequestGenericFilter("folder", get_folder_by_name("oldones"));
      $table->db->helper->RequestFilterLevels($args['levelset_f'], $args['level_f']);
      $table->db->helper->RequestFilterType($args['type']);
      $table->db->helper->RequestSort($sort);
      $result2 = $table->db->Query();
      if(!$result2)
        gui_button_error(  $table->db->GetError(), 500);
      $total2 = $table->db->NumRows();
  }

  $table->dialog->Speech();
  $table->dialog->TypeForm($args);
  if (!$mode_level)
  {
    $table->dialog->NavBar($total, $config['limit'], 'index.php', $nextargs);
    $diff = $args['diffview']=="on" ? true : false;
    $table->dialog->Table($result1, $diff, $total);
  }
  else
  {
    $table->dialog->Level($result1, $result2, $args, $total1, $total2);
  }

 /* $table->dialog->SideBar( array("registered" => $table->online_users_registered,
                                "guests"     => $table->online_users_guest,
                                "list"       => $table->online_users_list)
                           );
 */
 $table->dialog->SideBar( );
  
 if (!$mode_level)
    $table->dialog->NavBar($total, $config['limit'], 'index.php', $nextargs);
}
?>

</div><!-- fin "main" -->

<?php
$table->dialog->Stats($table->GetStats());
/* Close avant le footer, car db inutile et pour les statistiques de temps */
$table->Close();
$table->dialog->Footer();
?>

</div><!-- fin "page" -->
</body>
</html>
