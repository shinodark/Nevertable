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
include_once ROOT_PATH ."config.inc.php";
include_once ROOT_PATH ."includes/common.php";
include_once ROOT_PATH ."includes/classes.php";

session_start();
//args process
$args = get_arguments($_POST, $_GET);

$table = new Nvrtbl($config, "DialogStandard");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<?php $table->PrintHtmlHead("Nevertable - Neverball Hall of Fame"); ?>

<body>
<div id="page">
<div id="main">
<?php

  /* Ajout de la table */
  $table->db->RequestInit("CUSTOM");
  $table->db->RequestCustom("ALTER TABLE `nvrtbl_rec` ADD `user_id` INT NOT NULL AFTER `pseudo`");
  $res = $table->db->Query();
  if (!$res)
    {
      echo "Erreur ajout de la table user_id";
      exit(1);
    }
    
  /* ouvre tous les users */
  $table->db->RequestInit("SELECT", "users");
  $res = $table->db->Query();

  $user_hash = array();

  while($val = $table->db->FetchArray($res))
  {
    $user_hash[$val['pseudo']] = $val['id'];

    echo "User #".$val['id']." : ".$val['pseudo']." -> ". $val['id'] ."<br/>\n";
  }

  echo "<br/><br/>\n";
  
  /* ouvre tous les records */
  $table->db->RequestInit("SELECT", "rec");
  $res = $table->db->Query();

  /* garde trace des records d'utilisateurs non enregistrés */
  $old_users_ind = -1;
  $old_users = array();

  $user_id=0;
  
  while($val = $table->db->FetchArray($res))
  {

    $table->db->RequestInit("UPDATE", "rec");
    /* Le pseudo est un user enregistré */
    if ( array_key_exists ($val['pseudo'], $user_hash) )
    {
       $table->db->RequestUpdateSet(array("user_id" => $user_hash[$val['pseudo']]), true);
       echo "Record #".$val['id']." : ".$val['pseudo']." -=> UID ".$user_hash[$val['pseudo']]."<br/>\n";
    }
    else /* Le pseudo est qqn de pas enregistré */
    {
       if (in_array ($val['pseudo'], $old_users))
       {
         $user_id = array_search ( $val['pseudo'], $old_users);
       }
       else
       {
         $old_users[$old_users_ind] = $val['pseudo'];
         $user_id = $old_users_ind;
         $old_users_ind--;
       }

       $table->db->RequestUpdateSet(array("user_id" => $user_id), true);
       echo "Record #".$val['id']." : ".$val['pseudo']." keep and UID set to ".$user_id." (non-member)<br/>\n";
    }
       
    $table->db->RequestGenericFilter("id", $val['id']);
    $table->db->RequestCustom("LIMIT 1");
    $table->db->Query();
  }

 /* Suppression pseudo */
 $table->db->RequestInit("CUSTOM");
 $table->db->RequestCustom("ALTER TABLE `nvrtbl_rec` DROP `pseudo`");
 $res = $table->db->Query();
 if (!$res)
 {
   echo "Erreur suppression de pseudo";
   exit(1);
 }

 echo "<br/><br/>\n";
 echo "Old users dump : <br/>\n";
 echo '$old_users = array(<br/>';
 foreach ($old_users as $i => $n)
 {
     echo $i ."=> \"".$n."\",<br/>\n";
 }
 echo ");<br/>\n";
 
 
?>
</div><!-- fin "main" -->

<?php
/* Close avant le footer, car db inutile et pour les statistiques de temps */
$table->Close();
$table->PrintFooter();
?>

</div><!-- fin "page" -->
</body>
</html>
