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

$table = new Nvrtbl($config, "DialogAdmin");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<?php


$table->PrintHtmlHead("Nevertable - Neverball Hall of Fame");
?>

<body>
<div id="page">
<?php   $table->PrintTop();  ?>
<div id="main">
<?php
if (!Auth::Check(get_userlevel_by_name("admin")))
{
  button_error("You have to be admin to access this page.", 400);
  exit;
}

function CheckConfig($conf_arr)
{ 
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
    button_error("Directories name have to finish by a /", 300); return false;
  }

  if ( ($conf_arr['limit'] < 0) || ($conf_arr['limit'] > 100) )
    { button_error("\"limit\" limits are [1..100]", 300); return false; }
    
  if ( ($conf_arr['sidebar_comments'] < 0) || ($conf_arr['sidebar_comments'] > 30) )
    { button_error("\"sidebar_comments\" limits are [1..30]", 300); return false; }
    
  if ( ($conf_arr['sidebar_comlength'] < 0) || ($conf_arr['sidebar_comlength'] > 300) )
    { button_error("\"sidebar_comlength\" limits are [1..300]", 300); return false; }
    
  if ( ($conf_arr['sidebar_autowrap'] < 0) || ($conf_arr['sidebar_autowrap'] > 50) )
    { button_error("\"sidebar_autowrap\" limits are [1..50]", 300); return false; }
    
  if ( ($conf_arr['upload_size_max'] < 1024) || ($conf_arr['upload_size_max'] > 10485760) )
    { button_error("\"upload_size_max\" limits are [1024..10485760]", 300); return false; }

  if ( ($conf_arr['avatar_size_max'] < 1000) || ($conf_arr['avatar_size_max'] > 500000) )
    { button_error("\"avatar_size_max\" limits are [1000..500000]", 300); return false; }

  if ( ($conf_arr['avatar_width_max'] < 8) || ($conf_arr['avatar_width_max'] > 512) )
    { button_error("\"avatar_width\" limits are [8..512]", 300); return false; }

  if ( ($conf_arr['avatar_height_max'] < 8) || ($conf_arr['avatar_height_max'] > 512) )
    { button_error("\"avatar_height_max\" limits are [8..512]", 300); return false; }

  if ( ($conf_arr['profile_quote_max'] < 30) || ($conf_arr['profile_quote_max'] > 10000) )
    { button_error("\"profile_quote_max\" limits are [30..10000]", 300); return false; }

  if ( ($conf_arr['profile_quote_max'] < 30) || ($conf_arr['profile_quote_max'] > 10000) )
    { button_error("\"profile_quote_max\" limits are [30..10000]", 300); return false; }

  return true;
}
  
if (isset($args['upconfig']))
{
  $ok=CheckConfig($_POST);
  if ($ok)
  {
    foreach ($_POST AS $c => $v)
    {
      $table->db->RequestInit("UPDATE", "conf");
      $table->db->RequestUpdateSet(array("conf_value" => $v));
      $table->db->RequestGenericFilter("conf_name", $c);
      $table->db->RequestLimit(1);
      if(!$table->db->Query())
      {
        button_error($table->db->GetError(), 400);
        $ok=false;
        break;
      }
    }
  }

  if ($ok)
    button("Configuration successfully updated.", 200);
    button("<a href=\"config.php\">Return to config panel</a>", 400);
}

else
{
  $table->db->RequestInit("SELECT", "conf");
  if(!$table->db->Query())
  {
    button_error($table->db->GetError(), 400);
    return false;
  }
?>
  <form class="nvform" method="post" action="config?upconfig" style="width: 700px;">
  <table>
  <tr><th>Table configuration</th></tr>
  <tr>
<?php while ($val = $table->db->FetchArray())
   {
     echo "<td>".$val['conf_name']."</td>\n";
     echo "<td><input type=\"text\" id=\"".$val['conf_name']."\" name=\"".$val['conf_name'].
                "\" size=\"30\" value=\"".$val['conf_value']."\"/></td>\n";
     echo "<td>".$val['conf_desc']."</td>\n";
     echo "<tr>\n";
   }
?>  
  <tr><td colspan="3"><center><input type="submit" value="Apply"></center></td></tr>
  </table>
  </form>

<?php
}
button("<a href=\"admin.php\">Return to admin panel</a>", 400);
?>
</div> <!-- fin main-->
<?php
$table->Close();
$table->PrintFooter();
?>

</div><!-- fin "page" -->
</body>
</html>
