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

if($args['to'] == 'showlinklist')
{
 header("Content-Type: text/plain");
 echo $_SESSION['download_list'];
 exit;
}

$table = new Nvrtbl("DialogStandard");

if(isset($args['link']))
  $special="<meta http-equiv=\"refresh\" content=\"0;URL=record.php?id=".$args['link']."\" />\n";
else
    $special="";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<?php $table->PrintHtmlHead("Nevertable - Neverball Hall of Fame", $special); ?>

<body>
<div id="page">
<?php   $table->PrintTop();  ?>
<div id="main">
<?php
$table->PrintPrelude();

/***************************************************/
/* ----------- AFFICHAGE   ------------------------*/
/***************************************************/
if (isset($args['link']))
{
  button("Redirecting...", 100);
}

else 
{
  if (isset($args['type'])) $nextargs .= "?type=".$args['type'];
  if (isset($args['sort'])) $nextargs .= "&amp;sort=".$args['sort'];
  if (isset($args['diffview'])) $nextargs .= "&amp;diffview=".$args['diffview'];
  if (isset($args['newonly'])) $nextargs .= "&amp;newonly=".$args['newonly'];
  if (isset($args['filter'])) $nextargs .= "&amp;filter=".$args['filter'];
  if (isset($args['filterval'])) $nextargs .= "&amp;filterval=".$args['filterval'];
  if (isset($args['levelset_f'])) $nextargs .= "&amp;levelset_f=".$args['levelset_f'];
  if (isset($args['level_f'])) $nextargs .= "&amp;level_f=".$args['level_f'];
  if (isset($args['folder'])) $nextargs .= "&amp;folder=".$args['folder'];

  $table->Show($args);
}


?>

</div><!-- fin "main" -->

<?php
/* Close avant le footer, car db inutile et pour les statistiques de temps */
$table->Close();
$table->PrintFooter();
?>

</div><!-- fin "page" -->
</body>
</html>
