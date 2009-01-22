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
define('NVRTBL', '1');

include_once ROOT_PATH ."config.inc.php";
include_once ROOT_PATH ."includes/common.php";
include_once ROOT_PATH ."includes/classes.php";


//args process
$args = get_arguments($_POST, $_GET);

try {
	
$table = new Nvrtbl();


if (isset($args['out']))
{
  if ($_SESSION['user_logged'])
    $was_logged=true;

    
  Auth::CloseSession();
  $table->RemoveOnlineUser();
  if ($was_logged)
  {
    $tpl_params = array("redirect" => "index.php");
    $tpl_params['message_array'] = array($lang['LOGIN_LOGOUT']);
    $table->template->Show('redirect', $tpl_params);
  }
  else
  {
  	throw new Exception($lang['LOGIN_NOTLOGIN']);
  }
}

else if (isset($args['in']))
{
  $auth = new Auth($table->db);
  $success=$auth->Perform($args['pseudo'], $args['passwd'], false, true);
 
  if ($success)
  {
    $table->AddOnlineUser();
    
    $tpl_params = array("message_array" => array($lang['LOGIN_LOGIN']));
    
    if (!empty($_SESSION['redirect']))
    {
      $tpl_params['redirect'] = $_SESSION['redirect'];
      $_SESSION['redirect'] = "";
    }
    else
      $tpl_params['redirect'] = "index.php";
      
    $table->template->Show('redirect', $tpl_params);
  }
  else
  {
  	 throw new Exception($lang['LOGIN_AUTH_FAILED']);
  }
}


else
{
  $table->template->Show('login');
}

} catch (Exception $ex)
{
  $table->template->Show('error', array("exception" => $ex));
}

$table->Close();