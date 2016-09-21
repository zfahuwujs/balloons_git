<?php
/*
+--------------------------------------------------------------------------
|   CubeCart v3.0.15
|   ========================================
|   by Alistair Brookbanks
|	CubeCart is a Trade Mark of Devellion Limited
|   Copyright Devellion Limited 2005 - 2006. All rights reserved.
|   Devellion Limited,
|   22 Thomas Heskin Court,
|   Station Road,
|   Bishops Stortford,
|   HERTFORDSHIRE.
|   CM23 3EE
|   UNITED KINGDOM
|   http://www.devellion.com
|	UK Private Limited Company No. 5323904
|   ========================================
|   Web: http://www.cubecart.com
|   Date: Thursday, 4th January 2007
|   Email: sales (at) cubecart (dot) com
|	License Type: CubeCart is NOT Open Source Software and Limitations Apply 
|   Licence Info: http://www.cubecart.com/site/faq/license.php
+--------------------------------------------------------------------------
|	functions.inc.php
|   ========================================
|	Core Frontend Functions	
+--------------------------------------------------------------------------
*/

// debug
function debug($obj, $doexit = false) {		
	if (evo()) {
		echo "<pre>";
		var_dump($obj);
		echo "</pre>";
		if ($doexit) {
			exit;	
		}
	}
}

//////////////////////////////////
// detect if store is under SSL
////////

function detectSSL(){

	if($_SERVER["HTTPS"] == "on"){
	
		return TRUE;
	
	} elseif ($_SERVER["HTTPS"] == 1){
	
		return TRUE;
	
	} elseif ($_SERVER['SERVER_PORT'] == 443) {
	
		return TRUE;
	
	} else {
	
		return FALSE;
	
	}

}

// Seans simple function to return a users data (by customer_id)
function getUserData($userID){

	global $glob,$db;

	$UserData=$db->select("SELECT * FROM " . $glob['dbprefix'] . "CubeCart_customer WHERE customer_id = ".intval($userID));
	return $UserData;
}


//////////////////////////////////
// make output valid html to meet w3c standards
////////
function validHTML($var){
	
	$var = htmlspecialchars($var);
	// fix a slight bug due to data storage in older versions
	$var = eregi_replace("&amp;#39;","&#39;",$var);
	return $var;

}

//////////////////////////////////
// treat GET vars stop XSS
////////
function treatGet($text){
	
	/* Previously used prior to 3.0.13 
	   "Good but your not right..." - Roy Castle (Catch Phrase)
	   
	$text = preg_replace("/(\<script)(.*?)(script>)/si", "", $text);
	$text = strip_tags($text);
	$text = str_replace(array("'","\"",">","<","\\"), "", $text);
	
	*/
	
	/* Strong but too restrictive
	
	$text = htmlspecialchars($text, ENT_QUOTES); 
	*/
	
	// safen most dangerous chars 
	$text = str_replace(array('&', '"', '<', '>'),array('&amp;', '&quot;',
'&lt;', '&gt;'),$text);
	
	return $text;
	
}

//////////////////////////////////
// Get current page
////////
function currentPage(){
	
	global $glob,$config;
	
	$storeURL = str_replace("http://","",$glob['storeURL']);
	$storeURL_SSL = str_replace("https://","",$config['storeURL_SSL']);
	
	if($storeURL!==$storeURL_SSL && $config['ssl']==1){
	
		// for shared SSL full URL
		$currentPage = $GLOBALS['storeURL'].str_replace($GLOBALS['rootRel'],"/",treatGet($_SERVER['PHP_SELF']));
	
	} else {
	
		// for dedicated SSL relative URL
		$currentPage = treatGet($_SERVER['PHP_SELF']);
	
	}
	
	// build session parameter for shared SSL to pass over
	if(!ereg(array("ccUser","/admin/"),$_SERVER['PHP_SELF']) && !empty($_SESSION['ccUser']) && $storeURL!==$storeURL_SSL && $config['ssl']==1){
		
		$params['ccUser'] = $_SESSION['ccUser'];
		
	}
	
	// if GET vars is an array and $params merge them together
	if(is_array($_GET))
	{
		foreach($_GET as $key => $value)
		{
			$treatedGet[$key] = treatGet($value);
		}
	}

	if(is_array($treatedGet) && is_array($params)){
		
		$params = array_merge($treatedGet,$params);
	
	// else if GET vars is an array call it params
	} elseif(is_array($treatedGet)){
		
		$params = $treatedGet;
	
	}
	
	// if there are to be GET vars strip redir and rebuild query string
	if (is_array($params)) {
		
		$newParams = array();
		
		$i = 0;
		
		foreach($params as $paramName => $paramVal) {
			
			$i++;
			
			if ($paramName!=="redir") {
			
				$currentPage .= ($i==1 ? "?":"&amp;");
		
				$currentPage .= $paramName."=".$paramVal;
			
			}
			
		}
		
	}

	return $currentPage;
	

}

