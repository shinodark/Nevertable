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
define('NVRTBL', 1);
include_once ROOT_PATH ."config.inc.php";
include_once ROOT_PATH ."includes/common.php";
include_once ROOT_PATH ."includes/classes.php";

//args process
$args = get_arguments($_POST, $_GET);

$table = new Nvrtbl();

try {
	
/* Configuration of page title */
$tpl_params = array(
	"title" => "Record",
);


/* test si le record existe, et le charge. */
$replay_id = $args['id'];
if (empty($replay_id))
      throw new Exception("URL error.");
  
if (!empty($args['comuser_id']))
{
  $comuser = new User($table->db);
  $comuser->LoadFromId($args['comuser_id']); 
}

else if (isset($args['post']))
{ 
    if (!Auth::Check(get_userlevel_by_name("member")))
      throw new Exception($lang['NOT_MEMBER']);

    if ( empty($args['content']) )
      throw new Exception($lang['COMMENTS_ERR_EMPTY_CONTENT']);
      
    $com = new Comment($table->db);
    $content = GetContentFromPost($args['content']);
    $com->SetFields(array(
                        "replay_id"  => $args['id'],
                        "user_id"    => $_SESSION['user_id'],
                        "content"    => $content)
                  );
    $com->Insert();
   
    $tpl_params = array("redirect" => "record.php?id=".$args['id']);
    $tpl_params['message_array'] = array($lang['GUI_UPDATE_OK']);
    $table->template->Show('redirect', $tpl_params);
}
 
else if (isset($args['comdel']))
{
    if (!Auth::Check(get_userlevel_by_name("moderator")))
      throw new Exception($lang['NOT_MODERATOR']);

    if (empty($args['com_id']))
      throw new Exception("URL error");

    $com = new Comment($table->db);
    $com->LoadFromId($args['com_id']);
    $com->Purge();
    
    $tpl_params = array("redirect" => "record.php?id=".$args['id']);
    $tpl_params['message_array'] = array($lang['GUI_UPDATE_OK']);
    $table->template->Show('redirect', $tpl_params);   
}


else
{

  $total = $table->db->helper->CountRecordComments($args['id']);
  $tpl_params['page'] = empty($args['page']) ? 1 : $args['page'];
  if ($config['comments_limit'] > 0)
  	$tpl_params['nb_pages']  = ceil($total / $config['comments_limit']);
  else
  	$tpl_params['nb_pages']=1; 
  $tpl_params['comments_res'] = $table->db->helper->GetComments($args);
  $tpl_params['record_fields'] = $table->db->helper->GetRecordFields($args['id']);
  $tpl_params['total'] = $total;

  $table->template->Show("record", $tpl_params);
}


}
catch (Exception $ex) {
	$table->template->Show('error', array("exception" => $ex)); 
}

$table->Close();

