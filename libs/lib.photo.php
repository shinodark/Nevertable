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
	
define('PATHMAX',64);
	
class Photo
{
    var $db;
    var $photofile;
    var $fp;
    var $error;
    var $width;
    var $height;
    var $format;
    var $res_gd;

    /*__Constructeur__
    Cette fonction initialise l'objet Photo.
    */
    function Photo($filename)
    {
      $this->photofile = new FileManager($filename);
    }

    function Init()
    {
       global $config, $lang, $imagetypes;

       $picprop = getimagesize($this->photofile->GetFileName());
       if ($picprop == false)
       {
         $this->SetError($lang['PHOTO_ERR_OPEN']);
         return false;
       }
       $this->width  = $picprop[0];
       $this->height = $picprop[1];
       $this->format = $imagetypes[$picprop[2]];
        
       return true;
    }

    function _OpenResGD2()
    {
       switch($this->format)
       {
		 case "JPG": 
	            $this->res_gd = imagecreatefromjpeg($this->photofile->GetFileName()); break;
		 case "GIF": 
	            $this->res_gd = imagecreatefromgif($this->photofile->GetFileName()); break;
		 case "PNG": 
	            $this->res_gd = imagecreatefrompng($this->photofile->GetFileName()); break;
         default :
	   return false;
       }
       return true;
    }

    function _CloseResGD2()
    {
       imagedestroy($this->res_gd);
    }

    function Resize($x, $y)
    {
       global $config, $lang;

       if (!$this->_OpenResGD2())
       {
         $this->SetError($lang['PHOTO_ERR_FORMAT']);
         return false;
       }

       $new_photo = imagecreatetruecolor($x, $y);
	   imagecopyresized($new_photo, $this->res_gd, 0, 0, 0, 0, $x, $y, $this->width, $this->height);

       /* Remplacement du fichier par l'image redimensionnée */
       $this->photofile->Unlink();
       imagejpeg($new_photo, $this->photofile->GetFileName(), $config['photo_quality']);

       $this->width=$x;
       $this->height=$y;
       $this->format="JPG";
     
       $this->_CloseResGD2();

       return true;
    }
    
    function GetWidth()
    {
      return $this->width;
    }
    
    function GetHeight()
    {
      return $this->height;
    }
    
    function GetFormat()
    {
      return $this->format;
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

