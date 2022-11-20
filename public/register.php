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
define ('NVRTBL', 1);
include_once ROOT_PATH ."config.inc.php";
include_once ROOT_PATH ."includes/common.php";
include_once ROOT_PATH ."includes/classes.php";

//args process
$args = get_arguments($_POST, $_GET);

$args['pseudo']  = isset($args['pseudo']) ? trim($args['pseudo']) : false;

$table = new Nvrtbl();

try {
	

if(isset($args['valid']))
{
  if (empty($args['pseudo']) || empty($args['passwd1']) || empty($args['passwd2']) )
    throw new Exception ($lang['REGISTER_EMPTY_FIELDS']);


  $table->db->helper->SelectUserByName($args['pseudo']);
  if ($table->db->NumRows() > 0) // already registered
    throw new Exception ($lang['REGISTER_PSEUDO_EXISTS']);
  
  $table->db->helper->SelectUserByMail($args['email']);
  if ($table->db->NumRows() > 0) // already registered
    throw new Exception ($lang['REGISTER_MAIL_EXISTS']);
  
  if (!CheckMail($args['email']))
    throw new Exception ($lang['REGISTER_MAIL_NOTVALID']);

  if ($args['passwd1'] != $args['passwd2'])
    throw new Exception ($lang['REGISTER_PASSWD_CHECK']);
  
  if ((strtoupper($args['human'] ) != "HELLO") && (strtoupper($args['human']) != "HI"))
    throw new Exception ($lang['REGISTER_HUMAN_CHECK']);

  if (strlen($args['passwd1']) <= 4)
  	throw new Exception ($lang['REGISTER_PASSWD_LENGTH']);

  if (   (addslashes($args['pseudo']) != $args['pseudo'])
      || (addslashes($args['email'])   != $args['email']) )
  {
  	throw new Exception ($lang['REGISTER_SPECIAL_CHARS']);
  }
  
  $tpl_params['message_array'] = array();
  
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
    array_push($tpl_params['message_array'], $lang['REGISTER_FIRST_REGISTER']);
    
  //protection
  $fields = array(
      'pseudo' => addslashes($args['pseudo']),
      'passwd' => Auth::Hash($args['passwd1']),
      'email' => addslashes($args['email']),
      'level' => $level,
      'user_theme' => 'default',
  );

  //ajout dans la base

  $user = new User($table->db);
  $user->SetFields($fields);
  $user->Insert();

  array_push($tpl_params['message_array'], $lang['REGISTER_SUCCESSFUL']);
  $tpl_params['delay'] = 0;
  $tpl_params['redirect'] = "index.php";
  
  $table->template->Show('redirect', $tpl_params);
}
else
{
  $table->template->Show('register');
}

} catch (Exception $ex)
{
  $table->template->Show('error', array("exception" => $ex));
}

$table->Close();
