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

$table = new Nvrtbl("DialogStandard");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<?php $table->dialog->Head("Nevertable - Neverball Hall of Fame"); ?>

<body>
<div id="page">
<?php 
  $table->dialog->Top();
  $table->dialog->Prelude();
?>
<div id="main">
<?php

  $table->db->RequestInit("SELECT", "users");
  /* Contre le problème du osrt=0 si aucun get n'est passé */
  if (!isset($_GET['sort']))
     $args['sort'] = 2;
  switch($args['sort'])
  {
     case 0: $table->db->RequestGenericSort(array("pseudo"), "ASC"); break;
     case 1: $table->db->RequestGenericSort(array("stat_total_records", "stat_best_records"), array("DESC", "DESC"));
         break;
     default: 
     case 2: $table->db->RequestGenericSort(array("stat_best_records", "stat_total_records"), array("DESC", "DESC"));
         break;
     case 3: $table->db->RequestGenericSort(array("stat_comments"), "DESC"); break;
     case 4: $table->db->RequestGenericSort(array("level"), "ASC"); break;
     case 5: $table->db->RequestGenericSort(array("id"), "ASC"); break;
  }
  $res = $table->db->Query();
  if(!$res) {
      echo gui_button_error(  $table->db->GetError(), 400);
  }
  else
  {
    $table->dialog->MemberList($res);
  }

  gui_button_main_page();
?>
</div> <!-- fin main-->
<?php
$table->Close();
$table->dialog->Footer();
?>

</div><!-- fin "page" -->
</body>
</html>
