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
<?php $table->dialog->Head("Nevertable - Neverball Hall of Fame"); ?>

<body>
<div id="page">
<?php   $table->dialog->Top();  ?>
<div id="main">
<?php

function closepage()
{   global $table;
    echo "</div><!-- fin \"main\" -->\n";
    $table->Close();
    $table->dialog->Footer();
    echo "</div><!-- fin \"page\" -->\n";
    echo "</body>\n";
    echo "</html>\n";
    exit;
}


$tagboard = new TagBoard($table->dialog->db, $table->dialog->bbcode, $table->dialog->smilies, $table->dialog->style);

if (!Auth::Check(get_userlevel_by_name("moderator")))
{  
  gui_button_error($lang['NOT_MODERATOR'], 400);
  gui_button_back();
  closepage();
}

$nextargs = "tag_moder.php?";

if($args['to'] == "edit")
{
    gui_button("Editing tag #".$args['id']. " ...", 200);
    $nextargs = "tag_moder.php?id=".$args['id'];
}

if(isset($args['tag']))
{
   if (!isset($args['id']) || empty($args['id']))
   {
      gui_button_error($lang['TAG_NO_TAG'], 300);
   }
   else if(empty($args['tag_pseudo']) || empty($args['content']))
   {
      gui_button_error($lang['TAG_EMPTY_FIELD'], 300);
   }
   else
   {
    if (!$tagboard->Update($args['id'], $args['content'], $args['tag_pseudo'], $args['tag_link']))
      gui_button_error($tagboard->GetError(), 400);
   }
}

if($args['to'] == "del")
{
    if (isset($args['id']) && !empty($args['id']))
    {
      if (!$tagboard->Purge($args['id']))
      {
        gui_button_error($tagboard->GetError(), 400);
      }
      else
      {
        gui_button("tag #".$args['id']." deleted", 300);
      }
    }
}

$tagboard->dialog->Tags(true);
$tagboard->dialog->TagForm();
$tagboard->PrintOut();


if($args['to'] == "edit")
{
    $tagboard->db->RequestInit("SELECT", "tags");
    $tagboard->db->RequestGenericFilter("id", $args['id']);
    if(!$tagboard->db->Query())
      gui_button_error($tagboard->db->GetError(), 400);
    $val = $tagboard->db->FetchArray();
    print_r($val);
    echo "<script type=\"text/javascript\">
	    change_form_input('tagpostform', 'tag_pseudo', '".JavaScriptize($val['pseudo'])."');\n
            change_form_textarea('tagpostform', 'content', '".JavaScriptize($val['content'])."')
	  </script>\n";
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
