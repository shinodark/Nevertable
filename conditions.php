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

$table = new Nvrtbl();

try {
	
/* Cache for texts */
$cache = new Cache("text");
	
$langpath = ROOT_PATH . $config['lang_dir'] . $config['opt_user_lang'] . "/";
$conditions  = $langpath . "conditions.txt";

if ($cache->Hit('conditions_txt_'.$config['opt_user_lang']))
   echo $cache->Read();
else
{
   $f = new FileManager($conditions);
   if ($f->Exists())
   {
      $content = $table->template->RenderText($f->ReadString());
      $cache->Create('conditions_txt_'.$config['opt_user_lang'], $content);
   }
}
    
$tpl_params['content'] = $content . '<br/>'. gui_button_back();
$table->template->Show("generic", $tpl_params);

} catch (Exception $ex)
{
	$table->template->Show('error', array("exception" => $ex)); 
}	
	
$table->Close();
