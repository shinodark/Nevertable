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

define('ROOT_PATH', dirname(__FILE__) . '/');
define('NVRTBL', 1);
include_once ROOT_PATH ."config.inc.php";
include_once ROOT_PATH ."includes/common.php";
include_once ROOT_PATH ."includes/classes.php";

//args process
$args = get_arguments($_POST, $_GET);


$table = new Nvrtbl();

try {
	 
$edit_enable = false;
$cur_user_id = -1;

function CheckEditEnable()
{
	global $cur_user_id, $args;
	
	/* Guest viewer */
	if (!Auth::Check(get_userlevel_by_name("member")))
	{
	    $cur_user_id = $args['id'];
        return false;
	}
	
	if (!isset($args['id']))
	{
		/* a user edits his own profile */
		$cur_user_id =  $_SESSION['user_id'];
		return true;
	}
	else
	{
		if (!Auth::Check(get_userlevel_by_name("admin")) && isset($args['id']) && ($_SESSION['user_id'] != $args['id']) )
		{
			/* View only */
			$cur_user_id = $args['id'];
			return false;
		}
		else if (!Auth::Check(get_userlevel_by_name("admin")) && isset($args['id']) && ($_SESSION['user_id'] == $args['id']))
		{
			/* a user edits his own profile */
			$cur_user_id =  $_SESSION['user_id'];
			return true;
		}
		else if (Auth::Check(get_userlevel_by_name("admin")))
		{
			/* admin can edit all users*/
			$cur_user_id = $args['id'];
			return true;
		}
	}
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

$edit_enable = CheckEditEnable();

$tpl_params = array();
$tpl_params['message_array'] = array();
  
$user = new User($table->db);
$user->LoadFromId($cur_user_id);


/*
   TRAITEMENT DES EVENEMENTS 
*/

if (isset($args['upident']))
{
  if(!$edit_enable)
  	throw new Exception("WTF?");
  	
  if (!empty($args['email']) && $args['email'] != $user->GetMail())
  {
    $table->db->helper->SelectUserByMail($args['email']);
    if ($table->db->NumRows() > 0) // dejà existant
      throw new Exception($lang['REGISTER_MAIL_EXISTS']);

    if (!CheckMail($args['email']))
      throw new Exception($lang['REGISTER_MAIL_NOTVALID']);

    if ((addslashes($args['email']) != $args['email']) )
      throw new Exception($lang['REGISTER_SPECIAL_CHARS']);
     
    $user->SetFields(array('email' => addslashes($args['email'])));
    $user->Update();

  }
  if (!empty($args['passwd1']) && !empty($args['passwd2']) )
  {
    if ($args['passwd1'] != $args['passwd2'])
    	throw new Exception($lang['REGISTER_PASSWD_CHECK']);

    else
    {
      //mise à jour du nouveau mot de passe
      $user->SetFields(array('passwd' => Auth::Hash($args['passwd1'])));
      $user->Update();
    }
  }
  
  array_push($tpl_params['message_array'], $lang['GUI_UPDATE_OK']);
  $tpl_params['delay'] = 2;
  $tpl_params['redirect'] = "profile.php?id=".$user->GetId();
  $table->template->Show('redirect', $tpl_params);
}       

else if(isset($args['upavatar'])  && !isset($args['delavatar']))
{
  if(!$edit_enable)
  	throw new Exception("WTF?");
  	
  $up_dir = ROOT_PATH. $config['avatar_dir'];
  $tmp_dir = ROOT_PATH. $config['tmp_dir'];
  $tmp_file = tempnam($tmp_dir, 'av_');
  if (!$tmp_file)
  	throw new Exception("Error on creating temp file.");


  $f = new FileManager();
  $f->Upload($_FILES, 'uploadfile', $tmp_dir, basename($tmp_file), true);

  $p = new Photo($f->GetFileName());
  try {
  	$p->Init();
  }
  catch (Exception $ex)
  {
      $f->Unlink();
      throw $ex;
  }

  /* Vérification des limites */
  if ($p->GetWidth() > $config['avatar_width_max'] || $p->GetHeight > $config['avatar_height_max'] )
  {
      $factor = $p->GetWidth() / $p->GetHeight();
      $factor_config = $config['avatar_width_max'] / $config['avatar_height_max'];
      try {
	      if ($factor > $factor_config)
	        $ret = $p->Resize($config['avatar_width_max'], floor($p->GetHeight()*$config['avatar_width_max']/$p->GetWidth()));
	      else
	        $ret = $p->Resize(floor($p->GetWidth()*$config['avatar_height_max']/$p->GetHeight()), $config['avatar_height_max']);
      }
      catch (Exception $ex)
      {
        $f->Unlink();
        throw $ex;
      }
      
      array_push($tpl_params['message_array'], $lang['PROFILE_AVATAR_RESIZE_OK']);
  }
  $final_name = $f->GetHash() . '.' .  strtolower($p->GetFormat());
  try {
     $f->Move($up_dir,  $final_name, true);
  }
  catch (Exception $ex)
  {
  	  $f->Unlink();
      throw $ex;
  }

  $user->SetFields(array(
        "user_avatar"  => $final_name,
  ));
  $user->Update();
  
  array_push($tpl_params['message_array'], $lang['GUI_UPDATE_OK']);
  $tpl_params['delay'] = 2;
  $tpl_params['redirect'] = "profile.php?id=".$user->GetId();
  $table->template->Show('redirect', $tpl_params);
}

else if (isset($args['upavatar']) && $args['delavatar'])
{ 
  if(!$edit_enable)
  	throw new Exception("WTF?");
  	
  /* Effacement de la photo actuelle */
  $oldf = new FileManager($config['avatar_dir'].$user->GetAvatar());
  $oldf->Unlink();
  $user->SetFields(array(
        "user_avatar"  => "",
    ));
  $user->Update();
  
  array_push($tpl_params['message_array'], $lang['GUI_UPDATE_OK']);
  $tpl_params['delay'] = 2;
  $tpl_params['redirect'] = "profile.php?id=".$user->GetId();
  $table->template->Show('redirect', $tpl_params);
}


else if(isset($args['upinfos']))
{
   if(!$edit_enable)
     throw new Exception("WTF?");
  	
   $val = CheckLimitsInfo($args);
   if ($val == false)
     throw new Exception("A parmameter is outbounds");
   
   $user->SetFields($val);
   $user->Update();
   
  array_push($tpl_params['message_array'], $lang['GUI_UPDATE_OK']);
  $tpl_params['delay'] = 2;
  $tpl_params['redirect'] = "profile.php?id=".$user->GetId();
  $table->template->Show('redirect', $tpl_params);
}

else 
{
  $tpl_params['user'] = $user;
  $tpl_params['edit_enable'] = $edit_enable;
  $table->template->Show('profile', $tpl_params);
}
  
$table->Close();

}
catch (Exception $ex)
{
  $table->template->Show('error', array("exception" => $ex));
}