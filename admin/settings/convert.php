<?php
/*

Currency update mod v1.02
by Paul Webb
www.toucanwebdesign.com

Ecommerce specialist website designers.

+--------------------------------------------------------------------------
|   CubeCart v3.0.2
|   ========================================
|   by Alistair Brookbanks
|	CubeCart is a Trade Mark of Devellion Limited
|   (c) 2005 Devellion Limited
|   Devellion Limited,
|   Westfield Lodge,
|   Westland Green,
|   Little Hadham,
|   Nr Ware, HERTS.
|   SG11 2AL
|   UNITED KINGDOM
|   http://www.devellion.com
|	UK Private Limited Company No. 5323904
|   ========================================
|   Web: http://www.cubecart.com
|   Date: Friday, 12 August 2005
|   Email: info (at) cubecart (dot) com
|	License Type: CubeCart is NOT Open Source Software and Limitations Apply 
|   Licence Info: http://www.cubecart.com/site/faq/license.php
+--------------------------------------------------------------------------
|	convert.php
|   ========================================
|	Auto update currencies	
+--------------------------------------------------------------------------
*/
include("../../includes/ini.inc.php");
include("../../includes/global.inc.php");
require_once("../../classes/db.inc.php");
$db = new db();
include_once("../../includes/functions.inc.php");
$config = fetchDbConfig("config");

include_once("../../language/".$config['defaultLang']."/lang.inc.php");
$enableSSl = 1;
include_once("../../includes/sslSwitch.inc.php");
include("../includes/auth.inc.php");
include("../includes/functions.inc.php");

