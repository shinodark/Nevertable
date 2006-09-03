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

define('TPL_DIR','templates/');
	
class Template
{
  var $name;
  var $file;
  var $tpl_dir;
  var $tpl_content;

  /*__Constructeur__
	
  Cette fonction initialise l'objet FileManager.
  */
  function Template($name="")
  {
    global $config;
    
    $this->name = $name . ".tpl";
    $this->file = new FileManager();
    $this->tpl_dir = ROOT_PATH . $config['theme_dir'] . TPL_DIR ;
  }

  function Load($name="")
  {
    if (empty($this->name))
      $this->name = $name . ".tpl";
    if (empty($this->name))
      return false;
    $file->SetFileName($this->tpm_dir . $this->name);
    $this->tpl_content = $file->ReadString();
    if (!$tpl->content)
    {
       $this->SetError($file->GetError());
       return false;
    }
    return true;
  }

  /* Remplace les données des clés entre { } par les données dynamiques */
  }
  function Process($subst_arr)
  {
    if (empty($this->tpl_content))
      return false;
    foreach ($subst_arr as $key => $subst)
    {
      $this->tpl_content = str_replace('{'.$key.'}', $subst, $this->tpl_content);
    }
    return $this->tpl_content;
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
