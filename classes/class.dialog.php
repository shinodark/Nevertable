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

class Dialog
{
  var $db;
  var $navbar_cache;
  var $smilies;
  var $bbcode;
  var $style;
  var $output;
  var $table;

  /*__CONSTRUCTEUR__*/
  function Dialog(&$db, &$parent, &$o_style)
  {
    $this->db = &$db;
    $this->table = &$parent;
    $this->smilies = new Smilies();
    $this->bbcode = new parse_bbcode();
    $this->style = &$o_style;
    $this->output = "";
  }

  function Output($string="")
  {
      if (empty($string))
          echo $this->output;
      else
          echo $string;
      $this->output = "";
  }

  function Head($title, $special="")
  {
    $this->output .=  "<head>\n";
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
    $this->output .=   "<div id=\"top\">\n"; 
    $this->output .=  "<center>";
    $this->output .=   "<a href=\"http://www.nevercorner.net/table/\">".$this->style->GetImage('top')."</a>";
    $this->output .=  "</center>";
    $this->output .=   "</div>\n\n";
    $this->Output();
  }


  function NavBar($total, $limit, $callback, $nextargs="")
  {
    global $args, $config;

    $page = (empty($args['page'])) ? 1 : $args['page'];
    if ($limit > 0) $nb_pages  = ceil($total / $limit);
    else $nb_pages=1;

    if ($nb_pages <=1)
	    return;

    $this->output .= "<div class=\"navbar\">\n";

    if ($page > 1)
      $this->output .= '<a href="'.$callback.'?page='.($page-1).$nextargs.'">&nbsp;&lt;&nbsp;</a>'."\n";
    else
      $this->output .= "&nbsp;&lt;&nbsp;\n";

 
    for ($i=1; $i<=$nb_pages; $i++)
    {
      if ($i != $page)  
         $this->output .= '&nbsp;<a href="'.$callback.'?page='.$i.$nextargs.'">'.$i.'</a>&nbsp;';
      else
         $this->output .= "&nbsp;<b>" . $i . "</b>&nbsp;";
      $this->output .= "&nbsp;\n";
    }

    if ($page < $nb_pages)
      $this->output .= '<a href="'.$callback.'?page='.($page+1).$nextargs.'">&nbsp;&gt;&nbsp;</a>'."\n";
    else
      $this->output .= "&nbsp;&gt;&nbsp;\n";
    
    $this->output .= "</div><!-- fin navbar -->\n";
    $this->Output();
  }

  function Stats($stats)
  {
    global $config, $lang;

    $this->output .=   "<div id=\"stats\">\n";
    
    $list = "";
    for ($i=0; $i<$stats['registered']-1; $i++)
	$list .= $stats['registered_list'][$i] . ', ';
    $list .= $stats['registered_list'][$stats['registered']-1];

    $this->output .=   '<span class="st_label">'.$lang['STATS_NUMBERS_LABEL'].'</span>';
    $this->output .=   '<span class="st_text">'.sprintf($lang['STATS_NUMBERS_TEXT'], $stats['registered'], $stats['guests']).'</span>';
    $this->output .=   "<br/>\n";
    $this->output .=   '<span class="st_label">'.$lang['STATS_LIST'].'</span>';
    $this->output .=   '<span class="st_text">'.$list.'</span>';
    
    $this->output .=   "</div>\n\n";

    $this->Output();
  }

  function NeverStats()
  {
    global $config;

    $this->output  = "<div>\n";
    $this->output .= '<center><applet codebase="'.ROOT_PATH.'contrib/neverstats" code="Neverstats.class" width=820 height=580>'."\n";
    $this->output .= '<param name="width" value="820">'."\n";
    $this->output .= '<param name="height" value="580">'."\n";
    $this->output .= '<param name="URL" value="http://'.$_SERVER['SERVER_NAME'] .'/'.$config['nvtbl_path'] . $config['neverstats_liste'].'">'."\n";
    $this->output .= '</applet></center>'."\n";
    $this->output .= "</div>\n";
    
    $this->Output();
  }

  
  function Footer()
  {
    global $config, $lang;

    $this->output .=   "<div id=\"perfs\">\n";
    $this->output .=   sprintf($lang['FOOTER_PERFS'], $this->table->getProcessTime(), $this->db->GetReqCount()) . "\n";
    $this->output .=   "</div>\n\n";

    $this->output .=   "<div id=\"footer\">\n";
    $this->output .=   "Nevertable ".$config['version']." powered by shino\n";
    $this->output .=   "</div><!-- fin perfs -->\n\n";
 
    /* wz_tooltip */
    $this->output .=  "<script type=\"text/javascript\" src=\"".ROOT_PATH."includes/js/wz_tooltip.js\"></script>\n";
    
    $this->Output();
  }