//////////////////////////////////
// Fetch the config info
////////
function fetchDbConfig($confName) {
	
	global $glob, $db;
	
	$result = $db->select("SELECT array FROM ".$glob['dbprefix']."CubeCart_config WHERE name = ".$db->mySQLSafe($confName));
	
		if($result==TRUE){
		
			$base64Encoded = unserialize($result[0]['array']);
			
			foreach($base64Encoded as $key => $value){
				$base64Decoded[base64_decode($key)] = stripslashes(base64_decode($value));
			}
			
			return $base64Decoded;
			
		} else {
		
			return FALSE;
			
		}
	
}
//////////////////////////////////
// Format file size to be friendly
////////
function format_size($rawSize) {
	if ($rawSize / 1048576 > 1)
		return round($rawSize/1048576, 1) . ' MB';
	else if ($rawSize / 1024 > 1)
		return round($rawSize/1024, 1) . ' KB';
	else
		return round($rawSize, 1) . ' Bytes';
}
//////////////////////////////////
// Get Category Directory 
////////
/* <rf> search engine friendly url mod */
function getCatDir($catName, $cat_father_id, $catId, $link=FALSE, $skipFirstSymbol=FALSE, $reverseSort=TRUE){
/* <rf> end mod */
	
	global $sessId,$db,$config,$glob,$lang_folder;
	
	$config['dirSymbol'] = "&raquo;&nbsp;";
	// get category array for cat dir
	$query = "SELECT cat_id, cat_name, cat_father_id FROM ".$glob['dbprefix']."CubeCart_category ORDER BY cat_id DESC";
	$catArray = $db->select($query);
	
	// get category array in foreign innit
	$resultsForeign = $db->select("SELECT cat_master_id as cat_id, cat_name FROM ".$glob['dbprefix']."CubeCart_cats_lang WHERE cat_lang = '".$lang_folder."'");
	
	/* <rf> mod to make immediate category level a link also */
	if($link == TRUE){
		/* <rf> search engine friendly mods */	
		if($config['sef'] == 0) {		
			$dirArray[0] = $config['dirSymbol']."<a href='".$GLOBALS['rootRel']."index.php?ccUser=".$sessId."&amp;catId=".$catId."&amp;act=viewCat' class='txtLocation'>".$catName."</a>";
		} else {			
			$dirArray[0] = $config['dirSymbol']."<a href='".$GLOBALS['rootRel']."index.php?act=viewCat&amp;catId=".$catId."' class='txtLocation'>".$catName."</a>";			
		}
		/* <rf> end mod */
	} else {			
		$dirArray[0] = $config['dirSymbol'].$catName;	
	}
	/* <rf> end of mod to make immediate category level a link also */
	
	for ($i=0; $i<=count($catArray); $i++) {
		
		if(is_array($resultsForeign)){

			for ($k=0; $k<count($resultsForeign); $k++){

				if($resultsForeign[$k]['cat_id'] == $catArray[$i]['cat_id']){
				
					$catArray[$i]['cat_name'] = validHTML($resultsForeign[$k]['cat_name']);
				
				}
				
			}
		
		}
		
		if(isset($catArray[$i]['cat_id']) && $catArray[$i]['cat_id']==$cat_father_id) {
			
			if($link == TRUE){
/* <rf> search engine friendly mods */	
				if($config['sef'] == 0) {		
					$dirArray[$i+1] = $config['dirSymbol']."<a href='".$GLOBALS['rootRel']."index.php?ccUser=".$sessId."&amp;catId=".$catArray[$i]['cat_id']."&amp;act=viewCat' class='txtLocation'>".$catArray[$i]['cat_name']."</a>";
			} else {			
				$dirArray[$i+1] = $config['dirSymbol']."<a href='".$GLOBALS['rootRel']."index.php?act=viewCat&amp;catId=".$catArray[$i]['cat_id']."' class='txtLocation'>".$catArray[$i]['cat_name']."</a>";			
			}

/* <rf> end mod */

			} else {
			
			$dirArray[$i+1]= $config['dirSymbol'].$catArray[$i]['cat_name'];
			
			}
			
			$cat_father_id = $catArray[$i]['cat_father_id'];
		
		}
	
	}

	/* <rf> search engine friendly url mod */		
	if($reverseSort) {
		krsort($dirArray);
	} else {
		ksort($dirArray);
	}
	/* <rf> end mod */

	reset($dirArray);
	
	$dir = "";
	
	foreach($dirArray as $key => $value){
	 	$dir.= $value;
	}

    /* <rf> search engine friendly url mod */
	if($skipFirstSymbol) {
		$dir = substr($dir, strlen($config['dirSymbol']));
	}
    /* <rf> end mod */

	return $dir;
}
//////////////////////////////////
// Change table data class alternately 
////////
function cellColor($i, $tdEven="tdEven", $tdOdd="tdOdd"){
 	
	$cellColor = "";
	
	if(is_double($i/2)){
		$cellColor = $tdOdd;
	} else {
		$cellColor = $tdEven;
	} 
	
	return $cellColor;
}
//////////////////////////////////
// Sale Price
////////
function salePrice($normPrice, $salePrice){
	
	global $config;
	
	if($config['saleMode']==1){
		
			if($salePrice<$normPrice && $salePrice>0){
		
				return $salePrice;
			
			} else {
			
				return FALSE;
			
			} 
	
	} elseif($config['saleMode']==2) {
		
		$saleValue = $normPrice * ((100-$config['salePercentOff'])/100);
		
			if($saleValue<$normPrice && $saleValue>0){ 
				return $saleValue; 
			} else { 
				return FALSE; 
			}
		 
	} else {
		return FALSE;
	}
}
//////////////////////////////////
// Format Price
////////
function priceFormat($price, $dispNull = FALSE){
	
	if($price == TRUE){
	
		global $currencyVars;
		
		$price = $price * $currencyVars[0]['value'];
		
		return $currencyVars[0]['symbolLeft'].number_format($price,2,'.',',').$currencyVars[0]['symbolRight'];
	
	} elseif($dispNull == TRUE) {
		global $currencyVars;
		return $currencyVars[0]['symbolLeft'].number_format($price,2,'.',',').$currencyVars[0]['symbolRight'];
	
	} else {
	
		return FALSE;
		
	}

}

