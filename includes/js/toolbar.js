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

/*
Some code here come from dotclear, Olivier Meunier
*/

function setTextArea(P) {
 textarea = P;
}

function encloseSelection(prefix, suffix) {
	textarea.focus();
	var start, end, sel, scrollPos, subst;
		
	if (typeof(document["selection"]) != "undefined") {
		sel = document.selection.createRange().text;
	} else if (typeof(textarea["setSelectionRange"]) != "undefined") {
		start = textarea.selectionStart;
		end = textarea.selectionEnd;
		scrollPos = textarea.scrollTop;
		sel = textarea.value.substring(start, end);
	}
	
	if (sel.match(/ $/)) { // exclude ending space char, if any
		sel = sel.substring(0, sel.length - 1);
		suffix = suffix + " ";
	}

	var res = (sel) ? sel : '';
	
	subst = prefix + res + suffix;
	
	if (typeof(document["selection"]) != "undefined") {
		var range = document.selection.createRange().text = subst;
		textarea.caretPos -= suffix.length;
	} else if (typeof(textarea["setSelectionRange"]) != "undefined") {
	    textarea.value = textarea.value.substring(0, start) + subst +
    	textarea.value.substring(end);
		if (sel) {
			textarea.setSelectionRange(start + subst.length, start + subst.length);
		} else {
			textarea.setSelectionRange(start + prefix.length, start + prefix.length);
		}
		textarea.scrollTop = scrollPos;
	}
}
	
function singleTag(htag) {
	var stag = '['+htag+']';
	var etag = '[/'+htag+']';
	encloseSelection(stag,etag);
}
	
function tbStrong() {
    singleTag('b');
}
	
function tbEm() {
	singleTag('i');
}

function tbUnderline() {
	singleTag('u');
}

function tbQuote() {
	singleTag('quote');
}

function tbLink() {
	var href = window.prompt('Enter URL:','http://');
	if (!href) { return; }
			
	var stag = '[url='+href;
	stag = stag+']';
	var text = window.prompt('Text:','');
    if (text) { stag = stag + text; }
    	
	etag = '[/url]';
			
	encloseSelection(stag,etag);
}

function tbImg() {
	var href = window.prompt('Enter image URL:','http://');
	if (!href) { return; }
			
	var stag = '[img]'+href;
	
    etag = '[/img]';
	
    encloseSelection(stag,etag);
}
