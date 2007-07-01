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

class Menu
{

    var $output;
    var $level;

	/*__Constructeur__
	
	Cette fonction initialise l'objet Replay.
	*/
	function Menu($levelP=0)
    {
       $this->level = $levelP;
    }

    function GetOutput()
    {
      return $this->output;
    }

    function AddItem($name, $link)
    {
        $new_name = $name;
        for ($i=0; $i<$this->level; $i++)
           $new_name = "&nbsp;&nbsp;" . $new_name;
	if (!empty($link))
           $this->output .= "<div class=\"menuitem\"><a href=\"".$link."\">".$new_name."</a><br/></div>\n";
	else
           $this->output .= "<div class=\"menuitem\">".$new_name."<br/></div>\n";
    }
    
    
    /* Add Sub Menu 
     * $menu is a type Menu from class.menu.php
     * */
    function AddSubMenu(&$menu)
    {
       $this->output .= $menu->GetOutput();
    }
}

