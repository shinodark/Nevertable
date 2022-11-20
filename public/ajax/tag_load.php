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

try {
	

$table = new Nvrtbl();

/* Id is tag_<id> */
$id = substr($_GET['id'], 4);

$table->db->NewQuery("SELECT", "tags");
$p = $config['bdd_prefix'];
$table->db->Select(
        array("tags"),
        array($p."tags.content AS content")
        );
$table->db->Where(
        array($p."tags.id"),
        array($id)
        );
$table->db->Limit(1);
$table->db->Query();
$val = $table->db->FetchArray();
echo $val['content'];

} catch (Exception $ex)
{
	echo $ex->getMessage(); 
}

$table->Close();

