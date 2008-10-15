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
#

include_once ROOT_PATH ."classes/class.dialog.php";

class DialogStandard extends Dialog
{
  /*__CONSTRUCTEUR__*/
  function DialogStandard(&$db, &$parent, &$o_style)
  {
    parent::Dialog(&$db , &$parent, &$o_style);
  }

  function Prelude()
  {
    $this->output .=   "<div id=\"prelude\">\n";
    $this->output .=   "<center>\n";
    $this->output .=   "<a href=\"http://icculus.org/neverball/\">Neverball official site</a> &nbsp; | &nbsp;\n";
    $this->output .=   "<a href=\"http://www.happypenguin.org/show?Neverball\">Happypenguin page</a>&nbsp; | &nbsp;\n";
    $this->output .=   "<a href=\"http://www.nevercorner.net/forum/\">Neverforum</a>&nbsp; | &nbsp;\n";
    $this->output .=   "<a href=\"http://www.nevercorner.net/wiki/\">NeverWiki</a>\n";
    $this->output .=   "</center>\n";
    $this->output .=   "</div>\n\n";
    $this->Output();
  }
  
  function Speech()
  {
    global $lang, $config;

    $this->output .=   "<div id=\"speech\">\n";

    $langpath = ROOT_PATH . $config['lang_dir'] . $config['opt_user_lang'] . "/";

    $speech  = $langpath . "speech.txt";
    $f = new FileManager($speech);
    if ($f->Exists())
      $this->output .=  $f->ReadString() . "<br />\n";
    $this->output .=   "</div>\n\n";
    
    $this->output .=   "<div id=\"announce\">\n";

    $announce = $langpath . "announce.txt";
    $f = new FileManager($announce);
    if ($f->Exists())
      $this->output .=  $f->ReadString() . "<br />\n";
    $this->output .=   "</div>\n\n";

    $this->Output();
  }
  
  function SideBar($online_stats=array())
  {
    global $config, $lang;

    $rec = new Record($this->db);
    $bar = new SideBar();

    $bar->AddBlock_Welcome();
    //$bar->AddBlock_stats($online_stats);
    
    
    /* Menu */
    if (Auth::Check(get_userlevel_by_name("member")))
    {
      $menu_main = new Menu();

      if (Auth::Check(get_userlevel_by_name("admin")))
      {
        $menu_main->AddItem($lang['MENU_ADMIN'], "admin/admin.php");
        $menu_sub = new Menu(1);
        $count_incoming =  $this->db->helper->CountRecords(get_folder_by_name("incoming"), get_type_by_name("all"));
	if ($count_incoming > 0)
           $menu_sub->AddItem('<b>'.sprintf($lang['MENU_ADMIN_INCOMING'],$count_incoming).'</b>' , "admin/admin.php?folder=".get_folder_by_name("incoming")."&amp;type=".get_type_by_name('all'));
	else
           $menu_sub->AddItem(sprintf($lang['MENU_ADMIN_INCOMING'],$count_incoming) , "admin/admin.php?folder=".get_folder_by_name("incoming")."&amp;type=".get_type_by_name('all'));
        $menu_sub->AddItem($lang['MENU_ADMIN_MANAGEMENT'], "admin/management.php");
        $menu_sub->AddItem($lang['MENU_ADMIN_MEMBERS'], "admin/memberlist.php");
        $menu_sub->AddItem($lang['MENU_ADMIN_FILE_EXPLORER'], "admin/filexplorer.php");
      
        $menu_main->AddSubMenu($menu_sub);
      }
      if (Auth::Check(get_userlevel_by_name("admin")))
      {
        $menu_main->AddItem($lang['MENU_MODERATOR'], "");
        $menu_sub = new Menu(1);
        $menu_sub->AddItem($lang['MENU_MODERATOR_TAGBOARD'], "admin/tag_moder.php");
        $menu_main->AddSubMenu($menu_sub);
      }

      $menu_main->AddItem($lang['MENU_MEMBER_UPLOAD'], "upload.php");
      $menu_main->AddItem($lang['MENU_MEMBER_MEMBERS'], "memberlist.php");
      $menu_main->AddItem($lang['MENU_MEMBER_STATS'], "stats.php");
      $menu_main->AddItem($lang['MENU_MEMBER_PROFILE'], "profile.php");
      $menu_main->AddItem($lang['MENU_MEMBER_OPTIONS'], "options.php");
      $menu_main->AddItem($lang['MENU_MEMBER_LOGOUT'], "login.php?out");
      
      $bar->AddBlock_MenuBar($lang['MENU_MEMBER'], $menu_main);
    }
    else
    {
      $bar->AddBlock_LoginForm();
      $menu_main = new Menu();
      $menu_main->AddItem($lang['MENU_REGISTER'], "register.php");
      $menu_main->AddItem($lang['MENU_FORGOT_PASSWD'], "forgot.php");
      $menu_main->AddItem($lang['MENU_MEMBER_MEMBERS'], "memberlist.php");
      $menu_main->AddItem($lang['MENU_MEMBER_STATS'], "stats.php");

      $bar->AddBlock_MenuBar("", $menu_main);
    }
    
    $bar->AddBlock_TagBoard($this->db, $this->bbcode, $this->smilies, $this->style);
    $bar->AddBlock_LastComments($this->db, $this->bbcode, $this->smilies);
    $bar->AddBlock_Legend($this->style);
    $bar->AddBlock_Baneers();
    
    $this->output .= $bar->End();
    $this->Output();
    
  }

