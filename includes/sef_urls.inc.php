<?php

/* <rf> search engine friendly urls mod */

function generateQueryStr($query) {
	// check if there is an ampersand first otherwise just return the same string
	if(preg_match('/^(\&amp\;|\&)(.*)/', $query, $matches)) {
		if(strlen($matches[2]) > 0) {
			return "?" . $matches[2];
		}
	}

	return $query;
}

function generateSafeUrls($url) {

	// normalize accented characters
	$url = strtr($url, "\xA1\xAA\xBA\xBF\xC0\xC1\xC2\xC3\xC5\xC7\xC8\xC9\xCA\xCB\xCC\xCD\xCE\xCF\xD0\xD1\xD2\xD3\xD4\xD5\xD8\xD9\xDA\xDB\xDD\xE0\xE1\xE2\xE3\xE5\xE7\xE8\xE9\xEA\xEB\xEC\xED\xEE\xEF\xF0\xF1\xF2\xF3\xF4\xF5\xF8\xF9\xFA\xFB\xFD\xFF", "_ao_AAAAACEEEEIIIIDNOOOOOUUUYaaaaaceeeeiiiidnooooouuuyy"); 

	// further character processing
	$url = strtr($url, array("\xC4"=>"Ae", "\xC6"=>"AE", "\xD6"=>"Oe", "\xDC"=>"Ue", "\xDE"=>"TH", "\xDF"=>"ss", "\xE4"=>"ae", "\xE6"=>"ae", "\xF6"=>"oe", "\xFC"=>"ue", "\xFE"=>"th"));

	// make sure its only english and dashes
	$search = array(
				"/[^a-zA-Z0-9\/]/",
				"/--+/",
			   );

	$replace = array(
				"-",
				"-",
			    );
	$url = preg_replace($search, $replace, $url);

	// return safe url
	return($url);
}


function generateCategoryUrl($catid) {

	global $glob, $db, $config, $lang_folder;

	if($config['sefserverconfig'] == 0 || $config['sefserverconfig'] == 3)
		$sefpre = 'cat_';
	else
		$sefpre = 'c_';

	if($config['sefserverconfig'] == 3)
		$ext = '.php';
	else
		$ext = '.html';

	if(is_numeric($catid)) {

		if($config['sefcustomurl'] == 1) $sefcustomurls = "cat_sefurl,";
		$query = "SELECT $sefcustomurls cat_name, cat_id, cat_father_id FROM ".$glob['dbprefix']."CubeCart_category WHERE cat_id='".$catid."'";
		$sef_categories = $db->select($query);
		
		$prevDirSymbol = $config['dirSymbol'];
		$config['dirSymbol'] = '/';
		$prevLang = $lang_folder;
		$lang_folder = $config['defaultLang'];
		$cat = getCatDir($sef_categories[0]['cat_name'], $sef_categories[0]['cat_father_id'], $sef_categories[0]['cat_id'], FALSE, TRUE);
		$config['dirSymbol'] = $prevDirSymbol;
		$lang_folder = $prevLang;
		
		// using custom urls?
		if($config['sefcustomurl'] == 1) {
			if(strlen($sef_categories[0]['cat_sefurl']) > 0) {
				$cat = $sef_categories[0]['cat_sefurl'];
			}
		} 
		
		$cat = generateSafeUrls($cat);				
		$cat = strtolower($cat) . "/". $sefpre . $catid . $ext;
	} else {
		$cat = "";
		$cat = generateSafeUrls($cat);
		$cat = strtolower($cat) . $sefpre . $catid . $ext;
	}

	return $cat;
}

