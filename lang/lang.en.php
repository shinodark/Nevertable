<?php
# ***** BEGIN LICENSE BLOCK *****
# This file is part of Trombi .
# Copyright (c) 2004 Francois Guillet and contributors. All rights
# reserved.
#
# Trombi is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
# 
# Trombi is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with Trombi; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
# ***** END LICENSE BLOCK *****
#

global $lang;

$lang['code']			= "en";

/*
  STRUCTURES INTERNES
 */

//types
$lang['all']			= "all";
$lang['best time']		= "best time";
$lang['most coins']		= "most coins";
$lang['freestyle']		= "freestyle";

//folders
$lang['contest']		= "contest";
$lang['incoming']		= "incoming";
$lang['trash']			= "trash";
$lang['oldones']		= "oldones";

//newonly
$lang['off']			= "off";
$lang['3 days']			= "3 days";
$lang['1 week']			= "1 week";
$lang['2 weeks']		= "2 weeks";
$lang['1 month']		= "1 month";

//userlevel
$lang['root']			= "root";
$lang['admin']			= "admin";
$lang['moderator']		= "moderator";
$lang['member']			= "member";
$lang['guest']			= "guest";


/*
  GUI
 */
$lang['GUI_YES']        	= "YES";
$lang['GUI_NO']      		= "NO";

$lang['GUI_BUTTON_BACK']        = "Go back";
$lang['GUI_BUTTON_RETURN']      = "Back";
$lang['GUI_BUTTON_MAINPAGE']    = "Back to main page";
$lang['GUI_BUTTON_MAINPAGE_ADMIN'] = "Back to admin panel";
$lang['GUI_BUTTON_RETURN']      = "Back to  %s";
$lang['GUI_BUTTON_APPLY']       = "Apply";
$lang['GUI_BUTTON_REDIRECT']    = "Redirecting...";

$lang['GUI_INVALID_USER']       = "Invlid user";

$lang['GUI_UPDATE_OK']      =  "Update successful";
$lang['GUI_UPLOAD_OK']      =  "Upload successful.";

$lang['NOT_ROOT']               = "You have to be root to see this page.";
$lang['NOT_ADMIN']              = "You have to be admin to see this page.";
$lang['NOT_MODERATOR']          = "You have to be moderator.";
$lang['NOT_MEMBER']             = "You have to be registered to view this page.";
$lang['LOGIN_TO_POST']          = "You have to login to post comments.";

$lang['CHECK_ERR_TOOLONG']  =  "Field too long: %s, limit is: %d";
$lang['CHECK_ERR_LIMITS']   =  "Field out of bounds: %s, bouns are : [ %d - %d ]";

/*
  REGISTER
*/
$lang['REGISTER_EMPTY_FIELDS']      = "Incomplete fields.";
$lang['REGISTER_PSEUDO_EXISTS']     = "Pseudo already exists.";
$lang['REGISTER_MAIL_EXISTS']       = "This e-mail already exists.";
$lang['REGISTER_MAIL_NOTVALID']     = "Invalid e-mail.";
$lang['REGISTER_PASSWD_CHECK']      = "Paswords don't match.";
$lang['REGISTER_PASSWD_LENGTH']     = "Password have to count at least 5 characters.";
$lang['REGISTER_SPECIAL_CHARS']     = "Don't use specials characters.";
$lang['REGISTER_FIRST_REGISTER']    = "You're the first registered member, welcome root ;)";
$lang['REGISTER_WELCOME_SUBJECT']   = "Welcome on the Nevertable !";
$lang['REGISTER_WELCOME_MESSAGE1']  = "You're registration is finished. \n Here are your account informations : \n !";
$lang['REGISTER_WELCOME_MESSAGE1']  = "Thank you !";
$lang['REGISTER_SUCCESSFUL']        = "You're registration is finished. You can log in ;)";

$lang['REGISTER_FORM_TITLE']        = "Registration form";
$lang['REGISTER_FORM_PSEUDO']       = "Pseudo : ";
$lang['REGISTER_FORM_EMAIL']        = "Email : ";
$lang['REGISTER_FORM_PASSWD1'] 	    = "Password : ";
$lang['REGISTER_FORM_PASSWD2'] 	    = "Password, again : ";

/*
  FORGOT
*/

$lang['FORGOT_FORM_TITLE']          = "I've forgot my password...";
$lang['FORGOT_EMPTY_MAIL']          = "You have to fill your email.";
$lang['FORGOT_INVALID_MAIL']        = "Invalid email, not fonud in database.";
$lang['FORGOT_EMAIL_SENT']          = "new password created, email sent.";

