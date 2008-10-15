<?php
# ***** BEGIN LICENSE BLOCK *****
# This file is part of Trombi .
# Copyright (c) 2004 Francois Guillet and contributors. All rights
# reserved.
#
# Trombi is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
# 
# Trombi is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with Trombi; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
# ***** END LICENSE BLOCK *****

define('ROOT_PATH', "./");
include_once ROOT_PATH ."config.inc.php";
include_once ROOT_PATH ."includes/common.php";
include_once ROOT_PATH ."includes/classes.php";

//args process
$args = get_arguments($_POST, $_GET);

$table = new nvrtbl();
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
{  
    global $table;
    gui_button_back();
    gui_button_return("Options", "options.php");
    echo "</div><!-- fin \"main\" -->\n";
    $table->Close();
    $table->dialog->Footer();
    echo "</div><!-- fin \"page\" -->\n</body>\n</html>\n";
    exit;
}

function CheckLimitsOptions($args)
{
  global $lang, $langs;

  $val= array();
  $err = true;

  $val['user_theme'] = GetContentFromPost($args['theme']);
  $err = $err & CheckLimitInterval($val['opt_theme'], 0, 5, $lang['OPTIONS_FORM_THEME']);

  $val['user_lang'] = get_lang_by_number(GetContentFromPost($args['lang']));
  $err = in_array($val['user_lang'], $langs);
  if ($err == false)
	  gui_button_error("Bad lang", 200);
  $val['user_sort'] = GetContentFromPost($args['sort']);
  $err = $err & CheckLimitInterval($val['user_sort'], 0, 6, $lang['OPTIONS_FORM_SORT']);
  $val['user_limit'] = GetContentFromPost($args['limit']);
  $err = $err & CheckLimitInterval($val['user_limit'], 1, 50, $lang['OPTIONS_FORM_LIMIT']);
  $val['user_comments_limit'] = GetContentFromPost($args['comments_limit']);
  $err = $err & CheckLimitInterval($val['user_comments_limit'], 1, 50, $lang['OPTIONS_FORM_COMMENTS_LIMIT']);
  $val['user_sidebar_comments'] = GetContentFromPost($args['sidebar_comments']);
  $err = $err & CheckLimitInterval($val['user_sidebar_comments'], 1, 15, $lang['OPTIONS_FORM_SIDEBAR_COMMENTS']);
  $val['user_sidebar_comlength'] = GetContentFromPost($args['sidebar_comlength']);
  $err = $err & CheckLimitInterval($val['user_sidebar_comlength'], 5, 100, $lang['OPTIONS_FORM_SIDEBAR_COMLENGTH']);


  if ($err == false) return $err;
  else return $val;
}

if (!Auth::Check(get_userlevel_by_name("member")))
{          
  gui_button_error($lang['NOT_MEMBER'], 400);
  closepage();
}
  
$user = new User($table->db);
if(!$user->LoadFromId($_SESSION['user_id']))
{
  gui_button_error($user->GetError(), 400);
  closepage();
}

/*
   TRAITEMENT DES EVENEMENTS 
*/
if(isset($args['upoptions']))
{
   $val = CheckLimitsOptions($args);
   if ($val == false)
   {
      closepage();
   }

   $user->SetFields($val);
   if (! $user->Update())
   {
     gui_button_error($user->GetError(), 300);
     closepage();
   }
   
   $_SESSION['options_saved'] = false;
   gui_button($lang['GUI_UPDATE_OK'], 300);
   gui_button_return("Options", "options.php");
   echo "</div><!-- fin \"main\" -->\n";
   $table->Close();
   $table->dialog->Footer();
   echo "</div><!-- fin \"page\" -->\n</body>\n</html>\n";
   exit;

   /* rafraichit le cache des options */
}

/*
   AFFICHAGE DE LA PAGE
*/

  /* Options */
  $form = new Form("post", "options.php?upoptions", "options_form", 600);
  $form->AddTitle($lang['OPTIONS_FORM_TITLE']);

  $form->Br();
  $form->AddSelect("sort", "sort", $sort_type, $lang['OPTIONS_FORM_SORT']  );
  $form->Br();
  $form->AddSelect("theme", "theme", $themes, $lang['OPTIONS_FORM_THEME']  );
  $form->Br();
  $form->AddSelect("lang", "lang", $langs, $lang['OPTIONS_FORM_LANG']  );
  $form->Br();
  $form->AddInputText("limit", "limit", $lang['OPTIONS_FORM_LIMIT'], 5, $user->GetLimit());
  $form->Br();
  $form->AddInputText("comments_limit", "comments_limit", $lang['OPTIONS_FORM_COMMENTS_LIMIT'], 5, $user->GetCommentsLimit());
  $form->Br();
  $form->AddInputText("sidebar_comments", "sidebar_comments", $lang['OPTIONS_FORM_SIDEBAR_COMMENTS'], 5, $user->GetSidebarComments());
  $form->Br();
  $form->AddInputText("sidebar_comlength", "sidebar_comlength", $lang['OPTIONS_FORM_SIDEBAR_COMLENGTH'], 5, $user->GetSidebarComLength());
  $form->Br();
  $form->AddInputSubmit();
  echo $form->End();

  echo '<script>';
    echo "change_form_select('sort',  '".$user->GetSort()."');";
    echo "change_form_select('theme',  '".$user->GetTheme()."');";
    echo "change_form_select('lang',  '".get_lang_by_name($user->GetLang())."');";
  echo '</script>';

gui_button_main_page();
?>
</div><!-- fin "main" -->
<?php    
$table->Close();
$table->dialog->Footer();
?>

</div><!-- fin "page" -->
</body>
</html>