if(permission("settings","read")==FALSE){
	header("Location: ".$GLOBALS['rootRel']."admin/401.php");
	exit;
}
include("../../includes/global.inc.php");
include("../includes/header.inc.php");
?>
<p class="pageTitle"><?php echo $lang['admin']['settings']['currencies'];?></p>
<?php
if(isset($msg)){ 
	echo $msg;
} else { 
?>
<p class="copyText">Currency Converter Running....</p>
<?php 
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://www.ecb.int/stats/eurofxref/eurofxref-daily.xml");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$output = curl_exec($ch);
curl_close($ch); 
	
$output = explode('</Cube>', $output);
$output = explode("\n", $output[0]);
	
for($i=8; $i<41; $i++){
	$temp = $output[$i];
	$currency = explode("currency=", $temp);
	$currency = substr($currency[1], 1, 3);
	
	$rate = explode("rate=", $output[$i]);
	$rate = explode("/>", $rate[1]);
	$rate = substr($rate[0], 1, (strlen($rate[0])-2));
		
	if($currency=='GBP'){
		$gbp = $rate;
	}
	if($currency=='USD'){
		$usd = $rate;
	}
	if($currency=='JPY'){
		$jpy = $rate;
	}
	if($currency=='CAD'){
		$cad = $rate;
	}
	if($currency=='AUD'){
		$aud = $rate;
	}
	if($currency=='CHF'){
		$chf = $rate;
	}
	if($currency=='RUB'){
		$rub = $rate;
	}
	if($currency=='CNY'){
		$cny = $rate;
	}
	if($currency=='ZAR'){
		$zar = $rate;
	}
}

$eur = '1';
$result = "SELECT * FROM ".$glob['dbprefix']."CubeCart_currencies WHERE value='1.00000'";
$row = $db->select($result);

if($row){
	$defcurr=$row[0]["code"];
	
	if ($defcurr=='GBP'){
		$incurr=$gbp;
	}
	if ($defcurr=='USD'){
		$incurr=$usd;
	}
	if ($defcurr=='EUR'){
		$incurr=$eur;
	}
	if ($defcurr=='JPY'){
		$incurr=$jpy;
	}
	if ($defcurr=='CAD'){
		$incurr=$cad;
	}
	if ($defcurr=='AUD'){
		$incurr=$aud;
	}
	if ($defcurr=='CHF'){
		$incurr=$chf;
	}
	if ($defcurr=='RUB'){
		$incurr=$rub;
	}
	if ($defcurr=='CNY'){
		$incurr=$cny;
	}
	if ($defcurr=='ZAR'){
		$incurr=$zar;
	}
	
	$pound = ('1'/$incurr);
	
	//     Euro update
	$exchanged = round($pound, 5); 
	$timer = $db->mySQLSafe(time());
	$sql = $db->misc("update ".$glob['dbprefix']."CubeCart_currencies set value='$exchanged',lastUpdated=$timer where name='Euro'");
	
	if($sql == TRUE){
		echo "<p class=copyText>Euro Currency updated</p>";
	}else{
		echo "<p class=warnText>ERROR updating database $timer</p>";
	}
	
	//     UK Pounds update

	/*$gbp = ($pound*$gbp);
	$exchanged = round($gbp, 5); 
	$timer = $db->mySQLSafe(time());
	$sql = $db->misc("update ".$glob['dbprefix']."CubeCart_currencies set value='$exchanged',lastUpdated=$timer where name='US Dollars'");
	
	if($sql == TRUE){
		echo "<p class=copyText>US Dollars Currency updated</p>";
	}else{
		echo "<p class=warnText>ERROR updating database $timer</p>";
	}*/

	//     US Dollars update
			
	$usd = ($pound*$usd);
	$exchanged = round($usd, 5); 
	$timer = $db->mySQLSafe(time());
	$sql = $db->misc("update ".$glob['dbprefix']."CubeCart_currencies set value='$exchanged',lastUpdated=$timer where name='US Dollars'");
	
	if($sql == TRUE){
		echo "<p class=copyText>US Dollars Currency updated</p>";
	}else{
		echo "<p class=warnText>ERROR updating database $timer</p>";
	}
			
	//      Japanese Yen update
			
	$jpy = ($pound*$jpy);
	$exchanged = round($jpy, 5); 
	$timer = $db->mySQLSafe(time());
	$sql = $db->misc("update ".$glob['dbprefix']."CubeCart_currencies set value='$exchanged',lastUpdated=$timer where name='Japanese Yen'");
	
	if($sql == TRUE){
		echo "<p class=copyText>Japanese Yen Currency updated</p>";
	}else{
		echo "<p class=warnText>ERROR updating database $timer</p>";
	}
	
	//      Canadian Dollars update
	
	$cad = ($pound*$cad);
	$exchanged = round($cad, 5); 
	$timer = $db->mySQLSafe(time());
	$sql = $db->misc("update ".$glob['dbprefix']."CubeCart_currencies set value='$exchanged',lastUpdated=$timer where name='Canadian Dollars'");
	
	if($sql == TRUE){
		echo "<p class=copyText>Canadian Dollars Currency updated</p>";
	}else{
		echo "<p class=warnText>ERROR updating database $timer</p>";
	}
	
	//      Australian Dollars update
	
	$aud = ($pound*$aud);
	$exchanged = round($aud, 5); 
	$timer = $db->mySQLSafe(time());
	$sql = $db->misc("update ".$glob['dbprefix']."CubeCart_currencies set value='$exchanged',lastUpdated=$timer where name='Australian Dollars'");
	
	if($sql == TRUE){
		echo "<p class=copyText>Australian Dollars Currency updated</p>";
	}else{
		echo "<p class=warnText>ERROR updating database $timer</p>";
	}
	
	//      Swiss Francs update
	
	$chf = ($pound*$chf);
	$exchanged = round($chf, 5); 
	$timer = $db->mySQLSafe(time());
	$sql = $db->misc("update ".$glob['dbprefix']."CubeCart_currencies set value='$exchanged',lastUpdated=$timer where name='Swiss Francs'");
	
	if($sql == TRUE){
		echo "<p class=copyText>Swiss Francs Currency updated</p>";
	}else{
		echo "<p class=warnText>ERROR updating database $timer</p>";
	}
	
	//      Russian Rubles update
	
	$rub = ($pound*$rub);
	$exchanged = round($rub, 5); 
	$timer = $db->mySQLSafe(time());
	$sql = $db->misc("update ".$glob['dbprefix']."CubeCart_currencies set value='$exchanged',lastUpdated=$timer where name='Russian Rubles'");
	
	
	if($sql == TRUE){
		echo "<p class=copyText>Russian Rubles Currency updated</p>";
	}else{
		echo "<p class=warnText>ERROR updating database $timer</p>";
	}
	//      Chinese Yuan update
	
	$cny = ($pound*$cny);
	$exchanged = round($cny, 5); 
	$timer = $db->mySQLSafe(time());
	$sql = $db->misc("update ".$glob['dbprefix']."CubeCart_currencies set value='$exchanged',lastUpdated=$timer where name='Chinese Yuan'");
	
	if($sql == TRUE){
		echo "<p class=copyText>Chinese Yuan Currency updated</p>";
	}else{
		echo "<p class=warnText>ERROR updating database $timer</p>";
	}
	
	//      South African Rand update
	
	$zar = ($pound*$zar);
	$exchanged = round($zar, 5); 
	$timer = $db->mySQLSafe(time());
	$sql = $db->misc("update ".$glob['dbprefix']."CubeCart_currencies set value='$exchanged',lastUpdated=$timer where name='South African Rand'");
	
	if($sql == TRUE){
		echo "<p class=copyText>South African Rand Currency updated</p>";
	}else{
		echo "<p class=warnText>ERROR updating database $timer</p>";
	}
}

include("../includes/footer.inc.php"); ?>