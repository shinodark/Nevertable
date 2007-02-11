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
include_once ROOT_PATH ."classes/class.tag.dialog.php";

class Tagboard
{
  var $db;
  var $dialog;
  var $cache;
  var $out;
    
  function Tagboard(&$db, &$bbcode, &$smilies, &$style)
  {
    $this->out = "";
    $this->db = &$db;
    $this->cache  = new Cache("text");
    $this->dialog = new Tag_Dialog($this->db, $this->cache, $bbcode, $smilies, $style, $this->out);
  }

  function Show($args)
  {
    global $strings;

    if (isset($args['tag']))
    {
      if(empty($args['tag_pseudo']) || empty($args['content']))
      {
         $this->out .= "<span class=\"tag_error\">".$strings['tag_emptyfield']."</span>\n";
      }
      else
      {
	 $ok = $this->AntiFlood() && $this->AntiSpam($args['content']);
	 if ($ok)
           $this->Insert($args['content'], $args['tag_pseudo'], $args['tag_link']);
      }
    }

    $this->dialog->Tags();
    $this->dialog->TagForm();
    //$this->dialog->Append("<b>Tagboard is closed unti antispam feaetures are implemented.</b>");
    return $this->out;
  }

  function PrintOut()
  {
    echo $this->out;
  }

  function Insert($content, $pseudo, $link)
  {
    global $config, $strings;

    $tag = GetContentFromPost($content);
    if(strlen($tag) > $config['tag_maxsize'])
    {
      $this->out .= "<span class=\"tag_error\">".$strings['tag_toolong']."</span>\n";
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
      $this->db->RequestInit("INSERT", "tags");
      $this->db->RequestInsert($fields);
      if(!$this->db->Query())
      {
        $this->out .= "<span class=\"tag_error\">".$strings['tag_toolong']."</span>\n";
      return false;
      }
    }
    /* Purge du cache */
    $this->cache->Dirty("tags");
    return true;
  }

  function Update($id, $content, $pseudo, $link)
  {
    global $config, $strings;

    $tag = GetContentFromPost($content);
    if(strlen($tag) > $config['tag_maxsize'])
    {
        $this->out .= "<span class=\"tag_error\">".$strings['tag_toolong']."</span>\n";
        $this->SetError($strings['tag_toolong']);
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
        "link"        => $link,
        );
      $this->db->RequestInit("UPDATE", "tags");
      $this->db->RequestUpdateSet($fields, true);
      $this->db->RequestGenericFilter("id", $id);
      if(!$this->db->Query())
      {
        $this->out .= "<span class=\"tag_error\">".$strings['tag_toolong']."</span>\n";
        $this->SetError($this->db->GetError());
        return false;
      }
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
    $this->db->RequestInit("DELETE", "tags");
    $this->db->RequestGenericFilter("id", $id);
    if (!$this->db->Query())
    {
       $this->SetError($this->db->GetError());
       return false;
    }
    /* Purge du cache */
    $this->cache->Dirty("tags");
    return true;
  }

  /******
    GESTION ANTI SPAM
  ******/

  function AntiFlood()
  {
    global $config;

    $this->db->RequestInit("SELECT", "tags", "COUNT(id) AS num" );
    $this->db->RequestGenericFilter("ip_log", $_SERVER['REMOTE_ADDR']);
    $this->db->RequestGenericFilter_ge_timestamp($config['tag_flood_limit']);
    $this->db->RequestGenericSort(array("timestamp"), "DESC");
    $this->db->RequestLimit($config['tag_limit']);
    $res = $this->db->Query();
    if (!$res)
    {
	button_error($this->db->GetError(), 200);
	return false;
    }
    $val = $this->db->FetchArray();
    if ($val['num'] > 0)
    {
        $this->out .= "<span class=\"tag_error\">You can't flood this tagboard.</span>\n";
	return false;
    }
    else
        return true;
  }
  
  function AntiSpam($content)
  {
    $tag = GetContentFromPost($content);
    $ok = false;

    if (!Auth::Check(get_userlevel_by_name("member")))
    {
      if (
	   strstr($tag, '[url') == FALSE 
	&& strstr($tag, 'sex') == FALSE
      )
        $ok = false;
      else
	$ok = true;
    }
    else
	$ok = true;

    if (!$ok)
    {
      $this->out .= "<span class=\"tag_error\">Spam detected. Tag denied.</span>\n";
    }
    return $ok;
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