  function Table($mysql_results, $diffview=false, $total="")
  {
    global $lang;

    $this->output .=  "<div class=\"results\">\n";
    $this->output .=  "<div class=\"results-prelude\">\n";
    if (!empty($total))
      $this->output .=  sprintf($lang['RESULTS_PRELUDE'], $total)."\n";
    $this->output .=  "</div>\n";

    $this->output .=  "<table><tr>\n";
    $this->_ResultsHeader();
    $this->output .=  "</tr>\n";
    $i=0;
    /* liste des liens pour téléchargements */
    $_SESSION['download_list'] = "";
    $diffref = array();
    while ($fields = $this->db->FetchArray($mysql_results))
    {
      /* garde la référence en mémoire pour le diff */
      if ($diffview && $i==0)
        $diffref = $fields;
      $this->_RecordLine($i, $fields, true, $diffview, $diffref);
      $this->_JumpLine(12);
      $i++;
    }
    $this->output .=  "</table>\n";
    $this->output .=  "<br/>\n";
    
    $this->output .=  "<center>".$lang['TABLE_RESULTS_LIST']."<a href=\"?to=showlinklist\" target=\"_blank\">Link_List.lst</a></center>\n";
    $this->output .=  "</div>\n\n";
    
    $this->Output();
  }
  
  function Level($results_contest, $results_oldones, $args, $total1="", $total2="")
  {
    global $config;

    $level = (integer) $args['level_f'];
    $set   = (integer) $args['levelset_f'];

    /* Récupère le chemin du levelshot */
    $p = $config['bdd_prefix'];
    $this->db->Select(
        array("maps", "sets"),
        array($p."maps.map_solfile AS map_solfile", $p."sets.set_path AS set_path")
    );
    $this->db->Where(
        array($p."sets.id"),
        array($p."maps.set_id"),
        "AND", false
    );
    $this->db->Where(
        array($p."maps.set_id", $p."maps.level_num"),
        array($set, $level)
    );
    $this->db->Limit(1);
    $this->db->Query();
    $val = $this->db->FetchArray();

    /* records du contest */
    $this->output .=  "<div class=\"results\">\n";
    
    $this->output .=  "<table width=\"400px\"><tr>\n";
    $this->output .=  "<th>Level Shot</th></tr>\n";
    $this->output .=  "<tr><td>";
    $this->output .=  "<center>".GetShot($val['set_path'], $val['map_solfile'])."</center>\n";
    $this->output .=  "</td></tr>\n";
    $this->output .=  "</table>\n";
    $this->output .=  "<br/>\n";

    if ($total1 != 0) {

    $this->output .=  "<div class=\"results-prelude\">\n";
    $this->output .=  "Contest records (".(integer)$total1.")\n";
    $this->output .=  "</div>\n";
      
    $this->output .=  "<table><tr>\n";
    $this->_ResultsHeader();
    $this->output .=  "</tr>\n";
    $i=0;
    while ($fields = $this->db->FetchArray($results_contest))
    {
      $this->_RecordLine($i, $fields, false);
      $this->_JumpLine(12);
      $i++;
    }
    $this->output .=  "</table>\n";
    $this->output .=  "<br/>\n";
    }

    /* records anciens */
    if ($total2 != 0) {
    
    $this->output .=  "<div class=\"results-prelude\">\n";
    $this->output .=  "Old records (".(integer)$total2.")\n";
    $this->output .=  "</div>\n";
      
    $this->output .=  "<table><tr>\n";
    $this->_ResultsHeader();
    $this->output .=  "</tr>\n";
    $i=0;
    while ($fields = $this->db->FetchArray($results_oldones))
    {
      $this->_RecordLine($i, $fields, false);
      $this->_JumpLine(12);
      $i++;
    }
    $this->output .=  "</table>\n";
    }
    $this->output .=  "</div>\n\n";
    $this->Output();
  }

