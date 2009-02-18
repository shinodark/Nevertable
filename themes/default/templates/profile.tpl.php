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
if (!defined('NVRTBL'))
	exit;
	
/* Template for profile page */
/**
 * @param: user User object
 * @param: edit_enable Enable editing forms
 * */

global $lang, $config;

if (!isset($edit_enable))
	$edit_enable = false;


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang['code'] ?>" lang="<?php echo $lang['code'] ?>">
<head>
<?php $this->SubTemplate('_head'); ?>
</head>
<body>
<div id="page">
<div id="top">
<?php $this->SubTemplate('_top');?>
</div> 
<div id="main">
<div id="prelude">
<?php $this->SubTemplate('_menu');?>
</div>

<div class="generic_form" style="width: 700px;">
<table>
<tr>
<th colspan="2"><?php echo sprintf($lang['PROFILE_TITLE'], $user->GetPseudo()) ?></th>
</tr>
<tr>
<td>
<div class="embedded">
<table>
<tr><td width="130" valign="top">
<center><?php echo $user->GetAvatarHtml() ?></center>
<br/>
<center><?php echo $lang[get_userlevel_by_number($user->GetLevel())] ?></center>

<br/>
<center>
<?php
    for($i=0; $i<CalculRank($user->GetBestRecords()); $i++)
      echo $this->table->style->GetImage('rank');
?>
</center>
</td><td>
<table>
<tr>
<td class="row2" width="150"><?php echo $lang['PROFILE_LOCALISATION'] ?> </td>
<td class="row1"><?php echo $user->GetLocalisation() ?></td>
</tr>
<tr>
<td class="row2"><?php echo $lang['PROFILE_WEB'] ?></td>
<td class="row1"><?php echo $user->GetWebHtml() ?></td>
</tr>
<tr>
<td class="row2"><?php echo $lang['PROFILE_SPEECH'] ?></td>
<td class="row1"><?php echo $this->Rendertext($user->GetSpeech()) ?></td>
</tr>
</table>
<br/>
<table>
<tr>
<td class="row2" width="150"><?php echo $lang['PROFILE_TOTAL_RECORDS'] ?></td>
<td class="row1"><?php echo $user->GetTotalRecords() ?></td>
</tr>
<tr>
<td class="row2"><?php echo $lang['PROFILE_BEST_RECORDS'] ?></td>
<td class="row1"><?php echo $user->GetBestRecords() ?></td>
</tr>

<tr>
<td class="row2"><?php echo $lang['PROFILE_BEST_TIME'] ?></td>
<td class="row1">
<?php
    for ($i=0; $i<$user->CountBestRecords_WithType(get_type_by_name("best time")); $i++)
      echo $this->table->style->GetImage('best time');
?>
</td>
</tr>
<tr>
<td class="row2"><?php echo $lang['PROFILE_BEST_COINS'] ?></td>
<td class="row1">
<?php
    for ($i=0; $i<$user->CountBestRecords_WithType(get_type_by_name("most coins")); $i++)
      echo $this->table->style->GetImage('most coins');
?>
</td>
</tr>
<tr>
<td class="row2"><?php echo $lang['PROFILE_FAST_UNLOCK'] ?></td>
<td class="row1">
<?php
    for ($i=0; $i<$user->CountBestRecords_WithType(get_type_by_name("fast unlock")); $i++)
      echo $this->table->style->GetImage('fast unlock');
?>
</td>
</tr>
<tr>
<td class="row2"><?php echo $lang['PROFILE_FREESTYLE'] ?></td>
<td class="row1">
<?php
    for ($i=0; $i<$user->CountBestRecords_WithType(get_type_by_name("freestyle")); $i++)
      echo $this->table->style->GetImage('freestyle');
?>
</td>
</tr>
<tr>
<td class="row2"><?php echo $lang['PROFILE_COMMENTS'] ?></td>
<td class="row1"><?php echo $user->GetComments(); ?></td>
</tr>
<tr>
<td colspan="2">
</td>
</tr>
</table>
</td>
</tr>
<tr>
<td colspan="2">
<center>
<a href="<?php echo ROOT_PATH ?>/index.php?folder=0&amp;filter=user_id&amp;filterval=<?php echo $user->GetId() ?> "><?php echo sprintf($lang['PROFILE_VIEWALL'],$user->GetPseudo()) ?></a>
<br/>
<a href="<?php echo ROOT_PATH ?>/index.php?folder=0&amp;type=<?php echo get_type_by_name("freestyle") ?>&amp;filter=user_id&amp;filterval=<?php echo $user->GetId() ?> "><?php echo sprintf($lang['PROFILE_VIEWALL_PERSONNALS'],$user->GetPseudo()) ?></a>
<br/>
<a href="<?php echo ROOT_PATH ?>/index.php?folder=1&amp;filter=user_id&amp;filterval=<?php echo $user->GetId() ?>"><?php echo sprintf($lang['PROFILE_VIEWALL_CONTEST'],$user->GetPseudo()) ?></a>
</center>
</td>
</tr>
</table>

