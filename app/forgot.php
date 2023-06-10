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
define('NVRTBL', 1);
include_once ROOT_PATH ."config.inc.php";
include_once ROOT_PATH ."includes/common.php";
include_once ROOT_PATH ."includes/classes.php";

//args process
$args = get_arguments($_POST, $_GET);

$table = new Nvrtbl();

try {

if(isset($args['run']))
{
  if (empty($args['email']))
    throw new Exception($lang['FORGOT_EMPTY_MAIL']);
  
  $res = $table->db->helper->SelectUserByMail($args['email']);
  if ($table->db->NumRows() == 0) // pas trouvé
  {
    throw new Exception ($lang['FORGOT_INVALID_MAIL']);
  }

  $val = $table->db->FetchArray($res);

  $keychars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
  $length = 8;
  $newpass = "";
  $max=strlen($keychars)-1;
  for ($i=0;$i<$length;$i++)
    $newpass .= substr($keychars, rand(0, $max), 1);

  //Update password
  if (!isset($val['id']))
    exit;
  $table->db->NewQuery("UPDATE",  "users");
  $table->db->UpdateSet(array("passwd" => Auth::Hash($newpass)));
  $table->db->Where("id", $val['id']);
  $table->db->Limit(1);
  $table->db->Query();
  
  //envoie du mail
  $m= new Mail; // create the mail
  $m->From( $config['admin_mail']);
  $m->To($args['email']);
  $m->Subject( "Nevertable : password recovery" );	
  $message  = "Try this... \n\n";
  $message .= "   login    : ".$val['pseudo']."\n";
  $message .= "   password : ".$newpass."\n\n";
  $message .= "See you soon !";
  $m->Body( $message);	// set the body
  $m->Send();	// send the mail
  
  $tpl_params = array();
  $tpl_params['message_array'] = array($lang['FORGOT_EMAIL_SENT']);
  $tpl_params['delay'] = 0;
  $tpl_params['redirect'] = "index.php"; 
  $table->template->Show('redirect', $tpl_params);    
}
else
{
  $table->template->Show('forgot');
}

} catch (Exception $ex)
{
  $table->template->Show('error', array("exception" => $ex));
}

$table->Close();

