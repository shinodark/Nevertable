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


$table->PrintHtmlHead("Nevertable - Neverball Hall of Fame");
?>

<body>
<div id="page">
<?php   $table->PrintTop();  ?>
<div id="main">
<?php
if (!Auth::Check(get_userlevel_by_name("admin")))
{
  button_error("You have to be admin to access this page.", 400);
  exit;
}

/* process de l'upload */
else if ($args['to'] == 'upload2')
{
  $id = $args['id'];
  
  $rec = new Record($table->db);
  $rec->LoadFromId($id);

  $replay_name   = basename($_FILES['uploadfile']['name']);
  $up_dir        = ROOT_PATH . $config['replay_dir'].get_folder_by_number($rec->GetFolder());
  
  $f   = new FileManager();
  $table->PrintRecordByFields($rec->GetFields());
 
  $r=$rec->GetReplay();
  /* Effacement de l'ancien fichier si il existe */
  if( !empty($r))
  {
      $f->SetFileName($rec->GetReplayRelativePath());
      $ret = $f->Unlink();
      if (!$ret)
         button_error($f->GetError(), 500);
      else
         button("Old replay file ". $rec->GetReplayRelativePath()." deleted", 500);
  }

  /* Upload */
  $ret = $f->Upload($_FILES, 'uploadfile', $up_dir, $replay_name);
  if(!$ret)
  {
    button_error($f->GetError(), 500);
  }
  else
  {
    button("File uploaded", 500);
  }
  
  /* Modification du record */
  $rec->SetFields(array("replay" => $f->GetBaseName()));
  $ret = $rec->Update(true);
  if(!$ret)
  {
    button_error($rec->GetError(), 500);
  }
  else
  {
    button("Record updated", 500);
  }

  button("<a href=\"admin.php\">Return to admin panel</a>", 400);
}

else
{
  $id = $args['id'];
  
  $rec = new Record($table->db);
  $rec->LoadFromId($id);
  $table->PrintRecordByFields($rec->GetFields());
  button("Max size of file : ".floor($config['upload_size_max']/1024)."kB.",550);
  $nextargs = "?id=".$id ;
  $table->PrintUploadForm();

  button_back();
}
  
?>
</div> <!-- fin main-->
<?php
$table->Close();
$table->PrintFooter();
?>

</div><!-- fin "page" -->
</body>
</html>
