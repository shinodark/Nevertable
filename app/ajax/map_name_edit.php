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

define('ROOT_PATH', dirname(dirname(__FILE__)) . '/');
define('NVRTBL', 1);
include_once ROOT_PATH ."config.inc.php";
include_once ROOT_PATH ."includes/common.php";
include_once ROOT_PATH ."includes/classes.php";


try {

$table = new Nvrtbl();

if (!Auth::Check(get_userlevel_by_name("admin")))
  throw new Exception($lang['NOT_ADMIN']);
  
/* Id is map_<id> */
$id = substr($_POST['id'], 4);
  
if (empty($id))
  throw new Exception("id error.");
if (empty($_POST['value']))
  throw new Exception("empty value not allowed.");

$content = rawurldecode($_POST['value']);
$content = str_replace( "&#038;", "&", $content );
$content = GetContentFromPost($content);
/* Name limit to 5 in database */
$content = substr($content, 0, 5);

$map = new Map($table->db);
$map->LoadFromId($id);
$map->SetFields(array("level_name" => $content));
$map->Update();

echo $content;

} catch (Exception $ex)
{
	echo $ex->getMessage(); 
}

$table->Close();