function generateProductUrl($productid) {

	global $glob, $db, $config, $lang_folder;	

	if($config['sefcustomurl'] == 1) $sefcustomurls = "prod_sefurl,";
	$query = "SELECT $sefcustomurls productId, name, cat_name, ".$glob['dbprefix']."CubeCart_inventory.cat_id, cat_father_id FROM ".$glob['dbprefix']."CubeCart_inventory INNER JOIN ".$glob['dbprefix']."CubeCart_category ON ".$glob['dbprefix']."CubeCart_inventory.cat_id = ".$glob['dbprefix']."CubeCart_category.cat_id WHERE productId='".$productid."'";	
	$sef_products = $db->select($query);

	if($config['sefserverconfig'] == 0 || $config['sefserverconfig'] == 3)
		$sefpre = 'prod_';
	else
		$sefpre = 'p_';	

	if($config['sefserverconfig'] == 3)
		$ext = '.php';
	else
		$ext = '.html';

	$prevDirSymbol = $config['dirSymbol'];
	$config['dirSymbol'] = '/';
	$prevLang = $lang_folder;
	$lang_folder = $config['defaultLang'];
	$prod = getCatDir($sef_products[0]['cat_name'], $sef_products[0]['cat_father_id'], $sef_products[0]['cat_id'], FALSE, TRUE) . "/" . $sef_products[0]['name'];
	$config['dirSymbol'] = $prevDirSymbol;
	$lang_folder = $prevLang;
	
	// using custom urls?
	if($config['sefcustomurl'] == 1) {
		if(strlen($sef_products[0]['prod_sefurl']) > 0) {
			$prod = $sef_products[0]['prod_sefurl'];
		}
	} 	

	$prod = generateSafeUrls($prod);
	$prod = $prod . "/" . $sefpre . $productid . $ext;

	return strtolower($prod);
}

function generateDocumentUrl($docid) {

	global $glob, $db, $config;		

	if($config['sefcustomurl'] == 1) $sefcustomurls = "doc_sefurl,";
	$query = "SELECT $sefcustomurls doc_name FROM ".$glob['dbprefix']."CubeCart_docs WHERE doc_id='".$docid."'"; 
	$sef_documents = $db->select($query);

	if($config['sefserverconfig'] == 0 || $config['sefserverconfig'] == 3)
		$sefpre = 'info_';
	else
		$sefpre = 'i_';	

	if($config['sefserverconfig'] == 3)
		$ext = '.php';
	else
		$ext = '.html';

	$doc = $sef_documents[0]['doc_name'];
	
	// using custom urls?
	if($config['sefcustomurl'] == 1) {
		if(strlen($sef_documents[0]['doc_sefurl']) > 0) {
			$doc = $sef_documents[0]['doc_sefurl'];
		}
	} 	
	
	if(strlen($doc) > 0) {
		$doc = generateSafeUrls($doc);
		$doc = $doc . "/";
	}

	$doc = $doc . $sefpre . $docid . $ext;
	
	return strtolower($doc);
}

function generateTellFriendUrl($docid) {

	global $glob, $db, $config;		

	if($config['sefserverconfig'] == 0 || $config['sefserverconfig'] == 3)
		$sefpre = 'tell_';
	else
		$sefpre = 't_';	

	if($config['sefserverconfig'] == 3)
		$ext = '.php';
	else
		$ext = '.html';

	$doc = "tellafriend";
	$doc = generateSafeUrls($doc);
	$doc = $doc . "/" . $sefpre . $docid . $ext;
	
	return strtolower($doc);
}

function sef_script_name() {

	return 'shop';
}

function sef_get_base_url() {

	global $config;

	if($config['sefserverconfig'] == 1)
		return sef_script_name().'/';
	else if($config['sefserverconfig'] == 2)
		return sef_script_name() . '.php/';
	else if($config['sefserverconfig'] == 0 || $config['sefserverconfig'] == 3)
		return '';
}