//////////////////////////////////
// Walk through files and folders in directory
////////
function walk_dir($path, $oneLevel=1) {

	$retval = array();

	if ($dir = opendir($path)) {
		
		while (false !== ($file = readdir($dir)))
		{
			
			if ($file[0]==".") { continue; }
			
				if (is_dir($path."/".$file) AND  $oneLevel==1) {
					 
					$retValMerge = walk_dir($path."/".$file);
	
					if(is_array($retValMerge)){
						$retval = array_merge($retval,$retValMerge);
					}
					
				} else if (is_file($path."/".$file)) {
					
					$retval[]=$path."/".$file;
				
				}
			
			}
		
		closedir($dir);
		
		}
		sort($retval);
	return $retval;
	
}
//////////////////////////////////
// List Modules
////////
function listModules($path){
	
	if ($handle = opendir($path)) {
	
	   while (false !== ($folder = readdir($handle))) {
			
			if(!eregi("[.]", $folder)){
		   		
				$folders[] = $folder;
			
			}
	   
	   }
	
	   closedir($handle);
	}
	
	return $folders;

}
//////////////////////////////////
// Check image extention
////////
function CheckImgExt($filename) {
	$img_exts = array("gif","jpg", "jpeg","png");
	foreach($img_exts as $this_ext) {
		if (preg_match("/\.$this_ext$/", $filename)) {
			return TRUE;
		}
	}
	return FALSE;
}
//////////////////////////////////
// Make time from time()
////////
function formatTime ($timestamp, $format=FALSE) {
	
	global $config;

	if($format == FALSE){
		$format = $config['timeFormat'];
	} 
	
	$sign = substr($config['timeOffset'],0,1);
	$value = substr($config['timeOffset'],1);
	
	if($sign=="+"){
		$seconds = $timestamp+$value;
	} elseif($sign=="-"){
		$seconds = $timestamp-$value;
	} else {
		$seconds = $timestamp;
	}
	return strftime($format,$seconds);
}
//////////////////////////////////
// Admin Permissions
////////
// define variables
function permission ($section,$permission) {
	global $ccAdminData;
	
	// check if index exists and if not create it
	if(!isset($ccAdminData[$section][$permission])){
		$ccAdminData[$section][$permission] = "";
	}
	
	if($ccAdminData[$section][$permission]==1 OR $ccAdminData[0]['isSuper']==1){
		$result = TRUE;
	} else {
		$result = FALSE;
	}
	return $result;
}
//////////////////////////////////
// Make Random Password
////////
function randomPass() {
		$chars = array("a","A","b","B","c","C","d","D","e","E","f","F","g","G","h","H","i","I","j","J", "k","K","l","L","m","M","n","N","o","O","p","P","q","Q","r","R","s","S","t","T", "u","U","v","V","w","W","x","X","y","Y","z","Z","1","2","3","4","5","6","7","8","9","0");
	
		$max_chars = count($chars) - 1;
		srand((double)microtime()*1000000);
	
		for($i = 0; $i < 8; $i++)	{
			$newPass = ($i == 0) ? $chars[rand(0, $max_chars)] : $newPass . $chars[rand(0, $max_chars)];
		}
		
		return $newPass;
}
//////////////////////////////////
// Recover Post Variables as hidden fields
////////
function recoverPostVars($array, $skipKey){
		
		$hiddenFields = "";
		
		foreach($array as  $key => $value) {
			
			
			// strip quotes if already in
			$value = str_replace(array("\'","'"),"&#39;",$value);
		
			// Stripslashes 
			if (get_magic_quotes_gpc()==1) { 
				$value = stripslashes($value); 
			} 
			
			if($key==$skipKey){
				
				$hiddenFields .= "<input type='hidden' name='".$key."' value='".$value."' />\r\n";
			
			} else {
			
				$hiddenFields .= "<input type='hidden' name='".$key."' value='".validHTML($value)."' />\r\n";
			
			}
			
		}
		
		return $hiddenFields;
}
//////////////////////////////////
// Seconds to human readable time
////////
function readableSeconds($time = 0) {
	
	$hours    = (int)floor($time/3600);
	$minutes  = (int)floor($time/60)%60;
	$seconds  = (int)$time%60;
	$output   = ""; 
	
	if($hours==1) $output  = $hours." hour";       
 
	else if($hours>1) $output  = $hours." hours";   

	if($output and $minutes>0 and $seconds>0) $output .= ", ";

	else if($output and $minutes>0 and $seconds==0) $output .= " and ";

	$s = ($minutes>1)  ? "s" : NULL;

	if($minutes>0) $output .= $minutes." minute$s"; 

	$s = ($seconds>1) ? "s" : NULL;

	if($output and $seconds>0) $output .= " and ";   

	if($seconds>0) $output .= $seconds." second".$s; 

	else if(!$output and $seconds==0) $output  = "0 seconds";

	return $output;
}

