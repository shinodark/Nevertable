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

class Auth
{
  var $db;
  var $error;

  function Auth(&$db)
  {
    $this->db = &$db;
  }
  
  /* Commence une session vierge, ou récupération du cookie */
  function SessionBegin()
  {
    global $config;
    session_start();
    
    /* if cookie is present.. */
    if ( isset($_COOKIE[$config["cookie_name"]]))
	{
      $cookiedata = unserialize(stripslashes($_COOKIE[$config["cookie_name"]]));
      if ($cookiedata["auto"] && !isset($_SESSION['user_logged']))
        $this->Perform($cookiedata["user"], $cookiedata["md5"], true, false);
	}
  }

  function Perform($login, $passwd, $md5_passwd=false, $cookie=false)
  {
      global $config;

      if (empty($login) || empty($passwd))
      {
        $this->SetError("Empty field. Try again.");
        return false;
      }
      if ($login !== addslashes($login))
        return false;

      $res = $this->db->helper->SelectUserByName($login);
      if ($this->db->NumRows($res) !== 1)
      {
        $this->SetError("No match !");
        return false;
      }
      $val = $this->db->FetchArray($res);
      
      if (!$md5_passwd)
        $passwd = md5($passwd);  
       
      if($passwd == $val['passwd'])
      { 
        $_SESSION['user_logged'] = true;
        $_SESSION['user_id']     = $val['id'];
        $_SESSION['user_pseudo'] = $login;
        $_SESSION['user_email']  = $val['email'];
        $_SESSION['user_level']  = $val['level'];
        $_SESSION['options_loaded']  = false;

        if ($cookie)
        {
          /* create cookie */
          $cookiedata["auto"] = true;
          $cookiedata["user"] = $_SESSION['user_pseudo'];
          $cookiedata["md5"]  = $val['passwd'];
       	  setcookie($config["cookie_name"], serialize($cookiedata), time()+$config["cookie_expire"], $config["cookie_path"], $config["cookie_domain"], false);
        }
        return true;
      }
      else
      {
        $_SESSION['user_logged'] = false;
        $_SESSION['user_id']     = 0;
        return false;
      }
  }

  function CloseSession()
  {
    global $config;

    $_SESSION=array();
    session_unset();
    session_destroy();
    /* autologin false" in cookie + clean up */
    $cookiedata["auto"] = false;
    $cookiedata["user"] = "";
    $cookiedata["md5"] = "";
    setcookie($config["cookie_name"], serialize($cookiedata), time()+$config["cookie_expire"], $config["cookie_path"], $config["cookie_domain"], false);
  }

  function Check($level)
  {
    return (isset($level) && $_SESSION['user_logged'] && $_SESSION['user_level']<=$level);
  }
  
  function CheckUser($user_id)
  {
    return ($_SESSION['user_logged'] && ($_SESSION['user_level']<=get_userlevel_by_name("member")) && ($_SESSION['user_id'] === $user_id));
  }

  function _SaveUserOptions()
  {
    global $config;
    $_SESSION['opt_limit'] = $config['limit'];
    $_SESSION['opt_comments_limit'] = $config['comments_limit'];
    $_SESSION['opt_sidebar_comments'] = $config['sidebar_comments'];
    $_SESSION['opt_sidebar_comlength'] = $config['sidebar_comlength'];
    $_SESSION['opt_user_sort'] = $config['opt_user_sort'];
    $_SESSION['opt_user_theme'] = $config['opt_user_theme'];
    $_SESSION['opt_user_lang'] = $config['opt_user_lang'];
    $_SESSION['options_saved'] = true;
  }

  function _LoadUserOptions()
  {
    global $config;
    $config['limit'] = $_SESSION['opt_limit'];
    $config['comments_limit'] = $_SESSION['opt_comments_limit'];
    $config['sidebar_comments'] = $_SESSION['opt_sidebar_comments'];
    $config['sidebar_comlength'] = $_SESSION['opt_sidebar_comlength'];
    $config['opt_user_sort'] = $_SESSION['opt_user_sort'];
    $config['opt_user_theme'] = $_SESSION['opt_user_theme'];
    $config['opt_user_lang'] = $_SESSION['opt_user_lang'];
  }

  function SetError($error)
  {
    $this->error = $error;
  }
    
  function GetError()
  {
    return $this->error;
  }
}
