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
#
if (!defined('NVRTBL'))
	exit;
	
include_once ROOT_PATH ."libs/lib.filemanager.php";

class Cache
{
  var $cache_dir;
  var $type;
  var $CacheFile;
  var $cache_hit;

  /*__Constructeur__
	
  Cette fonction initialise l'objet Cache.
@param: type "text" or "array"
  */
  function Cache($type="array", $cache_dir="")
  {
    global $config;
    if (empty($cache_dir)) $this->cache_dir = $config['cache_dir'];
    else $this->cache_dir = $cache_dir;
    $this->type = $type;
    $this->cache_hit = false;

    $this->CacheFile = new FileManager();
  }

  function Hit($ident)
  {
    $this->CacheFile->SetFileName($this->cache_dir . $ident . ".cache");
    $this->cache_hit = $this->CacheFile->Stat();
    if (!$this->cache_hit) // Cache miss
      return false;
    else
      return true;
  }

  function Create($ident, $cache_data)
  {
    $this->CacheFile->SetFileName($this->cache_dir .  $ident . ".cache");
    if (is_array($cache_data))
       $cache_data = serialize($cache_data);
    $this->CacheFile->Write($cache_data);
    return true;
  }

  function Dirty($ident)
  {
    $this->CacheFile->SetFileName($this->cache_dir . $ident . ".cache");
    if ($this->CacheFile->Exists())
    	$this->CacheFile->Unlink(); 

    return true;
  }

  function Read()
  {
    if (!$this->cache_hit)
    {
      $this->SetError("Trying to read a non-existent cache.");
      return false;
    }
    if($this->type === "array") $ret = $this->_ReadArray();
    else if($this->type === "text") $ret = $this->_ReadText();
    else $ret = $this->_ReadText();
    return $ret;
  }

  function _ReadArray()
  {
    $cache_data = $this->CacheFile->Read();
    if ($cache_data === false)
    {
      $this->SetError("Error reading cache file " . $this->CacheFile->GetFileName());
      return false;
    }
    $cache_arr = unserialize(stripslashes($cache_data));
    return $cache_arr;
  }
  
  function _ReadText()
  {
    $cache_data = $this->CacheFile->Read();
    if ($cache_data === false)
    {
      $this->SetError("Error reading cache file " . $this->CacheFile->GetFileName());
      return false;
    }
    return $cache_data;
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
