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
    
/*****************************/
/*------- AUTH PART HTTP  ---*/
/*****************************/
/*
if(file_exists("passwd"))
{  
	$fd = fopen("passwd", "r");  
    $line = trim(fgets($fd));
	if( (strcmp(crypt($PHP_AUTH_PW, "nevertable"),$line) != 0) ||
        (strcmp($PHP_AUTH_USER,"admin") != 0) )
    {
		header('WWW-Authenticate: Basic realm="Nevertable admin panel"');
		header('HTTP/1.0 401 Unauthorized');
		echo 'Authorization required to acces admin panel.';
		exit;
	}
    else
    {
        echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
    }
    fclose($fd);
}
else
{
		echo 'Authentification error.';
        exit;
}
*/
/*****************************/
/*------- FIN AUTH HTTP -----*/
/*****************************/
	
define('ROOT_PATH', "../");
include_once ROOT_PATH."config.inc.php";
include_once ROOT_PATH."includes/common.php";
include_once ROOT_PATH."includes/classes.php";
include_once ROOT_PATH."classes/class.dialog_admin.php";
 
$table = new Nvrtbl("DialogAdmin");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>

<?php $table->PrintHtmlHead("Nevertable - Neverball Hall of Fame"); ?>

<body>
<div id="page">
<?php   $table->PrintTop();  ?>
<div id="main">
<?php
if (!Auth::Check(get_userlevel_by_name('admin')))
{
    button_error("You have to be logged as an admin to access admin panel.",500);
    button_back();
    echo "</div><!-- fin \"main\" -->\n";
    $table->Close();
    $table->PrintFooter();
    echo "</div><!-- fin \"page\" -->\n";
    echo "</body>\n";
    echo "</html>\n";
    exit;
}
$table->PrintPrelude();

//args process
$args = get_arguments($_POST, $_GET);

/***************************************************/
/* ---------------- DIRECT LINKING ----------------*/
/***************************************************/
if(isset($args['link']))
{
  $rec = new Record($table->db);
  $results = $table->db->RequestMatchComments($args['link']);
  $table->PrintRecordById($args['link']);
  $table->dialog->Comments($results, $config['date_format']);
    
  button("<a href=?>Return to admin panel</a>", 200);
}

/***************************************************/
/* ------------------- MOVE   ---------------------*/
/***************************************************/

/*__TRASH__*/
else if ($args['to'] == 'rectrash')
{
  $id=$args['id'];

  $rec = new Record($table->db);
  $ret = $rec->LoadFromId($id);
  if (!$ret)
    button_error($rec->GetError(), 300);
  else
  {
    /* garde en mémoire l'état du record avant déplacement */
    $wasbest = $rec->IsBest();
    if (!$rec->Move(get_folder_by_name("trash")))
      button_error($rec->GetError(), 300);

    /* gestion des records, qqsoit le résultat, des erreurs de $rec->Move étant */
    /* non critiques, il peut y avoir modification quand même */
    if($rec->GetType()!=get_type_by_name("freestyle"))
    {
      /* Gestion */
      $ret = $table->ManageBestRecords($rec->GetFields(), $rec->GetType());
      if ($wasbest)
      {
        button("This was a best record...", 400);
        button($ret['nb']."&nbsp;record(s) are best records for this level/levelset/type now.", 500);
        button($ret['imports']."&nbsp;record(s) imported from \"oldones\".", 500);
      }
    }
    button("record trashed : ", 200);
    $table->PrintRecordByFields($rec->GetFields());
  }

  button("<a href=\"admin.php?folder=".get_folder_by_name("trash")."\">Return to admin panel</a>", 200);
}

/*__PURGE__*/
else if ($args['to'] == 'recpurge')
{
  $id=$args['id'];

  $rec = new Record($table->db);
  $ret = $rec->LoadFromId($id);
  if (!$ret)
    button_error($rec->GetError(), 300);
  else
  {
    /* effacement de l'enregistrement de la bdd, plus le fichier */
    $ret = $rec->Purge(true);
    if (!$ret)
    {
      button_error($rec->GetError(), 300);
    }
    else
    {
      button("record deleted : ", 200);
      $table->PrintRecordByFields($rec->GetFields());
    }
  }

  button("<a href=\"admin.php?folder=".get_folder_by_name("trash")."\">Return to admin panel</a>", 200);
}