  function Record($fields)
  {
    $this->output .=  "<div class=\"oneresult\">\n";
    $this->output .=  "<table><tr>\n";
    $this->output .=  "<th style=\"width: 28px;\"></th>\n"; // new
    $this->output .=  "<th>old</th>\n"; // days
    $this->output .=  "<th style=\"width: 28px;\"></th>\n"; // best
    $this->output .=  "<th style=\"width: 32px;\"></th>\n"; // type
    $this->output .=  "<th style=\"width: 100px;\">player</th>\n";
    $this->output .=  "<th>set</th>\n";
    $this->output .=  "<th>level</th>\n";
    $this->output .=  "<th>time</th>\n";
    $this->output .=  "<th>coins</th>\n";
    $this->output .=  "<th></th>\n"; // replay
    $this->output .=  "<th></th>\n"; // comments
    $this->output .=  "<th></th>\n"; // goto same records
    $this->output .=  "</tr>\n";

    /* can be useful in case of print a new added record */
    $u = new User($this->db);
    $s = new Set($this->db);
    if(!$u->LoadFromId($fields['user_id']))
        gui_button_error($u->GetError(), 400);
    if(!$s->LoadFromId($fields['levelset']))
        gui_button_error($s->GetError(), 400);
    $fields['pseudo'] = $u->GetPseudo();
    $fields['set_name'] = $s->GetName();
    $this->_RecordLine(0, $fields, false);
    
    $this->output .=  "</table>\n";
    $this->output .=  "</div>\n";
    $this->Output();
  }

  function RecordLink($fields)
  {
      global $config;

      $u = new User($this->db);
      $s = new Set($this->db);
      if(!$u->LoadFromId($fields['user_id']))
          gui_button_error($u->GetError(), 400);
      if(!$s->LoadFromId($fields['levelset']))
          gui_button_error($s->GetError(), 400);

      
      $this->output .=  "<div class=\"embedded\">\n";
      $this->output .=  "<br/>\n";
      $link = "http://".$_SERVER['SERVER_NAME'] ."/".$config['nvtbl_path'] . "?link=".$fields['id'];
      $value =  "[url=".$link."]" .
                 get_type_by_number($fields['type']) ." by ".$u->GetPseudo()." on ". $s->GetName() . " " . $fields['level'].
                "[/url]";
      $this->output .=  "<center><a href=\"".$link."\"  >bblink : </a>\n";
      $this->output .=  "<input type=\"text\" size=\"".strlen($value)."\" value=\"".$value."\" readonly />\n";
      $this->output .=  "</center>\n";
      $this->output .=  "<br/>\n";
      $this->output .=  "</div>\n";
      
      $this->Output();
  }