/*
  LOGIN
*/
$lang['LOGIN_NOTLOGIN'] 	    = "You're not logged in";
$lang['LOGIN_ALREADYLOGIN'] 	    = "You're already logged in";
$lang['LOGIN_LOGIN'] 	            = "You're logged in<br/>Redirecting to main page...";
$lang['LOGIN_LOGOUT'] 	            = "You're logged out<br/>Redirecting to main page...";

$lang['LOGIN_FORM_TITLE']           = "Connection";
$lang['LOGIN_FORM_PSEUDO']          = "login";
$lang['LOGIN_FORM_PASSWD']          = "pass";

/*
  TABLE
*/
$lang['RESULTS_PRELUDE']	    = "%d displayed records";
$lang['TABLE_HEADER_OLD']	    = "old";
$lang['TABLE_HEADER_PLAYER']	    = "player";
$lang['TABLE_HEADER_SET']	    = "set";
$lang['TABLE_HEADER_LEVEL']	    = "lvl";
$lang['TABLE_HEADER_TIME']	    = "time";
$lang['TABLE_HEADER_COINS']	    = "coins";

$lang['TABLE_RESULTS_LIST']	    = "Download list of displayed records : ";
$lang['TABLE_ATTACH']		    = "Show all records of this level";

$lang['TABLE_NO_COMMENT']	    = "Comment !";
$lang['TABLE_ONE_COMMENT']	    = "1 comment";
$lang['TABLE_COMMENTS']	            = "%d comments";

/*
  MEMBER
*/

$lang['MEMBER_HEADER_NAME']	    = "Name";
$lang['MEMBER_HEADER_RECORDS']	    = "Records";
$lang['MEMBER_HEADER_BEST_RECORDS'] = "Best";
$lang['MEMBER_HEADER_RANK'] 	    = "Rank";
$lang['MEMBER_COMMENTS'] 	    = "Comments";
$lang['MEMBER_MAIL']   	            = "Email";
$lang['MEMBER_CATEGORY'] 	    = "Auth level";

$lang['MEMBER_NO_AVATAR']	    = "No avatar";

/*
  FORMULAIRE D'EDITION DES OPTIONS
*/
$lang['OPTIONS_FORM_TITLE']         = "My options";

$lang['OPTIONS_FORM_SORT']          = "Default sort : ";
$lang['OPTIONS_FORM_THEME']         = "Theme : ";
$lang['OPTIONS_FORM_LANG']          = "Language : ";
$lang['OPTIONS_FORM_LIMIT']         = "Records per page : ";
$lang['OPTIONS_FORM_COMMENTS_LIMIT'] = "Comments per page : ";
$lang['OPTIONS_FORM_SIDEBAR_COMMENTS'] = "Last comments in sidebar : ";
$lang['OPTIONS_FORM_SIDEBAR_COMLENGTH']  = "Length of comments preview in sidebar : ";

/*
  FORMULAIRE DU TAGBOARD
*/
$lang['TAG_FORM_PSEUDO']            = "Pseudo";
$lang['TAG_FORM_CONTENT']           = "Your tag";
$lang['TAG_FORM_SUBMIT']            = "Tag!";
$lang['TAG_EMPTY_CONTENT']          = "Nothing is filled.";
$lang['TAG_EMPTY_PSEUDO']           = "No peusdo.";
$lang['TAG_NO_TAG']                 = "No tag slected.";
$lang['TAG_TOO_LONG']               = "Tag too long.";

/*
  MENU DE LA SIDEBAR
*/
$lang['SIDEBAR_WELCOME']	    = "Welcome %s";

$lang['SIDEBAR_LOGIN']		    = "Connection";
$lang['SIDEBAR_LAST_COMMENTS']	    = "Last comments";
$lang['SIDEBAR_LEGEND_BEST']	    = "Best records for this level.";
$lang['SIDEBAR_LEGEND_BEST_TIME']   = "Best time.";
$lang['SIDEBAR_LEGEND_MOST_COINS']  = "Most coins.";
$lang['SIDEBAR_LEGEND_FREESTYLE']   = "Freestyle.";

$lang['MENU_ADMIN']		    = "Admin panel";
$lang['MENU_ADMIN_INCOMING']	    = "Incoming (%d)";
$lang['MENU_ADMIN_MANAGEMENT']	    = "Content management";
$lang['MENU_ADMIN_MEMBERS']	    = "Members management";
$lang['MENU_ADMIN_FILE_EXPLORER']   = "File explorer";

$lang['MENU_MODERATOR']		    = "Moderator menu";
$lang['MENU_MODERATOR_TAGBOARD']    = "Tagboard";

$lang['MENU_MEMBER']                = "Member menu";

