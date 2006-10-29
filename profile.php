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
<?php $table->PrintHtmlHead("Nevertable - Neverball Hall of Fame"); ?>

<body>
<div id="page">
<?php   $table->PrintTop();  ?>
<div id="main">
<?php

function closepage()
{  global $table;
    echo "</div><!-- fin \"main\" -->\n";
    $table->Close();
    $table->PrintFooter();
    echo "</div><!-- fin \"page\" -->\n";
    echo "</body>\n";
    echo "</html>\n";
}
    
if (!Auth::Check(get_userlevel_by_name("member")))
{          
  button_error("You need to log in to edit your profile !", 400);
  button_back();
  closepage();
  exit;
}

else if(isset($args['editpasswd']))
{
  if ( empty($args['passwd1']) || empty($args['passwd2']) )
  {
    button_error("Empty fields. Pseudo, email and password have to be filled.", 400);
    exit;
  }
  
  if ($args['passwd1'] != $args['passwd2'])
  {
    button_error("Passwords don't match. Try again...", 400);
    button_back();
    closepage();
    exit;
  }
  
  //mise à jour du nouveau mot de passe
  if (!isset($_SESSION['user_id']))
    exit;
  $table->db->RequestInit("UPDATE",  "users");
  $table->db->RequestUpdateSet(array("passwd" => md5($args['passwd1'])), $conservative);
  $table->db->RequestGenericFilter("id", $_SESSION['user_id']);
  $table->db->RequestLimit(1);
  if(!$table->db->Query()) {
    button_error($table->db->GetError(),500);
    button_back();
    closepage();
    exit;
  }

  button("Passsword changed.", 400);
  button("<a href=\"./\">Return to main page</a>", 200);
  
  echo "</div>\n";
  $table->Close();
  $table->PrintFooter();
  exit;
}

else if(isset($args['upavatar']))
{
  $up_dir = ROOT_PATH. $config['avatar_dir'];
  $tmp_dir = ROOT_PATH. $config['tmp_dir'];
  $tmp_file = tempnam($tmp_dir, 'av_');
  if (!$tmp_file)
  {
    button_error("Error on creating temp file.", 300);
    button_back();
  }
  else {

  $f = new FileManager();
  $ret = $f->Upload($_FILES, 'uploadfile', $tmp_dir, basename($tmp_file), true);

  if(!$ret)
  {
    button_error($f->GetError(), 500);
    button_back();
  }
  
  else {

    $u = new User($table->db);
    if(!$u->LoadFromId($_SESSION['user_id']))
    {
       button_error($u->GetError(), 400);
    }
    else {

    /* Analyse */
    $picprop = getimagesize($tmp_file);
    if (!$picprop)
    {
      button_error("Error getting image attributes.", 400);
      $ret = $f->Unlink();
      if(!$ret) button_error($f->GetError(), 500);
      button_back();
    }
    else
    {
      /* Vérification des limites */
      if ($picprop[0] > $config['avatar_width_max']
        ||$picprop[1] > $config['avatar_height_max']
         )
      {
        button_error("Image too large, please use size < 128x128.", 400);
      }
      else {
        /* effacement du fichier ancien si existant */
        $old_avatar = $u->GetAvatar();
        if (!empty($old_avatar))
        {
          $oldf = new FileManager($config['avatar_dir'].$old_avatar);
          $oldf->Unlink();
        }
        
        /* copie du fichier définitif */
        $new_name = strtolower(md5($_SESSION['user_pseudo'].$_SESSION['user_id']) .".".$imagetypes[$picprop[2]]);
        $f->Move($up_dir, $new_name, true);

        button("File successfully uploaded.", 400);

        /* mise à jour profil */

       $u->SetFields(array(
            "user_avatar"  => $new_name,
        ));

       $u->Update();

      } /* Vérifications */
    } /* getimagesize () */
    } /* user -> LoadFromId   */
  } /* Upload() */
  } /* tempnam() */
button("<a href=\"profile.php\">Return to profile</a>", 300);
}

else if(isset($args['editinfos']))
{
  $localisation = $args['localisation'];
  $web = $args['web'];
  $speech = $args['speech'];

  $localisation = GetContentFromPost($localisation);
  $web = GetContentFromPost($web);
  $speech = GetContentFromPost($speech);

  if(strlen($speech) > $config['profile_quote_max'])
  {
    button_error("quote too long", 300);
  }
  else
  {
  
  $u = new User($table->db);
  if(!$u->LoadFromId($_SESSION['user_id']))
  {
     button_error($u->GetError(), 400);
  }
  $u->SetFields(array(
      "user_localisation"   => $localisation,
      "user_web"            => $web,
      "user_speech"         => $speech,
      ));
  if($u->Update())
    button("Profile updated !", 300);
  else
    button_error($u->GetError(), 400);
  }
  button("<a href=\"profile.php\">Return to profile</a>", 300);
  
}

else if(isset($args['editopts']))
{
  $sort              = $args['sort'];
  $theme             = $args['theme'];
  $limit             = (integer) $args['limit'];
  $sidebar_comments  = (integer) $args['sidebar_comments'];
  $sidebar_comlength = (integer) $args['sidebar_comlength'];
  
  $u = new User($table->db);
  if(!$u->LoadFromId($_SESSION['user_id']))
  {
     button_error($u->GetError(), 400);
  }
  else {

  $u->SetFields(array(
      "user_sort"      => $sort,
      "user_theme"      => $theme,
      "user_limit"      => $limit,
      "user_sidebar_comments"  => $sidebar_comments,
      "user_sidebar_comlength" => $sidebar_comlength,
      /* on remet lest autres champs en lest protégeant */
      "user_localisation"   => addslashes($u->GetLocalisation()),
      "user_web"            => addslashes($u->GetWeb()),
      "user_speech"         => addslashes($u->GetSpeech()),
      ));
  if($u->Update())
    button("Profile updated !", 300);
  else
    button_error($u->GetError(), 400);
  }
  button("<a href=\"profile.php\">Return to profile</a>", 300);
}

