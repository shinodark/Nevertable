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
include_once ROOT_PATH ."config.inc.php";
include_once ROOT_PATH ."includes/common.php";
include_once ROOT_PATH ."includes/classes.php";

//args process
$args = get_arguments($_POST, $_GET);

$table = new Nvrtbl("DialogStandard");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<?php $table->dialog->Head("Nevertable - Neverball Hall of Fame"); ?>

<body>
<div id="page">
<?php   $table->dialog->Top();  ?>
<div id="main">
<?php

function closepage()
{   global $table;
    gui_button_back();
    echo "</div><!-- fin \"main\" -->\n";
    $table->Close();
    $table->dialog->Footer();
    echo "</div><!-- fin \"page\" -->\n</body>\n</html>\n";
    exit;
}

if (empty($args['id']))
{
  gui_button_error("URL error", 400);
  closepage();
}

/* test si le record existe, et le charge. */
$replay_id = $args['id'];
if (empty($replay_id)) {
  gui_button_error("URL error.", 200);
  closepage();
}
  
$rec = new Record($table->db);
if(!$rec->LoadFromId($args['id']))
{
  gui_button_error($rec->GetError(), 400);
  closepage();
} 

if (!empty($args['comuser_id']))
{
  $comuser = new User($table->db);
  if(!$comuser->LoadFromId($args['comuser_id']))
  {
    gui_button_error($user->GetError(), 400);
    closepage();
  } 
}

/*
   TRAITEMENT DES EVENEMENTS 
*/

  if (isset($args['upcomments']) && !isset($args['preview']))
  { 
    if (!Auth::Check(get_userlevel_by_name("member")))
    {          
      gui_button_error($lang['NOT_MEMBER'], 400);
      closepage();
    }

    if ( empty($args['content']) )
    {
      gui_button_error($lang['COMMENTS_ERR_EMPTY_CONTENT'], 400);
    }

    else if (empty($args['comuser_id']))
    {
      gui_button_error("URL error", 400);
      closepage();
    }
    else
    {
      $com = new Comment($table->db);
      $content = GetContentFromPost($args['content']);
      $com->SetFields(array(
                           "replay_id"  => $args['id'],
                           "user_id"    => $args['comuser_id'],
                           "content"    => $content)
                    );
     $ret = $com->Insert();
     if (!$ret) {
       gui_button_error($com->GetError(), 500);
     }
     else
     {
       gui_button($lang['GUI_UPDATE_OK'], 300);
     }

    }
  }
  
  /* Edition du commentaire par l'utilisateur */
  if (isset($args['comedit']))
  { 
    if (!Auth::Check(get_userlevel_by_name("member")))
    {          
      gui_button_error($lang['NOT_MEMBER'], 400);
      closepage();
    }

    if (empty($args['com_id']) || empty($args['comuser_id']) )
    {
      gui_button_error("URL error", 400);
      closepage();
    }
    $com = new Comment($table->db);
    $ret = $com->LoadFromId($args['com_id']);
    if (!$ret)
    {
      gui_button_error($com->GetError(), 500);
      closepage();
    }

    $jsfriendly = CleanContent($com->GetContent());
    $nextargs = "record.php?comedit2";
    $table->dialog->CommentForm($comuser->GetId(), $comuser->GetPseudo(), $jsfriendly, $nextargs);
    closepage();
  }
 
  if (isset($args['comedit2']) && !isset($args['preview']) )
  { 
    if (!Auth::Check(get_userlevel_by_name("member")))
    {          
      gui_button_error($lang['NOT_MEMBER'], 400);
      closepage();
    }
    
    if (empty($args['com_id']) || empty($args['comuser_id']) )
    {
      gui_button_error("URL error", 400);
      closepage();
    }
    
    $com = new Comment($table->db);
    $ret = $com->LoadFromId($args['com_id']);
    if (!$ret)
    {
      gui_button_error($com->GetError(), 500);
      closepage();
    }
 
    $auth_ok = false;
    if (Auth::Check(get_userlevel_by_name("moderator"))
    ||  Auth::CheckUser($comuser->GetId()))
    {
       $auth_ok = true;
    }

    if ($auth_ok !== true)
    {
      gui_button_error($lang['NOT_MODERATOR'], 500);
      closepage();
    }

    if ( empty($args['content']))
    {
      gui_button_error($lang['COMMENTS_ERR_EMPTY_CONTENT'],400);
      closepage();
    }

    $content = GetContentFromPost($args['content']);
    $com->SetFields(array("content"    => $content));
                           
    $ret = $com->Update(true);
    if (!$ret)
       gui_button_error($com->GetError(), 400);
    else
       gui_button($lang['GUI_UPDATE_OK'], 300);
  }
 
  if (isset($args['comdel']))
  { 
    if (!Auth::Check(get_userlevel_by_name("moderator")))
    {          
      gui_button_error($lang['NOT_MODERATOR'], 400);
      closepage();
    }

    if (empty($args['com_id']))
    {
      gui_button_error("URL error", 400);
      closepage();
    }

    $com = new Comment($table->db);
    $com->LoadFromId($args['com_id']);
    $ret = $com->Purge();
    if (!$ret)
      gui_button_error($com->GetError());
    else
      gui_button($lang['GUI_UPDATE_OK'], 400);
  }

  if(isset($args['preview']))
  {
    if (empty($args['content']))
    {
      gui_button_error($lang['COMMENTS_ERR_EMPTY_CONTENT'], 400);
    }

    else {

    gui_button($lang['COMMENTS_PREVIEW_WARNING'], 400);
    echo "<br/>\n";
    /* Affichage du preview */
    echo "<div class=\"comments\">\n";
    echo "<div class=\"comments_content\">\n";
    /* table générale */
    echo "<table>\n";
    echo "<tr><th>".$lang['COMMENTS_PREVIEW_TITLE'] ."</th></tr>\n";
    echo "<tr><td>\n";

    /* table de contenu */
    echo "<div class=\"embedded\">\n";
    echo "<table>";
    $content_prev = GetContentFromPost($args['content']);
    $content_prev = LineFeed2Html($content_prev);
    $content_prev = $table->dialog->bbcode->parse($content_prev, "all", false);
    $content_prev = $table->dialog->smilies->Apply($content_prev);
    echo "<tr>";
    echo "<td width=\"130px\" valign=\"top\">".$comuser->GetAvatarHtml()."</td>\n";
    echo "<td class=\"com_content\">".$content_prev."</td>\n";
    echo "</tr>\n";
    echo "</table>\n";
    echo "</div>\n"; /* fin table de contenu */
   
    echo "</table>\n";
    echo "</div>\n"; /* fin comments_contents */
   
   
    $jsfriendly = CleanContent($args['content']);
    if (isset($args['comedit2']))
      $nextargs = "record.php?comedit2";
    else
      $nextargs = "record.php?upcomments";
    $table->dialog->CommentForm($comuser->GetId(), $comuser->GetPseudo(), $jsfriendly, $nextargs);
    
    echo "</div>\n"; /* fin comments */
    closepage();
    }
  }