$lang['MENU_MEMBER_UPLOAD']         = "Upload a record";
$lang['MENU_MEMBER_MEMBERS']        = "Members list";
$lang['MENU_MEMBER_STATS']          = "Stats";
$lang['MENU_MEMBER_PROFILE']        = "My profile";
$lang['MENU_MEMBER_OPTIONS']        = "My options";
$lang['MENU_MEMBER_LOGOUT']         = "Log out";

$lang['MENU_REGISTER']              = "Register";
$lang['MENU_FORGOT_PASSWD']         = "Forgot your password?";

/*
  AFFICHAGE DES COMMENTAIRES
*/
$lang['COMMENTS_NOCOMMENT']         = "No post at the moment";
$lang['COMMENTS_FORM_PREVIEW']      = "Preview";
$lang['COMMENTS_PREVIEW_WARNING']   = "Note: this is a preview, no actual comment has been posted yet ^-^";
$lang['COMMENTS_PREVIEW_TITLE']     = "Preview";

$lang['COMMENTS_ERR_EMPTY_CONTENT'] = "You have written nothing.";

$lang['COMMENTS_FORM_PSEUDO']       = "Pseudo : ";

$lang['COMMENTS_POPUP_EDIT']        = "Edit this comment";
$lang['COMMENTS_POPUP_DELETE']      = "Delete this comment";

/*
  AFFICHAGE DU PROFIL
 */

$lang['PROFILE_NOTTLOGIN']	    = "You have to log in to edit your profile.";
$lang['PROFILE_PASSKO']	            = "Passwords don't match.";

$lang['PROFILE_FORM_IDENT_TITLE']    = "Change your identification";
$lang['PROFILE_FORM_IDENT_PSEUDO']   = "Pseudo : ";
$lang['PROFILE_FORM_IDENT_MAIL']     = "New email : ";
$lang['PROFILE_FORM_IDENT_PASSWD1']  = "New password : ";
$lang['PROFILE_FORM_IDENT_PASSWD2']  = "New password, again : ";

$lang['PROFILE_FORM_INFO_TITLE']    = "Personnal informations";
$lang['PROFILE_FORM_INFO_INFO']     = "No fieds are required";
$lang['PROFILE_FORM_INFO_LOCAL']    = "Localisation : ";
$lang['PROFILE_FORM_INFO_WEB']      = "Personnal web site : ";
$lang['PROFILE_FORM_INFO_SPEECH']   = "Personnal quote : ";

$lang['PROFILE_FORM_AVATAR_TITLE']  = "Upload an avatar";
$lang['PROFILE_FORM_AVATAR_FILE']   = "File : ";
$lang['PROFILE_FORM_AVATAR_DEL']    = "Delete avatar";
$lang['PROFILE_FORM_AVATAR_LIMITS'] = "max size : %dx%d (%dkB)";

$lang['PROFILE_AVATAR_RESIZE_OK']   = "Picture too big, it was resized. ";


$lang['PROFILE_TITLE']              = "%s profile";
$lang['PROFILE_LOCALISATION']       = "Localization ";
$lang['PROFILE_WEB']                = "Personal website ";
$lang['PROFILE_SPEECH']             = "Personnal quote ";
$lang['PROFILE_LEVEL']              = "Auth level ";
$lang['PROFILE_TOTAL_RECORDS']      = "Total records ";
$lang['PROFILE_BEST_RECORDS']       = "Best records ";
$lang['PROFILE_BEST_TIME']          = "Best time ";
$lang['PROFILE_BEST_COINS']         = "Most coins ";
$lang['PROFILE_FREESTYLE']          = "Freestyle ";
$lang['PROFILE_COMMENTS']           = "Comments ";
$lang['PROFILE_VIEWALL']            = "View all records of %s ";
$lang['PROFILE_VIEWALL_CONTEST']    = "View all records of %s (contest only)";

/*
  UPLOAD
*/
$lang['UPLOAD_REGISTERED']           = "Your record is posted. Please wait validation of an admin :)";
$lang['UPLOAD_FORM_TITLE']           = "Upload a record";
$lang['UPLOAD_FORM_SIZEMAX']         = "Max size of a replay file : %dkB";
$lang['UPLOAD_FORM_PSEUDO']          = "Your pseudo : ";
$lang['UPLOAD_FORM_REPLAYFILE']      = "Replay file : ";
$lang['UPLOAD_FORM_TYPE']            = "Type : ";

/*
  PAGE PRINCIPALE
*/

$lang['STATS_NUMBERS_LABEL']        = "Visitors : ";
$lang['STATS_NUMBERS_TEXT']         = "%d registerd, %d guests.";
$lang['STATS_LIST']                 = "Registered users : ";

$lang['FOOTER_PERFS']               = "Page loads in %ss with %d queries.";

