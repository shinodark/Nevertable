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

include_once ROOT_PATH ."classes/class.sidebar.php";
include_once ROOT_PATH ."classes/class.menu.php";

class DialogStandard
{
  var $db;
  var $navbar_cache;
  var $smilies;
  var $bbcode;
  var $style;
  var $output;
   

  /*__CONSTRUCTEUR__*/
  function DialogStandard(&$db, &$o_style)
  {
    $this->db = &$db;
    $this->smilies = new Smilies();
    $this->bbcode = new parse_bbcode();
    $this->style = $o_style;
    $this->output = "";
  }

  function Output($string="")
  {
      if (empty($string))
          echo $this->output;
      else
          echo $string;
  }

  function HtmlHead($title, $special="")
  {
    $this->output  =  "<head>\n";
    $this->output .=  "<title>$title</title>\n";
    $this->output .=  "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"/>\n";
    $this->output .=  "<link rel=\"stylesheet\" href=\"".$this->style->GetCss()."\" type=\"text/css\" />\n";
    $this->output .=  "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"Nevertable rss feed\" href=\"http://www.nevercorner.net/table/rss.php\" />\n";
    /*$this->output .=  "<link rel=\"shortcut icon\" href=\"./favicon.ico\" />\n";*/
    $this->output .=  "<script type=\"text/javascript\" src=\"".ROOT_PATH."includes/js/jsutil.js\"></script>\n";

    if(!empty($special)) $this->output .=  $special; 
    $this->output .=  "</head>\n\n";

    $this->Output();
  }

  function Top()
  {
    $this->output  =   "<div id=\"top\">\n"; 
    $this->output .=  "<center>";
    $this->output .=   "<a href=\"http://www.nevercorner.net/table/\">".$this->style->GetImage('top')."</a>";
    $this->output .=  "</center>";
    $this->output .=   "</div>\n\n";
    $this->Output();
  }


  function Prelude()
  {
    $this->output  =   "<div id=\"prelude\">\n";
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
    $this->output =   "<div id=\"speech\">\n";

    $speech  = ROOT_PATH . "speech.txt";
    if (file_exists($speech))
    {
      $line = file_get_contents($speech);
      $this->output .=  $line . "<br />\n";
    }
    
    $announce = ROOT_PATH . "announce.txt";
    if (file_exists($announce))
    {
      $line = file($announce);
      foreach ($line as $nb => $value)
        $this->output .=  $value;
    }
    $this->output .=   "</div>\n\n";
    $this->Output();
  }
  
  function NavBar($page, $limit, $total)
  {
    global $nextargs;

    if(isset($this->navbar_cache))
    {
        $this->output =  $this->navbar_cache;
        $this->Output();
        return;
    }

    $page = (empty($page)) ? 1 : $page;
    $off = (empty($page)) ? 0 : ($page-1)*$limit;
  
    $this->output  = "<div class=\"navbar\">\n";
    $this->output .= "<center>\n";
  
    $pages  = ceil($total / $limit);
  
    if ($off > 0)
      $this->output .= "<a href=\"".$nextargs."&amp;page=".($page-1)."\">prev</a>\n";
    else
      $this->output .= "prev\n";

    $this->output .= "&nbsp;|&nbsp;";
 
    for ($i=1; $i<=$pages; $i++)
    {
      if ($i != $page)  
         $this->output .= "<a href=\"".$nextargs."&amp;page=".$i."\">".$i."</a>";
      else
         $this->output .= "<b>" . $i . "</b>";
      $this->output .= "&nbsp;\n";
    }

    $this->output .= "&nbsp;|&nbsp;";
    if ($off+$limit <= $total)
      $this->output .= "<a href=\"".$nextargs."&amp;page=".($page+1)."\">next</a>\n";
    else
      $this->output .= "next\n";
    
    $this->output .= "</center>\n";
    $this->output .= "</div>\n";
    $this->navbar_cache = $this->output;

    $this->Output();
  }

