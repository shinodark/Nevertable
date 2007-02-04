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

function change_editform(idP,user_idP,pseudoP,levelsetP,levelP,timeP,coinsP,replayP,typeP) {
	document.forms['editform'].id.value  = idP;
	document.forms['editform'].user_id.value  = user_idP;
	document.forms['editform'].pseudo.value  = pseudoP;
    document.forms['editform'].levelset.selectedIndex = levelsetP-1;
    document.forms['editform'].level.selectedIndex = levelP;
    document.forms['editform'].time.value = timeP;
    document.forms['editform'].coins.value = coinsP;
    document.forms['editform'].replay.value = replayP;
    document.forms['editform'].type.selectedIndex = typeP;
}

function update_typeform_fields(tableP, folderP, levelsetP, levelP, diffviewP, newonlyP) {
	document.forms['typeform'].table.selectedIndex  = tableP;

    //hack for hard coded folder value in main page
	if (folderP==0)
        document.forms['typeform'].folder.selectedIndex  = 0;
    else if (folderP == 3)
        document.forms['typeform'].folder.selectedIndex  = 1;
    else
        document.forms['typeform'].folder.selectedIndex  = 2;

	document.forms['typeform'].levelset_f.selectedIndex  = levelsetP;
	document.forms['typeform'].level_f.selectedIndex  = levelP;
	if (diffviewP=="on")
        document.forms['typeform'].diffview.checked  = true;
    else
        document.forms['typeform'].diffview.checked  = false;
	document.forms['typeform'].newonly.selectedIndex  = newonlyP;
}

function update_typeform_fields_admin(tableP, folderP, bestonlyP, newonlyP, levelsetP, levelP) {
	document.forms['typeform_admin'].table.selectedIndex  = tableP;
	document.forms['typeform_admin'].folder.selectedIndex  = folderP;
	if (bestonlyP=="on")
        document.forms['typeform_admin'].bestonly.checked  = true;
    else
        document.forms['typeform_admin'].bestonly.checked  = false;
	document.forms['typeform_admin'].newonly.selectedIndex  = newonlyP;
	document.forms['typeform_admin'].levelset_f.selectedIndex  = levelsetP;
	document.forms['typeform_admin'].level_f.selectedIndex  = levelP;
}

function update_commentform_fields(pseudoP, contentP) {
	document.forms['commentform'].pseudo.value  = pseudoP;
	document.forms['commentform'].content.value  = contentP;
}

function update_memberform_fields(formid, levelP) {
    document.forms['memberform_'+formid].authlevel.selectedIndex = levelP;
}

function update_profile_optionform_fields(sortP, themeP) {
    if (sortP == "old")
      document.forms['options'].sort.selectedIndex = 0;
    else if (sortP == "pseudo")
      document.forms['options'].sort.selectedIndex = 1;
    else if (sortP == "level")
      document.forms['options'].sort.selectedIndex = 2;
    else if (sortP == "type")
      document.forms['options'].sort.selectedIndex = 3;
    else if (sortP == "time")
      document.forms['options'].sort.selectedIndex = 4;
    else if (sortP == "coins")
      document.forms['options'].sort.selectedIndex = 5;

    document.forms['options'].theme.selectedIndex = themeP;
}

//function update_tagform_fields(pseudoP, linkP, contentP) {
function update_tagform_fields(pseudoP, contentP) {
    document.forms['tagform'].tag_pseudo.value  = pseudoP;
	//document.forms['tagform'].tag_link.value  = linkP;
	document.forms['tagform'].content.value  = contentP;
}