$lang['TYPE_FORM_TITLE']            = "Display options";
$lang['TYPE_FORM_TABLE_SELECT']     = "Table select : ";
$lang['TYPE_FORM_FOLDER_SELECT']    = "Folder : ";
$lang['TYPE_FORM_SET']              = "Set : ";
$lang['TYPE_FORM_LEVEL']            = "Level : ";
$lang['TYPE_FORM_FILTERS']          = "Filters";
$lang['TYPE_FORM_DIFFVIEW']         = "Diff view";
$lang['TYPE_FORM_NEWONLY']          = "New records only : ";


/*********
  ADMIN
 *********/

/*
  PAGE PRINCIPALE
*/

$lang['ADMIN_PRELUDE_INCOMING']  = "incoming (%d)";
$lang['ADMIN_PRELUDE_TRASH']     = "trash (%d)";
$lang['ADMIN_PRELUDE_OLDONES']   = "oldones (%d)";
$lang['ADMIN_PRELUDE_CONTEST']   = "contest (%d)";

/*
  MENU DE LA SIDEBAR
*/
$lang['ADMIN_MENU_TITLE']   	 = "Admin menu";
$lang['ADMIN_MENU_CHECK']   	 = "Check files integrity";
$lang['ADMIN_MENU_RECOMPUTE']  	 = "Recompute records status";
$lang['ADMIN_MENU_CONFIG']  	 = "Configuration";
$lang['ADMIN_MENU_MANAGEMENT'] 	 = "Content management";
$lang['ADMIN_MENU_MEMBERS']	 = "Members management";
$lang['ADMIN_MENU_SETS']  	 = "Sets / Levels";
$lang['ADMIN_MENU_FILE_EXPLORER']= "Files management";
$lang['ADMIN_MENU_PURGE_TRASH']  = "Empty trash";
$lang['ADMIN_MENU_TAGBOARD_MOD'] = "Tagboard moderation";
$lang['ADMIN_MENU_LEAVE']        = "Leave admin panel";

/*
  CONFIG
*/
$lang['ADMIN_CONFIG_FORM_TITLE'] = "Configuration";

/*
  MEMBERS
*/
$lang['ADMIN_MEMBERS_TITLE'] 	   = "Members management";
$lang['ADMIN_MEMBERS_FORM_UPDATE'] = "Update";
$lang['ADMIN_MEMBERS_FORM_DELETE'] = "Delete!";

$lang['ADMIN_MEMBERS_CONFIRM_DELETE'] = "Warning: it will delete ALL records of this user ... Are you sure ?";

/*
  MANAGEMENT
*/
$lang['ADMIN_MANAGEMENT_LANG_FORM_TITLE']      = "Select language to manage";
$lang['ADMIN_MANAGEMENT_LANG_FORM_LANG']       = "Lang : ";

$lang['ADMIN_MANAGEMENT_ANNOUNCE_FORM_TITLE']  = "Annoucement editor";
$lang['ADMIN_MANAGEMENT_SPEECH_FORM_TITLE']    = "Speech editor";
$lang['ADMIN_MANAGEMENT_CONDITIONS_FORM_TITLE']= "Conditions editor";

/*
  PURGETRASH
*/
$lang['ADMIN_PURGETRASH_SURE']     = "Confirm you want to empty trash ?";

/*
  EDITFORM
*/
$lang['ADMIN_EDIT_FORM_TITLE']     = "Edit a record";
$lang['ADMIN_EDIT_FORM_PSEUDO']    = "Pseudo : ";
$lang['ADMIN_EDIT_FORM_SET']       = "Set : ";
$lang['ADMIN_EDIT_FORM_LEVEL']     = "Level : ";
$lang['ADMIN_EDIT_FORM_TYPE']      = "Type : ";
$lang['ADMIN_EDIT_FORM_TIME']      = "Time (sesconds), >=9999 if goal is not reached : ";
$lang['ADMIN_EDIT_FORM_COINS']     = "Coins : ";
$lang['ADMIN_EDIT_FORM_REPLAY_FILE']= "Name of r eplay file : ";

/* 
  AUTRES
 */

/* Les mois en français pour la date */
global $lang_months;
$lang_months = array (
    1 => "January",
    2 => "February",
    3 => "March",
    4 => "April",
    5 => "May",
    6 => "June",
    7 => "July",
    8 => "August",
    9 => "September",
    10 => "October",
    11 => "November",
    12 => "Décember",
    );

global $lang_days;
$lang_days = array (
    "Mon" => "Monday",
    "Tue" => "Tuesday",
    "Wen" => "Wednesday",
    "Thu" => "Thursday",
    "Fri" => "Friday",
    "Sat" => "Saturday",
    "Sun" => "Sunday",
    );