/*
   AFFICHAGE DES COMMENTAIRES
 */
  $table->dialog->Record($rec->GetFields());
  $table->dialog->RecordLink($rec->GetFields());

  $table->dialog->Output('<div id="comments">'."\n");

 
  /* gestion du numéro de page et de l'offset */
  $off = ($args['page']-1) * $config['comments_limit'];
      
  /* Comptage du nombre total */
  $table->db->RequestInit("SELECT", "com", "COUNT(id)");
  $table->db->RequestGenericFilter("replay_id", $replay_id);
  $table->db->Query();
  $val = $table->db->FetchArray();
  $total = $val['COUNT(id)'];

  $results = $table->db->helper->RequestMatchRecords(array("id" => $replay_id));
  $p = $config['bdd_prefix'];
  $table->db->RequestSelectInit(
         array("com", "users"),
            array(
                $p."com.id AS id",
                $p."com.replay_id AS replay_id",
                $p."com.user_id AS user_id",
                $p."com.content AS content",
                $p."com.timestamp AS timestamp",
                $p."users.pseudo AS user_pseudo",
                $p."users.user_avatar AS user_avatar",
                ),
            "SELECT", "com");
  $table->db->RequestGenericFilter($p."com.user_id", $p."users.id", "AND", false);
  $table->db->RequestGenericFilter($p."com.replay_id", $replay_id);
  $table->db->RequestGenericSort(array($p."com.timestamp"), "ASC");
  $table->db->RequestLimit($config['comments_limit'], $off);
  if (!$res = $table->db->Query())
     gui_button_error("error on query.");

  $table->dialog->Comments($res);
  

/*
   AFFICHAGE DU FORMULAIRE
*/
  
if (!Auth::Check(get_userlevel_by_name("member")))
{
  gui_button($lang['LOGIN_TO_POST'], 400);
}
else
{
 $table->dialog->CommentForm($_SESSION['user_id'], $_SESSION['user_pseudo'], "", 'record.php?upcomments') ;
}

  $table->dialog->NavBar($total, $config['comments_limit'], 'record.php', '&amp;id='.$args['id']);

$table->dialog->Output('</div><!-- comments -->');

gui_button_main_page();

?>
</div> <!-- fin main-->
<?php
$table->Close();
$table->dialog->Footer();
?>

</div><!-- fin "page" -->
</body>
</html>
