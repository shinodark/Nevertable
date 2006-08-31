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

$table = new Nvrtbl($config, "DialogStandard");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<?php $table->PrintHtmlHead("Nevertable - Neverball Hall of Fame"); ?>

<body>
<div id="page">
<?php   $table->PrintTop();  ?>
<div id="main">
<div id="prelude">
<?php 
$conditions  = ROOT_PATH . "conditions.txt";
if (file_exists($conditions))
{
  $line = file_get_contents($conditions);
  echo $line . "<br />\n";
}

?>
</div><!-- fin "prelude" -->

<?php button_back();?>

</div><!-- fin "main" -->

<?php
/* Close avant le footer, car db inutile et pour les statistiques de temps */
$table->Close();
$table->PrintFooter();
?>

</div><!-- fin "page" -->
</body>
</html>
