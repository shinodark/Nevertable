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

define('ROOT_PATH', "../");
define('NVRTBL', 1);
include_once ROOT_PATH ."config.inc.php";
include_once ROOT_PATH ."includes/common.php";
include_once ROOT_PATH ."includes/classes.php";

//args process
$args = get_arguments($_POST, $_GET);

try {
	

$table = new Nvrtbl();

if (!Auth::Check(get_userlevel_by_name("admin")))
  throw new Exception($lang['NOT_ADMIN']);
  
$replay_path = ROOT_PATH.$config['replay_dir'];
  
$tpl_params['message_array'] = array();

/*
 * TRAITEMENT DES EVENEMENTS
 * */
if (isset($args['setadd']))
{
  $tmp_dir = ROOT_PATH. $config['tmp_dir'];
  $tmp_file = tempnam($tmp_dir, 'set_');
  if (!$tmp_file)
    throw new Exception("Error on creating temp file.");

  $f = new FileManager();
  $f->Upload($_FILES, 'setfile', $tmp_dir, basename($tmp_file), true);
  $f->Open();
	  
  try
  {
	  $set_name = trim($f->ReadLine());
	  $f->ReadLine(); /* Difficulty */
	  $set_shortname = $f->ReadLine();
	  $set_path = "map-".trim($set_shortname); /* path */
	  $f->ReadLine(); /* shot */
	  $f->ReadLine(); /* empty */
	
	  $s = new Set($table->db);
	
	  $s->SetFields(array(
		  "set_name" => $set_name,
		  "set_shortname" => $set_shortname,
		  "set_path" => $set_path,
	  ));
  
      $s->Insert();
  	  $num = 1;

	  while (!$f->IsEof())
	  {
	    $map_solfile =  basename(trim($f->ReadLine()));
	    if (!empty($map_solfile))
	    {
	      $s->AddMap($num, $map_solfile);
	    }
	    $num++;
	  }
  }
  catch (Exception  $ex)
  {
  	   $f->Unlink();
  	   throw $ex;
  }
  
  $f->Close();
  $f->Unlink();

  array_push( $tpl_params['message_array'], $lang['GUI_UPDATE_OK']);
  $tpl_params['redirect'] = "sets.php";
  $tpl_params['delay'] = 2;
  $table->template->Show('redirect', $tpl_params);
}

else if (isset($args['setaction']) && isset($args['delete']))
{ 
  array_push( $tpl_params['message_array'], "This will delete ALL maps and ALL records of this set...");
  array_push( $tpl_params['message_array'], "This will delete ALL maps and ALL records of this set...");
  array_push( $tpl_params['message_array'], '<a href="?delete2&amp;set_id='.$args['set_id'].'"><b>Yes</b></a>');
  $tpl_params['redirect'] = "sets.php";
  $tpl_params['delay'] = 0;
  $table->template->Show('redirect', $tpl_params);
}

else if (isset($args['delete2']))
{
  $s = new Set($table->db);
  $s->LoadFromId($args['set_id']);
  $s->Purge();
  
  array_push( $tpl_params['message_array'], $lang['GUI_UPDATE_OK']);
  $tpl_params['redirect'] = "sets.php";
  $tpl_params['delay'] = 2;
  $table->template->Show('redirect', $tpl_params);
}

else if (isset($args['setaction']) && isset($args['rename']))
{
  $s = new Set($table->db);
  $s->LoadFromId($args['set_id']);
  
  $fields = array("set_name" => trim(GetContentFromPost($args['newname'])));
  $s->SetFields($fields);
  $s->Update();

  array_push( $tpl_params['message_array'], $lang['GUI_UPDATE_OK']);
  $tpl_params['redirect'] = "sets.php";
  $tpl_params['delay'] = 2;
  $table->template->Show('redirect', $tpl_params);
}
else
{
  $table->template->Show('admin/sets', $tpl_params);
}


} catch (Exception $ex)
{
	$table->template->Show('error', array("exception" => $ex)); 
}
$table->Close();