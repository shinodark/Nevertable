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
	
$table->db->NewQuery("SELECT", "users");
/* Contre le problème du osrt=0 si aucun get n'est passé */
if (!isset($_GET['sort']))
   $args['sort'] = 2;
switch($args['sort'])
{
   case 0: $table->db->Sort(array("pseudo"), "ASC"); break;
   case 1: $table->db->Sort(array("stat_total_records", "stat_best_records"), array("DESC", "DESC"));
       break;
   default: 
   case 2: $table->db->Sort(array("stat_best_records", "stat_total_records"), array("DESC", "DESC"));
       break;
   case 3: $table->db->Sort(array("stat_comments"), "DESC"); break;
   case 4: $table->db->Sort(array("level"), "ASC"); break;
   case 5: $table->db->Sort(array("id"), "ASC"); break;
}
$tpl_params['members'] = $table->db->Query();
$table->template->Show('memberlist', $tpl_params);

} catch (Exception $ex)
{
  $table->template->Show('error', array("exception" => $ex));
}
