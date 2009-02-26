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
define('NVRTBL' ,1);
include_once ROOT_PATH ."config.inc.php";
include_once ROOT_PATH ."includes/common.php";
include_once ROOT_PATH ."includes/classes.php";

//args process
$args = get_arguments($_POST, $_GET);

try {
	

$table = new Nvrtbl();

if (!Auth::Check(get_userlevel_by_name("admin")))
  throw new Exception($lang['NOT_ADMIN']);

$manage_lang = get_lang_by_number($args['manage_lang']);
if (!in_array($manage_lang, $langs))
   $manage_lang = $lang['code'];

$langpath = ROOT_PATH . $config['lang_dir']. $manage_lang . "/";
$tpl_params['message_array'] = array();

$cache = new Cache('text');

if (isset($args['upannounce']))
{
    file_put_contents($langpath . "announce.txt", stripslashes($args['announce']));
    $cache->Dirty('announce_txt_'.$manage_lang);
    array_push( $tpl_params['message_array'], "Announcement updated.");
    $tpl_params['redirect'] = "management.php";
    $tpl_params['delay'] = 2;
    $table->template->Show('redirect', $tpl_params);
}

else if (isset($args['upspeech']))
{
    file_put_contents($langpath . "speech.txt", stripslashes($args['speech']));
    $cache->Dirty('speech_txt_'.$manage_lang);
    array_push( $tpl_params['message_array'], "Speech updated.");
    $tpl_params['redirect'] = "management.php";
    $tpl_params['delay'] = 2;
    $table->template->Show('redirect', $tpl_params);
}

else if (isset($args['upconditions']))
{
    file_put_contents($langpath . "conditions.txt", stripslashes($args['conditions']));
    $cache->Dirty('conditions_txt_'.$manage_lang);
    array_push( $tpl_params['message_array'], "Conditions updated.");
    $tpl_params['redirect'] = "management.php";
    $tpl_params['delay'] = 2;
    $table->template->Show('redirect', $tpl_params);
}

else
{

  
  $tpl_params['manage_lang'] = $manage_lang;

  if (file_exists($langpath . "announce.txt"))
    $tpl_params['announce'] = file_get_contents($langpath . "announce.txt");
  if (file_exists($langpath . "speech.txt"))
    $tpl_params['speech'] = file_get_contents($langpath . "speech.txt");
  if (file_exists($langpath . "conditions.txt"))
    $tpl_params['conditions'] = file_get_contents($langpath . "conditions.txt");

  
  $table->template->Show('admin/management', $tpl_params);
}

} catch (Exception $ex)
{
	$table->template->Show('error', array("exception" => $ex)); 
}

$table->Close();