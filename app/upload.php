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

define('ROOT_PATH', dirname(__FILE__) . '/');
define('NVRTBL', 1);
include_once ROOT_PATH ."config.inc.php";
include_once ROOT_PATH ."includes/common.php";
include_once ROOT_PATH ."includes/classes.php";

//args process
$args = get_arguments($_POST, $_GET);

$table = new Nvrtbl();

try {
	
if(isset($args['autoadd']))
{
  $tpl_params = array("redirect" => "index.php");
  $tpl_params['message_array'] = array();
	
  /* toujours off pour ce cas, puisqu'on va dans incoming d'abord */
  $overwrite == "off";

  if (!Auth::Check(get_userlevel_by_name("member")))         
  	throw new Exception($lang['NOT_MEMBER']);
  
  if (empty($_SESSION['user_id']))
  	throw new Exception($lang['GUI_INVALID_USER']);
  
  $rec = new Record($table->db);

  $fields = array(
	   "type"      => $args['type'],
	   "user_id"   => $args['user_id'],
	   "folder"    => get_folder_by_name("incoming"),
	  );
  $rec->SetFields($fields);
    
  $up_dir = ROOT_PATH. $config['replay_dir'];
    
  /* Upload du fichier */
  $maps = $table->db->helper->SelectMapsName();
  $sets = $table->db->helper->SelectShortSets();
  $f = new FileManager();
  $u = new User($table->db);
  $u->LoadFromId($rec->GetUserId());
  $replayName = sprintf("__%s_%05d.nbr",
  	$u->GetPseudo(),
  	99999//Id non encore connu à ce stade
  	);
  	

  $f->Upload($_FILES, 'replayfile', $up_dir, $replayName);
  
  try
  {	
	  /* Analyse */
	  $rep = new Replay($table->db, $f->GetFileName(), $rec->GetType());
	  
	  $rep->Init();
	
	  	 
	  /* Insertion du record */
	  $rec->SetFields($rep->GetFields());
	      
	  /* récupération de la case "goal not reached */
	  if (!$rep->IsGoalReached())
	  {
	     $rec->SetFields(array(
		     "time" => 9999,
		     "type" => get_type_by_name("freestyle"), /* force freestyle */
	     ));
	  }
	    
	  $rec->SetFields(array("replay" => $replayName));
	
	  $ret = $rec->Insert();
	  
	  /* Mise à jour de l'ID */
	  $replayName = sprintf("%s_%s_%s_%05d.nbr",
	    // get rid of spaces in file names 
	  	str_replace(" ", "_", $sets[$rec->GetSet()]),
	  	$maps[$rec->GetSet()][$rec->GetLevel()],
	  	$u->GetPseudo(),
	  	$rec->GetId()
	  );
	  
	
	  
	  $rec->SetFields(array("replay" => $replayName));
	  
      /* See if it's a best record as it should be if != freestyle */
	  if ($rec->GetType() != get_type_by_name("freestyle"))
	  {
	  	if(!is_best_record_by_type($rec->GetFields()))
	  	{
	  		/* If not force freestyle */
	  		array_push( $tpl_params['message_array'], $lang['UPLOAD_NOT_BEST_RECORD']);
	  		$rec->SetFields(array("type" => get_type_by_name("freestyle")));
	  	}
	  	else
	  	{
	  		/* We set isbest field here so admin can be sure it was verified */
	  		$rec->SetFields(array("isbest" => true));
	  	}
	  }
	  
	  $rec->Update(true);
	  
	  $rep->ChangePLayerName($_SESSION['user_pseudo']);
	  
	  $f->Move($up_dir, $replayName);
  }
  
  catch (Exception $ex)
  {
  	$ret = $f->Unlink();
  	throw $ex;
  }

  $tpl_params['delay'] = 0;
  array_push( $tpl_params['message_array'], $lang['UPLOAD_REGISTERED']);
  $record_fields =  $rec->GetFields();
  $record_fields['pseudo'] = $u->GetPseudo();
  $s = new Set($table->db);
  $s->LoadFromId($rec->GetSet());
  $record_fields['set_name'] = $s->GetName();
  $maps = $table->db->helper->SelectMapsName();
  $record_fields['level_name'] = $maps[$s->GetId()][$rec->GetLevel()];
  $tpl_params['subtemplates_array'] = array('_replay', '_record');
  $tpl_params['subparams_array'] = array(array("fields" => $record_fields), array("replay_struct" => $rep->GetStruct()));
  $tpl_params['subdivclass_array'] = array("oneresult", "oneresult" );
  
  $table->template->Show('redirect', $tpl_params);
}

else
{
  $table->template->Show('upload');
}

} catch (Exception $ex)
{
  $table->template->Show('error', array("exception" => $ex));
}

$table->Close();

