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

define('ROOT_PATH', "./");
define('NVRTBL', '1');
	
include_once ROOT_PATH ."config.inc.php";
include_once ROOT_PATH ."includes/common.php";
include_once ROOT_PATH ."includes/classes.php";

//args process
$args = get_arguments($_POST, $_GET);

if (isset($args['folder']))
   $args['folder'] = (integer)$args['folder'];
$args['sort'] = (integer)$args['sort'];
if (isset($args['folder']))
   $args['type'] = (integer)$args['type'];

// default values
if(!isset($args['filter']))
   $args['filter']='none';
if(isset($args['filter']) && !isset($args['filterval']))
   $args['filter']='none';
if(!isset($args['type']))
   $args['type'] = get_type_by_name("all");
if(!isset($args['diffview']))
   $args['diffview'] = "off";
if(!isset($args['bestonly']))
   $args['bestonly'] = "off";
if(!isset($args['newonly']))
   $args['newonly'] = get_newonly_by_name("off");
if(!isset($args['levelset_f']))
   $args['levelset_f'] = 0;     // all by default
if(!isset($args['level_f']))  
   $args['level_f'] = 0;        // all by default
if(!isset($args['folder']))
   $args['folder'] = get_folder_by_name("contest");

try {
   
 
  $table = new Nvrtbl();

  if(isset($args['link']))
  {
  	 $tpl_params['redirect'] = "record.php?id=". $args['link'];
     $tpl_params['delay'] = 1;
     $table->template->Show('redirect', $tpl_params);
     $table->Close();
     exit;
  }
  
  if($args['to'] == 'showlinklist')
  {
    header("Content-Type: text/plain");
    echo $_SESSION['download_list'];
    exit;
  }
  
  /* Configuration of page title */
  $tpl_params = array(
	"title" => "Nevertable - Neverball Hall of Fame",
	);
  
  /* Check permissions */
  if (($args['folder'] == get_folder_by_name("incoming")) &&  !Auth::Check(get_userlevel_by_name("admin")))
  	 throw new Exception($lang['NOT_ADMIN']);
  
  /* Manage events */
  if (isset($args['rectocontest']))
  {
  	 if ( !Auth::Check(get_userlevel_by_name("admin")) )
  	   throw new Exception($lang['NOT_ADMIN']);
    
  	 $id=$args['id'];
    
     if (empty($id))
    	throw new Exception("URL error");
    	
     $tpl_params['message_array'] = array();
    
     $rec = new Record($table->db);
     $rec->LoadFromId($id);

    
     /* Deplacement */
     $ret = $rec->Move(get_folder_by_name("contest"));
  
     /* gestion des records, qqsoit le resultat, des erreurs de $rec->Move etant */
     /* non critiques, il peut y avoir modification quand même */
     if($rec->GetType()!=get_type_by_name("freestyle"))
     {
        /* Gestion */
        $ret = $table->ManageBestRecords($rec->GetFields(), $rec->GetType());
        if ($ret['isbest'])
        {
          array_push( $tpl_params['message_array'], "This is a new best record !");
          array_push( $tpl_params['message_array'], $ret['nb']."&nbsp;record(s) are best records for this level/levelset/type now.");
          array_push( $tpl_params['message_array'], $ret['beaten']."&nbsp;record(s) are obsolete.");
        }
        else
        {
          array_push( $tpl_params['message_array'], "This record is not the best one ! moved in \"oldones\" !");
        }
     }
     else
     {
     	array_push( $tpl_params['message_array'], "Record moved to contest.");
     }
     $tpl_params['redirect'] = "index.php?folder=". get_folder_by_name("incoming");
     $tpl_params['delay'] = 5;
     $table->template->Show('redirect', $tpl_params);
  }
  
  else if (isset($args['rectotrash']))
  {
  	 if ( !Auth::Check(get_userlevel_by_name("admin")) )
  	   throw new Exception($lang['NOT_ADMIN']);
    
  	 $id=$args['id'];
  	 $tpl_params['message_array'] = array();
    
     if (empty($id))
    	throw new Exception("URL error");

     $rec = new Record($table->db);
     $rec->LoadFromId($id);


     /* garde en mémoire l'état du record avant déplacement */
     $wasbest = $rec->IsBest();
     !$rec->Move(get_folder_by_name("trash"));
  
     /* gestion des records, qqsoit le résultat, des erreurs de $rec->Move étant */
     /* non critiques, il peut y avoir modification quand même */
     if($rec->GetType()!=get_type_by_name("freestyle"))
     {
        /* Gestion */
        $ret = $table->ManageBestRecords($rec->GetFields(), $rec->GetType());
        if ($wasbest)
        {
           array_push( $tpl_params['message_array'], "This was a best record...");
           array_push( $tpl_params['message_array'], $ret['nb']."&nbsp;record(s) are best records for this level/levelset/type now.");
           array_push( $tpl_params['message_array'], $ret['imports']."&nbsp;record(s) imported from \"oldones\".");
        }
     }
     
     array_push( $tpl_params['message_array'], "Record trashed.");
     
     $tpl_params['redirect'] = "index.php?folder=". $args['folder'];
     $tpl_params['delay'] = 5;
     $table->template->Show('redirect', $tpl_params);
  }
  
  else if (isset($args['recdelete']))
  {
  	 if ( !Auth::Check(get_userlevel_by_name("admin")) )
  	   throw new Exception($lang['NOT_ADMIN']);
    
  	 $id=$args['id'];
  	 $tpl_params['message_array'] = array();
    
     if (empty($id))
    	throw new Exception("URL error");

     $rec = new Record($table->db);
     $rec->LoadFromId($id);
     
     /* effacement de l'enregistrement de la bdd, plus le fichier */
     $rec->Purge(true);

     array_push( $tpl_params['message_array'], "Record deleted");
     $tpl_params['redirect'] = "index.php?folder=". get_folder_by_name("trash");
     $tpl_params['delay'] = 5;
     $table->template->Show('redirect', $tpl_params);
  }
  
  else
  {		
  	
	  /* Level mode */
	  if (isset($args['level_f']) && isset($args['levelset_f'])
	     && ($args['level_f'] > 0) && ($args['levelset_f'] > 0))
	  {
	    $mode_level = true;
	  }
	  
  	  /* Check selection */
	  if (!($mode_level && ($args['type'] > 0)))
	  {
	  	 $args['diffview'] = "off";
	  }
	  else
	  {
	  	 if ( $args['diffview'] == "on")
	  	 {
	  	 	
	  	 }
	  }
  
	  /* Keep filters */
	  $nextargs = "index.php?";
	  if (isset($args['type'])) $nextargs .= "&amp;type=".$args['type'];
	  if (isset($args['sort'])) $nextargs .= "&amp;sort=".$args['sort'];
	  if (isset($args['diffview'])) $nextargs .= "&amp;diffview=".$args['diffview'];
	  if (isset($args['newonly'])) $nextargs .= "&amp;newonly=".$args['newonly'];
	  if (isset($args['filter'])) $nextargs .= "&amp;filter=".$args['filter'];
	  if (isset($args['filterval'])) $nextargs .= "&amp;filterval=".$args['filterval'];
	  if (isset($args['levelset_f'])) $nextargs .= "&amp;levelset_f=".$args['levelset_f'];
	  if (isset($args['level_f'])) $nextargs .= "&amp;level_f=".$args['level_f'];
	  if (isset($args['folder'])) $nextargs .= "&amp;folder=".$args['folder'];

	
	  /* RÃ©cupÃ©ration de l'option utilisateur de l'ordre de tri par dÃ©faut */
	  if (empty($args['sort']) && !Auth::Check(get_userlevel_by_name("member")))
	    $sort = get_sort_by_name("old");
	  else if (empty($args['sort']) && Auth::Check(get_userlevel_by_name("member")))
	    $sort = $config['opt_user_sort'];
	  else
	    $sort = $args['sort'];
	    
	  /* Keep list of links */
	  $_SESSION['download_list'] = "";
	
	  
	  $tpl_params['last_comments'] = $table->db->helper->GetLastComments();
	  
	  /* Affichage normal */
	  /* ---------------- */
	  if (!$mode_level)
	  {
	  	 $total = $table->db->helper->CountFilteredRecords($args);
	     $tpl_params['records']  = $table->db->helper->GetFilteredRecords($args);
	     $tpl_params['total']    = $total;
	       
	     /* Configuration of navigation bar */
	     $tpl_params['page'] = empty($args['page']) ? 1 : $args['page'];
	     if ($config['limit'] > 0)
	     	$tpl_params['nb_pages']  = ceil($total / $config['limit']);
	     else
	     	$tpl_params['nb_pages']=1; 
	    
	     $table->template->Show("index", $tpl_params);
	  }
	
	  /* Mode fiche de niveau */
	  /* -------------------- */
	  else 
	  {
	     $results = $table->db->helper->GetFilteredRecordsLevel($args);
	     $tpl_params['rec_contest']  = $results['rec_contest'];
	     $tpl_params['rec_oldones']  = $results['rec_oldones'];
	     $tpl_params['diffview'] = $args['diffview']=="on" ? true : false;
	     $tpl_params['level_shot']   = $table->db->helper->GetLevelShot($args['level_f'],  $args['levelset_f']);
	     $table->template->Show("level", $tpl_params);
	  }

  }
	  
	   //$_SESSION['download_list'] .= replay_link($fields['folder'], $fields['replay'])
}
catch (DBException $ex) {
	$table->template->Show('error', array("exception" => $ex)); 
}
catch (Exception $ex) {
	$table->template->Show('error', array("exception" => $ex)); 
}


$table->Close();
?>
