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
<?php   $table->dialog->Top();  ?>
<div id="main">
<?php

function closepage()
{  global $table;
    gui_button_back();
    gui_button_return("Profile", "profile.php");
    echo "</div><!-- fin \"main\" -->\n";
    $table->Close();
    $table->dialog->Footer();
    echo "</div><!-- fin \"page\" -->\n</body>\n</html>\n";
    exit;
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

/*
   TRAITEMENT DES EVENEMENTS 
*/

if (isset($args['upident']))
{
  if (!empty($args['email']) && $args['email'] != $user->GetMail())
  {
    $table->db->helper->SelectUserByMail($args['email']);
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
  if (!empty($args['passwd1']) && !empty($args['passwd2']) )
  {
    if ($args['passwd1'] != $args['passwd2'])
    {
      gui_button_error($lang['REGISTER_PASSWD_CHECK'], 400);
      closepage();
    }
    else
    {
      //mise à jour du nouveau mot de passe
      $user->SetFields(array('passwd' => md5($args['passwd1'])));
      if (! $user->Update())
      {
        gui_button_error($user->GetError(), 300);
        closepage();
      }

      gui_button($lang['GUI_UPDATE_OK'], 300);
    }
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

  $f = new FileManager();
  $ret = $f->Upload($_FILES, 'uploadfile', $tmp_dir, basename($tmp_file), true);

  if(!$ret)
  {
    gui_button_error($f->GetError(), 500);
    closepage();
  }

  $p = new Photo($f->GetFileName());
  if (!$p->Init())
  {
      $f->Unlink();
      gui_button_error($p->GetError(), 500);
      closepage();
  }

  /* Vérification des limites */
  if ($p->GetWidth() > $config['avatar_width_max'] || $p->GetHeight > $config['avatar_height_max'] )
  {
      $factor = $p->GetWidth() / $p->GetHeight();
      $factor_config = $config['photo_width_max'] / $config['avatar_height_max'];
      if ($factor > $factor_config)
        $ret = $p->Resize($config['avatar_width_max'], floor($p->GetHeight()*$config['avatar_width_max']/$p->GetWidth()));
      else
        $ret = $p->Resize(floor($p->GetWidth()*$config['avatar_height_max']/$p->GetHeight()), $config['avatar_height_max']);
      if (!$ret)
      {
        $f->Unlink();
        gui_button_error($p->GetError(), 500);
        closepage();
      }

      gui_button($lang['PROFILE_AVATAR_RESIZE_OK'], 400);
  }
  $final_name = $f->GetHash() . '.' .  strtolower($p->GetFormat());
  if (!$f->Move($up_dir,  $final_name, true))
  {
     $f->Unlink();
     gui_button_error($f->GetError(), 500);
     closepage();
  }
  $user->SetFields(array(
        "user_avatar"  => $final_name,
  ));
  $user->Update();

}

if (isset($args['upavatar']) && $args['delavatar'])
{ 
  /* Effacement de la photo actuelle */
  $oldf = new FileManager($config['avatar_dir'].$user->GetAvatar());
  $oldf->Unlink();
  $user->SetFields(array(
        "user_avatar"  => "",
    ));
  $user->Update();
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

/*
   AFFICHAGE DE LA PAGE
*/

  $table->dialog->UserProfile($user);


  /* Identification  */
  $form = new Form("post", "profile.php?upident", "password", 600);
  $form->AddTitle($lang['PROFILE_FORM_IDENT_TITLE']);
  $form->AddInputText("pseudo", "pseudo", $lang['PROFILE_FORM_IDENT_PSEUDO'], 20, $user->GetPseudo(), "readonly");
  $form->Br();
  $form->AddInputText("email", "email", $lang['PROFILE_FORM_IDENT_MAIL'], 20, $user->GetMail());
  $form->Br();
  $form->AddInputPassword("passwd1", "passwd1", $lang['PROFILE_FORM_IDENT_PASSWD1']);
  $form->Br();
  $form->AddInputPassword("passwd2", "passwd2", $lang['PROFILE_FORM_IDENT_PASSWD2']);
  $form->Br();
  $form->AddInputSubmit();
  echo $form->End();


  /* Personal infos */
  $form = new Form("post", "profile.php?upinfos", "personnal", 600);
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
  $form = new Form("post", "profile.php?upavatar", "avatar_form", 600, "multipart/form-data");
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
  
gui_button_main_page();
?>
</div> <!-- fin main-->
<?php
$table->Close();
$table->dialog->Footer();
?>

</div><!-- fin "page" -->
</body>
</html>