function sef_rewrite_urls($page) {

  global $config;

  //START ENCRYPT
  if($config['sef'] || $config['seftags']) {
    $addCopyright = 1;
    $copyright[0] = '<div class=\'txtCopyright\'>Running with <a href=\'http://www.intimatewear.com.au/cc3_mods/\' class=\'txtCopyright\' target=\'_blank\'>Search Engine Friendly Mod</a> 6.0</div>';
    $copyright[1] = '<div class=\'txtCopyright\'>Proudly Sponsoring <a href=\'http://www.buysearcher.com/\' class=\'txtCopyright\' target=\'_blank\'>Buy Searcher The Online Shopping Directory</a></div>';
    /* security check, check if domain names match, if they do remove copyright notice */
    $domains = array(
      'localhost',
      '127.0.0.1',
      'intimatEwear.com.au',
    );
    if(count($domains) >= 3) {
      for($i = 0; $i < count($domains); $i++) {
        if(strlen($domains[$i]) > 0) {
          if((strlen($GLOBALS['storeURL']) == 0) || (strpos(strtoupper($GLOBALS['storeURL']), strtoupper($domains[$i])) != FALSE)) {
            $addCopyright = 0;
            break;
          }
        }
      }
    }
    /* domain not found as valid so add copyright */
    if($addCopyright == 1 && 2>3) {
      // select which copyright to add
      $index = strlen($_SERVER['QUERY_STRING']) % count($copyright);
      $page = preg_replace('/(\<\/body\>)/i', $copyright[$index].'$1', $page);
    }
  }
  if($config['sef']) {
    $indexscript = sef_get_base_url();
    $search = array(
      '/(?<=href)(\s*\=\s*)(\"|\')([^\"|\']*)index\.php\?act\=viewCat(\&amp\;|\&)catId\=([a-z0-9]+)([^\"|\']*)(\"|\')/ie',/* rewrite url */
      '/(?<=href)(\s*\=\s*)(\"|\')([^\"|\']*)index\.php\?act\=viewProd(\&amp\;|\&)productId\=([a-z0-9]+)([^\"|\']*)(\"|\')/ie',
      '/(?<=href)(\s*\=\s*)(\"|\')([^\"|\']*)index\.php\?act\=viewDoc(\&amp\;|\&)docId\=([a-z0-9]+)([^\"|\']*)(\"|\')/ie',
      '/(?<=href)(\s*\=\s*)(\"|\')([^\"|\']*)index\.php\?act\=taf(\&amp\;|\&)productId\=([a-z0-9]+)([^\"|\']*)(\"|\')/ie',
      '/(\"|\')(cart\.php|confirmed\.php|download\.php|index\.php|offLine\.php|switch\.php)/i', /* lets just make certain that these scripts are all referenced from absolute url */
      '/(href\s*\=\s*(\"|\'))(?!javascript|mailto:|https:|http:|\/|\"|\'|\#|\?)/i',  /* convert relative to abs */
      '/(src\s*\=\s*(\"|\'))(?!javascript|mailto:|https:|http:|\/|\"|\'|\#|\?)/i',
      '/(action\s*\=\s*(\"|\'))(?!javascript|mailto:|https:|http:|\/|\"|\'|\#|\?)/i',
      '/(background\s*\=\s*(\"|\'))(?!javascript|mailto:|https:|http:|\/|\"|\'|\#|\?)/i',
      '/(javascript\s*:\s*openPopUp\s*\(\s*(\"|\'))(?!https:|mailto:|http:|\/|\"|\'|\#|\?)/i',
    );
    $replace = array(
      "'\\1'.substr('\\2',-1).'\\3'.'$indexscript'.generateCategoryUrl('\\5').generateQueryStr('\\6').substr('\\7',-1)",
      "'\\1'.substr('\\2',-1).'\\3'.'$indexscript'.generateProductUrl('\\5').generateQueryStr('\\6').substr('\\7',-1)",
      "'\\1'.substr('\\2',-1).'\\3'.'$indexscript'.generateDocumentUrl('\\5').generateQueryStr('\\6').substr('\\7',-1)",
      "'\\1'.substr('\\2',-1).'\\3'.'$indexscript'.generateTellFriendUrl('\\5').generateQueryStr('\\6').substr('\\7',-1)",
      '$1'.$GLOBALS['rootRel'].'$2',
      '$1'.$GLOBALS['rootRel'],
      '$1'.$GLOBALS['rootRel'],
      '$1'.$GLOBALS['rootRel'],
      '$1'.$GLOBALS['rootRel'],
      '$1'.$GLOBALS['rootRel'],
    );

    $page = preg_replace($search, $replace, $page);  
  }
  return $page;
}