//////////////////////////////////
// make session id
////////
function makeSessId(){
	srand((double)microtime()*1000000);
	return md5(uniqid(rand()));
}
//////////////////////////////////
// make session id
////////
function c(){

	global $config;
		
		if(isset($config['lkv']) && $config['lkv']>0 && ereg("^([0-9]{6})+[-]+([0-9])+[-]+([0-9]{4})$",$config['lk'])){
			
			return FALSE;
			
		} else {
		
			return base64_decode("IChQb3dlcmVkIGJ5IEN1YmVDYXJ0KQ==");
		
		}
		
}
//////////////////////////////////
// get country by Id
////////
function countryName($id){
	
	global $db,$glob;
	
	$country = $db->select("SELECT id, printable_name FROM ".$glob['dbprefix']."CubeCart_iso_countries WHERE id = ".$db->mySQLSafe($id));
	
	if($country == TRUE) {
	
		return $country[0]['printable_name'];
		
	} else {
	
		return FALSE;
	
	}

}
//////////////////////////////////
// get country by ISO
////////
function countryNameByIso($id){
	
	global $db,$glob;
	
	$country = $db->select("SELECT id, printable_name FROM ".$glob['dbprefix']."CubeCart_iso_countries WHERE iso = ".$db->mySQLSafe($id));
	
	if($country == TRUE) {
	
		return $country[0]['printable_name'];
		
	} else {
	
		return FALSE;
	
	}

}
//////////////////////////////////
// get country ISO by Id
////////
function countryIso($id){
	
	global $db,$glob;
	
	$country = $db->select("SELECT iso FROM ".$glob['dbprefix']."CubeCart_iso_countries WHERE id = ".$db->mySQLSafe($id));
	
	if($country == TRUE) {
	
		return $country[0]['iso'];
		
	} else {
	
		return FALSE;
	
	}

}
//////////////////////////////////
// get tax by Id
////////
function taxRate($id){
	
	global $db,$glob;
	
	$tax = $db->select("SELECT percent FROM ".$glob['dbprefix']."CubeCart_taxes WHERE id = ".$db->mySQLSafe($id));
	
	if($tax == TRUE) {
	
		return $tax[0]['percent'];
		
	} else {
	
		return FALSE;
	
	}

}
//////////////////////////////////
// get order status by Id
////////
function orderStatus($id){
	
	global $lang;
	
	return $lang['orderState'][$id];
	
}
//////////////////////////////////
// validate email address
////////
function validateEmail($email){

	if(eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,5})$",trim($email))){
	
		return TRUE;
	
	} else {
	
		return FALSE;
	
	}

}
//////////////////////////////////
// get alternate language of product
////////
function prodAltLang($productId){
	
	global $db, $lang_folder, $glob, $config;
	
	if($lang_folder !== $config['defaultLang']){
	
		$foreignVal = $db->select("SELECT name, description FROM ".$glob['dbprefix']."CubeCart_inv_lang WHERE prod_master_id = ".$db->mySQLSafe($productId)." AND prod_lang=".$db->mySQLSafe($lang_folder));
		
		if($foreignVal==TRUE){
		
			return $foreignVal[0];
			
		} else {
		
			return FALSE;
		
		}
	
	} else {
	
		return FALSE;
		
	}

}

