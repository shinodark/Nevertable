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
    var $header_end_off;
    var $is_init;

	/*__Constructeur__
	
	Cette fonction initialise l'objet Replay.
	*/
    function Replay(&$db, $filename, $type)
    {
        $this->db = &$db;
        $this->replayname = $filename;
        $this->type = $type;
        
        $this->header_end_off = -1;
        $this->is_init = false;
	}

    function Init()
    {
        global $config;

		$f = new FileManager($this->replayname);
	
		$f->Open();
            
		$this->struct_replay['magic'] = $f->ReadInt();
	        if ($this->struct_replay['magic'] != MAGIC_1_5)
		{
		    if ($this->struct_replay['magic'] == MAGIC_1_4)
	               $this->SetError("File is replay file from Neverball 1.4. Please convert it to 1.5 before uploading.");
		    else
	               $this->SetError("File is probably not a replay file");
	            return false;
	        }
	
		$this->struct_replay['version'] = $f->ReadInt();
	
	    $this->struct_replay['timer']   = $f->ReadInt();
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
		
		/* Keep header end position for ChangePlayerName() */
		$this->header_end_off = $f->Tell();

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
       $this->db->Query();

       if ($this->db->NumRows() < 1)
          $this->SetError("Can't get valid level or set from replay file!");
       
       $val = $this->db->FetchArray();
       $this->level = $val['level_num'];
       $this->set   = $val['set_id'];
       
       $this->is_init = true;
        
       return true;
    }
    
    /* This function create a new replay file with the name of the player from the table */
    /* It should ne transparent to upload.php */
    function ChangePLayerName($name)
    {	
    	if (!$this->is_init)
    		return false;
    	if ($this->header_end_off == -1)
    		return false;
    	if (strlen($name) > MAXNAM)
    		$length = MAXNAM;
    	else
    		$length = strlen($name);
    	
    	$fr = new FileManager($this->replayname);
    	/* open a swap file */
    	$fw = new FileManager($this->replayname.".tmp");
	
    	$fr->Open('r');
		$fw->Open('a');
		
		/* Copy header data */
		$fw->WriteInt($this->struct_replay['magic']);
		$fw->WriteInt($this->struct_replay['version']);
		$fw->WriteInt($this->struct_replay['timer']);
		$fw->WriteInt($this->struct_replay['coins']);
		$fw->WriteInt($this->struct_replay['state']);
		$fw->WriteInt($this->struct_replay['mode']);
	    
		$fw->WriteStringLength($name, $length);
		$fw->WriteStringLength($this->struct_replay['date'], strlen($this->struct_replay['date']));
		
		$fw->WriteStringLength($this->struct_replay['shot'], strlen($this->struct_replay['shot']));
		$fw->WriteStringLength($this->struct_replay['solfile'], strlen($this->struct_replay['solfile']));
	
		$fw->WriteInt($this->struct_replay['time']);
		$fw->WriteInt($this->struct_replay['goal']);
		$fw->WriteInt($this->struct_replay['score']);
		$fw->WriteInt($this->struct_replay['balls']);
		$fw->WriteInt($this->struct_replay['times']);
		
		/* Copy datas after header */
		$fr->Seek($this->header_end_off);
		$dataswap = $fr->Read();
		$fw->Write($dataswap);
	
		/* Clode and delete first file */
		$fr->Close();
		$fr->Unlink();
		
		/* Replace with new one */
		$fw->Rename(basename($fr->filename));
		$fw->Close();
    }
    
    function GetTime()
    {
        return $this->struct_replay['timer'] / 100.0;
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
            "time"      => $this->struct_replay['timer'] / 100.0,
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

