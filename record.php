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

if (isset($args['preview']))
{
  if ($args['to'] == "comedit" || $args['to'] == "comedit2")  
    $args['prev_to'] = "comedit";
  else if ($args['to'] == "addcomment")
    $args['prev_to'] = "addcomment";
  else
    $args['prev_to'] = "error";
  $args['to'] = 'compreview';
}

$table = new Nvrtbl("DialogStandard");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<?php $table->PrintHtmlHead("Nevertable - Neverball Hall of Fame"); ?>

<body>
<div id="page">
<?php   $table->PrintTop();  ?>
<div id="main">
<?php


/***************************************************/
/* ----------- POST  COMMENT  ---------------------*/
/***************************************************/
if($args['to'] == 'compreview')
{
   $replay_id = $_POST['id'];
   $content   = $_POST['content'];
   $user_id   = $_POST['user_id'];
   $com_id    = $args['comid'];

   if (empty($user_id) || empty($content))
   {
     button_error("You can't post an empty message ^-^",400);
    // keep content if exists
     $nextargs = "record.php?content=".CleanContent($content)."&amp;to=addcomment";
   }
   else if (empty($replay_id) || ( ($args['prev_to'] == "comedit") && empty($com_id) ))
   {
    button_error("URL error",200);
   }
   else{

   $o_user = new User($table->db);
   $o_user->LoadFromId($user_id);

   echo "<div class=\"comments\">\n";
   button("This is only a preview, nothing is submitted yet!", 400);
   echo "<br/>\n";
   /* table générale */
   echo "<table>\n";
   echo "<tr><th>Preview</th></tr>\n";
   echo "<tr><td>\n";

   /* table de contenu */
   echo "<div class=\"embedded\">\n";
   echo "<table>";
   $content_prev = GetContentFromPost($content);
   $content_prev = LineFeed2Html($content_prev);
   $content_prev = $table->dialog->bbcode->parse($content_prev, "all", false);
   $content_prev = $table->dialog->smilies->Apply($content_prev);
   echo "<tr>";
   echo "<td width=\"130px\" valign=\"top\">".$o_user->GetAvatarHtml()."</td>\n";
   echo "<td class=\"com_content\">".$content_prev."</td>\n";
   echo "</tr>\n";
   echo "</table>\n";
   echo "</div>\n";
   /* fin table de contenu */
   
   echo "</table>\n";
   echo "</div>\n";
   /* fin table générale */

   }

   switch($args['prev_to'])
   {
     case "addcomment" :  $nextargs = "record.php?to=addcomment"; break;
     case "comedit"    :  $nextargs = "record.php?to=comedit2&amp;comid=".$com_id; break;
     default:
     case error        :  $nextargs = "record.php"; break;
   }
   $jsfriendly = CleanContentPost($content);
   $table->PrintCommentForm($replay_id, $jsfriendly, $user_id);

   button("<a href=\"record.php?id=".$replay_id."\">Return to record</a>", 300);
}

else if($args['to'] == 'addcomment')
{
  $replay_id = $_POST['id'];
  $content   = $_POST['content'];
  $user_id   = $_POST['user_id'];


  if (!Auth::Check(get_userlevel_by_name("member")))
  {
    button_error("You have to log in to post a comment !", 400);
  }
  
  else 
  {
  
  if ( empty($content))
  {
    button_error("You can't post an empty message ^-^",400);
    button_back();
  }
  else if (empty($replay_id))
  {
    button_error("URL error",200);
  }
  else
  {
     $com = new Comment($table->db);
     $content = GetContentFromPost($content);
     $com->SetFields(array("id"         => $comid,
                           "replay_id"  => $replay_id,
                           "user_id"    => $user_id,
                           "content"    => $content)
                    );

     $ret = $com->Insert();

     if (!$ret)
     {
       button_error($com->GetError(), 500);
     }
     else
     {
       $u = new User($table->db);
       $ret = $u->LoadFromId($user_id);
       if ($ret)
         $u->_RecountComments();
       else
         button_error($u->GetError(), 500);
       
       $content = ""; // vide pour ne pas conserver dans commentform
       button("Your comment is added ! Thanks !", 400);
     }
    $jsfriendly = CleanContentPost($content);
    $table->Post($replay_id, $jsfriendly);
  }

  } // fin auth

  button("<a href=\"index.php\">Return to table</a>", 200);
}

