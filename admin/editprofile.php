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
    gui_button_back();
    echo "</div><!-- fin \"main\" -->\n";
    $table->Close();
    $table->dialog->Footer();
    echo "</div><!-- fin \"page\" -->\n</body>\n</html>\n";
    exit;
}

function CheckLimitsInfo($args)
{
  global $lang;

  $val= array();
  $err = true;

  $val['user_localisation'] = GetContentFromPost($args['user_localisation']);
  $err = $err & CheckLimitLength($val['user_localisation'], 40, $lang['PROFILE_FORM_INFO_LOCAL']);

  $val['user_speech'] = GetContentFromPost($args['user_speech']);
  $err = $err & CheckLimitLength($val['user_speech'], 300, $lang['PROFILE_FORM_INFO_SPEECH']);

  $val['user_web'] = CheckUrl(GetContentFromPost($args['user_web']));
  $err = $err & CheckLimitLength($val['user_web'], 80, $lang['PROFILE_FORM_INFO_WEB']);

  if ($err == false) return $err;
  else return $val;
}

function CheckLimitsOptions($args)
{
  global $lang;

  $val= array();
  $err = true;

  $val['user_theme'] = GetContentFromPost($args['theme']);
  $err = $err & CheckLimitInterval($val['opt_theme'], 0, 5, $lang['OPTIONS_FORM_THEME']);
  $val['user_sort'] = GetContentFromPost($args['sort']);
  $err = $err & CheckLimitInterval($val['user_sort'], 0, 6, $lang['OPTIONS_FORM_SORT']);
  $val['user_limit'] = GetContentFromPost($args['limit']);
  $err = $err & CheckLimitInterval($val['user_limit'], 1, 50, $lang['OPTIONS_FORM_LIMIT']);
  $val['user_sidebar_comments'] = GetContentFromPost($args['sidebar_comments']);
  $err = $err & CheckLimitInterval($val['user_sidebar_comments'], 1, 15, $lang['OPTIONS_FORM_SIDEBAR_COMMENTS']);
  $val['user_sidebar_comlength'] = GetContentFromPost($args['sidebar_comlength']);
  $err = $err & CheckLimitInterval($val['user_sidebar_comlength'], 5, 100, $lang['OPTIONS_FORM_SIDEBAR_COMLENGTH']);


  if ($err == false) return $err;
  else return $val;
}

if (!Auth::Check(get_userlevel_by_name("admin")))
{
  gui_button_error($lang['NOT_ADMIN'], 400);
  closepage();;
}

if (!isset($args['id']))
{
  gui_button_error("URL error, id not set", 400);
  closepage();
}

$user = new User($table->db);
if(!$user->LoadFromId($args['id']))
{
  gui_button_error($u->GetError(), 400);
  closepage();
}


/*
   TRAITEMENT DES EVENEMENTS 
*/

if (isset($args['upident']))
{
  if (!empty($args['email']) && $args['email'] != $user->GetMail())
  {
    $table->db->helper->SlectUserByMail($args['email']);
    if ($table->db->NumRows() > 0) // dejà existant
    {
      gui_button_error($lang['REGISTER_MAIL_EXISTS'], 500);
      closepage();
    }

    if (!CheckMail($args['email']))
    {
      gui_button_error($lang['REGISTER_MAIL_NOTVALID'], 400);
      closepage();
    }

    if ((addslashes($args['email']) != $args['email']) )
    {
      gui_button_error($lang['REGISTER_SPECIAL_CHARS'], 400);
      closepage();
    }
     
    $user->SetFields(array('email' => addslashes($args['email'])));
    if (! $user->Update())
    {
      gui_button_error($user->GetError(), 300);
      closepage();
    }

    gui_button($lang['GUI_UPDATE_OK'], 300);
  }
} 

if(isset($args['upavatar'])  && !isset($args['delavatar']))
{
  $up_dir = ROOT_PATH. $config['avatar_dir'];
  $tmp_dir = ROOT_PATH. $config['tmp_dir'];
  $tmp_file = tempnam($tmp_dir, 'av_');
  if (!$tmp_file)
  {
    gui_button_error("Error on creating temp file.", 300);
    closepage();
  }
  else {

  $f = new FileManager();
  $ret = $f->Upload($_FILES, 'uploadfile', $tmp_dir, basename($tmp_file), true);

  if(!$ret)
  {
    gui_button_error($f->GetError(), 500);
    closepage();
  }
  
    $picprop = getimagesize($tmp_file);
    if (!$picprop)
    {
      gui_button_error("Error getting image attributes.", 400);
      $ret = $f->Unlink();
      if(!$ret)
         gui_button_error($f->GetError(), 500);
      closepage();
    }
    else
    {
      /* Vérification des limites */
      if ($picprop[0] > $config['avatar_width_max']
        ||$picprop[1] > $config['avatar_height_max']
         )
      {
        gui_button_error("Image too large, please use size < 128x128.", 400);
        closepage();
      }
      else {
        /* effacement du fichier ancien si existant */
        $old_avatar = $user->GetAvatar();
        if (!empty($old_avatar))
        {
          $oldf = new FileManager($config['avatar_dir'].$old_avatar);
          $oldf->Unlink();
        }
        
        /* copie du fichier définitif */
        $new_name = strtolower(md5($user->GetPseudo().$user->GetId()) .".".$imagetypes[$picprop[2]]);
        $f->Move($up_dir, $new_name, true);

        gui_button($lang['GUI_UPLOAD_OK'], 400);

        /* mise à jour profil */

       $user->SetFields(array(
            "user_avatar"  => $new_name,
        ));

       $user->Update();

      } /* Vérifications */
    } /* getimagesize () */
  } /* tempnam() */
  
  gui_button_main_page();
}


