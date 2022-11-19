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
	
if (!defined('PATHMAX'))
	define('PATHMAX',64);
	
class FileManager
{
  var $filename;
  var $handle;

  /*__Constructeur__
	
  Cette fonction initialise l'objet FileManager.
  */
  function FileManager($filename="")
  {
    $this->filename = ROOT_PATH.$filename;
  }

  /** files est la strcture $_FILES d'origine 
   ** ident est le nom d'identification du fichier dans le formulaire
   ** upload_dir le répertoire ou sauver le fichier
   ** file_name le nom du fichier à sauver
  **/
  function Upload($files, $ident, $upload_dir, $file_name, $always_overwrite=false)
  {
    $tmp_file = $files[$ident]['tmp_name'];
    
    if (empty($tmp_file))
    	$this->SetError("No uploaded file found.");

    /* si pas de slash à la fin */
    if (preg_match("/\/$/",$upload_dir)==0)
      $upload_dir = $upload_dir."/"  ;

    if (version_compare(phpversion(),'4.2.0','>='))
      $udp_error = $files[$ident]['error'];
    else
	  $udp_error = -1;

    if($udp_error != 0)
    {
      switch($udp_error)
      {
        case 1 : $str = "File size exceeds the authorized php limit."; break;
        case 2 : $str = "File size exceeds the configured limit."; break;
        case 3 : $str = "File was only partially uploaded."; break;
        case 4 : $str = "What ? No file uploaded !"; break;
        default: $str = "Unknown error, code ".$udp_error.".";
      }
      $this->SetError($str);
      return false;
    }

    if (!$always_overwrite)
    {
      /* boucle les noms de fichers jusqu'à trouver un fichier qui n'existe pas */
      $i=0; $base=$file_name;
      while (file_exists($upload_dir . $file_name))
      {
        $file_name = $base . "_" . $i;
        $i++;
      }
    }

    if(@move_uploaded_file($tmp_file, $upload_dir . $file_name))
    {
      $this->filename = $upload_dir.$file_name;
      chmod($upload_dir.$file_name,fileperms($upload_dir) & ~0111);
    }
    else
    { 
      $this->SetError("Error uploading file on server.");
      return false;
    }
  
    return true;
  }

  function Unlink()
  {
      if(@unlink($this->filename)) {
        return true;
      }
      else {
        $this->SetError("Error deleting file ".$this->filename." from server !");
      }
  }

  function Move($newdir, $newname="", $always_overwrite=false)
  {
    if (empty($newname))
      $newname=basename($this->filename);
    /* si pas de slah à la fin */
    if (preg_match("/\/$/",$newdir)==0)
      $newdir = $newdir."/"  ;
    $newfile = $newdir.$newname;

    /* boucle pour changer le nom si il existe déjà */
    /* si le nom contient déjà un _, on élimine pour réindexer (sinon on accumule les _0_0_0...) */
    if(preg_match('/^(.*)_[^_]*$/', $newfile, $matches)>0)
      $base=$matches[1];
    else
      $base=$newfile;
    
    $i=0;
    if (!$always_overwrite)
    {
      while (file_exists($newfile))
      {
        $newfile = $base . "_" . $i;
        $i++;
      }
    }
    
    if(!@rename($this->filename, $newfile))
    {
      $this->SetError("Error Moving file ".$this->filename." to ".$newfile.".");
      return false;
    }
    else
    {
      $this->filename = $newfile;   
      return true;
    }
  }

  function Rename($newname)
  {
    $newname = dirname($this->filename)."/".$newname;
    if (file_exists($newname))
    {
      $this->SetError("File with name ".$newname." already exists, don't overwrite.");
      return false;
    }

    if(!@rename($this->filename, $newname))
    {
      $this->SetError("Error Renaming file ".$this->filename." .");
      return false;
    }
    else
    {
      return true;
    }
  }

  function DirList($dir, $show_hidden=false)
  {
    $d=0; $f=0;
    $f_arr=array(); $d_arr=array();
    if ($dirhandle = @opendir($dir))
    {
      while (false !== ($file = @readdir($dirhandle)))
      {
        if (is_file($dir."/".$file))
        {
          if ((($show_hidden === false) && ($file[0]!=='.')) || $show_hidden === true)
            $f_arr[$f++] = $file;
        }
        else if (is_dir($dir."/".$file) && $file !== "." && $file !== "..")
          $d_arr[$d++] = $file;
      }
      closedir($dirhandle);
      return array("files" => $f_arr, "subdir" => $d_arr);
    }
    else
    {
      $this->SetError("Error opening directory ".$dir." .");
      return false;
    }
  }

  function GetSize($unit="o")
  {
    $res = @stat($this->filename);
    if (!$res)
    {
      $this->SetError("Error in stat file ".$this->filename." .");
      return false;
    }
    switch($unit)
    {
      case "Mo": $ret = floor($res['7']/1024/1024); break;
      case "ko": $ret = floor($res['7']/1024); break;
      case "o":
      default : $ret = $res['7']; break;
    }
    return $ret;
  }