  function Comments($mysql_results)
  {
    global $config, $lang;

    if ($this->db->NumRows($mysql_results)>0)
    {
      while ($val = $this->db->FetchArray($mysql_results))
      {
        if (empty($val['user_pseudo']))
           $val['user_pseudo'] = "[Guest]";

        $this->output .=  "<div class=\"comments\">\n";
        $this->output .=  "<a name=\"".$val['id']."\"></a>\n";
        /* table générale */
        $this->output .=  "<table>\n";
        $this->output .=  "<tr><td>\n";
 
        /* table d'entete */
        $this->output .=  "<div class=\"embedded\">\n";
        $this->output .=  "<table class=\"com_header\">\n";
        $this->output .=  "<tr>\n";
        $this->output .=  "<td><a href=\"viewprofile.php?id=".$val['user_id']."\">".$val['user_pseudo']."</a></td><td style=\"text-align: right;\">".GetDateLang(GetDateFromTimestamp($val['timestamp']))."</td>\n";
        
        if (Auth::Check(get_userlevel_by_name("moderator")))
        {
          $this->output .=  "<td style=\"width: 16px;\">";
          $this->output .=  '<a href="?comedit&amp;com_id='.$val['id'].'&amp;comuser_id='.$val['user_id'].'&amp;id='.$val['replay_id'].'">';
          $this->output .=  $this->style->GetImage('edit', $lang['COMMENTS_POPUP_EDIT']);
          $this->output .=  "</a></td>\n";
          $this->output .=  "<td style=\"width: 16px;\">";
          $this->output .=  '<a href="?comdel&amp;com_id='.$val['id'].'&amp;user_id='.$val['user_id'].'&amp;id='.$val['replay_id'].'">';
          $this->output .=  $this->style->GetImage('del', $lang['COMMENTS_POPUP_DELETE']);
          $this->output .=  "</a></td>\n";
        }

        else if (Auth::CheckUser($val['poster_id']))
        {
          $this->output .=  "<td style=\"width: 16px;\">";
          $this->output .=  '<a href="?comedit&amp;com_id='.$val['id'].'&amp;comuser_id='.$val['user_id'].'&amp;id='.$val['replay_id'].'">';
          $this->output .=  $this->style->GetImage('edit', $lang['COMMENTS_POPUP_EDIT']);
          $this->output .=  "</a></td>\n";
        }
        
        $this->output .=  "</tr>\n";
        $this->output .=  "</table>\n";
        $this->output .=  "</div>\n";
        /* fin table d'entete */
        
        $this->output .=  "</td></tr><tr><td>\n";

        /* table de contenu */
        $this->output .=  "<div class=\"embedded\">\n";
        $this->output .=  "<table>";
        $content = $this->bbcode->parse($val['content'], "all", false);
        $content = $this->smilies->Apply($content);
        $this->output .=  "<tr>";
        if (!empty($val['user_avatar']))
          $avatar_html = "<img src=\"".ROOT_PATH.$config['avatar_dir']."/".$val['user_avatar']."\" alt=\"\" />";
	else
	  $avatar_html = "";
        $this->output .=  "<td width=\"130px\" valign=\"top\"><center>".$avatar_html."</center></td>\n";
        $this->output .=  "<td class=\"com_content\">".$content."</td>\n";
        $this->output .=  "</tr>\n";
        $this->output .=  "</table>\n";
        $this->output .=  "</div>\n";
        /* fin table de contenu */

        $this->output .=  "</td></tr>\n";
        $this->output .=  "</table>\n";
        /* fin table générale */

        $this->output .=  "</div>\n";
      }
    }
    else
    {
        $this->output .= gui_button_noecho($lang['COMMENTS_NOCOMMENT'], 400);
    }

    $this->Output();
  }

  function MemberList($mysql_results)
  {
    global $lang; 

    $this->output .=  "<center>\n";
    $this->output .=  "<div class=\"results\" style=\"width:650px; float: none; padding: 5 0 5 0;\">\n";
    $this->output .=  "<table style=\"text-align: center;\"><tr>\n";
    $this->output .=  "<th style=\"text-align: center;\"><a href=\"memberlist.php?sort=0\">".$lang['MEMBER_HEADER_NAME']."</a></th>\n";
    $this->output .=  "<th style=\"text-align: center;\"><a href=\"memberlist.php?sort=1\">".$lang['MEMBER_HEADER_RECORDS']."</a></th>\n";
    $this->output .=  "<th style=\"text-align: center;\"><a href=\"memberlist.php?sort=2\">".$lang['MEMBER_HEADER_BEST_RECORDS']."</a></th>\n";
    $this->output .=  "<th style=\"text-align: center;\"><a href=\"memberlist.php?sort=2\">".$lang['MEMBER_HEADER_RANK']."</a></th>\n";
    $this->output .=  "<th style=\"text-align: center;\"><a href=\"memberlist.php?sort=3\">".$lang['MEMBER_COMMENTS']."</a></th>\n";
    $this->output .=  "<th style=\"text-align: center;\"><a href=\"memberlist.php?sort=4\">".$lang['MEMBER_CATEGORY']."</a></th>\n";
    $this->output .=  "</tr>\n";
    $i=0;
    while ($fields = $this->db->FetchArray($mysql_results))
    {
      $this->_MemberLine($i, $fields);
      $this->_JumpLine(5);
      $i++;
    }
    $this->output .=  "</table>\n";
    $this->output .=  "</div>\n";
    $this->output .=  "</center>\n";
    $this->Output();
  }

