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
<?php $table->PrintHtmlHead("Nevertable - Neverball Hall of Fame"); ?>

<body>
<div id="page">
<?php   $table->PrintTop();  ?>
<div id="main">
<?php
function closepage()
{  global $table;
    echo "</div><!-- fin \"main\" -->\n";
    $table->Close();
    $table->PrintFooter();
    echo "</div><!-- fin \"page\" -->\n";
    echo "</body>\n";
    echo "</html>\n";
}
    
if(isset($args['valid']))
{
  if (empty($args['pseudo']) || empty($args['passwd1']) || empty($args['passwd2']) )
  {
    button_error("Empty fields. Pseudo, email and password have to be filled.", 400);
    button_back();
    closepage();
    exit;
  }
  
  $table->db->MatchUserByName($args['pseudo']);
  if ($table->db->NumRows() > 0) // dejà existant
  {
    button_error("This pseudo is already used, please set another one...", 400);
    button_back();
    closepage();
    exit;
  }
  
  $table->db->MatchUserByMail($args['email']);
  if ($table->db->NumRows() > 0) // dejà existant
  {
    button_error("This mail adress is already used, please set another one...", 500);
    button_back();
    closepage();
    exit;
  }
  
  if (!CheckMail($args['email']))
  {
    button_error("Email adress is not valid. Try again...", 400);
    button_back();
    closepage();
    exit;
  }
  if ($args['passwd1'] != $args['passwd2'])
  {
    button_error("Passwords don't match. Try again...", 400);
    button_back();
    closepage();
    exit;
  }

  if (strlen($args['passwd1']) <= 4)
  {
    button_error("Passwords need to be longer than 5 caracters. Try again...", 500);
    button_back();
    closepage();
    exit;
  }
  
  // test du premier utilisateur, si oui c'est l'admin
  if ($table->db->CountRows("users") == 0)
    $level=get_userlevel_by_name("root");
  else
    $level=get_userlevel_by_name("member");
  
  if ($level == get_userlevel_by_name("root"))
    button("You're first registered user, you're root ;)", 400);

  if (   (addslashes($args['pseudo']) != $args['pseudo'])
      || (addslashes($args['email'])   != $args['email']) )
  {
    button_error("Please don't use special characters. Try again...", 400);
    button_back();
    closepage();
    exit;
  }

  //envoie du mail de bienvenue
  $m= new Mail; // create the mail
  $m->From( $config['admin_mail']);
  $m->To($args['email']);
  $m->Subject( "Welcome to nevertable ! " );	
  $message  = "Your registration is completed ! \n";
  $message .= "You can login as : \n\n";
  $message .= "   login    : ".$args['pseudo']."\n";
  $message .= "   password : ".$args['passwd1']."\n\n";
  $message .= "Thanks !";
  $m->Body( $message);	// set the body
  $m->Send();	// send the mail

    
  //protection
  $fields = array(
      'pseudo' => addslashes($args['pseudo']),
      'passwd' => md5($args['passwd1']),
      'email' => addslashes($args['email']),
      'level' => $level,
      'user_theme' => 'Sulfur',
  );

  //ajout dans la base

  $user = new User($table->db);
  $user->SetFields($fields);
  if (!$user->Insert())
  {
    button_error($user->GetError(),500);
    button_back();
    closepage();
    exit;
  }
  
  button("You're registered ! Welcome to Nevertable !", 400);
  button("<a href=\"./\">Return to main page</a>", 200);
  
  closepage();
  exit;
}
else
{
?>
      <div class="nvform" style="width: 400px;">
      <form class="nvform" action="register.php?valid" method="post">
      <table><tr>
      <th colspan="2" align="center"> Register form </th></tr>
      <td><label for=\"pseudo\">name : </label></td>
      <td><input type="text" id="pseudo" name="pseudo" size="30" /></td>
      </tr><tr>
      <td><label for=\"email\">email : </label></td>
      <td><input type="text" id="email" name="email" size="30" /></td>
      </tr><tr>
      <td><label for=\"passwd1\">password : </label></td>
      <td><input type="password" id="passwd1" name="passwd1" size="30" /></td>
      </tr><tr>
      <td><label for=\"passwd2\">password again : </label></td>
      <td><input type="password" id="passwd2" name="passwd2" size="30" /></td>
      </tr><tr>
      <td colspan="2"><center><input type="submit" value="Register" /></center></td>
      </form>
      </tr></table>
      </div>
<?php
}
button("<a href=\"index.php\">Return to table</a>", 300);
?>
</div> <!-- fin main-->
<?php
$table->Close();
$table->PrintFooter();
?>

</div><!-- fin "page" -->
</body>
</html>
