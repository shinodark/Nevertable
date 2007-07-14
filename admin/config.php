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
    gui_button_error("Directories name have to finish by a /", 300); return false;
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
      if(!$table->db->Query())
      {
        gui_button_error($table->db->GetError(), 400);
        $ok=false;
        break;
      }
    }
  }

  if ($ok)
  {
    gui_button("Configuration successfully updated.", 200);
  }
}

  $table->db->NewQuery("SELECT", "conf");
  if(!$table->db->Query())
  {
    gui_button_error($table->db->GetError(), 400);
    return false;
  }
  
  $form = new Form("post", "config.php?upconfig", "config_form", 700);
  $form->AddTitle($lang['ADMIN_CONFIG_FORM_TITLE']);
  while ($val = $table->db->FetchArray())
  {
     $option = 'onmouseover="return escape(\''.$val['conf_desc'].'\')" ';
     $form->AddInputText($val['conf_name'], $val['conf_name'], $val['conf_name'], 30, $val['conf_value'], $option);
     $form->Br();
  }
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