//////////////////////////////////
// fetch SpamCode 
////////
function fetchSpamCode($ESC,$del=FALSE)
{
	
	global $db, $glob;
	
	// check DB
	$result = $db->select("SELECT SpamCode, userIp FROM ".$glob['dbprefix']."CubeCart_SpamBot WHERE uniqueId = ".$db->mySQLSafe($ESC));
	
	if($result==TRUE)
	{
	
		if($del==TRUE)
		{
			// delete this SpamCode and any older ones lingering
			$db->delete($glob['dbprefix']."CubeCart_SpamBot", "uniqueId = ".$db->mySQLSafe($ESC)." OR `time` < ".time()-3600);
		
		}
	
		return $result[0];
	
	} 
	else 
	{
	
		return FALSE;
	
	}

}

//////////////////////////////////
// create SpamCode
////////
function createSpamCode($spamCode)
{

	global $db, $glob;
	
	$uniqueId = makeSessId();
	
	$data['uniqueId'] = $db->mySQLSafe($uniqueId);
	$data['time'] = $db->mySQLSafe(time());
	$data['spamCode'] = $db->mySQLSafe($spamCode);
	$data['userIp'] = $db->mySQLSafe($_SERVER['REMOTE_ADDR']);
	
	// insert into DB
	$insert = $db->insert($glob['dbprefix']."CubeCart_SpamBot", $data);
	
	return $uniqueId;

}

