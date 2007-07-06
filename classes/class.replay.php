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
define('MAXNAM',9);
define('DATELEN',20);

define('MAGIC_1_5', 0x52424EAF); /* version 1.5 */
define('MAGIC_1_4', 0x4E425250); /* verdion 1.4 */
	
class Replay
{
    var $db;
    var $replayname;
    var $fp;
    var $struct_replay;
    var $error;
    var $type;

	/*__Constructeur__
	
	Cette fonction initialise l'objet Replay.
	*/
    function Replay(&$db, $filename, $type)
    {
        $this->db = $db;
        $this->replayname = $filename;
        $this->type = $type;
	}

    function Init()
    {
        global $config;

	$f = new FileManager($this->replayname);

	if (!$f->Open())
	{
            $this->error = "Error opening file";
            return false;
	}

	$this->struct_replay['magic'] = $f->ReadInt();
        if ($this->struct_replay['magic'] != MAGIC_1_5)
	{
	    if ($this->struct_replay['magic'] == MAGIC_1_4)
               $this->error = "File is replay file from Neverball 1.4. Please convert it to 1.5 before uploading.";
	    else
               $this->error = "File is probably not a replay file";
            return false;
        }

	$this->struct_replay['version'] = $f->ReadInt();

        $this->struct_replay['timer']   = $f->ReadInt() / 100.0;
        $this->struct_replay['coins']   = $f->ReadInt() ;
        $this->struct_replay['state']   = $f->ReadInt();
	$this->struct_replay['mode']    = $f->ReadInt() ;

        $this->struct_replay['player']  = trim($f->ReadStringLength(MAXNAM));
	$this->struct_replay['date']    = trim($f->ReadStringLength(DATELEN));

        $this->struct_replay['shot']    = trim($f->ReadStringLength(PATHMAX));
	$this->struct_replay['solfile'] = trim($f->ReadStringLength(PATHMAX));

        $this->struct_replay['time']    = $f->ReadInt();
        $this->struct_replay['goal']    = $f->ReadInt();
        $this->struct_replay['score']   = $f->ReadInt();
	$this->struct_replay['balls']   = $f->ReadInt();
	$this->struct_replay['times']   = $f->ReadInt();

        $f->Close();

        $map_solfile = basename($this->struct_replay['solfile']) ;
        $set_path    = dirname($this->struct_replay['solfile'] );
        
        /*  Check if this set/level is in the database */
        $p = $config['bdd_prefix'];
        $this->db->Select(
           array("maps", "sets"),
           array(
               $p."maps.map_solfile AS map_solfile",
               $p."sets.set_path AS set_path",
               $p."sets.id AS set_id",
               $p."maps.level_num AS level_num",
           )
        );
        $this->db->Where(
           array($p."sets.id"),
           array($p."maps.set_id"),
           "AND", false
        );
        $this->db->Where(
           array($p."sets.set_path", $p."maps.map_solfile"),
           array($set_path, $map_solfile)
       );
       $this->db->Limit(1);
       if (!$this->db->Query())
       {
          $this->error = "Can't query database for validation";
          return false;
       }
       if ($this->db->NumRows() < 1)
       {
          $this->error = "Can't get valid level or set from replay file!";
          return false;
       }
       $val = $this->db->FetchArray();
       $this->level = $val['level_num'];
       $this->set   = $val['set_id'];

       /* test replay name convention */
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
        
       return true;
    }
    
    function GetTime()
    {
        return $this->struct_replay['timer'];
    }
    
    function GetCoins()
    {
        return $this->struct_replay['coins'];
    }

    function GetSolFile()
    {
        return $this->struct_replay['solfile'];
    }

    function GetMode()
    {
        return $this->struct_replay['mode'];
    }
    
    function GetState()
    {
        return $this->struct_replay['state'];
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
            "time"      => $this->struct_replay['timer'],
            "coins"     => $this->struct_replay['coins'],
            "levelset"  => $this->set,
            "level"     => $this->level,
            );
    }
    
    function GetStruct()
    {
	return $this->struct_replay;
    }

    function IsGoalReached()
    {
       if ($this->struct_replay['state'] == get_replay_state_by_name('goal')
        || $this->struct_replay['state'] == get_replay_state_by_name('spec'))
       {
	       return true;
       }
       else
       {
	       return false;
       }
    }

    function GetError()
    {
        return $this->error;
    }
}

