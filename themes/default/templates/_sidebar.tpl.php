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

/**
 * Template of the side bar
 * @param: last_comments Last comments posted
 * */

global $config, $lang, $args, $nextargs;

$count_incoming = $this->table->db->helper->CountRecords(get_folder_by_name("incoming"), get_type_by_name("all"));

/* Menu */
if (Auth::Check(get_userlevel_by_name("member")))
{   ?>
<center><?php echo $lang['SIDEBAR_WELCOME'] ?>&nbsp;<b><a href="profile.php?id=<?php echo $_SESSION['user_id'] ?>"><?php echo $_SESSION['user_pseudo'] ?></a></b></center><br/><br/>

<div class="menubar">

<?php if (Auth::Check(get_userlevel_by_name("root"))) { ?>
<h1><?php echo $lang['MENU_ROOT'] ?></h1>

<div class="menuitem"><a href="admin/config.php"><?php echo $this->table->style->GetImage("menu_config") ?>
<?php echo $lang['ADMIN_MENU_CONFIG']?></a>
<br/></div>

<div class="menuitem"><a href="admin/memberlist.php"><?php echo $this->table->style->GetImage("menu_members") ?>
<?php echo $lang['MENU_ADMIN_MEMBERS'] ?></a>
<br/></div>

<?php } ?>



<?php if (Auth::Check(get_userlevel_by_name("admin"))) { ?>
<h1><?php echo $lang['MENU_ADMIN'] ?></h1>

<div class="menuitem"><?php echo $this->table->style->GetImage("menu_incoming") ?>
<a href="index.php?folder=3&amp;type=0">
<?php
if ($count_incoming > 0)
   echo '<b>';
echo sprintf($lang['MENU_ADMIN_INCOMING'],$count_incoming);
if ($count_incoming > 0)
   echo '</b>';
?>
</a>
<br/></div>

<div class="menuitem"><a href="index.php?folder=<?php echo get_folder_by_name("trash")?>">
<?php echo $this->table->style->GetImage("trash_full") ?>
<?php echo $lang['ADMIN_MENU_PURGE_TRASH']?></a>
<br/></div>

<div class="menuitem"><a href="admin/management.php"><?php echo $this->table->style->GetImage("menu_management") ?>
<?php echo $lang['MENU_ADMIN_MANAGEMENT']?></a>
<br/></div>

<div class="menuitem"><a href="admin/sets.php"><?php echo $this->table->style->GetImage("menu_sets") ?>
<?php echo $lang['ADMIN_MENU_SETS']?></a>
<br/></div>

<div class="menuitem"><a href="admin/checkdatabase.php"><?php echo $this->table->style->GetImage("menu_checkdatabase") ?>
<?php echo $lang['ADMIN_MENU_CHECK'] ?></a>
<br/></div>

<div class="menuitem"><a href="admin/recompute.php"><?php echo $this->table->style->GetImage("menu_recompute") ?>
<?php echo $lang['ADMIN_MENU_RECOMPUTE'] ?></a>
<br/></div>

<?php } ?>

<h1><?php echo $lang['MENU_MEMBER'] ?></h1>

<div class="menuitem"><?php echo $this->table->style->GetImage("menu_upload") ?>
<a href="upload.php"><?php echo $lang['MENU_MEMBER_UPLOAD'] ?></a>
<br/></div>
<div class="menuitem"><?php echo $this->table->style->GetImage("menu_userslist") ?>
<a href="memberlist.php"><?php echo $lang['MENU_MEMBER_MEMBERS'] ?></a>
<br/></div>
<!-- <div class="menuitem"><a href="stats.php"><?php echo $lang['MENU_MEMBER_STATS'] ?></a>
<br/></div>-->
<div class="menuitem"><a href="profile.php"><?php echo $this->table->style->GetImage("menu_profile") ?>
<?php echo $lang['MENU_MEMBER_PROFILE'] ?></a>
<br/></div>
<div class="menuitem"><a href="options.php"><?php echo $this->table->style->GetImage("menu_options") ?>
<?php echo $lang['MENU_MEMBER_OPTIONS'] ?></a>
<br/></div>
<div class="menuitem"><a href="login.php?out"><?php echo $this->table->style->GetImage("menu_logout") ?>
<?php echo $lang['MENU_MEMBER_LOGOUT'] ?></a>
<br/></div>
</div>
<?php
}
else // Guest menu
{	?>
<form action="login.php?in" method="post" name="login">
<table>
<tr>
<th>Connection</th>
</tr>
<tr>
<td><center>
<input type="text" id="pseudo" name="pseudo" size="10" value="Login" onfocus="if (this.value=='Login') this.value=''" />
</center></td>
</tr><tr>
<td><center>
&nbsp;&nbsp;<input type="password" id="passwd" name="passwd" size="10" value="passwd" onfocus="this.value=''" />
</center></td>
</tr><tr>
<td><center><input type="submit" value="Go!" /></center><br/></td>
</tr></table>
</form>
<br/>

<h1></h1>
<div class="menubar">
<div class="menuitem"><a href="register.php"><?php echo $this->table->style->GetImage("menu_register") ?>
<?php echo $lang['MENU_REGISTER']?></a><br/></div>
<div class="menuitem"><a href="forgot.php"><?php echo $this->table->style->GetImage("menu_profile") ?>
<?php echo $lang['MENU_FORGOT_PASSWD']?></a>
<br/></div>
<div class="menuitem"><a href="memberlist.php"><?php echo $this->table->style->GetImage("menu_userslist") ?>
<?php echo $lang['MENU_MEMBER_MEMBERS']?></a>
<br/></div>
<!-- <div class="menuitem"><a href="stats.php"><?php echo $lang['MENU_MEMBER_STATS']?></a>
<br/></div>-->
<?php } ?>

