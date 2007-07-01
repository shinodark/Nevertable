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

$manage_lang = get_lang_by_number($args['manage_lang']);
if (!in_array($manage_lang, $langs))
   $manage_lang = $lang['code'];

$langpath = ROOT_PATH . $config['lang_dir']. $manage_lang . "/";

if (isset($args['upannounce']))
{
    file_put_contents($langpath . "announce.txt", stripslashes($args['announce']));
    gui_button("Announcement updated.", 200);
}

if (isset($args['upspeech']))
{
    file_put_contents($langpath . "speech.txt", stripslashes($args['speech']));
    gui_button("Speech updated.", 200);
}

if (isset($args['upconditions']))
{
    file_put_contents($langpath . "conditions.txt", stripslashes($args['conditions']));
    gui_button("Conditions updated.", 200);
}


  $form = new Form("post", "management.php", "lang_form", 400);
  $form->AddTitle($lang['ADMIN_MANAGEMENT_LANG_FORM_TITLE']);
  $form->Br();
  $form->AddSelect("manage_lang", "manage_lang", $langs, $lang['ADMIN_MANAGEMENT_LANG_FORM_LANG']);
  $form->Br();
  $form->AddInputSubmit();
  echo $form->End();

  echo '<script>';
    echo "change_form_select('lang_form', 'manage_lang',  '".get_lang_by_name($manage_lang)."');";
  echo '</script>';


  if (file_exists($langpath . "announce.txt"))
    $cur_announce = file_get_contents($langpath . "announce.txt");
  if (file_exists($langpath . "speech.txt"))
    $cur_speech = file_get_contents($langpath . "speech.txt");
  if (file_exists($langpath . "conditions.txt"))
    $cur_conditions = file_get_contents($langpath . "conditions.txt");
    
  $form = new Form("post", "management.php?upannounce&amp;manage_lang=".get_lang_by_name($manage_lang)."", "announce_form", 700);
  $form->AddTitle($lang['ADMIN_MANAGEMENT_ANNOUNCE_FORM_TITLE']);
  $form->AddTextArea("announce", "announce", "", 5, $cur_announce);
  $form->Br();
  $form->AddInputSubmit();
  echo $form->End();

  $form = new Form("post", "management.php?upspeech&amp;manage_lang=".get_lang_by_name($manage_lang)."", "speech_form", 700);
  $form->AddTitle($lang['ADMIN_MANAGEMENT_SPEECH_FORM_TITLE']);
  $form->AddTextArea("speech", "speech", "", 20, $cur_speech);
  $form->Br();
  $form->AddInputSubmit();
  echo $form->End();

  $form = new Form("post", "management.php?upconditions&amp;manage_lang=".get_lang_by_name($manage_lang)."", "conditions_form", 700);
  $form->AddTitle($lang['ADMIN_MANAGEMENT_CONDITIONS_FORM_TITLE']);
  $form->AddTextArea("conditions", "conditions", "", 60, $cur_conditions);
  $form->Br();
  $form->AddInputSubmit();
  echo $form->End();

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