if(isset($args['upinfos']))
{
   $val = CheckLimitsInfo($args);
   if ($val == false)
   {
     gui_button_back();
     closepage();
   }
   
   $user->SetFields($val);
   if($user->Update())
   {
      gui_button($lang['GUI_UPDATE_OK'], 400);
   }
   else
   {
      gui_button_error($user->GetError(), 400);
      gui_button_back();
      closepage();
   }
}

if(isset($args['upoptions']))
{
   $val = CheckLimitsOptions($args);
   if ($val == false)
     closepage();
   $user->SetFields($val);

  $user->SetFields($val);
   if (! $user->Update())
   {
     gui_button_error($user->GetError(), 300);
     closepage();
   }
   else
   {
      gui_button($lang['GUI_UPDATE_OK'], 300);
      /* rafraichit le cache des options */
   }
}


/*
   AFFICHAGE DE LA PAGE
*/


  $table->dialog->UserProfile($user);

  /* Identification  */
  $form = new Form("post", "editprofile.php?upident&amp;id=".$user->GetId(), "password", 600);
  $form->AddTitle($lang['PROFILE_FORM_IDENT_TITLE']);
  $form->AddInputText("pseudo", "pseudo", $lang['PROFILE_FORM_IDENT_PSEUDO'], 20, $user->GetPseudo(), "readonly");
  $form->Br();
  $form->AddInputText("email", "email", $lang['PROFILE_FORM_IDENT_MAIL'], 20, $user->GetMail());
  $form->Br();
  $form->AddInputSubmit();
  echo $form->End();


  /* Personal infos */
  $form = new Form("post", "editprofile.php?upinfos&amp;id=".$user->GetId(), "personnal", 600);
  $form->AddTitle($lang['PROFILE_FORM_INFO_TITLE']);
  $form->Br();
  $form->AddLine($lang['PROFILE_FORM_INFO_INFO']);
  $form->AddInputText("user_localisation", "user_localisation", $lang['PROFILE_FORM_INFO_LOCAL'], 35, $user->GetLocalisation(), 'maxlength="40"');
  $form->Br();
  $form->AddInputText("user_web", "user_web", $lang['PROFILE_FORM_INFO_WEB'], 35, $user->GetWeb(), 'maxlength="80"');
  $form->Br();
  $form->AddTextArea("user_speech", "user_speech", $lang['PROFILE_FORM_INFO_SPEECH'], 4, $user->GetSpeech());
  $form->Br();
  $form->AddInputSubmit();
  echo $form->End();

  
  /* Avatar */
  $form = new Form("post", "editprofile.php?upavatar&amp;id=".$user->GetId(), "avatar_form", 600, "multipart/form-data");
  $form->AddTitle($lang['PROFILE_FORM_AVATAR_TITLE']);
  $form->Br();
  $form->AddLine(sprintf($lang['PROFILE_FORM_AVATAR_LIMITS'], $config['avatar_width_max'], $config['avatar_height_max'], $config['avatar_size_max']/1024 ));
  $form->AddInputFile("uploadfile", "uploadfile", $lang['PROFILE_FORM_AVATAR_FILE'], 40);
  $form->AddInputHidden("size_max", "MAX_FILE_SIZE", "", 0, $config['avatar_size_max']);
  $form->Br();
  $form->AddInputSubmit($lang['PROFILE_FORM_AVATAR_DEL'], 'delavatar');
  $form->Br();
  $form->AddInputSubmit();
  echo $form->End();

  /* Options */
  $form = new Form("post", "editprofile.php?upoptions&amp;id=".$user->GetId(), "options_form", 600);
  $form->AddTitle($lang['OPTIONS_FORM_TITLE']);

  $form->Br();
  $form->AddSelect("sort", "sort", $sort_type, $lang['OPTIONS_FORM_SORT']  );
  $form->Br();
  $form->AddSelect("theme", "theme", $themes, $lang['OPTIONS_FORM_THEME']  );
  $form->Br();
  $form->AddInputText("limit", "limit", $lang['OPTIONS_FORM_LIMIT'], 5, $user->GetLimit());
  $form->Br();
  $form->AddInputText("sidebar_comments", "sidebar_comments", $lang['OPTIONS_FORM_SIDEBAR_COMMENTS'], 5, $user->GetSidebarComments());
  $form->Br();
  $form->AddInputText("sidebar_comlength", "sidebar_comlength", $lang['OPTIONS_FORM_SIDEBAR_COMLENGTH'], 5, $user->GetSidebarComLength());
  $form->Br();
  $form->AddInputSubmit();
  echo $form->End();

  echo '<script>';
    echo "change_form_select('options_form', 'sort',  '".$user->GetSort()."');";
    echo "change_form_select('options_form', 'theme',  '".$user->GetTheme()."');";
  echo '</script>';

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
