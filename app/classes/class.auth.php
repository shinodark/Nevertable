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
if (!defined('NVRTBL'))
	exit;
	
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
    session_name("__table15");
    session_start();
    
    /* protect cooki against XSS */
    $cookie = array();
    foreach ($_COOKIE as $key => $value) {
    	$cookie[$key] = trim(strip_tags($value));
    }
    
    /* if cookie is present.. */
    if ( isset($cookie[$config["cookie_name"]]))
	  {
      $cookiedata = unserialize(stripslashes($cookie[$config["cookie_name"]]));
      if ($cookiedata["auto"] && !isset($_SESSION['user_logged']) && !empty($cookiedata["user"]) && !empty($cookiedata["sha1"]))
        $this->Perform($cookiedata["user"], $cookiedata["sha1"], true, false);
	  } else {
      $_SESSION['user_logged'] = false;
      $_SESSION['user_id']     = 0;
    }
  }

  function Perform($login, $passwd, $sha1_passwd=false, $cookie=false)
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
      
      if (!$sha1_passwd)
        $passwd = Auth::Hash($passwd);  
       
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
          $cookiedata["sha1"]  = $val['passwd'];
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

  static function Hash($v)
  {
  	return sha1($v);
  }
  static function Check($level)
  {
    return (isset($level) && isset($_SESSION['user_logged']) && $_SESSION['user_logged']
    	 && isset($_SESSION['user_level']) && $_SESSION['user_level']<=$level);
  }
  
  static function CheckUser($user_id)
  {
    return (isset($_SESSION['user_logged']) && $_SESSION['user_logged']
    		  && ($_SESSION['user_level']<=get_userlevel_by_name("member"))
    		  && ($_SESSION['user_id'] === $user_id));
  }

  static function _SaveUserOptions()
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

  static function _LoadUserOptions()
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
    throw new Exception($this->error);
  }
    
  function GetError()
  {
    return $this->error;
  }
}
