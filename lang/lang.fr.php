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
if (!defined('NVRTBL'))
	exit;
	
global $lang;

$lang['code']			= "fr";

/*
  STRUCTURES INTERNES
 */

//types
$lang['all']			= "tous";
$lang['best time']		= "meilleurs temps";
$lang['most coins']		= "meilleurs pièces";
$lang['freestyle']		= "freestyle";
$lang['fast unlock']	= "fast unlock";

//folders
$lang['contest']		= "contest";
$lang['incoming']		= "en attente";
$lang['trash']			= "poubelle";
$lang['oldones']		= "obsolètes";

//newonly
$lang['off']			= "désactivé";
$lang['3 days']			= "3 jours";
$lang['1 week']			= "1 semaine";
$lang['2 weeks']		= "2 semaines";
$lang['1 month']		= "1 mois";

//userlevel
$lang['root']			= "super admin";
$lang['admin']			= "admin";
$lang['moderator']		= "modérateur";
$lang['member']			= "membre";
$lang['guest']			= "invité";


/*
  GUI
 */
$lang['GUI_YES']        	= "OUI";
$lang['GUI_NO']      		= "NON";

$lang['GUI_BUTTON_BACK']        = "Précédent";
$lang['GUI_BUTTON_MAINPAGE']    = "Retourner à la page principale";
$lang['GUI_BUTTON_RETURN']      = "Retourner à la page %s";
$lang['GUI_BUTTON_APPLY']       = "Appliquer";
$lang['GUI_BUTTON_REDIRECT']    = "Redirection...";
$lang['GUI_BUTTON_CONTINUE']    = "Continuer";

$lang['GUI_INVALID_USER']       = "Utilisateur invalide";

$lang['GUI_UPDATE_OK']      =  "Modifications prises en compte";
$lang['GUI_UPLOAD_OK']      =  "Upload effectué.";

$lang['NOT_ROOT']               = "Vous devez être super-administrateur pour voir cette page";
$lang['NOT_ADMIN']              = "Vous devez être administrateur pour voir cette page";
$lang['NOT_MODERATOR']          = "Vous devez être modérateur pour voir cette page";
$lang['NOT_MEMBER']             = "Vous devez être inscrit pour voir cette page";
$lang['LOGIN_TO_POST']          = "Connectez-vous pour poster des commentaires.";

$lang['CHECK_ERR_TOOLONG']  =  "Champ trop long: %s, limite: %d";
$lang['CHECK_ERR_LIMITS']   =  "Champ hors limite: %s, limites : [ %d - %d ]";

/*
  REGISTER
*/
$lang['REGISTER_EMPTY_FIELDS']      = "Champs incomplets.";
$lang['REGISTER_PSEUDO_EXISTS']     = "Pseudo déjà pris.";
$lang['REGISTER_MAIL_EXISTS']       = "Adress e-mail déjà utilisée.";
$lang['REGISTER_MAIL_NOTVALID']     = "Adress e-mail invalide.";
$lang['REGISTER_PASSWD_CHECK']      = "Les 2 mots de passe saisis ne sont pas identiques.";
$lang['REGISTER_PASSWD_LENGTH']     = "Le mot de passe doit faire plus de 5 caractères.";
$lang['REGISTER_SPECIAL_CHARS']     = "N'utilisez pas de caractère spéciaux dans les champs.";
$lang['REGISTER_FIRST_REGISTER']    = "Vous êtes le premier inscrit, donc vous êtes root";
$lang['REGISTER_WELCOME_SUBJECT']   = "Bienvenue sur la Nevertable !";
$lang['REGISTER_WELCOME_MESSAGE1']  = "Votre inscritpion est terminée. \n Voici vos informations d'identification: \n !";
$lang['REGISTER_WELCOME_MESSAGE1']  = "Merci !";
$lang['REGISTER_SUCCESSFUL']        = "Votre inscirption est terminée. Vous pouvez vous logguer !";

$lang['REGISTER_FORM_TITLE']        = "Formulaire d'inscription";
$lang['REGISTER_FORM_PSEUDO']       = "Pseudo : ";
$lang['REGISTER_FORM_EMAIL']        = "Email : ";
$lang['REGISTER_FORM_PASSWD1'] 	    = "Mot de passe : ";
$lang['REGISTER_FORM_PASSWD2'] 	    = "Mot de passe, vérification : ";

/*
  FORGOT
*/

$lang['FORGOT_FORM_TITLE']          = "J'ai oublié mon mot de passe...";
$lang['FORGOT_EMPTY_MAIL']          = "Vous devez saisir votre adress mail.";
$lang['FORGOT_INVALID_MAIL']        = "email invalide, non trouvé dans la base.";
$lang['FORGOT_EMAIL_SENT']          = "Nouveau mot de passe généré, email envoyé.";

