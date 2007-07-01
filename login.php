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

if (isset($args['out']))
{
  if ($_SESSION['user_logged'])
    $was_logged=true;

  Auth::CloseSession();
  $table->RemoveOnlineUser();
  $special="<meta http-equiv=\"refresh\" content=\"1;URL=./\" />\n";
  $table->dialog->Head("Nevertable - Neverball Hall of Fame", $special);
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
  $table->dialog->Head("Nevertable - Neverball Hall of Fame", $special);
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>

<body>
<div id="page">
<?php   $table->dialog->Top();  ?>
<div id="main">
<?php

if(isset($args['out']))
{
  if (!$was_logged)
    gui_button_error($lang['LOGIN_NOTLOGIN'], 400);
  else
    gui_button($lang['LOGIN_LOGOUT'], 400);
}
else if($success)
{
  gui_button($lang['LOGIN_LOGIN'], 400);
}
else if(isset($args['ready']))
{
  $form = new Form("post", "login.php", "resgister", 300);
  $form->AddTitle($lang['LOGIN_FORM_TITLE']);
  $option = 'onfocus="if (this.value==\''.$lang['LOGIN_FORM_PSEUDO'].'\') this.value=\'\'" ';
  $form->AddInputText("pseudo", "pseudo", $lang['LOGIN_FORM_PSEUDO'], 10, $lang['LOGIN_FORM_PSEUDO'], $option);
  $form->Br();
  $option = 'onfocus=" this.value=\'\'" ';
  $form->AddInputPassword("passwd", "passwd", $lang['LOGIN_FORM_PASSWD'], 10, $lang['LOGIN_FORM_PASSWD'], $option);
  $form->Br();
  $form->AddInputSubmit("Login");
  echo $form->End();

  gui_button("<a href=\"index.php\">Return to main page</a>", 300);
}
else 
{
  gui_button_error("Auth failed. Try again...", 200);
  gui_button_error($auth->GetError(), 200);
  gui_button_back();
}
?>
</div><!-- fin "main" -->
<?php    
$table->Close();
$table->dialog->Footer();
?>

</div><!-- fin "page" -->
</body>
</html>