else
{
  $table->PrintProfile();

?>
 <!-- Password -->
 <div class="nvform" style="width: 500px;">
 <form class="nvform" id="password" name="password" action="profile.php?editpasswd" method="post">
 <table><tr>
 <th colspan="2" align="center"> Password change </th></tr>
 <tr>
 <td width="200px"><label for="pseudo">name : </label></td>
 <td><input type="text" id="pseudo" name="pseudo" size="30" value="<?php echo $_SESSION['user_pseudo']?>" readonly /></td>
 </tr><tr>
 <td><label for="passwd1">password : </label></td>
 <td><input type="password" id="passwd1" name="passwd1" size="30" /></td>
 </tr><tr>
 <td><label for="passwd2">password again : </label></td>
 <td><input type="password" id="passwd2" name="passwd2" size="30" /></td>
 </tr><tr>
 <td colspan="2"><center><input type="submit" value="Save" /></center></td>
 </form>
 </tr></table>
 </div>

 <!-- Personal infos -->
 <div class="nvform" id="personnal" name="personnal" style="width: 500px;">
 <form class="nvform" action="profile.php?editinfos" method="post">
 <table><tr>
 <th colspan="2" align="center"> Personals </th></tr>
 <tr>
 <td width="200px"><label for="localisation">Localisation : </label></td>
 <td><input type="text" id="localisation" name="localisation" size="35" value="<?php echo $table->current_user->GetLocalisation()?>" /></td>
 </tr><tr>
 <td><label for="web">Personnal site : </label></td>
 <td><input type="text" id="web" name="web" size="35" value="<?php echo $table->current_user->GetWeb()?>" /></td>
 </tr><tr>
 <td><label for="speech">Personnal quote : </label></td>
 <td><textarea id="speech" name="speech" rows="4" cols="35" style="width:250px;"><?php echo $table->current_user->GetSpeech() ?></textarea></td>
 </tr><tr>
 <td colspan="2"><center><input type="submit" value="Save" /></center></td>
 </tr></table>
 </form>
 </div>
 
 <!-- Options -->
 <div class="nvform" style="width: 500px;">
 <form class="nvform" id="options" name="options" action="profile.php?editopts" method="post">
 <table><tr>
 <th colspan="2" align="center"> Options </th></tr>
 <tr>
 <td width="200px"><label for="sort">Default sort : </label></td>
 <td>
 <select id="sort" name="sort">
   <option value="old">most recent first</option>
   <option value="pseudo">by pseudo</option>
   <option value="level">by level</option>
   <option value="type">by type</option>
   <option value="time">by time</option>
   <option value="coins">by coins</option>
 </select>
 </td>
 </tr><tr>
 <td><label for="theme">Theme : </label></td>
 <td>
 <select id="theme" name="theme">
   <?php
     global $themes;
     foreach ($themes as $key)
       echo "<option value=\"".$key."\">".$key."</option>\n";
   ?>
 </select>
 </td>
 </tr><tr>
 <td><label for="limit">Record limit display : </label></td>
 <td><input type="text" id="limit" name="limit" size="3" value="<?php echo $table->current_user->GetLimit()?>" /></td>
 </tr><tr>
 <td><label for="sidebar_comments">Sidebar last comments : </label></td>
 <td><input type="text" id="sidebar_comments" name="sidebar_comments" size="3" value="<?php echo $table->current_user->GetSidebarComments()?>" /></td>
 </tr><tr>
 <td><label for="sidebar_comlength">Sidebar comments preview length : </label></td>
 <td><input type="text" id="sidebar_comlength" name="sidebar_comlength" size="3" value="<?php echo $table->current_user->GetSidebarComLength()?>" /></td>
 </tr><tr>
 <td colspan="2"><center><input type="submit" value="Save" /></center></td>
 </tr></table>
 </form>

 <script type="text/javascript">update_profile_optionform_fields("<?php echo $table->current_user->GetSort() ?>","<?php echo get_theme_by_name($table->current_user->GetTheme()) ?>")</script>
 </div>

 <!-- Avatar -->
 <div class="nvform" id="avatar" name="avatar" style="width: 500px;">
 <form enctype="multipart/form-data" class="nvform" action="profile.php?upavatar" method="post">
 <table><tr>
 <th colspan="2" align="center"> Upload avatar </th>
 </tr>
 <tr>
 <td colspan="2">Image size must not exceed <?php echo $config['avatar_width_max']?>x<?php echo $config['avatar_height_max']?>, and size &lt; <?php echo $config['avatar_size_max']/1024?> kB.</td>
 </tr>
 <tr>
 <td width="200px"><input type="hidden" name="MAX_FILE_SIZE" value="<?php global $config; echo $config['avatar_size_max'] ?>" />
 <label for="uploadfile">Select a picture : </label></td>
 <td><input name="uploadfile" type="file" /></td>
 </tr>
 <tr>
 <td colspan="2"><center><input type="submit" value="Upload" /></center></td>
 </tr></table>
 </form>
 </div>



 
<?php
button("<a href=\"index.php\">Return to table</a>", 300);
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