/*
  LOGIN
*/
$lang['LOGIN_NOTLOGIN'] 	    = "Vous n'êtes pas connecté(e)";
$lang['LOGIN_ALREADYLOGIN'] 	    = "Vous êtes déjà connecté(e)";
$lang['LOGIN_LOGIN'] 	            = "Vous êtes conneté(e)<br/>Redirection vers la page principale...";
$lang['LOGIN_LOGOUT'] 	            = "Vous êtes déconneté(e)<br/>Redirection vers la page principale...";
$lang['LOGIN_AUTH_FAILED']			= "Authentification erron�e. Essayez encore ... (ou pas)";

$lang['LOGIN_FORM_TITLE']           = "Connexion";
$lang['LOGIN_FORM_PSEUDO']          = "login";
$lang['LOGIN_FORM_PASSWD']          = "pass";

/*
  TABLE
*/
$lang['RESULTS_PRELUDE']	    = "%d records";
$lang['TABLE_HEADER_OLD']	    = "âge";
$lang['TABLE_HEADER_PLAYER']	    = "joueur";
$lang['TABLE_HEADER_SET']	    = "set";
$lang['TABLE_HEADER_LEVEL']	    = "niv";
$lang['TABLE_HEADER_TIME']	    = "temps";
$lang['TABLE_HEADER_COINS']	    = "pièces";

$lang['TABLE_RESULTS_LIST']	    = "Télécharger la liste des records affichés : ";
$lang['TABLE_ATTACH']		    = "Montrer tous les records de ce niveau";

$lang['TABLE_NO_COMMENT']	    = "Postez !";
$lang['TABLE_ONE_COMMENT']	    = "1 commentaire";
$lang['TABLE_COMMENTS']	            = "%d commentaires";

/*
  MEMBER
*/

$lang['MEMBER_HEADER_NAME']	    = "Nom";
$lang['MEMBER_HEADER_RECORDS']	    = "Records";
$lang['MEMBER_HEADER_BEST_RECORDS'] = "Meilleurs";
$lang['MEMBER_HEADER_RANK'] 	    = "Classement";
$lang['MEMBER_COMMENTS'] 	    = "Commentaires";
$lang['MEMBER_MAIL']   	            = "e-mail";
$lang['MEMBER_CATEGORY'] 	    = "Droits";

$lang['MEMBER_NO_AVATAR']	    = "Pas d'avatar";

/*
  FORMULAIRE D'EDITION DES OPTIONS
*/
$lang['OPTIONS_FORM_TITLE']         = "Mes options";

$lang['OPTIONS_FORM_SORT']          = "Tri par défaut : ";
$lang['OPTIONS_FORM_THEME']         = "Style du site : ";
$lang['OPTIONS_FORM_LANG']          = "Langue : ";
$lang['OPTIONS_FORM_LIMIT']         = "Nombre de records par page : ";
$lang['OPTIONS_FORM_COMMENTS_LIMIT'] = "Nombre de commentaires par page : ";
$lang['OPTIONS_FORM_SIDEBAR_COMMENTS'] = "Nombre de derniers commentaires affichés dans la barre latérale : ";
$lang['OPTIONS_FORM_SIDEBAR_COMLENGTH']  = "Longueur du commentaire dans la barre latérale : ";

/*
  FORMULAIRE DU TAGBOARD
*/
$lang['TAG_FORM_PSEUDO']            = "Pseudo";
$lang['TAG_FORM_CONTENT']           = "Votre tag";
$lang['TAG_FORM_SUBMIT']            = "Tag!";
$lang['TAG_EMPTY_CONTENT']          = "Vous n'avez rien saisi.";
$lang['TAG_EMPTY_PSEUDO']           = "Pas de peusdo.";
$lang['TAG_NO_TAG']                 = "Pas de tag sélectionné.";
$lang['TAG_TOO_LONG']               = "Votre tag est trop long.";

/*
  MENU DE LA SIDEBAR
*/
$lang['SIDEBAR_WELCOME']	    = "Bienvenue";

$lang['SIDEBAR_LOGIN']		    = "Connexion";
$lang['SIDEBAR_LAST_COMMENTS']	    = "Derniers commentaires";
$lang['SIDEBAR_LEGEND_BEST']	    = "Meilleur record pour ce niveau.";
$lang['SIDEBAR_LEGEND_BEST_TIME']   = "Meilleur temps.";
$lang['SIDEBAR_LEGEND_MOST_COINS']  = "Meilleur pièces.";
$lang['SIDEBAR_LEGEND_FREESTYLE']   = "Freestyle.";
$lang['SIDEBAR_LEGEND_FAST_UNLOCK']   = "Fast Unlock.";