//////////////////////////////////
// get spambot image
////////
function imgSpambot($encodedSpamCode,$path='')
{	
	
	global $config;
	
	if($config['gdversion']>0)
	{
			
		$imgSpambot = "<img src=\"".$path."images/random/verifyGD.php?esc=".$encodedSpamCode."\" alt=\"\" title=\"\" />";
			
	} 
	else 
	{
		
		$imgSpambot = "<img src=\"".$path."images/random/verifySTD.php?esc=".$encodedSpamCode."&amp;n=1\" alt=\"\" title=\"\" />\r\n";
		$imgSpambot .= "<img src=\"".$path."images/random/verifySTD.php?esc=".$encodedSpamCode."&amp;n=2\" alt=\"\" title=\"\" />\r\n";
		$imgSpambot .= "<img src=\"".$path."images/random/verifySTD.php?esc=".$encodedSpamCode."&amp;n=3\" alt=\"\" title=\"\" />\r\n";
		$imgSpambot .= "<img src=\"".$path."images/random/verifySTD.php?esc=".$encodedSpamCode."&amp;n=4\" alt=\"\" title=\"\" />\r\n";
		$imgSpambot .= "<img src=\"".$path."images/random/verifySTD.php?esc=".$encodedSpamCode."&amp;n=5\" alt=\"\" title=\"\" />\r\n";
			
	}
	
	return $imgSpambot;
	
}

//Check for EU country	
function isEU($country){
	//last updated 21/06/2011 - http://europa.eu/about-eu/countries/index_en.htm
	switch ($country){
		//Austria
		case 14:
			return true;
			break;
		//Belgium
		case 21:
			return true;
			break;
		//Bulgaria
		case 33:
			return true;
			break;
		//Cyprus
		case 56:
			return true;
			break;
		//Czech Republic
		case 57:
			return true;
			break;
		//Denmark
		case 58:
			return true;
			break;
		//Estonia
		case 199:
			return true;
			break;
		//Finland, Suomi
		case 72:
			return true;
			break;
		//France
		case 73:
			return true;
			break;
		//Germany
		case 80:
			return true;
			break;
		//Greece
		case 83:
			return true;
			break;
		//Hungary
		case 97:
			return true;
			break;
		//Ireland
		case 103:
			return true;
			break;
		//Italy
		case 105:
			return true;
			break;
		//Latvia
		case 117:
			return true;
			break;
		//Luxemburg
		case 124:
			return true;
			break;
		//Malta
		case 132:
			return true;
			break;
		//Netherlands
		case 150:
			return true;
			break;
		//Poland
		case 171:
			return true;
			break;
		//Portugal
		case 172:
			return true;
			break;
		//Romania
		case 176:
			return true;
			break;
		//Slovakia
		case 193:
			return true;
			break;
		//Slovenia
		case 194:
			return true;
			break;
		//Spain
		case 199:
			return true;
			break;
		//Sweden
		case 205:
			return true;
			break;
		//United Kingdom
		case 225:
			return true;
			break;
		default:
			return false;
	}
		
}

function phpExtension(){
	if (preg_match("/.inc.php/i",$HTTP_SERVER_VARS['PHP_SELF']) || preg_match("/.inc.php/i",$_SERVER['PHP_SELF'])) {
        echo "<html>\r\n<head>\r\n<title>Forbidden 403</title>\r\n</head>\r\n<body><h3>Forbidden 403</h3>\r\nThe document you are requesting is forbidden.\r\n</body>\r\n</html>";
		exit;
	}
}

