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

define('PATHMAX',64);
	
class Replay
{
	var $replayname;
    var $fp;
    var $coins;
    var $time;
    var $set;
    var $level;
    var $error;
    var $solfile;
    var $type;

	/*__Constructeur__
	
	Cette fonction initialise l'objet Replay.
	*/
	function Replay($filename, $type)
	{
        $this->replayname = $filename;
        $this->type = $type;
	}

    function Init()
    {
        global $solfiles;

        if (!file_exists($this->replayname) || !is_readable($this->replayname) )
        {
            $this->error = "Replay file doesn't exist or is not readable";
            return false;
        }
        $this->fp  = fopen($this->replayname, "r");
        if (!$this->fp)
        {
            $this->error = "Error opening file";
            return false;
        }

        $magic = fread($this->fp, 4);
        if ($magic != "PRBN")
        {
            $this->error = "File is probably not a replay file";
            return false;
        }

        $this->time  = getShort($this->fp) / 100.0;
        $this->coins = getShort($this->fp);
        getString($this->fp, PATHMAX);   /* shot */
        $this->solfile = trim(getString($this->fp, PATHMAX));

        if (!isset($solfiles[$this->solfile]))
        {
            $this->error = "Can't match level from replay file!";
            return false;
        }
        
        $this->set = (integer)$solfiles[$this->solfile]['set'];
        if(!isset($this->set) || ($this->set < 0))
        {
            $this->error = "Can't get valid set from replay file!";
            return false;
        }
        $this->level = (integer)$solfiles[$this->solfile]['level'];
        if(!isset($this->level) || ($this->level < 0))
        {
            $this->error = "Can't get valid level from replay file!";
            return false;
        }

        /* test du nom du replay */
        $name = basename($this->replayname);
        if ($this->type == get_type_by_name("most coins"))
        {
          if ($name[0] !== 'c')
          {
            $this->error = "Most coin replay filename should start with 'c', see conditions.";
            return false;
          }
        }
        
        if ($this->type == get_type_by_name("freestyle"))
        {
          if ($name[0] !== 'f')
          {
            $this->error = "Freestyle replay filename should start with 'f', see conditions.";
            return false;
          }
        }

        fclose($this->fp);
        
        return true;
    }
    
    function GetTime()
    {
        return $this->time;
    }
    
    function GetCoins()
    {
        return $this->coins;
    }

    function GetSolFile()
    {
        return $this->solfile;
    }

    function GetSet()
    {
        return $this->set;
    }
    
    function GetLevel()
    {
        return $this->level;
    }

    function GetFields()
    {
        return array(
            "time"      => $this->time,
            "coins"     => $this->coins,
            "levelset"  => $this->set,
            "level"     => $this->level,
            );
    }

    function GetError()
    {
        return $this->error;
    }
}