$lang['MENU_ROOT']		    = "Menu Root";
$lang['MENU_ADMIN']		    = "Menu d'administration";
$lang['MENU_ADMIN_INCOMING']	    = "En attente (%d)";
$lang['MENU_ADMIN_MANAGEMENT']	    = "Gestion contenu";
$lang['MENU_ADMIN_MEMBERS']	    = "Gestion des membres";
$lang['MENU_ADMIN_FILE_EXPLORER']   = "Gestion des fichiers";

$lang['MENU_MODERATOR']		    = "Menu modérateur";
$lang['MENU_MODERATOR_TAGBOARD']    = "Tagboard";

$lang['MENU_MEMBER']                = "Menu utilisateur";

$lang['MENU_MEMBER_UPLOAD']         = "Soumettre un record";
$lang['MENU_MEMBER_MEMBERS']        = "Liste des participants";
$lang['MENU_MEMBER_STATS']          = "Statistiques";
$lang['MENU_MEMBER_PROFILE']        = "Editer mon profil";
$lang['MENU_MEMBER_OPTIONS']        = "Editer mes options";
$lang['MENU_MEMBER_LOGOUT']         = "Déconnexion";

$lang['MENU_REGISTER']              = "Inscription";
$lang['MENU_FORGOT_PASSWD']         = "Mot de passe oublié?";

/*
  AFFICHAGE DES COMMENTAIRES
*/
$lang['COMMENTS_NOCOMMENT']         = "Aucun post pour l'instant";
$lang['COMMENTS_FORM_PREVIEW']      = "Prévisualiser";
$lang['COMMENTS_PREVIEW_WARNING']   = "Attention, ceci n'est qu'une prévisualisation, le commentaire n'a pas encore été posté ^-^";
$lang['COMMENTS_PREVIEW_TITLE']     = "Prévisualisation";

$lang['COMMENTS_ERR_EMPTY_CONTENT'] = "Vous n'avez rien écrit.";

$lang['COMMENTS_FORM_PSEUDO']       = "Pseudo : ";

$lang['COMMENTS_POPUP_EDIT']        = "Editer ce commentaire";
$lang['COMMENTS_POPUP_DELETE']      = "Effacer ce commentaire";

/*
  AFFICHAGE DU PROFIL
 */

$lang['PROFILE_NOTTLOGIN']	    = "Vous devez vous connecter pour éditer votre profil.";
$lang['PROFILE_PASSKO']	            = "Vos mots de passe ne correspondent pas.";

$lang['PROFILE_FORM_IDENT_TITLE']    = "Modifier votre identification";
$lang['PROFILE_FORM_IDENT_PSEUDO']   = "Pseudo : ";
$lang['PROFILE_FORM_IDENT_MAIL']     = "Nouveau mail : ";
$lang['PROFILE_FORM_IDENT_PASSWD1']  = "Nouveau mot de passe : ";
$lang['PROFILE_FORM_IDENT_PASSWD2']  = "Nouveau mot de passe, vérification : ";

$lang['PROFILE_FORM_INFO_TITLE']    = "Informations personnelles";
$lang['PROFILE_FORM_INFO_INFO']     = "Aucun des champs ci dessous n'est obligatoire";
$lang['PROFILE_FORM_INFO_LOCAL']    = "Localisation : ";
$lang['PROFILE_FORM_INFO_WEB']      = "Site perso : ";
$lang['PROFILE_FORM_INFO_SPEECH']   = "Citation perso : ";

$lang['PROFILE_FORM_AVATAR_TITLE']  = "Uploader un avatar";
$lang['PROFILE_FORM_AVATAR_FILE']   = "Fichier : ";
$lang['PROFILE_FORM_AVATAR_DEL']    = "Effacer l'avatar";
$lang['PROFILE_FORM_AVATAR_LIMITS'] = "Taille max : %dx%d (%dko)";

$lang['PROFILE_AVATAR_RESIZE_OK']   = "Image trop grande, redimensionnée avec succès. ";


$lang['PROFILE_TITLE']              = "Profil de %s";
$lang['PROFILE_LOCALISATION']       = "Localisation ";
$lang['PROFILE_WEB']                = "Site perso ";
$lang['PROFILE_SPEECH']             = "Citation perso ";
$lang['PROFILE_LEVEL']              = "Permissions ";
$lang['PROFILE_TOTAL_RECORDS']      = "Total de records ";
$lang['PROFILE_BEST_RECORDS']       = "Meilleurs records ";
$lang['PROFILE_BEST_TIME']          = "Meilleurs temps ";
$lang['PROFILE_BEST_COINS']         = "Meilleurs pièces ";
$lang['PROFILE_FREESTYLE']          = "Records Freestyle ";
$lang['PROFILE_COMMENTS']           = "Commentaires ";
$lang['PROFILE_VIEWALL']            = "Voir tous les records de %s ";
$lang['PROFILE_VIEWALL_CONTEST']    = "Voir tous les records de %s (championnat seulement)";