/***************************************************/
/* ----------- COMMENTAIRES MODERATION ------------*/
/***************************************************/
else if ($args['to'] == 'comedit')
{
   $replay_id = $args['id'];
   $id        = $args['comid'];

   if (empty($replay_id) || empty($id))
   {
     button_error("URL error",200);
   } 
   else {

   $com = new Comment($table->db);
   $ret = $com->LoadFromId($id);
   if (!$ret)
   {
     button_error($com->GetError(), 500);
   }
   else
   {
     $auth_ok = false;

     if (Auth::Check(get_userlevel_by_name("moderator"))
     ||  Auth::CheckUser($com->GetUserId()))
     {
       $auth_ok = true;
     }

     if ($auth_ok !== true)
      button_error("You have to be a moderator or the author of this comment to edit it.", 500);
     else {
       
     if (!isset($replay_id) || !isset($id))
       button_error("URL error.", 200);
     else {

     $jsfriendly = CleanContent($com->GetContent());
     $nextargs = "record.php?comid=".$id."&amp;to=comedit2";
     $table->PrintCommentForm($replay_id, $jsfriendly, $com->GetUserId());
     }

     } // fin auth
   }

   }
   button("<a href=\"record.php?id=".$replay_id."\">Return to record</a>", 300);
}

else if($args['to'] == 'comedit2')
{
   $comid     = $args['comid'];
   $replay_id = $args['id'];
   $content   = $_POST['content'];
   $user_id   = $args['user_id'];
   $timestamp = $args['timestamp'];
     
   if (empty($comid) || empty($replay_id) || empty($user_id))
   {
     button_error("URL error",200);
   }

   else {
   
   $com = new Comment($table->db);
   
   /* on vérifie si le commentaire est valide */
   $ret = $com->LoadFromId($comid);
   if (!$ret)
   {
     button_error($com->GetError(), 500);
   }
   else
   {
     $auth_ok = false;

     if (Auth::Check(get_userlevel_by_name("moderator"))
     ||  Auth::CheckUser($com->GetUserId()))
     {
       $auth_ok = true;
     }

     if ($auth_ok !== true)
      button_error("You have to be a moderator or the author of this comment to edit it.", 500);
     else {
   
     if ( empty($content))
     {
       button_error("You can't post an empty message ^-^",400);
       button_back();
     }
     else
     {
       $content = GetContentFromPost($content);
       $com->SetFields(array("id"       => $comid,
                           "replay_id"  => $replay_id,
                           "user_id"    => $user_id,
                           "content"    => $content)
                    );

       $ret = $com->Update(true);
       if (!$ret)
       {
         button_error($com->GetError());
       }
       else
       {
         button("Comment edited !", 400);
       }
     }

   /* Reaffichage des commentaires */
   $table->Post($replay_id);

     }//fin auth
   }//fin Load comment

   }// URL error

  button("<a href=\"index.php\">Return to table</a>", 200);
}

else if ($args['to'] == 'comdel')
{
   $replay_id = $args['id'];
   $com_id    = $args['comid']; 

   if (!Auth::Check(get_userlevel_by_name("moderator")))
    button_error("You have to be a moderator to delete comments.", 400);
   else {

   if (empty($replay_id) || empty($com_id))
    button_error("URL error.", 200);
   else {

   $com = new Comment($table->db);
   $com->LoadFromId($com_id);
   $ret = $com->Purge();
   if (!$ret)
   {
     button_error($com->GetError());
   }
   else
   {
     button("Comment deleted !", 400);
   }
   
   $table->Post($replay_id);
   } // URL error
   } // fin auth

   button("<a href=\"index.php\">Return to table</a>", 200);
}

/**************************************/
/* ----------- AFFICHAGE -------------*/
/**************************************/
else
{
  $replay_id = $args['id'];
  if (empty($replay_id))
    button_error("URL error.", 200);
  else 
    $table->Post($replay_id);
  
   button("<a href=\"index.php\">Return to Table</a>", 300);
}

?>
</div> <!-- fin main-->
<?php
$table->Close();
$table->PrintFooter();
?>

</div><!-- fin "page" -->
</body>
</html>
