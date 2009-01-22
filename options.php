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
define('NVRTBL', 1);
include_once ROOT_PATH ."config.inc.php";
include_once ROOT_PATH ."includes/common.php";
include_once ROOT_PATH ."includes/classes.php";

//args process
$args = get_arguments($_POST, $_GET);

try {

$table = new Nvrtbl();

if (!Auth::Check(get_userlevel_by_name("member")))
  throw new Exception($lang['NOT_MEMBER']);
  
$tpl_params = array();
$tpl_params['message_array'] = array();

  
$user = new User($table->db);
$user->LoadFromId($_SESSION['user_id']);

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

if(isset($args['upoptions']))
{
   $val = CheckLimitsOptions($args);
   if ($val == false)
     throw new Exception("A parmameter is outbounds");

   $user->SetFields($val);
   $user->Update();
   
   $_SESSION['options_saved'] = false;

   array_push($tpl_params['message_array'], $lang['GUI_UPDATE_OK']);
   $tpl_params['delay'] = 2;
   $tpl_params['redirect'] = "options.php";
   $table->template->Show('redirect', $tpl_params);
}

else
{
	$tpl_params['user'] = $user;
	$table->template->Show('options', $tpl_params);
}

$table->Close();

}
catch (Exception $ex)
{
  $table->template->Show('error', array("exception" => $ex));
}