/*
  UPLOAD
*/
$lang['UPLOAD_REGISTERED']           = "Votre record est enregistré. Il apparaitra dans le contest quand un admin l'aura valider :)";
$lang['UPLOAD_FORM_TITLE']           = "Soumettre un record";
$lang['UPLOAD_FORM_SIZEMAX']         = "Taille maximum du fichier replay: %dkB";
$lang['UPLOAD_FORM_PSEUDO']          = "Votre pseudo : ";
$lang['UPLOAD_FORM_REPLAYFILE']      = "Fichier replay : ";
$lang['UPLOAD_FORM_TYPE']            = "Type : ";

/*
  PAGE PRINCIPALE
*/

$lang['STATS_NUMBERS_LABEL']        = "Internautes sur le site : ";
$lang['STATS_NUMBERS_TEXT']         = "%d inscrit(s), %d invité(s).";
$lang['STATS_LIST']                 = "Liste des inscrits connectés : ";

$lang['FOOTER_PERFS']               = "Page générée en %ss, avec %d requêtes.";

$lang['TYPE_FORM_TITLE']            = "Options d'affichage";
$lang['TYPE_FORM_TABLE_SELECT']     = "Choix de la table : ";
$lang['TYPE_FORM_FOLDER_SELECT']    = "Répertoire : ";
$lang['TYPE_FORM_SET']              = "Set : ";
$lang['TYPE_FORM_LEVEL']            = "Niveau : ";
$lang['TYPE_FORM_FILTERS']          = "Filtres";
$lang['TYPE_FORM_DIFFVIEW']         = "Vue différentielle";
$lang['TYPE_FORM_NEWONLY']          = "Nouveaux records seulement : ";


/*********
  ADMIN
 *********/

/*
  MENU DE LA SIDEBAR
*/
$lang['ADMIN_MENU_TITLE']   	 = "Menu d'administration";
$lang['ADMIN_MENU_CHECK']   	 = "Vérification d'intégrité des fichiers";
$lang['ADMIN_MENU_RECOMPUTE']  	 = "Recalcul le statut de tous les records";
$lang['ADMIN_MENU_CONFIG']  	 = "Configuration";
$lang['ADMIN_MENU_MANAGEMENT'] 	 = "Gestion du contenu statique";
$lang['ADMIN_MENU_MEMBERS']	 = "Gestion des membres";
$lang['ADMIN_MENU_SETS']  	 = "Sets / Niveaux";
$lang['ADMIN_MENU_FILE_EXPLORER']= "Gestion des fichiers";
$lang['ADMIN_MENU_PURGE_TRASH']  = "Vider la poubelle";
$lang['ADMIN_MENU_TAGBOARD_MOD'] = "Modération du tagboard";

/*
  CONFIG
*/
$lang['ADMIN_CONFIG_FORM_TITLE'] = "Configuration";

/*
  MEMBERS
*/
$lang['ADMIN_MEMBERS_TITLE'] 	   = "Gestion des membres";
$lang['ADMIN_MEMBERS_FORM_UPDATE'] = "Modifier";
$lang['ADMIN_MEMBERS_FORM_DELETE'] = "Supprimer!";

$lang['ADMIN_MEMBERS_CONFIRM_DELETE'] = "Cela va effacer TOUS les records de cet utilisateur... Confirmer ?";

/*
  MANAGEMENT
*/
$lang['ADMIN_MANAGEMENT_LANG_FORM_TITLE']      = "Choix de la langue à gérer";
$lang['ADMIN_MANAGEMENT_LANG_FORM_LANG']       = "Code langue : ";

$lang['ADMIN_MANAGEMENT_ANNOUNCE_FORM_TITLE']  = "Editer l'annonce de la page principale";
$lang['ADMIN_MANAGEMENT_SPEECH_FORM_TITLE']    = "Editer le texte de présentation";
$lang['ADMIN_MANAGEMENT_CONDITIONS_FORM_TITLE']= "Editer les conditions";


/* 
  AUTRES
 */

/* Les mois en français pour la date */
global $lang_months;
$lang_months = array (
    1 => "Janvier",
    2 => "Février",
    3 => "Mars",
    4 => "Avril",
    5 => "Mai",
    6 => "Juin",
    7 => "Juillet",
    8 => "Aout",
    9 => "Septembre",
    10 => "Octobre",
    11 => "Novembre",
    12 => "Décembre",
    );

global $lang_days;
$lang_days = array (
    "Mon" => "Lundi",
    "Tue" => "Mardi",
    "Wen" => "Mercredi",
    "Thu" => "Jeudi",
    "Fri" => "Vendredi",
    "Sat" => "Samedi",
    "Sun" => "Dimanche",
    );