</div>
</td>
</tr>
</table>
</div>


<?php if ($edit_enable) { ?>

<!--  Ident --> 
<div  class="generic_form" style="width: 600px;">
<form method="post" action="profile.php?upident&amp;id=<?php echo $user->GetId() ?>" name="password" >
<table><tr>
<th colspan="2" align="center"><?php echo $lang['PROFILE_FORM_IDENT_TITLE'] ?></th></tr><tr>
<td><label for="pseudo"><?php echo $lang['PROFILE_FORM_IDENT_PSEUDO'] ?></label></td>
<td colspan="1"><input type="text" id="pseudo" name="pseudo"  size="20" value="<?php echo $user->GetPseudo()?>" readonly="readonly"  /></td>
</tr><tr>
<td><label for="email"><?php echo $lang['PROFILE_FORM_IDENT_MAIL'] ?></label></td>
<td colspan="1"><input type="text" id="email" name="email"  size="20" value="<?php echo $user->GetMail() ?>" /></td>
</tr><tr>
<td><label for="passwd1"><?php echo $lang['PROFILE_FORM_IDENT_PASSWD1'] ?></label></td>
<td colspan="1"><input type="password" id="passwd1" name="passwd1"  size="20" /></td>

</tr><tr>
<td><label for="passwd2"><?php echo $lang['PROFILE_FORM_IDENT_PASSWD2'] ?></label></td>
<td colspan="1"><input type="password" id="passwd2" name="passwd2"  size="20" /></td>
</tr><tr>
<td colspan="2"><center><input type="submit"  /></center></td>
</tr></table></form>
</div>


<!-- Personnal infos -->
<div  class="generic_form" style="width: 600px;">
<form method="post" action="profile.php?upinfos&amp;id=<?php echo $user->GetId() ?>" name="personnal" >
<table><tr>
<th colspan="2" align="center"><?php echo $lang['PROFILE_FORM_INFO_TITLE'] ?></th></tr><tr>
</tr><tr>
<td colspan="2"><center><?php echo $lang['PROFILE_FORM_INFO_INFO'] ?></center></td></tr><tr>
<td><label for="user_localisation"><?php echo $lang['PROFILE_FORM_INFO_LOCAL'] ?></label></td>
<td colspan="1"><input type="text" id="user_localisation" name="user_localisation" value="<?php echo $user->GetLocalisation() ?>" size="35" maxlength="40" /></td>
</tr><tr>
<td><label for="user_web"><?php echo $lang['PROFILE_FORM_INFO_WEB'] ?></label></td>
<td colspan="1"><input type="text" id="user_web" name="user_web" value="<?php echo  $user->GetWeb() ?>" size="35" maxlength="80"  /></td>
</tr><tr>
<td><label for="user_speech"><?php echo $lang['PROFILE_FORM_INFO_SPEECH'] ?></label></td>
<td colspan="1">
<textarea id="user_speech" name="user_speech" rows="4" cols="" style="width:100%;">
<?php echo $user->GetSpeech() ?>
</textarea>
</td>
</tr><tr>
<td colspan="2"><center><input type="submit"  /></center></td>
</tr></table></form>
</div>

<div  class="generic_form" style="width: 600px;">
<form method="post" action="profile.php?upavatar&amp;id=<?php echo $user->GetId() ?>" name="avatar_form"  enctype="multipart/form-data">
<table><tr>
<th colspan="2" align="center"><?php echo $lang['PROFILE_FORM_AVATAR_TITLE'] ?></th></tr><tr>
</tr>
<tr>
<td colspan="2">
<center>
<?php echo sprintf($lang['PROFILE_FORM_AVATAR_LIMITS'], $config['avatar_width_max'], $config['avatar_height_max'], $config['avatar_size_max']/1024) ?>
</center>
</td>
</tr>
<tr>
<td><label for="uploadfile"><?php echo $lang['PROFILE_FORM_AVATAR_FILE'] ?></label></td>
<td colspan="1"><input type="file" id="uploadfile" name="uploadfile"  size="40" /></td>
<td colspan="2"><input type="hidden" id="size_max" name="MAX_FILE_SIZE"  value="<?php echo $config['avatar_size_max'] ?>" /></td>
</tr>
<tr>
<td colspan="2">
<center>
<input type="submit"  value="<?php echo $lang['PROFILE_FORM_AVATAR_DEL'] ?>" name="delavatar" />
</center>
</td>
</tr>
<tr>
<td colspan="2">
<center><input type="submit"  /></center>
</td>
</tr>
</table>
</form>
</div>

<?php } ?>

<div class="button" style="width:200px;">
<a href="index.php"><?php echo $lang['GUI_BUTTON_MAINPAGE'] ?></a>
</div>

</div><!--  main end -->
<div id="footer">
<?php $this->SubTemplate('_footer');?>
</div> 
</div><!-- page end -->
</body>
</html>