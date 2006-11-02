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
<?php $table->PrintHtmlHead("Nevertable - Neverball Hall of Fame"); ?>

<body>
<div id="page">
<?php 
  $table->PrintTop();
  $table->PrintPrelude();
?>
<div id="main">
<?php

if($args['to'] == 'autoadd')
{
  /* toujours off pour ce cas, puisqu'on va dans incoming d'abord */
  $overwrite == "off";
  
  if (!Auth::Check(get_userlevel_by_name("member")))
  {
    button_error("You need to log in to post a record.", 400);
  }
  else if (empty($_SESSION['user_id']))
  {
    button_error("Invalid user !", 300);
  }
  else if ($args['folder'] != get_folder_by_name("incoming"))
  {
      button_error("Error: folder has to be incoming.", 300);
  }
  else
  {
    $rec = new Record($table->db);
    $rec->SetFields($args);
    
    $up_dir = ROOT_PATH. $config['replay_dir'] . get_folder_by_number($rec->GetFolder());
    
    /* Upload du fichier */
    $f = new FileManager();
    $ret = $f->Upload($_FILES, 'replayfile', $up_dir, basename($_FILES['replayfile']['name']));

    if(!$ret)
    {
      button_error($f->GetError(), 500);
    }

    /* Analyse */
    $rep = new Replay($f->GetFileName(), $rec->GetType());
    if(!$rep->Init())
    { 
      /* erreur lors de l'analyse */
      button_error($rep->GetError(), 500);
      $ret = $f->Unlink();
      if(!$ret) button_error($f->GetError(), 500);
    }
    else
    {
      /* Insertion du record */
      $rec->SetFields($rep->GetFields());
      
      /* récupération de la case "goal not reached */
      if ($args['goalnotreached'] == "on")
          $rec->SetFields(array("time" => 9999));
      
      $rec->SetFields(array("replay" => $f->GetBaseName()));

      $ret = $rec->Insert();
      
      if(!$ret)
      {
        button_error($rec->GetError(), 500);
        if ($f->Unlink())
          button_error($f->GetError(), 500);
      }
      else
      {
        button("Your record is registered. An admin has to validate it before contest update.", 600);
        /* Aucune gestion à faire, puisque le record est dans "incoming" */
        $table->PrintRecordByFields($rec->GetFields());
      }
    }
  }

  button("<a href=\"index.php\">Return to table</a>", 200);
}

else
{
  button("Max size of file : ".floor($config['upload_size_max']/1024)."kB.",550);
  $table->PrintAddFormAuto();
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
