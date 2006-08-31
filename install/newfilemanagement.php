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

session_start();
//args process
$args = get_arguments($_POST, $_GET);

$table = new Nvrtbl($config, "DialogStandard");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<?php $table->PrintHtmlHead("Nevertable - Neverball Hall of Fame", "../main.css"); ?>

<body>
<div id="page">
<div id="main">
<?php

  /* ouvre tous les records */
  $table->db->RequestInit("SELECT", "rec");
  $res = $table->db->Query();

  $rec = new Record($table->db);
  /* Parcours */
  while($val = $table->db->FetchArray($res))
  {
    $rec->LoadFromId($val['id']);
    $oldreplay = $rec->GetReplay();
    $newreplay = basename($oldreplay)  ;
    $rec->SetFields(array("replay" => $newreplay));
    $rec->Update(true);
    echo "Record #".$val['id']." : ".$oldreplay. " => " . $newreplay . "<br/>\n";
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
