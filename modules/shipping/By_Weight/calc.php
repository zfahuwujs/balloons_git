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

|	calc.php

|   ========================================

|	Calculates shipping by weight (Lbs or Kgs)	

+--------------------------------------------------------------------------

*/

// per category shipping module

$module = fetchDbConfig("By_Weight");



if($module['status']==1){

$delInf = $cart->getDelAddr();
if($totalWeight==0 || empty($totalWeight)){
	$totalWeight = $cart->getTotalWeight();
}
// get the delivery ISO
if(isset($delCountry)){
	$countryISO = countryIso($delCountry);
}else{
	$countryISO = countryIso($delInf['delInf']['country']);
}



// build array of ISO Codes

$zones['1'] = explode(",",str_replace(" ","",strtoupper($module['zone1Countries'])));

$zones['2'] = explode(",",str_replace(" ","",strtoupper($module['zone2Countries'])));

$zones['3'] = explode(",",str_replace(" ","",strtoupper($module['zone3Countries'])));

$zones['4'] = explode(",",str_replace(" ","",strtoupper($module['zone4Countries'])));

$zones['5'] = explode(",",str_replace(" ","",strtoupper($module['zone5Countries'])));

$zones['6'] = explode(",",str_replace(" ","",strtoupper($module['zone6Countries'])));

$zones['7'] = explode(",",str_replace(" ","",strtoupper($module['zone7Countries'])));

$zones['8'] = explode(",",str_replace(" ","",strtoupper($module['zone8Countries'])));

// find the country

foreach ($zones as $key => $value){



	foreach($zones[$key] as $no => $iso){

	

		if($iso == $countryISO){

		

			$shipZone = $key;

		

		}

	

	}



}

// work out cost

$shipBands = explode(",",str_replace(" ","",$module['zone'.$shipZone.'RatesClass1']));

$noBands = count($shipBands);



if($noBands>0){



	for($n=0; $n<count($shipBands);$n++){

	

		$wheightCost = explode(":",str_replace(" ","",$shipBands[$n]));

		

		if($totalWeight<=$wheightCost[0]){

			

			$sumClass1 = $wheightCost[1]+$module['zone'.$shipZone.'Handling'];

			break;

			

		} elseif($totalWeight>$wheightCost[0] && $n+1==$noBands){

		

			$overWeight = TRUE;

		

		}

	

	}

	

}



unset($shipBands, $noBands);



$shipBands = explode(",",str_replace(" ","",$module['zone'.$shipZone.'RatesClass2']));

$noBands = count($shipBands);



if($noBands>0){



	for($n=0; $n<count($shipBands);$n++){

	

		$wheightCost = explode(":",str_replace(" ","",$shipBands[$n]));

		

		if($totalWeight<=$wheightCost[0]){

			

			$sumClass2 = $wheightCost[1]+$module['zone'.$shipZone.'Handling'];

			break;

			

		} elseif($totalWeight>$wheightCost[0] && $n+1==$noBands){

		

			$overWeight = TRUE;

		

		}

	

	}

	

}



unset($shipBands, $noBands);


$shipBands = explode(",",str_replace(" ","",$module['zone'.$shipZone.'RatesClass3']));

$noBands = count($shipBands);



if($noBands>0){



	for($n=0; $n<count($shipBands);$n++){

	

		$wheightCost = explode(":",str_replace(" ","",$shipBands[$n]));

		

		if($totalWeight<=$wheightCost[0]){

			

			$sumClass3 = $wheightCost[1]+$module['zone'.$shipZone.'Special'];

			break;

			

		} elseif($totalWeight>$wheightCost[0] && $n+1==$noBands){

		

			$overWeight = TRUE;

		

		}

	

	}

	

}



unset($shipBands, $noBands);


if($sum == 0){

	$sum = 0.00;

}



$taxVal = taxRate($module['zone'.$shipZone.'tax']);



if($sumClass1>0){

if($sumClass1<0.1){ $sumClass1 = 0 ; $lang['misc']['1stClass'] = "FREE";}

	if($taxVal>0 && $config['priceIncTax']==0){

	

		$val = ($taxVal / 100) * $sumClass1;

		$sumClass1 = $sumClass1 + $val;

	}



	$shippingPrice .= '<input type="radio" name="shipping" id="shipping_'.$shipKey.'" value="'.$sumClass1.'"';

	

	if($shipKey ==$cart->getVar('shipKey')){

		$shippingPrice .= " checked='checked'";

		$basket = $cart->setShipping(sprintf("%.2f",$sumClass1),$val,$lang['misc']['byWeight1stClass']);
		$firstPrice = $sumClass1;

	}

	if(!isset($firstPrice) || $firstPrice==0){
		$firstPrice = $sumClass1;
	}

	$sum = $sumClass1;

	
	$shippingPrice .= '" onclick="changeShipping(\''.$sumClass1.'\')" /><label style="color:black;" for="shipping_'.$shipKey.'">'.priceFormat($sumClass1)." ".$lang['misc']['1stClass'].'</label>'."<br/>";
	//$shippingPrice .= ">".priceFormat($sumClass1)." ".$lang['misc']['1stClass']."</option>\r\n";

	$shippingAvailable = TRUE;

	

	$shipKey++;

}



if($sumClass2>0){

if($sumClass2<0.1){ $sumClass2 = 0 ; $lang['misc']['2ndClass'] = "FREE";}

	if($taxVal>0 && $config['priceIncTax']==0){

	

		$val = ($taxVal / 100) * $sumClass2;

		$sumClass2 = $sumClass2 + $val;

	}



	$shippingPrice .= '<input type="radio" name="shipping" id="shipping_'.$shipKey.'" value="'.$sumClass2.'"';

	

	if($shipKey == $cart->getVar('shipKey')){

		$shippingPrice .= " checked='checked'";
		$basket = $cart->setShipping(sprintf("%.2f",$sumClass2),$val,$lang['misc']['byWeight2ndClass']);
		$firstPrice = $sumClass2;

	}

	if(!isset($firstPrice) || $firstPrice==0){
		$firstPrice = $sumClass2;
	}

	$sum = $sumClass2;

	
	$shippingPrice .= '" onclick="changeShipping(\''.$sumClass2.'\' /><label style="color:black" for="shipping_'.$shipKey.'">'.priceFormat($sumClass2)." ".$lang['misc']['2ndClass'].'</label>'."\r\n";
	//$shippingPrice .= ">".priceFormat($sumClass2)." ".$lang['misc']['2ndClass']."</option>\r\n";

	$shippingAvailable = TRUE;

	$shipKey++;

}



if($sumClass3>0){

if($sumClass3<0.1){ $sumClass3 = 0 ; $langSpecial = "FREE";}

	if($taxVal>0 && $config['priceIncTax']==0){

	

		$val = ($taxVal / 100) * $sumClass3;

		$sumClass3 = $sumClass3 + $val;

	}



	$shippingPrice .= '<input type="radio" name="shipping" id="shipping_'.$shipKey.'" value="'.$sumClass3.'"';

	

	if($shipKey == $cart->getVar('shipKey')){

		$shippingPrice .= " checked='checked'";
		$langSpecial = "Special";
		$basket = $cart->setShipping(sprintf("%.2f",$sumClass3),$val,$langSpecial);
		$firstPrice = $sumClass3;

	}

	if(!isset($firstPrice) || $firstPrice==0){
		$firstPrice = $sumClass3;
	}

	$sum = $sumClass3;

	
	$shippingPrice .= '" onclick="changeShipping(\''.$sumClass3.'\' /><label style="color:black" for="shipping_'.$shipKey.'">'.priceFormat($sumClass3)." ".$langSpecial.'</label>'."\r\n";
	//$shippingPrice .= ">".priceFormat($sumClass2)." ".$lang['misc']['2ndClass']."</option>\r\n";

	$shippingAvailable = TRUE;

	$shipKey++;

}



unset($module, $taxVal, $shipBands, $noBands, $zones, $wheightCost, $countryISO);

}

?>