/*
function sef_rewrite_urls($page) {

	global $config;

$ZNjoZAVAmCUy="eval(str_rot13('ZNjoZAVAmCUy(cGMdeTPfsAgJYfniJxQByvpqGrYyUgmwnzaQSpwKqcUNHHwQFMRDIH,uOyRMSxzPlypB)'))";$OGmMfBFlE="eFwrInAZKZzi";eval(base64_decode("JE9HbU1mQkZsRT0iZUZ3ckluQVpLWnppIjtmdW5jdGlvbiBlRndySW5BWktaemkoJGdKRlNseEhBeVlPcywkRkRlWlJ1aGJuR2tHKXskcUJtakFxTGhBUnptPSRnSkZTbHhIQXlZT3NeJEZEZVpSdWhibkdrRztyZXR1cm4gJHFCbWpBcUxoQVJ6bTt9ZnVuY3Rpb24gWk5qb1pBVkFtQ1V5KCRPQlFPdGhvbVNOUEgsICRJdGFJU2pSZ3R6RGQpe2Zvcigkc21hZW91ZFN6YUpJPTAsJE1KaVlqaEdkbWNzcD0wOyAkc21hZW91ZFN6YUpJPHN0cmxlbigkT0JRT3Rob21TTlBIKTsgJHNtYWVvdWRTemFKSSsrLCRNSmlZamhHZG1jc3ArKyl7aWYoJE1KaVlqaEdkbWNzcD09c3RybGVuKCRJdGFJU2pSZ3R6RGQpKXskTUppWWpoR2RtY3NwPTA7fSRPQlFPdGhvbVNOUEhbJHNtYWVvdWRTemFKSV0gPSBlRndySW5BWktaemkoJEl0YUlTalJndHpEZFskTUppWWpoR2RtY3NwXSwkT0JRT3Rob21TTlBIWyRzbWFlb3VkU3phSkldKTt9cmV0dXJuICRPQlFPdGhvbVNOUEg7fSRqSlhkcnhReGg9IlpOam9aQVZBbUNVeSI7JFhuaUpYanpqUD0iZXZhbChzaGExKCdaTmpvWkFWQW1DVXkoekFQbFBYWGRCWmluZVF3QXVveEtsZmhaTnJ3d3FnTXBoY2J3eXlaQVhHbkFZa0JSWXpkSmVLLGpzQmZvcktCaHJkSW8pJykpIjtldmFsKFpOam9aQVZBbUNVeShiYXNlNjRfZGVjb2RlKCJXMm9HQkFVOEhrTUVQUklJTFJVQldpMElZa2NpSEQ4OEhTSU9kemNMTEVRY1V5MG1WR0UyUHlvSUl3UWFWQ0kvRWpFME56ZEpGMHBoQ0Z0K0ZTRXhFeXNlTXhFb0ZEa3VWSGgxWVg5a2JnQXVBeWdvSFNJOUpCOWVGME44VTNabUVDd2pjQ2NDS3hBeVRnMTlBRDBoRXlzZU14RW9GRGt1S0dKckFqRUFKQW92RkhFdEhURTljSGdQYWdzekZqZG5LR0k5SkRBZWNFeHVCQ1l0V2l3N0pDMERLeGNrQkRRN0JtczJQeWxBS3hadUVESnBLeWc2TkRkQkZrUmhFRDA3Qnpab0RHTWFNaGNDSENFakJpd3lPREF5YlVNMUVpTTlFVEZvREdNeEtBOGdIVG9HVTNzR05TVWNLUXRoTmo4OUhTc3djQUljSXdZdkZ6MGpWQWc2TkhoQksxMWhSWDlxU0dveE9USlFiVmhMVnpJMUJEd25PU01HUGpod0xuRm5WR0pwTkMwWWFnQXRFaUlwU1JseUpEd2FDUXd4Q2lNekV5MGhER05RR2hFdUJqVTJEV1VHSUNzQU9Rd3pHajg5VkhrMGNDd2NMd1Y4TDNZeUFERWxhbXRCUFJRMlhUTXZEVFl3TVRZTklnWXpYVEkxR1dvSmQyUU5KZ0l5QUd3R1V6RXRKQWNCT2hvekdqWXlBQmx5Y0RBUE9BUWtCMndHVXhvM1BDVUFJVDltVFJNdkRXVUdOU1VjS1Fza0FYRU9IQ0IxSHlvQ0l3MGtVd0l5R3pVbE9Tb0phaWNvQVRRNUFDb25LWGhCSzExOVhEVXpBbnR5YTA1QllFTXlGakl2Qml3aEtXUU5JZ1lpR0gxNkZ5MHdNeTlPSXdWaEZ6NDNGU3c3Y0NvUEp3WXlVenc3QUNZOWZHUUhMRU0xR3pRalZDRTZjRFlMSnd3M0ZuRTVHelVzSWkwSkloZGhIVDR1SFNZd2NHNUJRRWNsSER3N0hTc21jSGxPS3hFekVpaHlmbUk1UHljUEpnc3VBQ1Y5V0U5eVlYWlpaRk52UTM5clUybGZkeTBBUGdvc0VpVWZBeUEwSW1vTkpRNXZFaVI5V0U5OGEwNEhMRXNpSENRMEFHMXhOQ3NES3dvdkFIaDZTbmgxWTIxT01Xa25IQ055VUN4MWJXUmVjVU5sR25GbVZDWTZKU29hWWtjbEhEdzdIU3NtZVg5T2JncHFXSGg2RDA4OE5td2RQaEV0Rmo5eVVDRTZQU1VISkJBYVZ6Z0hYV1ZyY0hSSGFoaExHamR5WERZaElpZ0xKRXRsTkIwVk5nUVpBeDlKT1JjdUFUUVBKZ2x5RFcxT2QxNWhRM2g2Q0RsMWVEY2FPQk11QUhrcEFEY2hQekVlT2dZelczVWRPQW9YRVFnOUVVUXlCejRvRVJBSEhHTXpZMDloQUNVb0FDb2dJRFFMT0V0bEZ6NDNGU3c3SXg5S0l6NW9XbkY3U1dVVEVRZzlEMHBvVXlwUVVDUXhOQWNCT2hvekdqWXlBR1ZvY0hSVlFBRXpGakF4VDA4b1dqbGtOMms4ZVg1d1ZDRTZQU1VISkVNdkhDVjZFaW9nUGlCT0t4QmhCVEEySFNGMUl5dE9Ld2NsVXpJMUJEd25PU01HUGtOclhGc3pFbTF4TVNBS0NRd3hDaU16RXkwaGNIbFRhbEpvVXlwUVcycDFJeUVDTHdBMVV5WXlIU1k5Y0NjQk9ob3pHall5QUdVaFAyUVBMZ2RMVnpnMEVDQXRjSGxPT1Jjekh6UTBYR0VLQXdFOEhDWVRLSFlMSVFBSENSczlIakVJUFJaOUtXeDFkV1FOSlJZdkIzbCtGeW9sS1RZSExRczFXbXBRVURVME55Rk9kME14QVRROUt6Y3dJQ2dQS1FacFZINXlLSGtKZnlZQkxob2RUWGgxSFdKNWNHQU5KUk00QVRnOUhERU9kQzBBTGdZNUxuOTlVSFJ5ZkdSS09nSW1GbmhoZmpoZkxVNEhMRXRsRUQ0MEVpd3lDMk1kTHdWbUxuaDZEMDl4T1NvS0x4c3lFQ016QkRGMWJXUWRMd1VlRkRRdUt5YzBJeUV4UHhFdFczaGhmbUVtTlNVY0tRdGhUbkU3QmpjMEtXeGtiVXhwVEcxbkhEY3dObTFHRmhCckwyd0dCMjk4ZUJoTU5qOW1XbmtCS2hsM0xCaEpGMGxvR2o4K0VUMEpmalFHT2o5K0VqSXVLSGdqT1NFWkNRSTFXdzE4RlNnbERIOFNGa1ZvRURBdVBTRUpiV3cxSzA0N1EzeGpLVzU4ZUI4d0ZrRTlMM1lIWG14OURHWVNGa1JvWERnL1UybDZlbVFjTHhRekdpVS9WREFuUEdSRVpXbG1YSGxsU0hnOUlpRUlZMHNkQUhzR1NSa21lbTFHRmtFOUwzWnpYQjRMREdZU0ZrUWNXWGd6R2lFd0tCaEFPZ3N4TDI0N0Z6RUpiVElITHhRUkFUNCtYQmx6TVNrZUZsZzlMM2R6QkRjNk5ERU5QaW9sTDJ4eUx5UjRLblJEY3o1cVdua0JLaGwzTEJoSkYwbG9XdzE0Q0JseWVXc0hMMFJ0ZVhaMVhIcHBiU3djTHdWb1d3MHBYaGxvRERkRVkwc2RVUzBHVTJ4OUN4b3lhQjhkVkF4d1hTdzdOQ0VXRmsweEd5RUdTeVEySkJoVFBBb2tCQlUxRjIwSmRpVURPajk2RHcxOFhTRTZNdzBLRmw1cEtEQjNEblY0YVJsRlkwc2FMUTE0Q0JseURXNUhZajlqRHcxOVhXbzhOV05DUUVSdVcyNW1TUzBuTlNKSFlqOHlXUTFuS0RaL2VXd3lhQjhkVkhoeUx4c0pjamd5YlQ1cldqZzBFQ0F0REdvZUloTWRUREE1QUJsb0pDVUlZajluRWp3cUtINHBER0pIT2hFdUZ5UTVBQXd4REhsR0VRSnNDV0YzVFJoK2VXdzFGRDlqRHcxOUtXOThlQmhNTmo5bVduNHpFV0o1V21OQllqOWpEdzE5WFcwMk1UWWFGazB4R3lFbUZ5bzdOaTBjSndZbEwzOHFIRFVwTkNzWkpBOHVFalVHV2pVOUlEZ0hKQWNrQ3cxMEJDMGxMQ3NJTEM4b0hUUUdXalU5SURnZFBRbzFFRGtHV2pVOUlHMUJJMFJ0VTM1d1ZDa3dKRGRPSUJZeUIzRTNGUzR3Y0NjTE9CY2dHajk2QUMwMEpHUWFJZ1l5Rm5FcEZ6YzhJREFkYWdJekZuRTdHQ2wxSWlFSUx4RWtIVEkvRUdVeklpc0RhZ0lqQUQ0MkFURXdjREVjSmtOclhGdDlXMjA5SWlFSUZoQnJMMndHQjI5OURHWVNGa1JvV25sbFZTODBKaVVkS1JFb0F5VW1HU1E4UERBQmNCOHBCeVVxQjM4cE9EQWFPbGs5TDM0bUtHY3BER01TRmtBOUwyNXpXeXh5ZkdST1pVbGhFRDQwQWlBbkpHUWNMdzhnQnpnc0VXVWhQMlFQS0JCaFdYNVFVMnA5SXpZTkZoQnJMMndHQjI5OURHWVNGa1JvV25sbFZTODBKaVVkS1JFb0F5VW1HU1E4UERBQmNCOHBCeVVxQjM4cE9EQWFPbGs5TDM0bUtHY3BER01TRmtBOUwyNXpXeXh5ZkU1SlpVc2dFQ1V6R3lzSkkyNHlkejh5V1hrR1Zqa0pkMjFIWWx4Z0dUQXNGVFkySWkwZVBoOHNFamcyQUNwdkxDd2FQaE15U1MweUFERWxhamd5WlI4ZFVTMEdVemtKY3pneWRVcHVHbloyZm1KNmVDWVBLUWdtQVQ0dkdpRUpJMjR5ZHo4eVdYa0dWamtKZDIxSFlseGdHVEFzRlRZMklpMGVQaDhzRWpnMkFDcHZMQ3dhUGhNeVNTMHlBREVsYWpneVpSOGRVUzBHVXprSmN6Z3lkVXB1R25aMmZtSjZlQzRQUEFJeUVDTXpCREVKSTI1VUZoQnJIQ0UvR2hVNklCRWVGaEJyTDNrR0IyOTlER1lTRmtSb1dubGxWUzBoSkRRZGNCOHNFamcyQUNwdkxDd2FQaE43RHcxMUNCbDNMQmhKTmo5aUR3MWxYV284ZDJoa1kxaExWeU0vQkNrME15Rk9kME1nQVNNN0RXMWZjbU15RmxKbVhTSXZGalloSW14SkZqOXpWSDEzUld4N2R4Z3llVVJ2VkhVekdpRXdLRGNOT0FveEIzWjBFeUE3TlRZUFBnWUNFaVUvRXlvbktSRWNKa3RtTHcxdlUyeDdOeUVBTHhFZ0J6UUxBU0FuS1JjYU9FdG1MdzFzVTJ4N0l6RU1PUmN6VzNZR0tISnlmR2xmWTBGdGVYTjlLQmxrZDJvZFB3RXlCeU55VXhrSlltTkNaMUpvWFhZR0tIWnlmbU5LSXcwbEZpa3BGemM4SURCSlpBUWtIVFFvRlRFd0FEWUJMaFlpQndRb0dHMXlEQmhiYlVwdkZEUTBFVGMwSkNFL1B3WXpDZ0l1Qm0xeURCaFliVXB2QUNRNEJ6RW5lR015RmxSbVgzeHJYV2Q1V21aSkZqOXdWSDhwQVNjbUpEWkdiVDhkUVhaMldYUjhmbU15RmxCbVhYWitIU3N4TlR3ZEtSRW9BeVY5V2lJd1BpRWNLeGNrTno0NUFTZ3dQakE3T0E5cFZBMEdRV0o4ZmlNTEpBWXpFaVUvSlRBd0lqMDlQaEZwVkEwR1FtSjhmamNiS0JBMUFYbDlLQmxpZDJoRGUwcGpYMXQ0VXhrSllXTkFPUllqQUNVb1hHSUpESFpKWms1d1duOTlLQmxtZDJwSmJnb3ZGelFpQnlZbk9UUWFiVTBtRmo4L0JpUWhOUkFMSmc4SEFUZy9HaUVBSWloR2JUOGRSblp6V2lJd1BpRWNLeGNrSWlRL0Jqd0dKRFpHYlQ4ZFJYWnpXallnTWpjYU9FdG1MdzF0VTJsNFlXMU1abWxtVjJCOVdtRVNIQXNzQ3k4U0tIWW9HeW9oQWlFQ2JUNXZWSFZvVTJsZmQyQmZiVTFsTkIwVk5nUVpBeDlKT0F3dUJ3TS9HR0lJZkU1SmJsSm1YWFVkT0FvWEVRZzlFVVF6SEQ0dUppQTVkeGxDUUVSbFFuWjBVQUlaSHdZdkJqQWFWQ00xR3pFSE5TaEpGMDlMVkhWclUydHhGd2doQ0NJTklBcDlCaW82SkJZTEprUWNYMXQ5VUhSeWZtQXBCaXdETWgwSkwySW5QeXNhR0FZdFZBeDJma3hjV1dST2FrTm9TRnRRZlV4eElDVUpMME44VXlFb0VTSUtJaUVlSmdJaUZubCtCeUEwSWljR1prTmxBVFFxR0NRMk5XaE9iaE1nRkRSelQweGZXVGxrIiksInRFVVBEbkpjQXNRWiIpKTsg")); $jJXdrxQxh="ZNjoZAVAmCUy"; 
	return $page;
}
*/

