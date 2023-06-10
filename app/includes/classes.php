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
	
include_once ROOT_PATH."classes/class.exception.php";
include_once ROOT_PATH."classes/class.db.php";
include_once ROOT_PATH."classes/class.db.helper.php";
include_once ROOT_PATH."classes/class.nvrtbl.php";
include_once ROOT_PATH."classes/class.auth.php";
include_once ROOT_PATH."classes/class.template.php";
include_once ROOT_PATH."classes/class.replay.php";
include_once ROOT_PATH."classes/class.record.php";
include_once ROOT_PATH."classes/class.comment.php";
include_once ROOT_PATH."classes/class.user.php";
include_once ROOT_PATH."classes/class.smilies.php";
include_once ROOT_PATH."classes/class.style.php";
include_once ROOT_PATH."classes/class.set.php";
include_once ROOT_PATH."classes/class.map.php";
include_once ROOT_PATH."classes/class.tag.tagboard.php";
include_once ROOT_PATH."libs/lib.filemanager.php";
include_once ROOT_PATH."libs/lib.mail.php";
include_once ROOT_PATH."libs/lib.bbcode.php";
include_once ROOT_PATH."libs/lib.cache.php";
include_once ROOT_PATH."libs/lib.form.php";
include_once ROOT_PATH."libs/lib.photo.php";
