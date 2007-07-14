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

$args['pseudo']  = trim($args['pseudo']);

$table = new Nvrtbl("DialogStandard");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<?php $table->dialog->Head("Nevertable - Neverball Hall of Fame"); ?>

<body>
<div id="page">
<?php   $table->dialog->Top();  ?>
<div id="main">
<?php
function closepage()
{  global $table;
    echo "</div><!-- fin \"main\" -->\n";
    $table->Close();
    $table->dialog->Footer();
    echo "</div><!-- fin \"page\" -->\n";
    echo "</body>\n";
    echo "</html>\n";
    exit;
}
    
if(isset($args['valid']))
{
  if (empty($args['pseudo']) || empty($args['passwd1']) || empty($args['passwd2']) )
  {
    gui_button_error($lang['REGISTER_EMPTY_FIELDS'], 400);
    gui_button_back();
    closepage();
  }

  $table->db->helper->SelectUserByName($args['pseudo']);
  if ($table->db->NumRows() > 0) // dejà existant
  {
    gui_button_error($lang['REGISTER_PSEUDO_EXISTS'], 400);
    gui_button_back();
    closepage();
  }
  
  $table->db->helper->SelectUserByMail($args['email']);
  if ($table->db->NumRows() > 0) // dejà existant
  {
    gui_button_error($lang['REGISTER_MAIL_EXISTS'], 500);
    gui_button_back();
    closepage();
  }
  
  if (!CheckMail($args['email']))
  {
    gui_button_error($lang['REGISTER_MAIL_NOTVALID'], 400);
    gui_button_back();
    closepage();
  }
  if ($args['passwd1'] != $args['passwd2'])
  {
    gui_button_error($lang['REGISTER_PASSWD_CHECK'], 400);
    gui_button_back();
    closepage();
  }

  if (strlen($args['passwd1']) <= 4)
  {
    gui_button_error($lang['REGISTER_PASSWD_LENGTH'], 500);
    gui_button_back();
    closepage();
  }

  if (   (addslashes($args['pseudo']) != $args['pseudo'])
      || (addslashes($args['email'])   != $args['email']) )
  {
    gui_button_error($lang['REGISTER_SPECIAL_CHARS'], 400);
    gui_button_back();
    closepage();
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

  
  // test du premier utilisateur, si oui c'est l'admin
  if ($table->db->CountRows("users") == 0)
    $level=get_userlevel_by_name("root");
  else
    $level=get_userlevel_by_name("member");
  
  if ($level == get_userlevel_by_name("root"))
    gui_button($lang['REGISTER_FIRST_REGISTER'], 400);
    
  //protection
  $fields = array(
      'pseudo' => addslashes($args['pseudo']),
      'passwd' => md5($args['passwd1']),
      'email' => addslashes($args['email']),
      'level' => $level,
      'user_theme' => 'default',
  );

  //ajout dans la base

  $user = new User($table->db);
  $user->SetFields($fields);
  if (!$user->Insert())
  {
    gui_button_error($user->GetError(),500);
    gui_button_back();
    closepage();
  }

  gui_button($lang['REGISTER_SUCCESSFUL'], 400);
  gui_button("<a href=\"./\">".$lang['GUI_BUTTON_MAINPAGE']."</a>", 200);
  
  closepage();
}
else
{
      $form = new Form("post", "register.php?valid", "resgister", 400);
      $form->AddTitle($lang['REGISTER_FORM_TITLE']);
      $form->AddInputText("pseudo", "pseudo", $lang['REGISTER_FORM_PSEUDO']);
      $form->Br();
      $form->AddInputText("email", "email", $lang['REGISTER_FORM_EMAIL']);
      $form->Br();
      $form->AddInputPassword("passwd1", "passwd1", $lang['REGISTER_FORM_PASSWD1']);
      $form->Br();
      $form->AddInputPassword("passwd2", "passwd2", $lang['REGISTER_FORM_PASSWD2']);
      $form->Br();
      $form->AddInputSubmit();
      echo $form->End();
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
