/***** BEGIN LICENSE BLOCK *****
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
# ***** END LICENSE BLOCK *****/

function change_form_input(formP, idP, P) {
	document.forms[formP].eval(idP).value  = P;
}

function change_form_select(formP, idP, P) {
	document.forms[formP].eval(idP).selectedIndex  = P;
}

function change_form_textarea(formP, idP, P) {
	document.forms[formP].eval(idP).value  = P;
}

function change_form_checkbox(formP, idP, P) {
	if (P == "on")
  	   document.forms[formP].eval(idP).checked  = true;
	else
  	   document.forms[formP].eval(idP).checked  = false;
}
