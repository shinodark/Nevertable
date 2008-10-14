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

$table = new Nvrtbl("DialogStandard");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<?php $table->dialog->Head("Nevertable - Neverball Hall of Fame"); ?>

<body>
<div id="page">
<?php 
  $table->dialog->Top();
  $table->dialog->Prelude();
?>
<div id="main">
<?php

function closepage()
{  global $table;
    gui_button_back();
    gui_button_return("Upload", "upload.php");
    echo "</div><!-- fin \"main\" -->\n";
    $table->Close();
    $table->dialog->Footer();
    echo "</div><!-- fin \"page\" -->\n</body>\n</html>\n";
    exit;
}


if(isset($args['autoadd']))
{
  /* toujours off pour ce cas, puisqu'on va dans incoming d'abord */
  $overwrite == "off";

  if (!Auth::Check(get_userlevel_by_name("member")))
  {          
    gui_button_error($lang['NOT_MEMBER'], 400);
    closepage();
  }
  
  if (empty($_SESSION['user_id']))
  {
    gui_button_error($lang['GUI_INVALID_USER'], 300);
    closepage();
  }
  
  $rec = new Record($table->db);

  $fields = array(
	   "type"      => $args['type'],
	   "user_id"   => $args['user_id'],
	   "folder"    => get_folder_by_name("incoming"),
	  );
  $rec->SetFields($fields);
    
  $up_dir = ROOT_PATH. $config['replay_dir'];
    
  /* Upload du fichier */
  $f = new FileManager();
  $u = new User($table->db);
  $u->LoadFromId($rec->GetUserId());
  $replayName = sprintf("S%02dL%02d_%s_%05d.nbr",
  	$rec->GetSet(),
  	$rec->GetLevel(),
  	$u->GetPseudo(),
  	0 //Id non encore connu ˆ ce stade
  	);
  $ret = $f->Upload($_FILES, 'replayfile', $up_dir, $replayName);

  if(!$ret)
  {
    gui_button_error($f->GetError(), 500);
    closepage();
  }

  /* Analyse */
  $rep = new Replay($table->db, $f->GetFileName(), $rec->GetType());
  if(!$rep->Init())
  { 
    /* erreur lors de l'analyse */
    gui_button_error($rep->GetError(), 500);
    $ret = $f->Unlink();
    if(!$ret)
      gui_button_error($f->GetError(), 500);
    closepage();
  }

  if(get_replay_mode_by_name("challenge") == $rep->GetMode())
  {
     gui_button_error("Challenge replays are not supported yet", 500);
     closepage();
  }
 
  /* Insertion du record */
  $rec->SetFields($rep->GetFields());
      
  /* récupération de la case "goal not reached */
  if (!$rep->IsGoalReached())
  {
     $rec->SetFields(array(
	     "time" => 9999,
	     "type" => get_type_by_name("freestyle"), /* force freestyle */
     ));
  }
    
  $rec->SetFields(array("replay" => $replayName));

  $ret = $rec->Insert();
  
  if(!$ret)
  {
     gui_button_error($rec->GetError(), 500);
     if ($f->Unlink())
        gui_button_error($f->GetError(), 500);
     closepage();
  }
  
  /* Mise ˆ jour de l'ID */
  $replayName = sprintf("S%02dL%02d_%s_%05d.nbr",
  	$rec->GetSet(),
  	$rec->GetLevel(),
  	$u->GetPseudo(),
  	$rec->GetId()
  );
  
  $ret = $f->Move($up_dir, $replayName);
  
  $rec->SetFields(array("replay" => $replayName));
  $rec->Update(true);
      
  if(!$ret)
  {
     gui_button_error($rec->GetError(), 500);
     if ($f->Unlink())
        gui_button_error($f->GetError(), 500);
     closepage();
  }
    
  gui_button($lang['UPLOAD_REGISTERED'], 600);
  /* Aucune gestion à faire, puisque le record est dans "incoming" */
  $table->dialog->Replay($rep->GetStruct());
  $table->dialog->Record($rec->GetFields());

  gui_button_main_page();
}

else
{
  $form = new Form("post", "upload.php?autoadd", "avatar_form", 600, "multipart/form-data");
  $form->AddTitle($lang['UPLOAD_FORM_TITLE']);
  $form->Br();
  $form->AddLine(sprintf($lang['UPLOAD_FORM_SIZEMAX'], floor($config['upload_size_max']/1024) ));
  $form->Br();
  $form->AddInputText("pseudo", "pseudo", $lang['UPLOAD_FORM_PSEUDO'], 10, $_SESSION['user_pseudo'], "readonly");
  $form->Br();
  $form->AddSelect("type", "type",array(1=>"best time", 2=>"most coins", 3=>"freestyle"), $lang['UPLOAD_FORM_TYPE']  );
  $form->Br();
  $form->AddInputFile("replayfile", "replayfile", $lang['UPLOAD_FORM_REPLAYFILE'], 40);
  $form->AddInputHidden("user_id", "user_id", "", 0, $_SESSION['user_id']);
  $form->AddInputHidden("size_max", "MAX_FILE_SIZE", "", 0, $config['upload_size_max']);
  $form->Br();
  $form->AddInputSubmit();
  echo $form->End();

  gui_button_main_page();
}


?>
</div> <!-- fin main-->
<?php
$table->Close();
$table->dialog->Footer();
?>

</div><!-- fin "page" -->
</body>
</html>
