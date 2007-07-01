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
include_once ROOT_PATH ."config.inc.php";
include_once ROOT_PATH ."includes/common.php";
include_once ROOT_PATH ."includes/classes.php";
include_once ROOT_PATH ."classes/class.dialog_admin.php";

//args process
$args = get_arguments($_POST, $_GET);

$table = new Nvrtbl("DialogAdmin");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<?php


$table->dialog->Head("Nevertable - Neverball Hall of Fame");
?>

<body>
<div id="page">
<?php   $table->dialog->Top();  ?>
<div id="main">
<?php

function closepage()
{   global $table;
    gui_button_back();
    echo "</div><!-- fin \"main\" -->\n";
    $table->Close();
    $table->dialog->Footer();
    echo "</div><!-- fin \"page\" -->\n</body>\n</html>\n";
    exit;
}

if (!Auth::Check(get_userlevel_by_name("admin")))
{
  gui_button_error($lang['NOT_ADMIN'], 400);
  closepage();;
}


/* process de l'upload */
if (isset($args['upload2']))
{
  $id = $args['id'];
  
  $rec = new Record($table->db);
  $rec->LoadFromId($id);

  $replay_name   = basename($_FILES['uploadfile']['name']);
  $up_dir        = ROOT_PATH . $config['replay_dir'].get_folder_by_number($rec->GetFolder());
  
  $f   = new FileManager();
  $table->dialog->Record($rec->GetFields());
 
  $r=$rec->GetReplay();
  /* Effacement de l'ancien fichier si il existe */
  if( !empty($r))
  {
      $f->SetFileName($rec->GetReplayRelativePath());
      $ret = $f->Unlink();
      if (!$ret)
      {
	      gui_button_error($f->GetError(), 500);
	      closepage();
      }
      gui_button("Old replay file ". $rec->GetReplayRelativePath()." deleted", 500);
  }

  /* Upload */
  $ret = $f->Upload($_FILES, 'uploadfile', $up_dir, $replay_name);
  if(!$ret)
  {
	  gui_button_error($f->GetError(), 500);
	  closepage();
  }
  gui_button("File uploaded", 500);
  
  /* Modification du record */
  $rec->SetFields(array("replay" => $f->GetBaseName()));
  $ret = $rec->Update(true);
  if(!$ret)
  {
     gui_button_error($rec->GetError(), 500);
     closepage();
  }
  gui_button("Record updated", 500);

}

else
{
  $id = $args['id'];
  
  $rec = new Record($table->db);
  $rec->LoadFromId($id);
  $table->dialog->Record($rec->GetFields());
  gui_button("Max size of file : ".floor($config['upload_size_max']/1024)."kB.",550);
  $nextargs = "?id=".$id ;

  $form = new Form("post", "upload.php?upload2&amp;id=".$id, "upload_form", 600, "multipart/form-data");
  $form->AddTitle($lang['UPLOAD_FORM_TITLE']);
  $form->Br();
  $form->AddLine(sprintf($lang['UPLOAD_FORM_SIZEMAX'], floor($config['upload_size_max']/1024) ));
  $form->Br();
  $form->AddInputFile("uploadfile", "uploadfile", $lang['UPLOAD_FORM_REPLAYFILE'], 40);
  $form->AddInputHidden("size_max", "MAX_FILE_SIZE", "", 0, $config['upload_size_max']);
  $form->Br();
  $form->AddInputSubmit();
  echo $form->End();
}

gui_button_main_page_admin();
?>
</div> <!-- fin main-->
<?php
$table->Close();
$table->dialog->Footer();
?>

</div><!-- fin "page" -->
</body>
</html>