<h2>TagBoard</h2>
<div id="tagboard">
<div id="tags">
<?php 
/* Tagboard is a but special as it manage its own url parameters */
$tagboard = new TagBoard($this->table);
echo $tagboard->Show($args) ?>
</div>
<form method="post" action="<?php echo $nextargs ?>&amp;tag" name="tagpostform" id="tagpostform">
<table><tr>
<td><label for="tag_pseudo">Pseudo</label></td></tr>
<tr><td><input type="text" id="tag_pseudo" name="tag_pseudo" maxlength="14" value="<?php echo $_SESSION['user_pseudo'] ?>" readonly /></td></tr>
<tr><td><label for="content">Your tag&nbsp;<a href="javascript:child=window.open('./popup_tagtools.php?referer_form=tagpostform', 'Smiles', 'fullscreen=no,toolbar=no,status=no,menubar=no,scrollbars=no,resizable=yes,directories=no,location=no,width=270,height=300,left='+(Math.floor(screen.width/2)-140));child.focus();">(extras)</a></label></td></tr>
<tr><td><textarea id="content" name="content" rows="5"></textarea>
</td></tr>
<tr><td><center><input type="submit" value="Tag!" /></center></td></tr>
</table>
</form>
</div>

<br/>

<h2><?php echo $lang['SIDEBAR_LAST_COMMENTS'] ?></h2>
<table>
<?php
 while($fields = $this->table->db->FetchArray($last_comments))
 {  
 	$text = $fields['content'];
 	if (strlen($text) > $config['sidebar_comlength'])
       $text = layout_wrap(substr($text,0,$config['sidebar_comlength']) . "...", $config['sidebar_autowrap'])  ; 
 	$text = $this->RenderText($text);
 	?>
	<tr><td class="comPreviewHeader">
	<a href="profile.php?id="<?php echo $fields['user_id']?>" title="View profile of <?php echo $fields['pseudo'] ?>"><?php echo $fields['pseudo'] ?></a>
	<a href="?levelset_f=<?php echo $fields['levelset'] ?>&amp;level_f=<?php echo $fields['level'] ?>" title="Show this level">[<?php echo $fields['set_name']."&nbsp;".$fields['level'] ?>]</a>
	<br/>
	<?php echo date($config['date_format_mini'],GetDateFromTimestamp($fields['timestamp'])) ?>
	</td></tr>
	<tr><td class="comPreview">
	<a href="record.php?id=<?php echo $fields['replay_id']."#".$fields['com_id'] ?>">
	<?php echo $text ?>
	</a>
	</td></tr>
<?php 	
 } ?>
</table>

<br/>

<table><tr>
<td><?php echo $this->table->style->GetImage('best') ?></td>
<td><?php echo $lang['SIDEBAR_LEGEND_BEST'] ?></td>
</tr><tr>
<td><?php echo $this->table->style->GetImage('best time') ?></td>
<td><?php echo $lang['SIDEBAR_LEGEND_BEST_TIME'] ?></td>
</tr><tr>
<td><?php echo $this->table->style->GetImage('most coins') ?></td>
<td><?php echo $lang['SIDEBAR_LEGEND_MOST_COINS'] ?></td>
</tr><tr>
<td><?php echo $this->table->style->GetImage('freestyle') ?></td>
<td><?php echo $lang['SIDEBAR_LEGEND_FREESTYLE'] ?></td>
</tr>
</table>

<br/>

<center>
<a href="rss.php">flux rss&nbsp;<img src="<?php echo ROOT_PATH.$config['image_dir']."xml.gif"?>" alt="xml" /></a>
<br/>
<a href="http://validator.w3.org/check?uri=referer"><img src="<?php echo ROOT_PATH.$config['image_dir']."logo-xhtml.png"?>" alt="Valid XHTML 1.0!" /></a>
<br/>
<a href="http://jigsaw.w3.org/css-validator/check/referer"><img src="<?php echo ROOT_PATH.$config['image_dir']."logo-css2.png"?>" alt="Valid CSS2 !" /></a>
<br/>
</center>

<?php if (Auth::Check(get_userlevel_by_name("moderator"))) { ?>
<script type="text/javascript">
 $(document).ready(function() {
  $(".tagedit").editable("ajax/tag_edit.php", { 
      indicator : "<img src='themes/default/images/indicator.gif'>",
      loadurl   : "ajax/tag_load.php",
      tooltip   : "Double click to edit...",
      type      : "textarea",
      style  	: "inherit",
      event     : "dblclick",
      submit    : "Edit!"
  });
 });
</script>
<?php } ?>