  function SideBar($online_stats=array())
  {
    global $config;

    $rec = new Record($this->db);
    $bar = new SideBar();

    $bar->AddBlock_Welcome();
    $bar->AddBlock_stats($online_stats);
    
    
    /* Menu */
    if (Auth::Check(get_userlevel_by_name("member")))
    {
      $menu_main = new Menu();

      if (Auth::Check(get_userlevel_by_name("admin")))
      {
        $menu_main->AddItem("Admin Panel", "admin/admin.php");
        $menu_sub = new Menu(1);
        $count_incoming =  $this->db->RequestCountRecords(get_folder_by_name("incoming"), get_type_by_name("all"));
        $menu_sub->AddItem($count_incoming." incoming records", "admin/admin.php?folder=".get_folder_by_name("incoming"));
        $menu_sub->AddItem("Management", "admin/management.php");
        $menu_sub->AddItem("Members mgmt", "admin/memberlist.php");
        $menu_sub->AddItem("File explorer", "admin/filexplorer.php");
      
        $menu_main->AddSubMenu($menu_sub);
      }
      if (Auth::Check(get_userlevel_by_name("admin")))
      {
        $menu_main->AddItem("Moderator Panel", "");
        $menu_sub = new Menu(1);
        $menu_sub->AddItem("Tagboard moderation", "admin/tag_moder.php");
        $menu_main->AddSubMenu($menu_sub);
      }

      $menu_main->AddItem("Upload a record", "upload.php");
      $menu_main->AddItem("Member list", "memberlist.php");
      $menu_main->AddItem("Your profile", "profile.php");
      $menu_main->AddItem("Logout", "login.php?out");
      
      $bar->AddBlock_MenuBar("Member Menu", $menu_main);
    }
    else
    {
      $bar->AddBlock_LoginForm();
      $menu_main = new Menu();
      $menu_main->AddItem("Register", "register.php");
      $menu_main->AddItem("Forgot your password ?", "forgot.php");
      $menu_main->AddItem("Member list", "memberlist.php");
      
      $bar->AddBlock_MenuBar("", $menu_main);
    }
    
    $bar->AddBlock_TagBoard($this->db, $this->bbcode, $this->smilies, $this->style);
    $bar->AddBlock_LastComments($this->db, $this->bbcode, $this->smilies);
    $bar->AddBlock_Legend($this->style);
    $bar->AddBlock_Baneers();
    
    $this->output = $bar->End();
    $this->Output();
    
  }
  
  function Footer($version, $time="")
  {
    $this->output = "";
    if(!empty($time))
    {
      $this->output .=   "<div id=\"stats\">\n";
      // $this->output .=   "Page generated using ".$time."s\n";
      $this->output .=   "Page generated using ".$time."s and ".$this->db->GetReqCount()." queries\n";
      $this->output .=   "</div>\n\n";
    }
 
    $this->output .=   "<div id=\"footer\">\n";
    $this->output .=   "nevertable ".$version." powered by <a href=\"http://shinobufan.free.fr/dotclear\">shino</a>\n";
    $this->output .=   "</div>\n\n";
 
    /* wz_tooltip */
    $this->output .=  "<script type=\"text/javascript\" src=\"".ROOT_PATH."includes/js/wz_tooltip.js\"></script>\n";
    
    $this->Output();
  }


