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
  
if (isset($args['reallypurge']))
{
  $table->db->RequestInit("SELECT", "rec");
  $table->db->RequestGenericFilter("folder", get_folder_by_name("trash"));
  $res = $table->db->Query();
  if(!$res)
    button_error($this->db->GetError());
  else
  {
    $rec = new Record($table->db);
    while($val = $table->db->FetchArray($res))
    {
      $rec->LoadFromId($val['id']);
      $rec->Purge(true); /* avec fichier attaché */
      button("Record #".$val['id']." totally erased.", 200);
    }
  }
}

else
{
    button("<b>Are tou sure you want to purge all trash folder ?</b>", 500);
    button("<a href=\"?reallypurge\"  style=\"color: red;\">YES</a>", 100);
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
