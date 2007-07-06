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

//args process
$args = get_arguments($_POST, $_GET);

$table = new Nvrtbl("DialogStandard");

if (!isset($args['dlcontest']) && !isset($args['dloldones'])  && !isset($args['list']))
{
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
$table->dialog->Prelude();

  echo '<center><h1>Download all replay files</h1></center>' ;
  echo '<center><p><a href="?dlcontest">contest.lst</a></center>';
  echo '<center><p><a href="?dloldones">oldones.lst</a></center>';
  echo '<center><h1>List records</h1></center>' ;
  echo '<center><p><a href="?list&amp;folder='.get_folder_by_name("contest").'">contest.txt</a></center>';
  echo '<center><p><a href="?list&amp;folder='.get_folder_by_name("oldones").'">oldones.txt</a></center>';

gui_button_main_page_admin();

?>
</div><!-- fin "main" -->

<?php
/* Close avant le footer, car db inutile et pour les statistiques de temps */
$table->Close();
$table->dialog->Footer();
?>

</div><!-- fin "page" -->
</body>
</html>

<?php
}
else
{
header("Content-Type: text/plain");
if(isset($args['dlcontest']))
{
 $lst = "";
	
 $table->db->Select(array("rec"), array("*"));
 $table->db->requestGenericFilter("folder", get_folder_by_name("contest"));
 $table->db->Query();
 while ($val = $table->db->FetchArray())
 {
	if (!empty($val['replay']))
	  $lst .= replay_link($val['folder'], $val['replay']) . "\n";
 }

 echo $lst;
 exit;
}

if(isset($args['dloldones']))
{
 $lst = "";
	
 $table->db->Select(array("rec"), array("*"));
 $table->db->requestGenericFilter("folder", get_folder_by_name("oldones"));
 $table->db->Query();
 while ($val = $table->db->FetchArray())
 {
	if (!empty($val['replay']))
	  $lst .= replay_link($val['folder'], $val['replay']) . "\n";
 }

 echo $lst;
 exit;
}

if(isset($args['list']))
{
 echo $table->GetStatsDump($args['folder']);
 exit;
}
}
