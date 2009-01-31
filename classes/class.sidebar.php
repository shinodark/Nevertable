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
	
class SideBar
{

    var $output;

	/*__Constructeur__
	
	Cette fonction initialise l'objet Replay.
	*/
	function SideBar()
    {
       
    }

    function End()
    {
       return $this->output;
    }

    /* Create a MenuBar 
     * $menu is a type Menu from class.menu.php
     * */
    function AddBlock_MenuBar($title, &$menu)
    {
       $this->output .= "<h2>".$title."</h2>\n";
       $this->output .= "<div class=\"menubar\">\n";
       $this->output .= $menu->GetOutput();
       $this->output .= "</div><!-- menubar -->\n";
    }

    /* Display stats of online users */
    function AddBlock_Stats($online_stats)
    {
      if (!empty($online_stats))
      {
        for ($i=0; $i<$online_stats['registered']; $i++)
          $tooltip .= $online_stats['list'][$i] . ", ";
        $tooltip = substr($tooltip, 0, strlen($tooltip)-2); // enlève la virgule à la fin

        $this->output .= "<center>\n";
        if (!empty($tooltip))
          $this->output .= "<span onmouseover=\"return escape('".$tooltip."')\">";
        $this->output .= $online_stats['registered'] . " registered users";
        if (!empty($tooltip))
          $this->output .= "</span>";
        $this->output .=  "<br/>\n";
        $this->output .=  $online_stats['guests'] . " guests\n";
        $this->output .=  "</center>\n";
        $this->output .=  "<br/>\n";
      }
    }

    /* Welcome Message */
    function AddBlock_Welcome()
    {
      global $lang;

      if (Auth::Check(get_userlevel_by_name("member")))
      $this->output .= "<center>".sprintf($lang['SIDEBAR_WELCOME'],"<b><a href=\"profile.php?id=".$_SESSION['user_id']."\">".$_SESSION['user_pseudo']."</a></b>")."</center><br/><br/>\n";
    }

    /* Login form */
    function AddBlock_LoginForm()
    {
 
    }
    
    /* Last Comments */
    function AddBlock_LastComments(&$db, &$bbcode, &$smilies)
    {
      global $config, $lang;

      $this->output .=  "<h2>".$lang['SIDEBAR_LAST_COMMENTS']."</h2>\n";

      $p = $config['bdd_prefix'];
      $db->Select(
        array("rec", "users", "com", "sets" ),
        array(
            $p."com.replay_id AS replay_id",
            $p."com.id AS com_id",
            $p."com.user_id AS user_id",
            $p."com.content AS content",
            $p."com.timestamp AS timestamp",
            $p."rec.levelset AS levelset",
            $p."rec.level AS level",
            $p."users.pseudo AS pseudo",
            $p."sets.set_name AS set_name",
            )
      );

      $db->Where(
        array($p."com.user_id", $p."com.replay_id", $p."rec.levelset"),
        array($p."users.id", $p."rec.id", $p."sets.id"),
        "AND", false
      );
      
      $db->Where(
        array($p."rec.folder", $p."rec.folder"),
        array(get_folder_by_name("contest"), get_folder_by_name("oldones")),
        "OR"
      );
      
      $db->Sort(array($p."com.timestamp"), "DESC");
      $db->Limit($config['sidebar_comments']);
      $res = $db->Query();

      if (!$res)
      {
       gui_button_error("Error fetching comments.", 200);
      } else {

      $this->output .=  "<table>\n";

      while($val = $db->FetchArray($res))
      {
        $this->output .=  "<tr><td class=\"comPreviewHeader\">\n";
        $this->output .=  "<a href=\"profile.php?id=".$val['user_id']."\" title=\"View profile of ".$val['pseudo']."\">\n";
        $this->output .=  $val['pseudo'];
        $this->output .=  "</a>\n";
        $this->output .=  "<a href=\"?levelset_f=".$val['levelset']."&amp;level_f=".$val['level']."\" title=\"Show this level\">\n";
        $this->output .=  "[".$val['set_name']."&nbsp;".$val['level']."]";
        $this->output .=  "</a>\n";
        $this->output .=  "\n</td></tr>\n";
        $this->output .=  "<tr><td class=\"comPreview\">\n";
        /* élimination des bbcodes */
        $contentout  = $val['content'];
        $tooltip_content = CleanContent($contentout);
        $tooltip_content = $bbcode->parse($tooltip_content, "all", false);
        $tooltip_content = $smilies->Apply($tooltip_content);
        $tooltip = "<table><tr><th>".date($config['date_format_mini'],GetDateFromTimestamp($val['timestamp']))."</th></tr><tr><td>".$tooltip_content."</td></tr></table>";
        $tooltip = Javascriptize($tooltip);
        if (strlen($contentout) > $config['sidebar_comlength'])
            $contentout = substr($contentout,0,$config['sidebar_comlength']) . "...";
        $contentout = wordwrap($contentout, $config['sidebar_autowrap'], "<br/>", 1);
        $this->output .=  "<div class=\"menuitem\" onmouseover=\"return escape('".$tooltip."')\">\n";
        $this->output .=  "<a href=\"record?id=".$val['replay_id']."#".$val['com_id']."\">\n";
        $this->output .=  $contentout;
        $this->output .=  "</a></div>\n";
        $this->output .=  "</td></tr>\n";
      }
      $this->output .=  "</table>\n";

      }
  
      $this->output .=  "<br/>";
    }
    /* Last Comments */
    function AddBlock_TagBoard(&$db, &$bbcode, &$smilies, &$style)
    {

    }

    function AddBlock_Legend(&$style)
    {


    }

    function AddBlock_Baneers()
    {

    }

}

