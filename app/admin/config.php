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

define('ROOT_PATH', dirname(dirname(__DIR__)) . '/');
define('NVRTBL', 1);
include_once ROOT_PATH ."config.inc.php";
include_once ROOT_PATH ."includes/common.php";
include_once ROOT_PATH ."includes/classes.php";


//args process
$args = get_arguments($_POST, $_GET);

try {
	
$table = new Nvrtbl();

if (!Auth::Check(get_userlevel_by_name("root")))
  throw new Exception($lang['NOT_ROOT']);


function CheckConfig($conf_arr)
{ 
  $err = true;

  if ( $conf_arr['nvtbl_path'][strlen($conf_arr['nvtbl_path'])-1] !== '/'
    || $conf_arr['replay_dir'][strlen($conf_arr['replay_dir'])-1] !== '/'
    || $conf_arr['image_dir'][strlen($conf_arr['image_dir'])-1] !== '/'
    || $conf_arr['shot_dir'][strlen($conf_arr['shot_dir'])-1] !== '/'
    || $conf_arr['smilies_dir'][strlen($conf_arr['smilies_dir'])-1] !== '/'
    || $conf_arr['theme_dir'][strlen($conf_arr['theme_dir'])-1] !== '/'
    || $conf_arr['avatar_dir'][strlen($conf_arr['avatar_dir'])-1] !== '/'
    || $conf_arr['cache_dir'][strlen($conf_arr['cache_dir'])-1] !== '/'
    || $conf_arr['tmp_dir'][strlen($conf_arr['tmp_dir'])-1] !== '/'
    || $conf_arr['cookie_path'][strlen($conf_arr['cookie_path'])-1] !== '/'
     )
  {
    throw new Exception("Directories name have to finish by a");
    return false;
  }

  $err = $err & CheckLimitInterval($conf_arr['limit'], 0, 100, 'limit' );
  $err = $err & CheckLimitInterval($conf_arr['sidebar_comments'], 0, 30, 'sidebar_comments' );
  $err = $err & CheckLimitInterval($conf_arr['sidebar_comlength'], 0, 300, 'sidebar_comlength' );
  $err = $err & CheckLimitInterval($conf_arr['sidebar_autowrap'], 0, 50, 'sidebar_autowrap' );
  $err = $err & CheckLimitInterval($conf_arr['upload_size_max'], 1024, 10485760, 'upload_size_max' );
  $err = $err & CheckLimitInterval($conf_arr['avatar_size_max'], 1024, 50*1024, 'avatar_size_max' );
  $err = $err & CheckLimitInterval($conf_arr['avatar_width_max'], 8, 512, 'avatar_width_max' );
  $err = $err & CheckLimitInterval($conf_arr['avatar_height_max'], 8, 512, 'avatar_height_max' );
  $err = $err & CheckLimitInterval($conf_arr['profile_quote_max'], 30, 10000, 'profile_quote_max' );
  $err = $err & CheckLimitInterval($conf_arr['tag_maxsize'], 1, 1024, 'tag_maxsize' );
  $err = $err & CheckLimitInterval($conf_arr['tag_limit'], 1, 50, 'tag_limit' );
  $err = $err & CheckLimitInterval($conf_arr['tag_flood_limit'], 1, 3600, 'tag_flood_limit' );

  return $err;
}
  
if (isset($args['upconfig']))
{
  $ok=CheckConfig($_POST);
  if ($ok)
  {
    foreach ($_POST AS $c => $v)
    {
      $table->db->NewQuery("UPDATE", "conf");
      $table->db->UpdateSet(array("conf_value" => $v));
      $table->db->Where("conf_name", $c);
      $table->db->Limit(1);
      $table->db->Query();
    }

    $tpl_params['message_array'] = array();
    array_push( $tpl_params['message_array'], $lang['GUI_UPDATE_OK']);
    $tpl_params['redirect'] = "config.php";
    $tpl_params['delay'] = 2;
    $table->template->Show('redirect', $tpl_params);
  }
}

else
{
  $table->db->NewQuery("SELECT", "conf");
  
  $table->template->Show('admin/config', array('config_res' => $table->db->Query()));
}


}
catch (Exception $ex) {
	$table->template->Show('error', array("exception" => $ex)); 
}

$table->Close();

?>