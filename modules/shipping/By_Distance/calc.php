<?php
$module = fetchDbConfig("By_Distance");
if($module['status']==1){

	$address = urlencode($module['location']);
	$url = "http://maps.google.com/maps/geo?q=".$address."&output=csv&key=".$config['apikey'];
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER,0); //Change this to a 1 to return headers
	curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
	//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$data = curl_exec($ch);
	curl_close($ch);
	$coordinates = explode(',',$data);
	
	$storeLongitude = $coordinates[2];
	$storeLatitude = $coordinates[3];
	
	#get delivery coordinates
	
	//$address = urlencode($basket['delInf']['add_1'].' '.$basket['delInf']['add_2'].' '.$basket['delInf']['town'].' '.$basket['delInf']['county'].' '.$basket['delInf']['postcode'].' '.countryName($basket['delInf']['country']));
	$address = urlencode($basket['delInf']['postcode']);
	$url = "http://maps.google.com/maps/geo?q=".$address."&output=csv&key=".$config['apikey'];
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER,0); //Change this to a 1 to return headers
	curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
	//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$data = curl_exec($ch);
	curl_close($ch);
	$coordinates = explode(',',$data);
	
	$deliveryLongitude = $coordinates[2];
	$deliveryLatitude = $coordinates[3];
	
	#Calculate Distance Beetween two locations (Here comes the best bit!)
	
	$distance = ceil((3958*3.1415926*sqrt(($deliveryLongitude-$storeLongitude)*($deliveryLongitude-$storeLongitude) + cos($deliveryLongitude/40075.16)*cos($storeLongitude/40075.16)*($deliveryLatitude-$storeLatitude)*($deliveryLatitude-$storeLatitude))/180)/1.6);
	
	for($i = 0; $i < $module['zones']; $i++){
		$radiusArray[$i] = $module['radius'.($i+1)];
		$minPriceArray[$i] = $module['minprice'.($i+1)];
	}
	
	for($i = 0; $i < count($radiusArray); $i++){
		if($i==0){
			if($distance < $radiusArray[$i]){
				if($subTotal >= $minPriceArray[$i] && $minPriceArray[$i] > 0){
					$shipprice = 0.00;
				}else{
					$shipprice = $module['price'.($i+1)];
				}
			}
		}else{
			if($distance >= $radiusArray[($i-1)] && $distance < $radiusArray[$i]){
				if($subTotal >= $minPriceArray[$i] && $minPriceArray[$i] > 0){
					$shipprice = 0.00;
				}else{
					$shipprice = $module['price'.($i+1)];
				}
			}
		}
	}
	
	/*if($distance < $module['radius1']){
		if($subTotal >= $module['minprice1'] && !empty($module['minprice1'])){
			$price = 0.00;
		}else{
			$price = $module['price1'];
		}
	}elseif($distance >= $module['radius1'] && $distance < $module['radius2']){
		if($subTotal >= $module['minprice2'] && !empty($module['minprice2'])){
			$price = 0.00;
		}else{
			$price = $module['price2'];
		}
	}elseif($distance >= $module['radius2'] && $distance < $module['radius3']){
		if($subTotal >= $module['minprice3'] && !empty($module['minprice3'])){
			$price = 0.00;
		}else{
			$price = $module['price3'];
		}
	}elseif($distance >= $module['radius3'] && $distance < $module['radius4']){
		if($subTotal >= $module['minprice4'] && !empty($module['minprice4'])){
			$price = 0.00;
		}else{
			$price = $module['price4'];
		}
	}elseif($distance >= $module['radius4'] && $distance < $module['radius5']){
		if($subTotal >= $module['minprice5'] && !empty($module['minprice5'])){
			$price = 0.00;
		}else{
			$price = $module['price5'];
		}
	}*/
	
	#Create shipping dropdown elements
	
	$shippingPrice .= "<option value='".$shipKey."'";
	if($shipKey == $basket['shipKey']){
		$shippingPrice .= " selected='selected'";
		$basket = $cart->setVar('By Distance within '.sprintf("%.2f",$distance).' miles',"shipMethod");
		$basket = $cart->setVar(sprintf("%.2f",$shipprice),"shipCost");
	}
	if($shipprice==0){
		$delivery = 'FREE';
	}else{
		$delivery = priceFormat($shipprice,true);
	}
	$shippingPrice .= ">".$delivery."</option>\r\n";
	$shippingAvailable = TRUE;
	$shipKey++;
}
?>