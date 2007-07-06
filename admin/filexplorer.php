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

$replay_path = ROOT_PATH.$config['replay_dir'];
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
  closepage();
}

/** RENOMMER **/
if (isset($args['filecation']) && isset($args['rename']))
{
  $folder_name = $args['folder'];
  $replay = $args['replay'];
  $newname = $args['newname'];
  if (empty($newname) || empty($replay))
  {
	  gui_button_error("Internal error in argument folder or replay", 500);
	  closepage();
  }
  
  /* recherche les records auxquels appartiennent ce fichier */
  $table->db->NewQuery("SELECT", "rec");
  $table->db->Where("replay", $replay);
  $table->db->Where("folder", $args['folder']);
  $res = $table->db->Query();
  if(!$res)
  {
	  guibutton_error($this->db->GetError());
	  closepage();
  }

  /* affichage des records concernés par la modif */
  if ($table->db->NumRows()>0)
  {
    echo "<br/><center><b>Record using that file : </b></center>\n";
    $nextargs="admin.php";
    $table->dialog->Table($res, array());
  }
  else 
     gui_button("No record is using that file ", 400);

  /* déplacement du fichier */
  $f = new FileManager($config['replay_dir'].$folder_name."/".$replay);
  if (!$f->Rename($newname))
  {
	  gui_button_error($f->GetError(), 400);
	  closepage();
  }

  /* modifications des records concernés */
  gui_button("File is renamed.", 200);
  $rec = new Record($table->db);
  /* recherche les records auxquels appartiennent ce fichier */
  /* on recommence, le fetcharray a déjà été  fait par l'affichage */
  /* de la table ! */
  $table->db->NewQuery("SELECT", "rec");
  $table->db->Where("replay", $replay);
  $table->db->Where("folder", $args['folder']);
  $res = $table->db->Query();
  if(!$res)
  {
	  gui_button_error($this->db->GetError());
	  closepage();
  }
  while($val = $table->db->FetchArray($res))
  {
    $rec->LoadFromId($val['id']);
    $rec->SetFields(array("replay" => $newname));
    $ret = $rec->Update(true);
    if (!$ret)
    {
      	  gui_button_error($rec->GetError());
	  closepage();
    }
    gui_button("record #".$val['id']." updated.", 400);
  }
}

/** EFFACER **/
if (isset($args['fileaction']) && isset($args['delete']))
{
  $folder_name = $args['folder'];
  $replay = $args['replay'];
  if (empty($replay))
  {
	  gui_button_error("Internal error in argument folder or replay", 500);
	  closepage();
  }
  /* recherche les records auxquels appartiennent ce fichier */
  $table->db->NewQuery("SELECT", "rec");
  $table->db->Where("replay", $replay);
  $table->db->Where("folder", $args['folder']);
  $res = $table->db->Query();
  if(!$res)
  {
	  gui_button_error($this->db->GetError());
	  closepage();
  }
  /* affichage des records concernés par la modif */
  if ($table->db->NumRows()>0)
  {
    echo "<br/><center><b>Record using that file : </b></center>\n";
    $nextargs="admin.php";
    $table->dialog->Table($res, array());
  }
  else 
    gui_button("No record is using that file ", 400);

  /* effacement du fichier */
  $f = new FileManager($config['replay_dir'].$folder_name."/".$replay);
  if (!$f->Unlink())
  {
	  gui_button_error($f->GetError(), 400);
	  closepage();
  }
  gui_button("File is deleted !", 200);
  $rec = new Record($table->db);
  /* recherche les records auxquels appartiennent ce fichier */
  /* on recommence, le fetcharray a déjà été  fait par l'affichage */
  /* de la table ! */
  $table->db->NewQuery("SELECT", "rec");
  $table->db->Where("replay", $replay);
  $table->db->Where("folder", $args['folder']);
  $res = $table->db->Query();
  if(!$res)
  {
	  gui_button_error($this->db->GetError());
	  closepage();
  }
  while($val = $table->db->FetchArray($res))
  {
    $ret = $rec->LoadFromId($val['id']);
    $rec->SetFields(array("replay" => ""));
    $ret = $rec->Update(true);
    if (!$ret)
    {
	    gui_button_error($rec->GetError());
	    closepage();
    }
    gui_button("record #".$val['id']." updated.", 400);
  }
}

/** main **/

$f = new FileManager();
$root = $f->DirList($replay_path);
foreach ($root["subdir"] as $dir )
{
  if (!empty($args['grep']))
    $nextargs="filexplorer.php?grep=".$args['grep']."#".$dir;
  else
    $nextargs="filexplorer.php#".$dir;
  echo "<a href=\"".$nextargs."\">: ".$dir." :</a>\n";
}

echo '<div style="margin-left: auto; margin-right: auto; width: 450px;">'."\n";
echo "<table>\n";
echo "<caption>File explorer</caption>\n";

foreach ($root["subdir"] as $dir )
{
    echo "<tr style=\"background: white;\"><td colspan=\"4\"><br/><b><a name=\"".$dir."\"></a>".$dir."</b><br/><hr/></td></tr>\n";
    echo "<tr><td colspan=\"4\" style=\"background: #fff; height: 2px;\"></td></tr>\n";
    $res = $f->DirList($replay_path.$dir);
    $list = $res['files'];
    $i=0;
    foreach ($list as $file)
    {
      /* si on filtre l'affichage, n'afficher que ce qui correspond */
      if (isset($args['grep']) && !empty($args['grep']) && !strstr($file, $args['grep']))
        continue;
      
      $rowclass=($i%2)?"row1":"row2"; $i++;
      echo "<tr class=\"".$rowclass."\"><td>&nbsp;&nbsp;$file</td>\n";
      $f->SetFilename($config['replay_dir'].$dir."/".$file);
      echo "<td>". $f->GetSize("ko") ." ko</td>\n";
      echo '<td style="text-align: right;">'."\n";
         echo "<form action=\"?fileaction\" method=\"post\">\n";
         echo "<input type=\"text\" name=\"newname\" value=\"".$file."\" size=\"10\" />\n";
         echo "<input type=\"hidden\" name=\"folder\" value=\"".$dir."\" /\n>";
         echo "<input type=\"hidden\" name=\"replay\" value=\"".$file."\" />\n";
         echo "<input type=\"submit\" value=\"rename\" name=\"rename\" />\n";
         echo "<input type=\"submit\" value=\"delete\" name=\"delete\" />\n";
         echo "</form>\n";
      echo "</td>\n";
      echo "</tr>\n";
      echo "<tr><td colspan=\"4\" style=\"background: #fff; height: 1px;\"></td></tr>\n";
    }
}

echo "</table>\n";
echo "</div>\n";

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
