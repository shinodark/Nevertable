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
  
if (isset($args['upannounce']))
{
    file_put_contents(ROOT_PATH . "announce.txt", stripslashes($args['announce']));
    button("Announcement updated.", 200);
}

if (isset($args['upspeech']))
{
    file_put_contents(ROOT_PATH . "speech.txt", stripslashes($args['speech']));
    button("Speech updated.", 200);
}

if (isset($args['upconditions']))
{
    file_put_contents(ROOT_PATH . "conditions.txt", stripslashes($args['conditions']));
    button("Conditions updated.", 200);
}

else
{
  if (file_exists(ROOT_PATH . "announce.txt"))
    $cur_announce = file_get_contents(ROOT_PATH . "announce.txt");
  if (file_exists(ROOT_PATH . "speech.txt"))
    $cur_speech = file_get_contents(ROOT_PATH . "speech.txt");
  if (file_exists(ROOT_PATH . "conditions.txt"))
    $cur_conditions = file_get_contents(ROOT_PATH . "conditions.txt");
    
?>
  <form class="nvform" method="post" action="management?upannounce" style="width: 700px;">
  <table><tr>
  <th>Announcement  Editor</th></tr>
  <tr><td>
  <textarea name="announce" rows="5" cols="90"><?php echo $cur_announce ?></textarea>
  </td></tr><tr><td>
  <center><input type="submit" value="Edit" /></center>
  </td></tr>
  </table>
  </form>

  <form class="nvform" method="post" action="management?upspeech" style="width: 700px;">
  <table><tr>
  <th>Speech  Editor</th></tr>
  <tr><td>
  <textarea name="speech" rows="20" cols="90"><?php echo $cur_speech ?></textarea>
  </td></tr><tr><td>
  <center><input type="submit" value="Edit" /></center>
  </td></tr>
  </table>
  </form>

  <form class="nvform" method="post" action="management?upconditions" style="width: 700px;">
  <table><tr>
  <th>Conditions  Editor</th></tr>
  <tr><td>
  <textarea name="conditions" rows="60" cols="90"><?php echo $cur_conditions ?></textarea>
  </td></tr><tr><td>
  <center><input type="submit" value="Edit" /></center>
  </td></tr>
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