/*__CONTEST__*/
else if ($args['to'] == 'repcontest')
{
  $id=$args['id'];
  $overwrite=$args['overwrite'];

  $rec = new Record($table->db);
  $ret = $rec->LoadFromId($id);
  if (!$ret)
    button_error($rec->GetError(), 300);
  else
  {

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
      $matches = $table->db->RequestMatchRecords($comp_array);
      $merge=false;
      if ($matches['nb'] > 1)
      {
        button_error("More than one record matches, unusual situation, aborting.", 500);
        button_error("Using normal injection instead...", 500);
        $merge=false;
      }
      else if ($matches['nb'] == 1)
        $merge=true;
      
      if ($merge)
      {
        button("One record matches, updating this one....", 500);
        /* Fusion avec la cible */
        $ret = $rec->Merge($matches[0]['id']);
        if(!$ret)  
          button_error($rec->GetError(), 500);
        else
          button("Record updated.", 200);
      }
      else
      {
        button("No record already matches in contest, so inserting new one.", 400);
        /* Deplacement */
        $ret = $rec->Move(get_folder_by_name("contest"));
        if (!$ret)
          button_error($rec->GetError(), 300);
      }
    } /* fin overwrite == yes */

    else
    {
      /* Deplacement */
      $ret = $rec->Move(get_folder_by_name("contest"));
    }
    if ($ret) 
    {
      /* gestion des records, qqsoit le résultat, des erreurs de $rec->Move étant */
      /* non critiques, il peut y avoir modification quand même */
      if($rec->GetType()!=get_type_by_name("freestyle"))
      {
        /* Gestion */
        $ret = $table->ManageBestRecords($rec->GetFields(), $rec->GetType());
        if ($ret['isbest'])
        {
          button("This is a new best record !", 400);
          button($ret['nb']."&nbsp;record(s) are best records for this level/levelset/type now.", 500);
          button($ret['beaten']."&nbsp;record(s) are obsolete.", 500);
        }
        else
        {
          button("This record is not the best one ! moved in \"oldones\" !", 500);
        }
      }
      else
      {
          button("Record moved to contest.", 500);
      }
      $table->PrintRecordByFields($rec->GetFields());
    }
    else
    {
      button_error($rec->GetError(), 500);
    }
  }

  button("<a href=\"admin.php?folder=".get_folder_by_name("contest")."&amp;levelset_f=".$rec->GetSet()."&amp;level_f=".$rec->GetLevel()."\">Return to admin panel</a>", 200);
}

/***************************************************/
/* ----------------- EDIT A RECORD ----------------*/
/***************************************************/

else if ($args['to'] == 'edit')
{
  // champs obligatoires (coins n'est pas testé il peut être == 0)
  if (empty($args['user_id']) || empty($args['time']))
  {
    button_error("Please fill all fields. Only replay is optional.", 300);
  }
  else
  {
    $rec = new Record($table->db);
    $rec->LoadFromId($args['id']);
    $oldtype = $rec->GetType();
    $rec->SetFields($args);
    $table->PrintRecordByFields($rec->GetFields());
    if($rec->GetType()==get_type_by_name("freestyle"))
        $rec->SetIsBest(false);
    $ret = $rec->Update();
    if(!$ret)
    {
      button_error($rec->GetError(), 500);
    }
    else
    {
      /* Gestion des records dans la catégorie "sortante "*/
      $ret1 = $table->ManageBestRecords($rec->GetFields(), $oldtype);
      /* Gestion des records dans la catégorie "entrante"*/
      $ret2 = $table->ManageBestRecords($rec->GetFields(), $rec->GetType());
      unset($ret);
      /* Sommation des mouvements de "best records"*/
      /* Calcul faux !
      $ret['nb']      =$ret1['nb']        + $ret2['nb'];
      $ret['beaten']  =$ret1['beaten']    + $ret2['beaten'];
      $ret['imports'] =$ret1['imports']   + $ret2['imports'];
      $ret['isbest']  =$ret1['isbest']    + $ret2['isbest'];
      */
      if ($ret['isbest'])
      {
        button("This is a new best record !", 400);
        button($ret2['nb']."&nbsp;record(s) are best records for this level/levelset/type now.", 500);
        button($ret2['beaten']."&nbsp;record(s) are obsolete.", 500);
      }
      else
      {
        button("This is not a best record...", 400);
        button($ret2['nb']."&nbsp;record(s) are best records for this level/levelset/type now.", 500);
        button($ret2['imports']."&nbsp;record(s) imported from \"oldones\".", 500);
      }
    }
  }

  button("<a href=\"admin.php?folder=".$args['folder']."&amp;levelset_f=".$rec->GetSet()."&amp;level_f=".$rec->GetLevel()."\">Return to admin panel</a>", 200);
} 

/***************************************************/
/* ----------- MAINTENANCE------- -----------------*/
/***************************************************/
else if ($args['to'] == "check")
{
   $table->CheckDatabase();
   button_back("<a href=\"javascript:history.go(-1)\">Back</a>", 100);
}

else if ($args['to'] == "recompute")
{
   $table->CheckAllRecords();
   button_back("<a href=\"admin.php?\">Return to admin panel</a>", 200);
}

/***************************************************/
/* ----------------- SHOW  ------------------------*/
/***************************************************/

else
{
  if (isset($args['type'])) $nextargs .= "?type=".$args['type'];
  if (isset($args['sort'])) $nextargs .= "&amp;sort=".$args['sort'];
  if (isset($args['bestonly'])) $nextargs .= "&amp;bestonly=".$args['bestonly'];
  if (isset($args['newonly'])) $nextargs .= "&amp;newonly=".$args['newonly'];
  if (isset($args['filter'])) $nextargs .= "&amp;filter=".$args['filter'];
  if (isset($args['filterval'])) $nextargs .= "&amp;filterval=".$args['filterval'];
  if (isset($args['levelset_f'])) $nextargs .= "&amp;levelset_f=".$args['levelset_f'];
  if (isset($args['level_f'])) $nextargs .= "&amp;level_f=".$args['level_f'];
  if (isset($args['folder'])) $nextargs .= "&amp;folder=".$args['folder'];

  $table->Show($args);
  $table->dialog->EditForm();
}
?>

</div><!-- fin "main" -->

<?php
/* Close avant le footer, car db inutile et pour les statistiques de temps */
$table->Close();
$table->PrintFooter();
?>

</div><!-- fin "page" -->
</body>
</html>