  function Table($mysql_results, $diffview=false, $total="")
  {
    $this->output  =  "<div class=\"results\">\n";
    $this->output .=  "<div class=\"results-prelude\">\n";
    if (!empty($total))
     $this->output .=  $total." records displayed\n";
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
    
    $this->output .=  "<center>Show results as downloadable list : <a href=\"?to=showlinklist\" target=\"_blank\">Link_List.lst</a></center>\n";
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
    $this->db->RequestSelectInit(
        array("maps", "sets"),
        array($p."maps.map_solfile AS map_solfile", $p."sets.set_path AS set_path")
    );
    $this->db->RequestGenericFilter(
        array($p."sets.id"),
        array($p."maps.set_id"),
        "AND", false
    );
    $this->db->RequestGenericFilter(
        array($p."maps.set_id", $p."maps.level_num"),
        array($set, $level)
    );
    $this->db->RequestLimit(1);
    $this->db->Query();
    $val = $this->db->FetchArray();

    /* records du contest */
    $this->output  =  "<div class=\"results\">\n";
    
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
    $this->output  =  "<div class=\"oneresult\">\n";
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
        button_error($u->GetError(), 400);
    if(!$s->LoadFromId($fields['levelset']))
        button_error($u->GetError(), 400);
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
          button_error($u->GetError(), 400);
      if(!$s->LoadFromId($fields['levelset']))
          button_error($u->GetError(), 400);

      
      $this->output  =  "<div class=\"embedded\">\n";
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

  function Comments($mysql_results, $date_format)
  {
    global $config;

    $this->output = "";

    if ($this->db->NumRows($mysql_results)>0)
    {
      while ($val = $this->db->FetchArray($mysql_results))
      {
        $this->output .=  "<div class=\"comments\">\n";
        $this->output .=  "<a name=\"".$val['id']."\"></a>\n";
        /* table générale */
        $this->output .=  "<table>\n";
        $this->output .=  "<tr><td>\n";
 
        /* table d'entete */
        $this->output .=  "<div class=\"embedded\">\n";
        $this->output .=  "<table class=\"com_header\">\n";
        $this->output .=  "<tr>\n";
        $this->output .=  "<td><a href=\"viewprofile.php?id=".$val['user_id']."\">".$val['user_pseudo']."</a></td><td style=\"text-align: right;\">".date($date_format,GetDateFromTimestamp($val['timestamp']))."</td>\n";
        
        if (Auth::Check(get_userlevel_by_name("moderator")))
        {
          $this->output .=  "<td style=\"width: 16px;\">";
          $this->output .=  "<a href=\"?to=comedit&amp;comid=".$val['id']."&amp;id=".$val['replay_id']."\">";
          $this->output .=  $this->style->GetImage('edit', "Edit this comment" );
          $this->output .=  "</a></td>\n";
          $this->output .=  "<td style=\"width: 16px;\">";
          $this->output .=  "<a href=\"?to=comdel&amp;comid=".$val['id']."&amp;id=".$val['replay_id']."\">";
          $this->output .=  $this->style->GetImage('del', "Delete this comments" );
          $this->output .=  "</a></td>\n";
        }

        else if (Auth::CheckUser($val['user_id']))
        {
          $this->output .=  "<td style=\"width: 16px;\">";
          $this->output .=  "<a href=\"?to=comedit&amp;comid=".$val['id']."&amp;id=".$val['replay_id']."\">";
          $this->output .=  $this->style->GetImage('edit', "Edit this comment" );
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
    $this->Output();
    }
  }

  function MemberList($mysql_results)
  {
    $this->output  =  "<center>\n";
    $this->output .=  "<div class=\"results\" style=\"width:650px; float: none; padding: 5 0 5 0;\">\n";
    $this->output .=  "<table style=\"text-align: center;\"><tr>\n";
    $this->output .=  "<th style=\"text-align: center;\"><a href=\"memberlist.php?sort=pseudo\">Name</a></th>\n";
    $this->output .=  "<th style=\"text-align: center;\"><a href=\"memberlist.php?sort=records\">Records number</a></th>\n";
    $this->output .=  "<th style=\"text-align: center;\"><a href=\"memberlist.php?sort=best\">Best records</a></th>\n";
    $this->output .=  "<th style=\"text-align: center;\"><a href=\"memberlist.php?sort=best\">Rank</a></th>\n";
    $this->output .=  "<th style=\"text-align: center;\"><a href=\"memberlist.php?sort=comments\">Comments</a></th>\n";
    $this->output .=  "<th style=\"text-align: center;\"><a href=\"memberlist.php?sort=cat\">Category</a></th>\n";
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

  function UserProfile($o_user)
  {
    $user_id= $o_user->GetId();
    $pseudo = $o_user->GetPseudo();
    $level  = $o_user->GetLevel();
    $avatar = $o_user->GetAvatarHtml();
    $speech = $o_user->GetSpeech();
    $speech = $this->bbcode->parse($speech, "all", false);
    $speech = $this->smilies->Apply($speech);

    $records = $o_user->GetTotalRecords();
    $records_best = $o_user->GetBestRecords();
    $records_besttime = $o_user->CountBestRecords_WithType(get_type_by_name("best time"));
    $records_mostcoins = $o_user->CountBestRecords_WithType(get_type_by_name("most coins"));
    $records_freestyle = $o_user->CountBestRecords_WithType(get_type_by_name("freestyle"));
    $comments = $o_user->GetComments();

    $location = $o_user->GetLocalisation();
    $web = $o_user->GetWebHtml();

    
    $this->output  =  "<div class=\"nvform\" style=\"width: 700px;\">\n";
    $this->output .=  "<table><th colspan=\"2\">Profile ".$pseudo."</th>\n";
    $this->output .=  "<tr><td>\n";

    /* table interne de pagination */
    $this->output .=  "<div class=\"embedded\">\n";
    $this->output .=  "<table>\n";
    $this->output .=  "<tr><td width=130px valign=\"top\">\n";
    $this->output .=  "<center>".$avatar."</center>\n";
    $this->output .=  "<br/>\n";
    $this->output .=  "<center>".get_userlevel_by_number($level)."</center>\n";
    $this->output .=  "<br/>\n";
    $this->output .=  "<center>";
    for($i=0; $i<CalculRank($records_best); $i++)
      $this->output .=  $this->style->GetImage('rank');
    $this->output .=  "</center>\n";
    $this->output .=  "</td><td>\n";
    
    /* infos */
    $this->output .=  "<table>\n";
    $this->output .=  "<tr>\n";
    $this->output .=  "<td class=\"row2\" width=\"150px\">Localisation  </td>\n";
    $this->output .=  "<td class=\"row1\">".$location."</td>\n";
    $this->output .=  "</tr>\n";
    $this->output .=  "<tr>\n";
    $this->output .=  "<td class=\"row2\">Web </td>\n";
    $this->output .=  "<td class=\"row1\">".$web."</td>\n";
    $this->output .=  "</tr>\n";
    $this->output .=  "<tr>\n";
    $this->output .=  "<td class=\"row2\">Quote </td>\n";
    $this->output .=  "<td class=\"row1\">".$speech."</td>\n";
    $this->output .=  "</tr>\n";
    $this->output .=  "</table>\n";
    /* fin infos */
    
    $this->output .=  "<br/>\n";

    /* stats */
    $this->output .=  "<table>\n";
    $this->output .=  "<tr>\n";
    $this->output .=  "<td class=\"row2\" width=\"150px\">Total Records </td>\n";
    $this->output .=  "<td class=\"row1\">".$records."</td>\n";
    $this->output .=  "</tr>\n";
    $this->output .=  "<tr>\n";
    $this->output .=  "<td class=\"row2\">Best Records </td>\n";
    $this->output .=  "<td class=\"row1\">".$records_best."</td>\n";
    $this->output .=  "</tr>\n";
    $this->output .=  "<tr>\n";
    $this->output .=  "<td class=\"row2\">Best Time</td>\n";
    $this->output .=  "<td class=\"row1\">";
    for ($i=0; $i<$records_besttime; $i++)
      $this->output .=  $this->style->GetImage('best time');
    $this->output .=  "</td>\n";
    $this->output .=  "</tr>\n";
    $this->output .=  "<tr>\n";
    $this->output .=  "<td class=\"row2\">Most coins</td>\n";
    $this->output .=  "<td class=\"row1\">";
    for ($i=0; $i<$records_mostcoins; $i++)
      $this->output .=  $this->style->GetImage('most coins');
    $this->output .=  "</td>\n";
    $this->output .=  "</tr>\n";
    $this->output .=  "<tr>\n";
    $this->output .=  "<td class=\"row2\">Freestyle</td>\n";
    $this->output .=  "<td class=\"row1\">";
    for ($i=0; $i<$records_freestyle; $i++)
      $this->output .=  $this->style->GetImage('freestyle');
    $this->output .=  "</td>\n";
    $this->output .=  "</tr>\n";
    $this->output .=  "<tr>\n";
    $this->output .=  "<td class=\"row2\">Comments </td>\n";
    $this->output .=  "<td class=\"row1\">". $comments . "</td>\n";
    $this->output .=  "</tr>\n";
    $this->output .=  "<tr>\n";
    $this->output .=  "<td colspan=\"2\">\n";
    $this->output .=  "</td>\n";
    $this->output .=  "</tr>\n";
    $this->output .=  "</table>\n";
    /* fin stats */

    $this->output .=  "</td></tr>\n";
    $this->output .=  "<tr><td colspan=\"2\">\n";

    $this->output .=  "<center>";
    $this->output .=  "<a href=\"".ROOT_PATH."index.php?folder=-1&filter=user_id&filterval=".$user_id." \">View all records of ".$pseudo."</a>\n";
    $this->output .=  "<br/><a href=\"".ROOT_PATH."index.php?folder=".get_folder_by_name("contest")."&filter=user_id&filterval=".$o_user->GetId()." \">View all records (contest only)</a>\n";
    $this->output .=  "</center>\n";
    
    $this->output .=  "</td></tr>\n";
    $this->output .=  "</table>\n";
    $this->output .=  "</div>\n";
    /* fin table interne pagination */

    $this->output .=  "</td></tr>\n";
    $this->output .=  "</table>\n";
    $this->output .=   "</div>\n";
    
    $this->Output();
  }
  
  
  /*__FORMULAIRES__*/

  function CommentForm($replay_id, $content_memory="", $user_id=-1)
  {
    global $nextargs;

    if ($user_id === -1)
    {
        $user_id = $_SESSION['user_id'];
        $pseudo  = $_SESSION['user_pseudo'];
    }
    else
    {
      $u = new User($this->db);
      if ($u->LoadFromId($user_id))
        $pseudo = $u->GetPseudo();
      else
        button_error($u->GetError(), 400);
    }

    $this->output  =   "<div class=\"nvform\" style=\"width: 600px;\">\n";
    $this->output .=   "<form method=\"post\" action=\"".$nextargs."\" name=\"commentform\">\n";
    $this->output .=   "<table><tr>\n";
    $this->output .=   "<td colspan=\"2\"><label for=\"pseudo\">name : </label>\n";
    $this->output .=   "<input type=\"text\" id=\"pseudo\" name=\"pseudo\" size=\"20\" readonly /></td>\n";
    
    $this->output .=   "</tr><tr>\n";
    $this->output .=   "<td colspan=\"2\"><center><textarea id=\"content\" name=\"content\" rows=\"12\" cols=\"90\"></textarea></center>\n";
    $this->output .=   "</td></tr><tr>\n";
  
    $this->output .=  "<td colspan=\"2\"><center><input type=\"submit\" name=\"preview\" value=\"Preview\" />\n";
    $this->output .=   "<input type=\"submit\" value=\"Submit\" /></center>\n";
    $this->output .=   "<input type=\"hidden\" name=\"id\" id=\"id\" value=\"".$replay_id."\" /></td>\n";
    $this->output .=   "<input type=\"hidden\" name=\"user_id\" id=\"user_id\" value=\"".$user_id."\" /></td>\n";
    $this->output .=   "</tr><tr>\n";
    $this->output .=   "<td>\n";
    /* barre d'outils */
    global $toolbar_el;

    $this->output .=  "<script type=\"text/javascript\" src=\"".ROOT_PATH."includes/js/toolbar.js\"></script>\n";
    $this->output .=  "<script type=\"text/javascript\">setTextArea(document.forms['commentform'].content)</script>\n";

    foreach($toolbar_el as $key => $func)
    {
        $this->output .=  "<a href=\"javascript:".$func."()\">".$this->style->GetIcon($key)."</a>";
    }

    $this->output .=  "&nbsp;&nbsp;\n";

    $this->output .=  "</td><td style=\"text-align: right\"\n";
   
    $this->output .=   "<a href=\"javascript:child=window.open('./popup_smilies.php?referer_form=commentform', 'Smiles', 'fullscreen=no,toolbar=no,status=no,menubar=no,scrollbars=no,resizable=yes,directories=no,location=no,width=270,height=200,left='+(Math.floor(screen.width/2)-140));child.focus()\">(smiles)</a>";

    /* fin -- barre d'outils */
    $this->output .=   "</td></tr>\n";
    $this->output .=   "</table>\n";
    $this->output .=   "</form>\n";
    $this->output .=   "</div>\n\n";

    $this->output .=  "<script type=\"text/javascript\">update_commentform_fields('".$pseudo."','".addcslashes($content_memory, "\0..\37")."')</script>\n";

    $this->Output();
  }

  function TypeForm($args)
  {
    global $types, $levels, $newonly;
  
    $this->output  =   "<div class=\"nvform\" style=\"width: 700px;\">\n";
    $this->output .=   "<form method=\"post\" action=\"?\" name=\"typeform\">\n";
    $this->output .=   "<table><tr>\n";
    $this->output .=   "<th colspan=\"6\">Selection switches</th>\n";
    $this->output .=   "</tr><tr>\n";
    $this->output .=   "<td><label for=\"table\">table select : </label>\n";
    $this->output .=   "<select name=\"type\" id=\"table\">\n";
  
    foreach ($types as $nb => $value)
      $this->output .=   "<option value=\"".$nb."\">".$types[$nb]["name"]."</option>\n";
  
    $this->output .=   "</select></td>\n";

    $this->output .=   "<td><label for=\"folder\">folder select : </label>\n";
    $this->output .=   "<select name=\"folder\" id=\"folder\">\n";
  
    /* Hard coded value for main page */
    $this->output .=   "<option value=\"0\">contest</option>\n";
    $this->output .=   "<option value=\"3\">old records</option>\n";
    $this->output .=   "<option value=\"-1\">all</option>\n";
    $this->output .=   "</select></td>\n";
  
    $this->output .=   "<td><label for=\"levelset_f\">levelset: </label>\n";
    $this->output .=   "<select name=\"levelset_f\" id=\"levelset_f\">\n";
    $this->output .=   "<option value=\"0\">all</option>\n";
    foreach ($this->db->GetSets() as $id => $name)
    {
      $this->output .=   "<option value=\"".$id."\">".$name."</option>\n";
    }
    $this->output .=   "</select></td>\n";
    $this->output .=   "<td><label for=\"level_f\">level: </label>\n";
    $this->output .=   "<select name=\"level_f\" id=\"level_f\">\n";
    $this->output .=   "<option value=\"0\">all</option>\n";
    foreach ($levels as $name => $value)
    {
      $this->output .=   "<option value=\"".$value."\">".$name."</option>\n";
    }
    $this->output .=   "</select></td>\n";
  
    $this->output .=   "</tr></table>\n";
    $this->output .=  "<br />\n";
  
    $this->output .=   "<table><tr>\n";
    $this->output .=   "<th colspan=\"6\">Filters</th>\n";
    $this->output .=   "</tr><tr>\n";
    $this->output .=   "<td><label for=\"diffview\">Diff view</label>\n";
    $this->output .=   "<input type=\"checkbox\" name=\"diffview\" id=\"diffview\" value=\"on\" /></td>\n";
    $this->output .=   "<td><label for=\"newonly\">Print only new records : </label>\n";
    $this->output .=   "<select name=\"newonly\" id=\"newonly\">\n";
  
    foreach ($newonly as $nb => $name)
      $this->output .=   "<option value=\"".$nb."\">".$name."</option>\n";
  
    $this->output .=   "</select></td>\n";
    $this->output .=   "</tr></table>\n";
    $this->output .=  "<br />\n";
    $this->output .=   "<center><input type=\"submit\" value=\"Change\" /></center>\n";
  
    $this->output .=   "</form>\n";
    $this->output .=   "</div>\n\n";

    $this->output .=  "<script type=\"text/javascript\">update_typeform_fields(".$args['type'].",".$args['folder'].",".$args['levelset_f'].",".$args['level_f'].",\"".$args['diffview']."\",".$args['newonly'].")</script>\n";
    
    $this->Output();
  }

  function EditForm()
  {

  }
  
  function AddFormAuto()
  {
    global $types, $nextargs;

    if (!Auth::Check(get_userlevel_by_name("member")))
    {
      button_error("You need to log in to post a record.", 400);
      return;
    }
    
    $this->output  =   "<div class=\"nvform\"  style=\"width: 600px;\">\n";
    $this->output .=   "<form enctype=\"multipart/form-data\" method=\"post\" action=\"upload.php?to=autoadd\" name=\"addform_auto\">\n";
    $this->output .=   "<table><tr>\n";
    $this->output .=   "<th colspan=\"6\">Auto add a record from replay file</th></tr><tr>\n";
    $this->output .=   "<td><label for=\"pseudo\">pseudo: </label></td>\n";
    $this->output .=   "<td colspan=\"3\"><input type=\"text\" id=\"pseudo\" name=\"pseudo\" size=\"10\" value=\"".$_SESSION['user_pseudo']."\" readonly /></td>";
    $this->output .=   "<td><input type=\"hidden\" id=\"user_id\" name=\"user_id\" size=\"10\" value=\"".$_SESSION['user_id']."\" readonly /></td><td></td></tr>\n<tr>";
    $this->output .=   "<td><label for=\"type\">type: </label></td>\n";
    $this->output .=   "<td><select id=\"type\" name=\"type\">\n";
  
    foreach ($types as $nb => $value)
    {
      if ($types[$nb]["name"] != "all")
          $this->output .=   "<option value=\"".$nb."\">".$types[$nb]["name"]."</option>\n";
    }
  
    $this->output .=   "</select>\n</td>";
    $this->output .=   "<td colspan=\"3\"><label for=\"goalnotreached\">Goal not reached</label>\n";
    $this->output .=   "<input type=\"checkbox\" name=\"goalnotreached\" id=\"goalnotreached\" value=\"on\" /></td>\n";
    
    $this->output .=  "</tr><tr>\n";
 
    $this->output .=   "<td><label for=\"replayfile\">Replay file : </label></td>\n";
    $this->output .=   "<td colspan=\"5\"><input id=\"replayfile\" name=\"replayfile\" type=\"file\" /></td>\n";
    $this->output .=   "<td><input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"".$config['upload_size_max']."\" />\n";

    $this->output .=  "<input type=\"hidden\" id=\"folder\" name=\"folder\" value=\"".get_folder_by_name("incoming")."\" />\n";
    $this->output .=  "</td>\n";
  
    $this->output .=   "</tr><tr><td>\n";

    $this->output .=   "<td colspan=\"2\"><center><input type=\"submit\" value=\"Go for it !\" /></center></td>\n";
    $this->output .=   "<td colspan=\"2\"><center><input type=\"reset\" value=\"Clear form\" /></center></td>\n";
    $this->output .=   "<td><input type=\"hidden\" id=\"to\" name=\"to\" value=\"autoadd\" /></td>\n";
    $this->output .=   "<td><input type=\"hidden\" id=\"user_id\" name=\"user_id\" value=\"".$_SESSION['user_id']."\" /></td>\n";
    $this->output .=   "</tr>\n";
  
    $this->output .=   "</table>\n";
    $this->output .=   "</form>\n";
    $this->output .=   "</div>\n\n";
    
    $this->Output();
  }


  /*__METHODES PRIVEES__*/
  
  function _RecordLine($i, $fields, $display_shot=true, $diffview=false, $diffref=array())
  {
    global $nextargs, $old_users;

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
    if(!empty($fields["replay"]))
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
       $str = "comment !";
    else if ($nb_comments==1)
       $str = "1 comment";
    else
       $str = $nb_comments . "&nbsp;comments";
       $this->output .=   "<td><a href=\"record.php?id=".$fields['id']."\">".$str."</a></td>";
  
    /* "attach" */
    $this->output .=  "<td><a href=\"index.php?levelset_f=".$fields['levelset']."&amp;level_f=".$fields['level']."&amp;folder=-1\" title=\"Show all records for this level.\">";
    $this->output .=  $this->style->GetImage('attach', "Show all records for this level.");
    $this->output .=  "</a></td>";
     
    $this->output .=   "</tr>\n";
  }
  
  function _MemberLine($i, $fields)
  {
    global $nextargs, $config;

    $rowclass = ($i % 2) ? "row1" : "row2";
    $this->output .=   "<tr class=\"".$rowclass."\">\n";

    /* Name */
    $this->output .=  "<td height=\"20px\">";
    if (!empty($fields['user_avatar']))
       $tooltip=Javascriptize("<center><img src=\"".ROOT_PATH.$config['avatar_dir']."/".$fields['user_avatar']."\" alt=\"\" /></center>");
    else
       $tooltip=Javascriptize("<center><i>No Avatar</i></center>");
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
    global $nextargs;

    $this->output .=  "<th style=\"width: 28px;\"></th>\n"; // new
    $this->output .=  "<th><a href=\"".$nextargs."&amp;sort=old\">old</a></th>\n"; // days
    $this->output .=  "<th style=\"width: 28px;\"></th>\n"; // best
    $this->output .=  "<th style=\"width: 32px;\"></th>\n"; // type
    $this->output .=  "<th style=\"width: 100px;\"><a href=\"".$nextargs."&amp;sort=user\">player</a></th>\n";
    $this->output .=  "<th><a href=\"".$nextargs."&amp;sort=level\">set</a></th>\n";
    $this->output .=  "<th><a href=\"".$nextargs."&amp;sort=level\">lvl</a></th>\n";
    $this->output .=  "<th><a href=\"".$nextargs."&amp;sort=time\">time</a></th>\n";
    $this->output .=  "<th><a href=\"".$nextargs."&amp;sort=coins\">cns</a></th>\n";
    $this->output .=  "<th></th>\n"; // replay
    $this->output .=  "<th></th>\n"; // comments
    $this->output .=  "<th></th>\n"; // goto same records
  }

  function _JumpLine($colspan)
  {
    $this->output .=  "<tr><td colspan=\"".$colspan."\" style=\"background: #fff; height: 2px;\"></td></tr>\n";
  }
}