function evo(){
	global $db, $glob;
	
	$flag = 0;
	
	$ips = $db->select("
	SELECT ip 
	FROM ".$glob['dbprefix']."mou_ip");
	
	if($ips == TRUE){
		for($i = 0; $i < count($ips); $i++){
			if($_SERVER['REMOTE_ADDR'] == $ips[$i]["ip"]){
				$flag = 1;
				$i = count($ips);
			}
		}
	}
	
	if($flag == 1){
		return true;
	}else{
		return false;
	}
}

function evoHide($hideId){
	global $db, $glob;
	$flag = 0;
	$elements = $db->select("SELECT id, hide FROM ".$glob['dbprefix']."evo_hide WHERE id = ".$hideId);
	if($elements == TRUE && $elements[0]['hide'] == 1){
		echo'class="elementHide"';
	}
}
function evoHideAlt($hideId){
	global $db, $glob;
	$flag = 0;
	$elements = $db->select("SELECT id, hide FROM ".$glob['dbprefix']."evo_hide WHERE id = ".$hideId);
	if($elements == TRUE && $elements[0]['hide'] == 1){
		echo'elementHide';
	}
}
function evoHideBol($hideId){
	global $db, $glob;
	$flag = 0;
	$elements = $db->select("SELECT id, hide FROM ".$glob['dbprefix']."evo_hide WHERE id = ".$hideId);
	if($elements == TRUE && $elements[0]['hide'] == 1){
		return true;
	}else{
		return false;
	}
}
if(!function_exists('eregi_replace')){
	function eregi_replace($patern,$replacement,$string){
		return preg_replace('/'.preg_quote($patern).'/i',$replacement,$string);
	}
}
if(!function_exists('ereg')){
	function ereg($patern,$string){
		return preg_match('/'.preg_quote($patern).'/i',$string);
	}
}
if(!function_exists('eregi')){
	function ereg($patern,$string){
		return preg_match('/'.preg_quote($patern).'/i',$string);
	}
}

function allSubcats($catId,$catArray=array()){
	global $db, $glob;
	$catArray[] = $db->mySQLSafe($catId);
	$cats = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_category WHERE cat_father_id = '.$db->mySQLSafe($catId));
	if($cats==true){
		for($i = 0; $i < count($cats); $i++){
			$catArray = array_merge($catArray,allSubcats($cats[$i]['cat_id']));
		}
	}
	return $catArray;
}
function curPageURL() {
 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $pageURL;
}
function mou(){
	global $db;
	
	$flag = 0;
	
	$ips = $db->select("
	SELECT ip 
	FROM ".$glob['dbprefix']."mou_ip");
	
	if($ips == TRUE){
		for($i = 0; $i < count($ips); $i++){
			if($_SERVER['REMOTE_ADDR'] == $ips[$i]["ip"]){
				$flag = 1;
				$i = count($ips);
			}
		}
	}
	
	if($flag == 1){
		return true;
	}else{
		return false;
	}
}
function getProductPriceWithTax($prodId, $priceType){
	global $db;
	$product = $db->select("SELECT price, taxType, sale_price, tradePrice, tradeSale FROM ".$glob['dbprefix']."CubeCart_inventory WHERE productId = ".$db->mySQLSafe($prodId));
	
	if($product){
		$tax = $db->select("SELECT percent FROM ".$glob['dbprefix']."CubeCart_taxes WHERE id = ".$db->mySQLSafe($product[0]['taxType']));
		if($priceType == 'price'){
			$price = $product[0]['price'] + ($product[0]['price'] * ($tax[0]['percent']/100));
		}
		if($priceType == 'sale_price'){
			$price = $product[0]['sale_price'] + ($product[0]['sale_price'] * ($tax[0]['percent']/100));
		}
		if($priceType == 'tradePrice'){
			$price = $product[0]['tradePrice'] + ($product[0]['tradePrice'] * ($tax[0]['percent']/100));
		}
		if($priceType == 'tradeSale'){
			$price = $product[0]['tradeSale'] + ($product[0]['tradeSale'] * ($tax[0]['percent']/100));
		}
		if(is_numeric($priceType)){
			$price = $priceType + ($priceType * ($tax[0]['percent']/100));
		}
	}
	return $price;
}
function topCat(){
	return false;
}
?>