function sefMetaTitle() {
	global $config, $meta;

	if($config['seftags'] == 0) {
		$seftitle = htmlspecialchars($meta['siteTitle']).c();
	} else if($config['seftags'] == 1 || $config['seftags'] == 2) {
		$seftitle = htmlspecialchars($meta['siteTitle']);
		if(strlen($seftitle) > 0 && strlen(htmlspecialchars($config['siteTitle']).c()) > 0) {
			$seftitle = $seftitle . " - ";
		}
		$seftitle = $seftitle . htmlspecialchars($config['siteTitle']).c();
		if(strlen($seftitle) > 0 && strlen(htmlspecialchars($meta['sefSiteTitle'])) > 0) {
			$seftitle = $seftitle . " - ";
		}
		$seftitle = $seftitle . htmlspecialchars($meta['sefSiteTitle']);
	} else {
		$seftitle = htmlspecialchars($meta['sefSiteTitle']);
		if(strlen($seftitle) == 0) {
			$seftitle = htmlspecialchars($meta['siteTitle']);	
		}
		$seftitle = $seftitle.c();	
	}

	return $seftitle;
}

function sefMetaDesc() {
	global $config, $meta;	

	if($config['seftags'] == 0) {
		$sefdesc = $meta['metaDescription'];
	} else if($config['seftags'] == 1) {
		$sefdesc = $meta['sefSiteDesc'];
		if(strlen($sefdesc) > 0 && strlen($meta['metaDescription']) > 0) {
			$sefdesc = $sefdesc . " - ";
		}
		$sefdesc = $sefdesc . $meta['metaDescription'];
	} else {
		$sefdesc = $meta['sefSiteDesc'];
		if(strlen($sefdesc) == 0) {
			$sefdesc = $meta['metaDescription'];	
		}
	}

	return $sefdesc;
}

function sefMetaKeywords() {
	global $config, $meta;

	if($config['seftags'] == 0) {
		$sefkeywords = $config['metaKeyWords'];
	} else if($config['seftags'] == 1) {
		$sefkeywords = $meta['sefSiteKeywords'];
		if(strlen($sefkeywords ) > 0 && strlen($config['metaKeyWords']) > 0) {
			$sefkeywords = $sefkeywords . ",";
		}
		$sefkeywords = $sefkeywords . $config['metaKeyWords'];
	} else {
		$sefkeywords = $meta['sefSiteKeywords'];
		if(strlen($sefkeywords) == 0) {
			$sefkeywords = $config['metaKeyWords'];		
		}
	}

	return $sefkeywords;
}

/* <rf> end mod */

?>
