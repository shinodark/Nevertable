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

/*
 * TRAITEMENT DES EVENEMENTS
 * */
if (isset($args['setadd']))
{
  $tmp_dir = ROOT_PATH. $config['tmp_dir'];
  $tmp_file = tempnam($tmp_dir, 'set_');
  if (!$tmp_file)
  {
    gui_button_error("Error on creating temp file.", 300);
    closepage();
  }

  $f = new FileManager();
  $ret = $f->Upload($_FILES, 'setfile', $tmp_dir, basename($tmp_file), true);

  if(!$ret)
  {
    gui_button_error($f->GetError(), 500);
    closepage();
  }

  if (!$f->Open())
  {
    gui_button_error($f->GetError(), 500);
    $f->Unlink();
    closepage();
  }

  $set_name = trim($f->ReadLine());
  $f->ReadLine(); /* Difficulty */
  $set_path = "map-".trim($f->ReadLine()); /* path */
  $f->ReadLine(); /* shot */
  $f->ReadLine(); /* empty */

  $s = new Set($table->db);

  $s->SetFields(array(
	  "set_name" => $set_name,
	  "set_path" => $set_path,
  ));

  if (!$s->Insert())
  {
    $f->Unlink();
    gui_button_error($s->GetError(), 500);
    closepage();
  }

  $num = 1;

  while (!$f->IsEof())
  {
    $map_solfile =  basename(trim($f->ReadLine()));
    if (!empty($map_solfile))
    {
      if(!$s->AddMap($num, $map_solfile))
      {
        $f->Unlink();
        gui_button_error($s->GetError(), 500);
        closepage();
      }
    }
    $num++;
  }

  $f->Close();
  $f->Unlink();

  gui_button($lang['GUI_UPDATE_OK'], 300);
}

if (isset($args['setaction']) && isset($args['delete']))
{
  gui_button_error("This will delete ALL maps and ALL records of this set...", 500);
  gui_button("Are you sure ?", 200);
  gui_button('<a href="?delete2&amp;set_id='.$args['set_id'].'"><b>Yes</b></a>', 100);

  closepage();
}

if (isset($args['delete2']))
{
  $s = new Set($table->db);
  if (!$s->LoadFromId($args['set_id']))
  {
     gui_button_error($s->GetError(), 500);
     closepage();
  }
  if (!$s->Purge())
  {
     gui_button_error($s->GetError(), 500);
     closepage();
  }
  
  gui_button($lang['GUI_UPDATE_OK'], 300);
}

if (isset($args['setaction']) && isset($args['rename']))
{
  $s = new Set($table->db);
  if (!$s->LoadFromId($args['set_id']))
  {
     gui_button_error($s->GetError(), 500);
     closepage();
  }

  $fields = array("set_name" => trim(GetContentFromPost($args['newname'])));
  $s->SetFields($fields);
  if (!$s->Update())
  {
     gui_button_error($s->GetError(), 500);
     closepage();
  }

  gui_button($lang['GUI_UPDATE_OK'], 300);
}


/** main **/
echo "<br/>";

/* __SETS__ */
echo '<div style="margin-left: auto; margin-right: auto; width: 450px;">'."\n";
echo "<table>\n";
echo "<caption>Sets</caption>\n";

$res = $table->db->helper->SelectSetsRes();

$i = 0;
while ($val = $table->db->FetchArray($res))
{
   $rowclass=($i%2)?"row1":"row2"; $i++;

   echo "<tr class=\"".$rowclass."\">\n";
   echo "<td>#".$val['id']."</td>\n";
   echo "<td>".$val['set_path']."</td>\n";
   echo "<td style=\"text-align: right;\">\n";
      echo "<form name=\"setform_".$i."\" id=\"setform_".$i."\" action=\"?setaction\" method=\"post\">\n";
      echo "<input type=\"text\" name=\"newname\" value=\"".$val['set_name']."\" size=\"30\" />\n";
      echo "<input type=\"hidden\" name=\"set_id\" value=\"".$val['id']."\" /\n>";
      echo "<input type=\"submit\" value=\"rename\" name=\"rename\"  />\n";
      echo "<input type=\"submit\" value=\"delete\" name=\"delete\" />\n";
      echo "</form>\n";
   echo "</td>\n";
   echo "</tr>\n";
}
echo "</table>\n";
echo "</div>\n";

$form = new Form("post", "?setadd", "setadd_form", 400, "multipart/form-data");
$form->AddTitle("Add a new set from set file");
$form->Br();
$form->AddInputFile("setfile", "setfile", "set file : ", 25);
$form->AddInputHidden("size_max", "MAX_FILE_SIZE", "", 0, 50000);
$form->Br();
$form->AddInputSubmit();
echo $form->End();


/* __MAPS */

$sets = $table->db->helper->SelectSets();

echo '<div style="margin-left: auto; margin-right: auto; width: 300px;">'."\n";
echo "<table>\n";
echo "<caption>Maps</caption>\n";

$i = 0;
foreach ($sets as $id => $name)
{
    echo "<tr style=\"background: white;\"><td colspan=\"4\"><br/><b><a name=\"".$name."\"></a>".$name."</b><br/><hr/></td></tr>\n";
    echo "<tr><td colspan=\"4\" style=\"background: #fff; height: 2px;\"></td></tr>\n";

    $set = new Set($table->db);
    $set->LoadFromId($id);
    $res = $set->GetMapsRes();
    while ($val = $table->db->FetchArray($res))
    {
       $rowclass=($i%2)?"row1":"row2"; $i++;

       echo "<tr class=\"".$rowclass."\">\n";
       echo "<td>#".$val['level_num']."</td>\n";
       echo "<td>".$val['map_solfile']."</td>\n";
       echo "</tr>\n";
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