  /*__FORMULAIRES__*/

  function TypeForm($args)
  {
    global $types, $levels, $newonly, $lang;
  
    $this->output .=   "<div class=\"generic_form\" style=\"width: 700px;\">\n";
    $this->output .=   "<form method=\"post\" action=\"?\" name=\"typeform\">\n";
    $this->output .=   "<table><tr>\n";
    $this->output .=   "<th colspan=\"6\">".$lang['TYPE_FORM_TITLE']."</th>\n";
    $this->output .=   "</tr><tr>\n";
    $this->output .=   "<td><label for=\"type\">".$lang['TYPE_FORM_TABLE_SELECT']."</label>\n";
    $this->output .=   "<select name=\"type\" id=\"type\">\n";
  
    foreach ($types as $nb => $value)
      $this->output .=   "<option value=\"".$nb."\">".$lang[$types[$nb]]."</option>\n";
  
    $this->output .=   "</select></td>\n";

    $this->output .=   "<td><label for=\"folder\">".$lang['TYPE_FORM_FOLDER_SELECT']."</label>\n";
    $this->output .=   "<select name=\"folder\" id=\"folder\">\n";
  
    /* Hard coded value for main page */
    $this->output .=   "<option value=\"1\">".$lang['contest']."</option>\n";
    $this->output .=   "<option value=\"4\">".$lang['oldones']."</option>\n";
    $this->output .=   "<option value=\"0\">".$lang['all']."</option>\n";
    $this->output .=   "</select></td>\n";
  
    $this->output .=   "<td><label for=\"levelset_f\">".$lang['TYPE_FORM_SET']."</label>\n";
    $this->output .=   "<select name=\"levelset_f\" id=\"levelset_f\">\n";
    $this->output .=     "<option value=\"0\">".$lang['all']."</option>\n";
    foreach ($this->db->helper->SelectSets() as $id => $name)
    {
      $this->output .=   "<option value=\"".$id."\">".$name."</option>\n";
    }
    $this->output .=   "</select></td>\n";
    $this->output .=   "<td><label for=\"level_f\">".$lang['TYPE_FORM_LEVEL']."</label>\n";
    $this->output .=   "<select name=\"level_f\" id=\"level_f\">\n";
    $this->output .=   "<option value=\"0\">".$lang['all']."</option>\n";
    foreach ($levels as $name => $value)
    {
      $this->output .=   "<option value=\"".$value."\">".$name."</option>\n";
    }
    $this->output .=   "</select></td>\n";
  
    $this->output .=   "</tr></table>\n";
    $this->output .=  "<br />\n";
  
    $this->output .=   "<table><tr>\n";
    $this->output .=   "<th colspan=\"6\">".$lang['TYPE_FORM_FILTERS']."</th>\n";
    $this->output .=   "</tr><tr>\n";
    $this->output .=   "<td><label for=\"diffview\">".$lang['TYPE_FORM_DIFFVIEW']."</label>\n";
    $this->output .=   "<input type=\"checkbox\" name=\"diffview\" id=\"diffview\" value=\"on\" /></td>\n";
    $this->output .=   "<td><label for=\"newonly\">".$lang['TYPE_FORM_NEWONLY']."</label>\n";
    $this->output .=   "<select name=\"newonly\" id=\"newonly\">\n";
  
    foreach ($newonly as $nb => $name)
      $this->output .=   "<option value=\"".$nb."\">".$lang[$name]."</option>\n";
  
    $this->output .=   "</select></td>\n";
    $this->output .=   "</tr></table>\n";
    $this->output .=  "<br />\n";
    $this->output .=   "<center><input type=\"submit\" value=\"".$lang['GUI_BUTTON_APPLY']."\" /></center>\n";
  
    $this->output .=   "</form>\n";
    $this->output .=   "</div>\n\n";

    /* special case with folder selct in main page */
    switch($args['folder'])
    {
       case get_folder_by_name("all")     : $ind_folder = 2; break;
       case get_folder_by_name("contest") : $ind_folder = 0; break;
       case get_folder_by_name("oldones") : $ind_folder = 1; break;
       default :$ind_folder = 2; break;
    }
    //Calcul de l'index du set pour la liste
    $i = 1;
    foreach ($this->db->helper->SelectSets() as $id => $name)
    {
      $ind_set_arr[$id] = $i;
      $i++;
    }

    $this->output .= "<script type=\"text/javascript\">\n";
    $this->output .=  "change_form_select('type',".$args['type'].");\n";
    $this->output .=  "change_form_select('folder',".$ind_folder.");\n";

    $this->output .=  "change_form_select('levelset_f',".$ind_set_arr[$args['levelset_f']].");\n";
    $this->output .=  "change_form_select('level_f',".$args['level_f'].");\n";
    $this->output .=  "change_form_checkbox('diffview','".$args['diffview']."');\n";
    $this->output .=  "change_form_select('newonly',".$args['newonly'].");\n";
    $this->output .= "</script>\n";

    $this->Output();
  }


