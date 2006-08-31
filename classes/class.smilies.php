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

class Smilies
{

  var $list;       /* liste des codes => icones*/
  var $isload;
  var $table;
  
  /*__Constructeur__
  Cette fonction initialise l'objet Record.

  @param: pointeur vers la base de donnée
  */
  function Smilies()
  {
    global $config;

    $smilpath = ROOT_PATH . $config['smilies_dir'] . "/";
    $smil = $smilpath . "smilies.txt";

    if (file_exists($smil))
    {
      $def = file($smil);

      foreach($def as $v)
      {
        $v = trim($v);
        if (preg_match('|^([^\s]*)[\s]+(.*)$|',$v,$matches))
         $this->list[$matches[1]] ="<img src=\"". $smilpath.$matches[2] ."\" alt=\"".$matches[1]."\" />";
      }
      $this->isload = true;
    }
    else
    {
      $this->SetError("Smilie files doesn't exist.");
      $this->isload = false;
    }
  }

  function Apply($content)
  {
    if ($this->isload)
    {
      $i=0;
      foreach($this->list as $code => $icon)
      {
        /* le code avec toujours des espaces autour */
        $patterns[$i]="/".preg_quote($code." ","/")."/";
        $replacements[$i]="&nbsp;".$icon."&nbsp;";
        $i++;
      }
      return preg_replace($patterns, $replacements, $content);
    }
    else
      return $content;
  }

  function GetIcon($code)
  {
    return $this->list[$code] ;
  }

  function GetList()
  {
    return $this->list;
  }

  function IsLoad()
  {
    return $this->isload;
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
