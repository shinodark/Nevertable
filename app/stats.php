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

define('ROOT_PATH', dirname(__FILE__) . '/');
include_once ROOT_PATH ."config.inc.php";
include_once ROOT_PATH ."includes/common.php";
include_once ROOT_PATH ."includes/classes.php";

//args process
$args = get_arguments($_POST, $_GET);

$table = new Nvrtbl();
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

  /* check validity of liste.txt file used by neverstats applet */
  $f = new FileManager($config['neverstats_liste']);
  $stat = $f->Stat();
  $refresh = false;
  if ($stat != false)
  {
     $t1 = $stat['mtime'];
     $days=timestamp_diff_in_days(time(), $t1); 
     if ($days > 1)
        $refresh = true;
  }
  if ($f->Exists() == false) /* doesn't exist */
  {
     $refresh = true;
  }
  if ($refresh)
  {
    $lst = $table->GetStatsDump("contest");
    if (!$f->Write($lst))
    {
        gui_button_error("Write GestStatsDump : ".$f->GetError(), 300);
        return;
    }
  }

  $table->dialog->NeverStats();
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