  function CommentForm($comuser_id, $comuser_pseudo, $content, $nextargs)
  {
   global $lang, $config, $toolbar_el, $args;

   $this->output .=  '<div class="generic_form" style="width: 650px;">'."\n";
   $this->output .=  '<form method="post" action="'.$nextargs.'" name="commentform">'."\n";
   $this->output .=  "<table><tr>\n";
   $this->output .=  "<td colspan=\"2\"><label for=\"pseudo\">".$lang['COMMENTS_FORM_PSEUDO'] ."</label>\n";
   $this->output .=  '<input type="text" id="pseudo" name="pseudo" size="20" value="'.$comuser_pseudo.'" readonly /></td>'."\n";
 
   $this->output .=  "</tr><tr>\n";
   $this->output .=  '<td colspan="2"><center><textarea id="content" name="content" rows="12"></textarea></center>'."\n";
   $this->output .=  "</td></tr><tr>\n";
  
   $this->output .= '<td colspan="2"><center><input type="submit" name="preview" value="'.$lang['COMMENTS_FORM_PREVIEW'].'" />'."\n";
   $this->output .=  "<input type=\"submit\" /></center>\n";
   $this->output .=  "<input type=\"hidden\" name=\"id\" id=\"user_id\" value=\"".$args['id']."\" /></td>\n";
   $this->output .=  "<input type=\"hidden\" name=\"comuser_id\" id=\"comuser_id\" value=\"".$comuser_id."\" /></td>\n";
   $this->output .=  "<input type=\"hidden\" name=\"com_id\" id=\"com_id\" value=\"".$args['com_id']."\" /></td>\n";
   $this->output .=  "</tr><tr>\n";
   $this->output .=  "<td>\n";


   /* barre d'outils */

   $this->output .= "<script type=\"text/javascript\" src=\"".ROOT_PATH."includes/js/toolbar.js\"></script>\n";
   $this->output .= "<script type=\"text/javascript\">setTextArea(document.forms['commentform'].content)</script>\n";

   foreach($toolbar_el as $key => $func)
   {
       $this->output .= "<a href=\"javascript:".$func."()\">".$this->style->GetIcon($key)."</a>";
   }

   $this->output .= "&nbsp;&nbsp;\n";

   $this->output .= "</td><td style=\"text-align: right\">\n";
   
   $this->output .=  "<a href=\"javascript:child=window.open('./popup_smilies.php?referer_form=commentform', 'Smiles', 'fullscreen=no,toolbar=no,status=no,menubar=no,scrollbars=no,resizable=yes,directories=no,location=no,width=270,height=220,left='+(Math.floor(screen.width/2)-140));child.focus()\">(smiles)</a>";

   /* fin -- barre d'outils */


   $this->output .=  "</td></tr>\n";
   $this->output .=  "</table>\n";
   $this->output .=  "</form>\n";
   $this->output .=  "</div>\n\n";

   if (!empty($content))
   {
    $this->output .= "<script type=\"text/javascript\">\n";
      $this->output .=  "change_form_textarea('content',  '".$content."')\n";
    $this->output .= "</script>\n";
   }
   $this->Output();
  }

