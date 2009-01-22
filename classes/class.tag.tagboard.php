<?php
# ***** BEGIN LICENSE BLOCK *****
# This file is part of Shinotag .
# Copyright (c) 2004 Francois Guillet and contributors. All rights
# reserved.
#
# Shinotag is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
# 
# Shinotag is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with Shinotag; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
# ***** END LICENSE BLOCK *****
#
if (!defined('NVRTBL'))
	exit;
	
class Tagboard
{
  var $db;
  var $cache;
  var $template;
  var $table;
  var $out;
    
  function Tagboard(&$parent)
  {
    $this->out = "";
    $this->db = &$parent->db;
    $this->cache  = new Cache("text");
    $this->template = new Template(&$parent);
  }

  function Show($args, $moder=false)
  {
    global $lang;

    $err = array();
    if (isset($args['tag'])) // add a tag
    {
      if(empty($args['tag_pseudo']))
        array_push ($err, $lang['TAG_EMPTY_PSEUDO']);

      else if (empty($args['content']))
      	array_push ($err, $lang['TAG_EMPTY_CONTENT']);

      else if ($args['tag_pseudo'] != $_SESSION['user_pseudo'])
      	array_push ($err, "don't cheat...");
      	
      else
        $this->Insert($args['content'], $args['tag_pseudo'], $args['tag_link'], $err);
    }
       
    $res = $this->db->helper->SelectTags();
    if($res)
    {
    	if (!$moder)
    		$tpl = '_tags';
        else
        	$tpl = 'admin/_tags';

        $cache_id = empty($err) ? 'tags' : ""; // Invalid cache if errors have to be displayed  
        $this->template->Show( $tpl, array("tags" => $res, "errors" => $err), $cache_id);
    
    }
  }


  function Insert($content, $pseudo, $link, &$err)
  {
    global $config, $lang;

    $tag = GetContentFromPost($content);
    if(strlen($tag) > $config['tag_maxsize'])
    {
      array_push ($err, $lang['TAG_TOO_LONG']);
      return false;
    }
    else
    {
      if (!empty($link) && (ereg("^http://",$link) == FALSE))
  	  {
		$link = "http://" . $link;
	  }
      $fields = array (
        "pseudo"      => $pseudo,
        "content"     => $tag,
        //"link"        => $link,
        "link"        => "",
	    "ip_log"      => $_SERVER['REMOTE_ADDR'],
        );
      $this->db->NewQuery("INSERT", "tags");
      $this->db->Insert($fields);
      $this->db->Query();
    }
    /* Purge du cache */
    $this->cache->Dirty("tags");
    return true;
  }

  function Update($id, $content)
  {
    global $config, $lang;
    
    if (empty($id))
    	return false;

    $tag = GetContentFromPost($content);

    if(strlen($tag) > $config['tag_maxsize'])
    {
        $this->SetError($lang['TAG_TOO_LONG']);
    }
    else
    {
      if (!empty($link) && (ereg("^http://",$link) == FALSE))
  	  {
		$link = "http://" . $link;
	  }
      $fields = array (
        "content"     => $tag,
        );
      $this->db->NewQuery("UPDATE", "tags");
      $this->db->UpdateSet($fields, true);
      $this->db->Where("id", $id);
      $this->db->Limit(1);
      $this->db->Query();
    }
    /* Purge du cache */
    $this->cache->Dirty("tags");

    return true;
  }

  function Purge($id)
  {
    if (!isset($id) || empty($id))
    {
      return false;
    }
    $this->db->NewQuery("DELETE", "tags");
    $this->db->Where("id", $id);
    $this->db->Query();
    
    /* Purge du cache */
    $this->cache->Dirty("tags");
    return true;
  }

  function SetError($error)
  {
    $this->error = $error;
    throw Exception($this->error);
  }
    
  function GetError()
  {
    return $this->error;
  }
}

