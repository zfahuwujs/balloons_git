<?php 
/*
 * FCKeditor - The text editor for internet
 * Copyright (C) 2003-2004 Frederico Caldeira Knabben
 * 
 * Licensed under the terms of the GNU Lesser General Public License:
 * 		http://www.opensource.org/licenses/lgpl-license.php
 * 
 * For further information visit:
 * 		http://www.fckeditor.net/
 * 
 * File Name: config.php
 * 	Configuration file for the File Manager Connector for PHP.
 * 
 * Version:  2.0 FC (Preview)
 * Modified: 2005-02-08 12:01:53
 * 
 * File Authors:
 * 		Frederico Caldeira Knabben (fredck@fckeditor.net)
 */

global $Config ;

// Path to user files relative to the document root.
// SECURITY TIP: Uncomment the following line to set a fixed path.

// include main config
include("../../../../../../../../../includes/global.inc.php");

$Config['UserFilesPath'] = '/images/' ;

$Config['AllowedExtensions']['File']	= array() ;
$Config['DeniedExtensions']['File']		= array('php','asp','aspx','ascx','jsp','cfm','cfc','pl','bat','exe','dll','reg') ;

$Config['AllowedExtensions']['Image']	= array('jpg','gif','jpeg','png', 'JPG', 'JPEG') ;
$Config['DeniedExtensions']['Image']	= array() ;

$Config['AllowedExtensions']['uploads']	= array('jpg','gif','jpeg','png', 'JPG', 'JPEG') ;
$Config['DeniedExtensions']['uploads']	= array() ;

$Config['AllowedExtensions']['Flash']	= array('swf','fla') ;
$Config['DeniedExtensions']['Flash']	= array() ;

$Config['AllowedExtensions']['Media']	= array('swf','fla','jpg','gif','jpeg','png','avi','mpg','mpeg', 'JPG', 'JPEG') ;
$Config['DeniedExtensions']['Media']	= array() ;

?>