  function EditForm()
  {

  }
  
  /*__METHODES PRIVEES__*/
  
  function _RecordLine($i, $fields, $display_shot=true, $diffview=false, $diffref=array())
  {
    global $nextargs, $lang;

    $rowclass = ($i % 2) ? "row1" : "row2";
    if ($display_shot)
    {
      $tooltip=Javascriptize(GetShotMini($fields['set_path'], $fields['map_solfile'], 128));
      $onmouseover = "onmouseover=\"return escape('".$tooltip."')\"";
    }
    if ($i==0 && $diffview)
      $this->output .=   "<tr class=\"row1\" style=\"font-weight: bold;\" ".$onmouseover.">\n";
    else
      $this->output .=   "<tr class=\"".$rowclass."\" ".$onmouseover.">\n";

    $days=timestamp_diff_in_days(time(), GetDateFromTimestamp($fields['timestamp']));
       
    /* updated recently ? (2 days) */
    if($days<=3)
      $this->output .=  "<td>".$this->style->GetImage('new')."</td>\n";
     else
        $this->output .=  "<td></td>\n";
  
    /* old */
    $this->output .=  "<td>".$days."&nbsp;d</td>\n";

    /* best ? */
    if($fields['isbest']==1)
      $this->output .=  "<td>".$this->style->GetImage('best')."</td>\n";
    else
        $this->output .=  "<td></td>\n";
      
    /* type image */
    $this->output .=   "<td>".$this->style->GetImage(get_type_by_number($fields['type']))."</td>\n";
  
    /* pseudo */
    $this->output .=   "<td><a href=\"viewprofile.php?id=".$fields['user_id']."\">";
    $this->output .=   $fields['pseudo'] ."</a></td>\n" ;
      
    /* set */
    $this->output .=   "<td>". $fields['set_name'] ."</td>\n" ;
    
    /* level */
    $this->output .=   "<td><a href=\"index.php?levelset_f=".$fields['levelset']."&amp;level_f=".$fields['level']."\"> " . $fields['level'] . "</a></td>\n" ;
    /* time */
    if ($diffview && !empty($diffref) && $i>0) {
       $time = $fields['time'] - $diffref['time'];
       $sign_display=true;
    }
    else {
       $time = $fields['time'];
       $sign_display=false;
    }
       
    $this->output .=   "<td>" . sec_to_friendly_display($time, $sign_display) . "</td>\n" ;
    /* coins */
    if ($diffview && !empty($diffref) && $i>0)
    {
       $coins = $fields['coins'] - $diffref['coins'];
       /* ajout du signe + dans le cas du diff, c'est plus joli*/
       $coins = ($coins<0) ? $coins : "+".$coins;
    }
    else
       $coins = $fields['coins'];
    $this->output .=   "<td>" . $coins . "</td>\n" ;

    /* replay */
    if(!empty($fields['replay']))
    {
      $replay  = replay_link($fields['folder'], $fields['replay']);
      $this->output .=   "<td>" . '<a href="' . $replay . '" type="application/octet-stream">replay</a>' . "</td>\n" ;
      $_SESSION['download_list'] .= replay_link($fields['folder'], $fields['replay']) . "\n";
    }
    else
    {
      $this->output .=   "<td>no replay</td>\n" ;
    }
      
    /* comments */
    $nb_comments = $fields['comments_count'];
    if ($nb_comments<1)
       $str = $lang['TABLE_NO_COMMENT'];
    else if ($nb_comments==1)
       $str = $lang['TABLE_ONE_COMMENT'];
    else
       $str = sprintf($lang['TABLE_COMMENTS'] ,$nb_comments);
       $this->output .=   "<td><a href=\"record.php?id=".$fields['id']."\">".$str."</a></td>";
  
    /* "attach" */
    $this->output .=  "<td><a href=\"index.php?levelset_f=".$fields['levelset']."&amp;level_f=".$fields['level']."&amp;folder=-1\" title=\"".$lang['TABLE_ATTACH']."\">";
    $this->output .=  $this->style->GetImage('attach', $lang['TABLE_ATTACH']);
    $this->output .=  "</a></td>";
     
    $this->output .=   "</tr>\n";
  }
  