  function UserProfile($o_user)
  {
    global $lang;

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

    
    $this->output .=  "<div class=\"generic_form\" style=\"width: 700px;\">\n";
    $this->output .=  "<table><th colspan=\"2\">".sprintf($lang['PROFILE_TITLE'], $pseudo)."</th>\n";
    $this->output .=  "<tr><td>\n";

    /* table interne de pagination */
    $this->output .=  "<div class=\"embedded\">\n";
    $this->output .=  "<table>\n";
    $this->output .=  "<tr><td width=\"130\" valign=\"top\">\n";
    $this->output .=  "<center>".$avatar."</center>\n";
    $this->output .=  "<br/>\n";
    $this->output .=  "<center>".$lang[get_userlevel_by_number($level)]."</center>\n";
    $this->output .=  "<br/>\n";
    $this->output .=  "<center>";
    for($i=0; $i<CalculRank($records_best); $i++)
      $this->output .=  $this->style->GetImage('rank');
    $this->output .=  "</center>\n";
    $this->output .=  "</td><td>\n";
    
    /* infos */
    $this->output .=  "<table>\n";
    $this->output .=  "<tr>\n";
    $this->output .=  "<td class=\"row2\" width=\"150\">".$lang['PROFILE_LOCALISATION']."</td>\n";
    $this->output .=  "<td class=\"row1\">".$location."</td>\n";
    $this->output .=  "</tr>\n";
    $this->output .=  "<tr>\n";
    $this->output .=  "<td class=\"row2\">".$lang['PROFILE_WEB']."</td>\n";
    $this->output .=  "<td class=\"row1\">".$web."</td>\n";
    $this->output .=  "</tr>\n";
    $this->output .=  "<tr>\n";
    $this->output .=  "<td class=\"row2\">".$lang['PROFILE_SPEECH']."</td>\n";
    $this->output .=  "<td class=\"row1\">".$speech."</td>\n";
    $this->output .=  "</tr>\n";
    $this->output .=  "</table>\n";
    /* fin infos */
    
    $this->output .=  "<br/>\n";

    /* stats */
    $this->output .=  "<table>\n";
    $this->output .=  "<tr>\n";
    $this->output .=  "<td class=\"row2\" width=\"150\">".$lang['PROFILE_TOTAL_RECORDS']."</td>\n";
    $this->output .=  "<td class=\"row1\">".$records."</td>\n";
    $this->output .=  "</tr>\n";
    $this->output .=  "<tr>\n";
    $this->output .=  "<td class=\"row2\">".$lang['PROFILE_BEST_RECORDS']."</td>\n";
    $this->output .=  "<td class=\"row1\">".$records_best."</td>\n";
    $this->output .=  "</tr>\n";
    $this->output .=  "<tr>\n";
    $this->output .=  "<td class=\"row2\">".$lang['PROFILE_BEST_TIME']."</td>\n";
    $this->output .=  "<td class=\"row1\">";
    for ($i=0; $i<$records_besttime; $i++)
      $this->output .=  $this->style->GetImage('best time');
    $this->output .=  "</td>\n";
    $this->output .=  "</tr>\n";
    $this->output .=  "<tr>\n";
    $this->output .=  "<td class=\"row2\">".$lang['PROFILE_BEST_COINS']."</td>\n";
    $this->output .=  "<td class=\"row1\">";
    for ($i=0; $i<$records_mostcoins; $i++)
      $this->output .=  $this->style->GetImage('most coins');
    $this->output .=  "</td>\n";
    $this->output .=  "</tr>\n";
    $this->output .=  "<tr>\n";
    $this->output .=  "<td class=\"row2\">".$lang['PROFILE_FREESTYLE']."</td>\n";
    $this->output .=  "<td class=\"row1\">";
    for ($i=0; $i<$records_freestyle; $i++)
      $this->output .=  $this->style->GetImage('freestyle');
    $this->output .=  "</td>\n";
    $this->output .=  "</tr>\n";
    $this->output .=  "<tr>\n";
    $this->output .=  "<td class=\"row2\">".$lang['PROFILE_COMMENTS']." </td>\n";
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
    $this->output .=  "<a href=\"".ROOT_PATH."index.php?folder=-1&amp;filter=user_id&amp;filterval=".$user_id." \">".sprintf($lang['PROFILE_VIEWALL'],$pseudo)."</a>\n";
    $this->output .=  "<br/><a href=\"".ROOT_PATH."index.php?folder=".get_folder_by_name("contest")."&amp;filter=user_id&amp;filterval=".$o_user->GetId()." \">".sprintf($lang['PROFILE_VIEWALL_CONTEST'],$pseudo)."</a>\n";
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

  function Replay($replay_struct)
  {
    $struct2name = array(
		"timer"  => "timer",
		"coins"	 => "coins",
		"state"	 => "state",
		"mode"	 => "mode",
		"player" => "player",
		"date"   => "date",
		"solfile"=> "solfile",
	);
    
    $struct2value = array(
		"timer"  => $replay_struct["timer"],
		"coins"	 => $replay_struct["coins"],
		"state"	 => get_replay_state_by_number($replay_struct["state"]),
		"mode"	 => get_replay_mode_by_number($replay_struct["mode"]),
		"player" => $replay_struct["player"],
		"date"   => $replay_struct["date"],
		"solfile"=> $replay_struct["solfile"],
	);

    $this->output .=  "<div class=\"oneresult\">\n";
    $this->output .=  "<table>\n";
    $this->output .=  "<caption>Replay file info</caption>\n";
    $this->output .=  "<tr>\n";
    foreach ($struct2name as $field => $name)
        $this->output .=  "<th>".$name."</th>\n";
    $this->output .=  "</tr>\n";
   
    $this->output .=  "<tr>\n";
    foreach ($struct2value as $field => $value)
        $this->output .=  "<td>".$value."</td>\n";
    $this->output .=  "</tr>\n";

    $this->output .=  "</table>\n";
    $this->output .=  "</div>\n";
    $this->Output();
  }
  
  /*__FORMULAIRES__*/

}

