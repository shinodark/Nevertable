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

$table = new Nvrtbl($config, "DialogStandard");

if (isset($args['out']))
{
  if ($_SESSION['user_logged'])
    $was_logged=true;

  Auth::CloseSession();
  $table->RemoveOnlineUser();
  $special="<meta http-equiv=\"refresh\" content=\"1;URL=./\" />\n";
  $table->PrintHtmlHead("Nevertable - Neverball Hall of Fame", $special);
}
else
{
  $auth = new Auth($table->db);
  $success=$auth->Perform($args['pseudo'], $args['passwd'], false, true);
 
  if ($success)
  {
    $table->AddOnlineUser();
    
    if (!empty($_SESSION['redirect']))
    {
      $special="<meta http-equiv=\"refresh\" content=\"1;URL=".$_SESSION['redirect']."\" />\n";
      $_SESSION['redirect'] = "";
    }
    else
     $special="<meta http-equiv=\"refresh\" content=\"1;URL=./\" />\n";
  }
  $table->PrintHtmlHead("Nevertable - Neverball Hall of Fame", $special);
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>

<body>
<div id="page">
<?php   $table->PrintTop();  ?>
<div id="main">
<?php

if(isset($args['out']))
{
  if (!$was_logged)
    button_error("You're not logged in.", 400);
  else
    button("Logged out ! Redirecting to main page...", 400);
}
else if($success)
{
  button("Logged in ! Redirecting ...", 400);
}
else if(isset($args['ready']))
{
  ?>
  <div class="nvform" style="width: 300px;">
  <form action="login.php" method="post" name="login">
  <table>
  <tr>
  <th>Ready ?</th>
  </tr>
  <tr>
  <td><center><input type="text" id="pseudo" name="pseudo" size="10" value="Login" onfocus="if (this.value=='Login') this.value=''" /></center></td>
  </tr><tr>
  <td><center>&nbsp;&nbsp;<input type="password" id="passwd" name="passwd" size="10" value="passwd" onfocus="this.value=''" /></center></td>
  </tr><tr>
  <td><center><input type="submit" value="Go!" /></center><br/></td>
  </tr></table>
  </form>
  </div>
  <?php
  button("<a href=\"index.php\">Return to main page</a>", 300);
}
else 
{
  button_error("Auth failed. Try again...", 200);
  button_error($auth->GetError(), 200);
  button_back();
}
?>
</div><!-- fin "main" -->
<?php    
$table->Close();
$table->PrintFooter();
?>

</div><!-- fin "page" -->
</body>
</html>