  function _MemberLine($i, $fields)
  {
    global $nextargs, $config, $lang;

    $rowclass = ($i % 2) ? "row1" : "row2";
    $this->output .=   "<tr class=\"".$rowclass."\">\n";

    /* Name */
    $this->output .=  "<td height=\"20px\">";
    if (!empty($fields['user_avatar']))
       $tooltip=Javascriptize("<center><img src=\"".ROOT_PATH.$config['avatar_dir']."/".$fields['user_avatar']."\" alt=\"\" /></center>");
    else
       $tooltip=Javascriptize("<center><i>".$lang['MEMBER_NO_AVATAR']."</i></center>");
    $this->output .=  "<a href=\"viewprofile.php?id=".$fields['id']."\" onmouseover=\"return escape('".$tooltip."')\">";
    $this->output .=  $fields['pseudo']."</a>\n";
    $this->output .=  "</td>\n";

    /* Record number */
    $this->output .=  "<td>";
    $this->output .=  $fields['stat_total_records'];
    $this->output .=  "</td>\n";

    /* Best records */
    $this->output .=  "<td>";
    $this->output .=  $fields['stat_best_records'];
    $this->output .=  "</td>\n";

    /* Rank */
    $this->output .=  "<td>";
    for($i=0; $i<CalculRank($fields['stat_best_records']); $i++)
      $this->output .=  $this->style->GetImage('rank');
    $this->output .=  "</td>\n";

    /* Comments */
    $this->output .=  "<td>";
    $this->output .=  $fields['stat_comments'];
    $this->output .=  "</td>\n";

    /* Category */
    $this->output .=  "<td>";
    $this->output .=  get_userlevel_by_number($fields['level']);
    $this->output .=  "</td>\n";

    $this->output .=  "</tr>\n";
  }

  function _ResultsHeader()
  {
    global $nextargs, $lang;

    $this->output .=  "<th style=\"width: 28px;\"></th>\n"; // new
    $this->output .=  "<th><a href=\"".$nextargs."&amp;sort=".get_sort_by_name("old")."\">".$lang['TABLE_HEADER_OLD']."</a></th>\n"; // days
    $this->output .=  "<th style=\"width: 28px;\"></th>\n"; // best
    $this->output .=  "<th style=\"width: 32px;\"></th>\n"; // type
    $this->output .=  "<th style=\"width: 100px;\"><a href=\"".$nextargs."&amp;sort=".get_sort_by_name("pseudo")."\">".$lang['TABLE_HEADER_PLAYER']."</a></th>\n";
    $this->output .=  "<th><a href=\"".$nextargs."&amp;sort=".get_sort_by_name("level")."\">".$lang['TABLE_HEADER_SET']."</a></th>\n";
    $this->output .=  "<th><a href=\"".$nextargs."&amp;sort=".get_sort_by_name("level")."\">".$lang['TABLE_HEADER_LEVEL']."</a></th>\n";
    $this->output .=  "<th><a href=\"".$nextargs."&amp;sort=".get_sort_by_name("time")."\">".$lang['TABLE_HEADER_TIME']."</a></th>\n";
    $this->output .=  "<th><a href=\"".$nextargs."&amp;sort=".get_sort_by_name("coins")."\">".$lang['TABLE_HEADER_COINS']."</a></th>\n";
    $this->output .=  "<th></th>\n"; // replay
    $this->output .=  "<th></th>\n"; // comments
    $this->output .=  "<th></th>\n"; // goto same records
  }

  function _JumpLine($colspan)
  {
    $this->output .=  "<tr><td colspan=\"".$colspan."\" style=\"background: #fff; height: 2px;\"></td></tr>\n";
  }
}

