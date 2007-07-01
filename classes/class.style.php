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

class Style
{
    var $current;
    var $theme_dir;

    /*__Constructeur__
       Cette fonction initialise l'objet Style.
    */
    function Style()
    {
      global $config;
      $this->current = $config['theme_default'];
      $this->theme_dir = ROOT_PATH . $config['theme_dir'] . $this->current;
    }

    function GetStyle()
    {
      return $this->current;
    }

    function Select($name="")
    {
      global $config;
      if (empty($name))
        $this->current= $config['theme_default'];
      else
      {
	if (is_dir(ROOT_PATH . $config['theme_dir'] . $name))
          $this->current = $name;
	else
          $this->current= $config['theme_default'];
      }
      $this->theme_dir = ROOT_PATH . $config['theme_dir'] . $this->current;
    }

    function GetImageDir()
    {
      return $this->theme_dir . "/images/";
    }
    
    function GetIconDir()
    {
      return $this->theme_dir . "/images/icons/";
    }
    
    function GetSmiliesDir()
    {
      return $this->theme_dir . "/images/smilies/";
    }

    function GetCss()
    {
      return $this->theme_dir . "/" . $this->current . ".css";
    }

    function GetImage($ident, $title="", $alt="", $special="")
    {
      global $icons, $config;

      if (empty($alt))
         $alt=$ident;

      $dir = $this->GetImageDir();
      if(!file_exists($dir.$icons[$ident]))
         $dir = $config['theme_default']."images/";
      return "<img src=\"".$dir . $icons[$ident] . "\" title=\"".$title."\" alt=\"".$alt."\" ".$special." />";
    }

    function GetIcon($ident)
    {
      global $config;

      $dir = $this->GetIconDir();
      if(!file_exists($dir.$ident))
         $dir = $config['theme_default']."images/icons/";
      return "<img src=\"".$dir . $ident.".png"."\" alt=\"\" />";
    }

}