  function Stat()
  {
    return @stat($this->filename);
  }

  function Exists()
  {
    return @file_exists($this->filename);
  }
  
  function Open($att = "r")
  {
    if (!isset($this->handle))
    {
        if ($att == "r" && (!file_exists($this->filename) || !is_readable($this->filename)) )
        {
            $this->SetError("file ".$this->filename." doesn't exist or is not readable");
            return false;
        }
       $this->handle = fopen($this->filename, $att);
    }
    if (!$this->handle)
    {
      $this->SetError("Error opening file ".$this->filename." with attribute ". $att);
      return false;
    }
    return true;
  }
  
  function Close()
  {
    if (isset($this->handle))
       @fclose($this->handle);
    unset($this->handle);
  }
  
  function IsEof()
  {
     if (!isset($this->handle))
         return true;
     return @feof($this->handle);
  }

  function Tell()
  {   
  	if (!isset($this->handle))
         return false;
    return @ftell($this->handle);
  }
  
  function Seek($off)
  {
  	if (!isset($this->handle))
  		return false;
  	return @fseek($this->handle, $off);
  }

  function Read()
  {
    if (!isset($this->handle))
    {
      $ret = $this->Open();
      if (!$ret)
        return false;
    }
    $data = @fread($this->handle, filesize($this->filename));
    $this->Close();
    return $data;
  }

  function ReadLine()
  {
    if (!isset($this->handle))
    {
      $ret = $this->Open();
      if (!$ret)
        return false;
    }
    $line = @fgets($this->handle, 4096);
    return $line;
  }

  function ReadString()
  {
     $data = file_get_contents($this->filename);
     if (!$data)
     {
       $this->SetError("Error opening file ".$this->filename." for reading.");
       return false;
     }
     return $data;
  }

  function Write($data)
  {
    if (!isset($this->handle))
    {
      $ret = $this->Open("w");
      if (!$ret)
        return false;
    }
    if (! @fwrite($this->handle, $data))
    {
      $this->SetError("Error writing data to file ".$this->filename." .");
      return false;
    }
    $this->Close();
    if (! @chmod ($this->filename, 0666))
    {
      $this->SetError("Error chmod file ".$this->filename." .");
    }
    return true;
  }
  
  function ReadInt()
  {
    if (!isset($this->handle))
    {
      $ret = $this->Open();
      if (!$ret)
        return false;
    }

    $BinaryData = fread($this->handle, 4);
    $UnpackedData = unpack("V", $BinaryData);
    return $UnpackedData[1];
  }
  
  function WriteInt($value)
  {
    if (!isset($this->handle))
    {
      $ret = $this->Open('a');
      if (!$ret)
        return false;
    }
    
    $PackedData = pack("V", $value);
    fwrite($this->handle, $PackedData, 4);
    return true;
  }
 
  function ReadShort()
  {
    if (!isset($this->handle))
    {
      $ret = $this->Open();
      if (!$ret)
        return false;
    }

    $BinaryData = fread($this->handle, 2);
    $UnpackedData = unpack("S", $BinaryData);
    return $UnpackedData[1];
  }
  
  function WriteShort($value)
  {
    if (!isset($this->handle))
    {
      $ret = $this->Open('a');
      if (!$ret)
        return false;
    }
    
    $PackedData = pack("S", $value);
    fwrite($this->handle, $PackedData, 2);
    return true;
  }

  function ReadStringLength($length)
  {
    if (!isset($this->handle))
    {
      $this->Open();
    }
    
    $c = chr(0);
    $str = " ";
    $i=0;
    $max = $length;
    do
    {
    	$c = fgetc($this->handle);
    	$str[$i] = $c;
    	$i++;
    }
    while ((ord($c) != 0) && ($max-- > 0) );
    
    if (ord($str[$i-1]) != 0)
    	$str[$i-1] = chr(0);

    return  $str;
  }
  
  function WriteStringLength($str, $length)
  {
    if (!isset($this->handle))
    {
      $this->Open('a+');
    }
    
    if (empty($str) || !($length > 0))
    	return false;
    $c = $str[0];
    $i=0;
    $max = $length - 1; //-1 for chr(0) at the end
    do
    {
    	fwrite($this->handle, $c, 1);
    	$i++;
    	$c = $str[$i];
    }
    while ((ord($c) != 0) && ($max-- > 0) );
    fwrite($this->handle, chr(0), 1);
    
    return  true;
  }


  function GetHash()
  {
    return md5_file($this->filename);
  }
  
  function GetFileName()
  {
    return $this->filename;
  }
  
  function GetBaseName()
  {
    return basename($this->filename);
  }
  
  function SetFileName($filename)
  {
    $this->filename = ROOT_PATH.$filename;
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
