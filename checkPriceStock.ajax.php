<?php
include_once("includes/ini.inc.php");
include_once("includes/global.inc.php");
$enableSSl = 1;
include_once("classes/db.inc.php");
$db = new db();
include_once("includes/functions.inc.php");
$config = fetchDbConfig("config");
include_once("includes/sessionStart.inc.php");
include_once("includes/sef_urls.inc.php");	
$sefroot = sef_script_name();
include_once("includes/sslSwitch.inc.php");
include_once("includes/session.inc.php");
include_once("includes/currencyVars.inc.php");

require_once("classes/newCart.php");

if(isset($_REQUEST)){
$prodId = $_REQUEST['prodId'];
$prodOpt = $_REQUEST['prodOpt'];
$result = array();
$prodOptString = substr($prodOpt,0,-1);

//check if prod has options
$hasOptions = $db->select("SELECT stock FROM ".$glob['dbprefix'] ."CubeCart_options_stock WHERE prodId = ".$db->mySQLSafe($prodId));
$price = $db->select("SELECT useStockLevel, stock_level, price, sale_price, tradePrice, tradeSale FROM ".$glob['dbprefix'] ."CubeCart_inventory WHERE productId = ".$db->mySQLSafe($prodId));
if($hasOptions == TRUE){



//check stock
$stock = $db->select("SELECT stock FROM ".$glob['dbprefix'] ."CubeCart_options_stock WHERE prodId = ".$db->mySQLSafe($prodId)." AND options = ".$db->mySQLSafe($prodOptString));



//get price
	if($stock[0]['stock'] > 0){
		
		$options = explode("|",$prodOptString);
		
		$priceDifference = 0;
		
		foreach($options as $option){
			
			$optionResult = $db->select("SELECT option_price, option_symbol FROM ".$glob['dbprefix'] ."CubeCart_options_bot WHERE assign_id = ".$db->mySQLSafe($option). " AND product= ".$db->mySQLSafe($prodId));
			if($optionResult[0]['option_symbol'] == "+"){
				$priceDifference += $optionResult[0]['option_price'];
			}elseif($result[0]['option_symbol'] == "-"){
				$priceDifference -= $optionResult[0]['option_price'];
			}
		}
		
		if($ccUserData[0]['customer_id'] > 0 && $ccUserData[0]['trade'] > 1){
			$tradeDiscount = $db->select("SELECT discount FROM ".$glob['dbprefix']."CubeCart_trade_accounts WHERE tradeAccId = ".$db->mySQLSafe($ccUserData[0]['trade']));
			$discount = $tradeDiscount[0]['discount']/100;
			$price[0]['tradePrice'] = $price[0]['price'] - ($price[0]['price'] * $discount);
			$price[0]['tradeSale'] = $price[0]['sale_price'] - ($price[0]['sale_price'] * $discount);
			if($ccUserData[0]['tax'] == 1){//no tax
				if(salePrice($price[0]['tradePrice'], $price[0]['tradeSale'])==FALSE){
					$resultPrice = $price[0]['tradePrice'] + $priceDifference;
					$result['price'] = priceFormat($resultPrice);
					$result['salePrice'] = "NULL";
				}else{
					$resultPrice = $price[0]['tradePrice'] + $priceDifference;
					$result['price'] = priceFormat($resultPrice);
					$resultSalePrice = $price[0]['tradeSale'] + $priceDifference;
					$result['salePrice'] = priceFormat($resultSalePrice);
				}
			}
			if($ccUserData[0]['tax'] == 2){//with tax
				if(salePrice($price[0]['tradePrice'], $price[0]['tradeSale'])==FALSE){
					$resultPrice = $price[0]['tradePrice'] + $priceDifference;
					$vatPrice = getProductPriceWithTax($prodId, $resultPrice);
					$result['price'] = priceFormat($vatPrice);
					$result['salePrice'] = "NULL";
				}else{
					$resultPrice = $price[0]['tradePrice'] + $priceDifference;
					$vatPrice = getProductPriceWithTax($prodId, $resultPrice);
					$result['price'] = priceFormat($vatPrice);
					
					$resultSalePrice = $price[0]['tradeSale'] + $priceDifference;
					$vatSalePrice = getProductPriceWithTax($prodId, $resultSalePrice);
					$result['salePrice'] = priceFormat($vatSalePrice);
				}
			}	
		}else{//no trade customer
			if($ccUserData[0]['tax'] == 1){//no tax
				if(salePrice($price[0]['price'], $price[0]['sale_price'])==FALSE){
					$resultPrice = $price[0]['price'] + $priceDifference;
					$result['price'] = priceFormat($resultPrice);
					$result['salePrice'] = "NULL";
				}else{
					$resultPrice = $price[0]['price'] + $priceDifference;
					$result['price'] = priceFormat($resultPrice);
					
					$resultSalePrice = $price[0]['sale_price'] + $priceDifference;
					$result['salePrice'] = priceFormat($resultSalePrice);
					//var_dump($resultSalePrice);
				}
			}
			if($ccUserData[0]['tax'] == 2){//with tax
				if(salePrice($price[0]['price'], $price[0]['sale_price'])==FALSE){
					$resultPrice = $price[0]['price'] + $priceDifference;
					$vatPrice = getProductPriceWithTax($prodId, $resultPrice);
					$result['price'] = priceFormat($vatPrice);
					$result['salePrice'] = "NULL";
				}else{
					$resultPrice = $price[0]['price'] + $priceDifference;
					$vatPrice = getProductPriceWithTax($prodId, $resultPrice);
					$result['price'] = priceFormat($vatPrice);
					
					$resultSalePrice = $price[0]['sale_price'] + $priceDifference;
					$vatSalePrice = getProductPriceWithTax($prodId, $resultSalePrice);
					$result['salePrice'] = priceFormat($vatSalePrice);
				}
			}	
		}
		
		
		$result['stock'] = $stock[0]['stock'];
		$result['emailMe'] = '0';
		$result['user'] = $ccUserData[0]['email'];
		}else{
			$result['stock'] = $stock[0]['stock'];
			$result['price'] = 'N/A';
			if($ccUserData[0]['customer_id'] > 0){
				$notify = $db->select("SELECT * FROM ".$glob['dbprefix'] ."CubeCart_stock_notify WHERE email = ".$db->mySQLSafe($ccUserData[0]['email'])." 
																AND productId = ".$db->mySQLSafe($prodId)." AND prodOptions = ".$db->mySQLSafe($prodOptString));
					if($notify==TRUE){
						$result['emailMeMsg'] = 'We will email you when this item is back in stock';
						$result['emailMe'] = '2';
					}else{
						$result['emailMeMsg'] = 'Email me when in stock';
						$result['emailMe'] = '1';
					}
			}else{
				$result['emailMe'] = '0';
			}
		}

	}else{//product without options
		//check if using stock
		if($price[0]['useStockLevel']==1){
			if($price[0]['stock_level'] > 0){
				$result['stock'] = $price[0]['stock_level'];
				
				if($ccUserData[0]['customer_id'] > 0 && $ccUserData[0]['trade'] > 1){
					$tradeDiscount = $db->select("SELECT discount FROM ".$glob['dbprefix']."CubeCart_trade_accounts WHERE tradeAccId = ".$db->mySQLSafe($ccUserData[0]['trade']));
					$discount = $tradeDiscount[0]['discount']/100;
					$price[0]['tradePrice'] = $price[0]['price'] - ($price[0]['price'] * $discount);
					$price[0]['tradeSale'] = $price[0]['sale_price'] - ($price[0]['sale_price'] * $discount);
					if($ccUserData[0]['tax'] == 1){//no tax
						if(salePrice($price[0]['tradePrice'], $price[0]['tradeSale'])==FALSE){
							$resultPrice = $price[0]['tradePrice'];
							$result['price'] = priceFormat($resultPrice);
							$result['salePrice'] = "NULL";
						}else{
							$resultPrice = $price[0]['tradePrice'];
							$result['price'] = priceFormat($resultPrice);
							$resultSalePrice = $price[0]['tradeSale'];
							$result['salePrice'] = priceFormat($resultSalePrice);
						}
					}
					if($ccUserData[0]['tax'] == 2){//with tax
						if(salePrice($price[0]['tradePrice'], $price[0]['tradeSale'])==FALSE){
							$resultPrice = $price[0]['tradePrice'];
							$vatPrice = getProductPriceWithTax($prodId, $resultPrice);
							$result['price'] = priceFormat($vatPrice);
							$result['salePrice'] = "NULL";
						}else{
							$resultPrice = $price[0]['tradePrice'];
							$vatPrice = getProductPriceWithTax($prodId, $resultPrice);
							$result['price'] = priceFormat($vatPrice);
							
							$resultSalePrice = $price[0]['tradeSale'];
							$vatSalePrice = getProductPriceWithTax($prodId, $resultSalePrice);
							$result['salePrice'] = priceFormat($vatSalePrice);
						}
					}	
				}else{//no trade customer
					if($ccUserData[0]['tax'] == 1){//no tax
						if(salePrice($price[0]['price'], $price[0]['sale_price'])==FALSE){
							$resultPrice = $price[0]['price'] ;
							$result['price'] = priceFormat($resultPrice);
							$result['salePrice'] = "NULL";
						}else{
							$resultPrice = $price[0]['price'];
							$result['price'] = priceFormat($resultPrice);
							
							$resultSalePrice = $price[0]['sale_price'];
							$result['salePrice'] = priceFormat($resultSalePrice);
							//var_dump($resultSalePrice);
						}
					}
					if($ccUserData[0]['tax'] == 2){//with tax
						if(salePrice($price[0]['price'], $price[0]['sale_price'])==FALSE){
							$resultPrice = $price[0]['price'];
							$vatPrice = getProductPriceWithTax($prodId, $resultPrice);
							$result['price'] = priceFormat($vatPrice);
							$result['salePrice'] = "NULL";
						}else{
							$resultPrice = $price[0]['price'];
							$vatPrice = getProductPriceWithTax($prodId, $resultPrice);
							$result['price'] = priceFormat($vatPrice);
							
							$resultSalePrice = $price[0]['sale_price'];
							$vatSalePrice = getProductPriceWithTax($prodId, $resultSalePrice);
							$result['salePrice'] = priceFormat($vatSalePrice);
						}
					}	
				}
								
			}else{
				$result['stock'] = 0;
				$result['price'] = 'N/A';
				
				if($ccUserData[0]['customer_id'] > 0){
					$notify = $db->select("SELECT * FROM ".$glob['dbprefix'] ."CubeCart_stock_notify WHERE email = ".$db->mySQLSafe($ccUserData[0]['email'])." 
																AND productId = ".$db->mySQLSafe($prodId));
					if($notify==TRUE){
						$result['emailMeMsg'] = 'We will email you when this item is back in stock';
						$result['emailMe'] = '2';
					}else{
						$result['emailMeMsg'] = 'Email me when in stock';
						$result['emailMe'] = '1';
					}
				}else{
					$result['emailMe'] = '0';
				}
			}
		}else{
			$result['stock'] = 666;
			$result['emailMe'] = '0';
			if($ccUserData[0]['customer_id'] > 0 && $ccUserData[0]['trade'] > 1){
					$tradeDiscount = $db->select("SELECT discount FROM ".$glob['dbprefix']."CubeCart_trade_accounts WHERE tradeAccId = ".$db->mySQLSafe($ccUserData[0]['trade']));
					$discount = $tradeDiscount[0]['discount']/100;
					$price[0]['tradePrice'] = $price[0]['price'] - ($price[0]['price'] * $discount);
					$price[0]['tradeSale'] = $price[0]['sale_price'] - ($price[0]['sale_price'] * $discount);
					if($ccUserData[0]['tax'] == 1){//no tax
						if(salePrice($price[0]['tradePrice'], $price[0]['tradeSale'])==FALSE){
							$resultPrice = $price[0]['tradePrice'];
							$result['price'] = priceFormat($resultPrice);
							$result['salePrice'] = "NULL";
						}else{
							$resultPrice = $price[0]['tradePrice'];
							$result['price'] = priceFormat($resultPrice);
							$resultSalePrice = $price[0]['tradeSale'];
							$result['salePrice'] = priceFormat($resultSalePrice);
						}
					}
					if($ccUserData[0]['tax'] == 2){//with tax
						if(salePrice($price[0]['tradePrice'], $price[0]['tradeSale'])==FALSE){
							$resultPrice = $price[0]['tradePrice'];
							$vatPrice = getProductPriceWithTax($prodId, $resultPrice);
							$result['price'] = priceFormat($vatPrice);
							$result['salePrice'] = "NULL";
						}else{
							$resultPrice = $price[0]['tradePrice'];
							$vatPrice = getProductPriceWithTax($prodId, $resultPrice);
							$result['price'] = priceFormat($vatPrice);
							
							$resultSalePrice = $price[0]['tradeSale'];
							$vatSalePrice = getProductPriceWithTax($prodId, $resultSalePrice);
							$result['salePrice'] = priceFormat($vatSalePrice);
						}
					}	
				}else{//no trade customer
					if($ccUserData[0]['tax'] == 1){//no tax
						if(salePrice($price[0]['price'], $price[0]['sale_price'])==FALSE){
							$resultPrice = $price[0]['price'] ;
							$result['price'] = priceFormat($resultPrice);
							$result['salePrice'] = "NULL";
						}else{
							$resultPrice = $price[0]['price'];
							$result['price'] = priceFormat($resultPrice);
							
							$resultSalePrice = $price[0]['sale_price'];
							$result['salePrice'] = priceFormat($resultSalePrice);
							//var_dump($resultSalePrice);
						}
					}
					if($ccUserData[0]['tax'] == 2){//with tax
						if(salePrice($price[0]['price'], $price[0]['sale_price'])==FALSE){
							$resultPrice = $price[0]['price'];
							$vatPrice = getProductPriceWithTax($prodId, $resultPrice);
							$result['price'] = priceFormat($vatPrice);
							$result['salePrice'] = "NULL";
						}else{
							$resultPrice = $price[0]['price'];
							$vatPrice = getProductPriceWithTax($prodId, $resultPrice);
							$result['price'] = priceFormat($vatPrice);
							
							$resultSalePrice = $price[0]['sale_price'];
							$vatSalePrice = getProductPriceWithTax($prodId, $resultSalePrice);
							$result['salePrice'] = priceFormat($vatSalePrice);
						}
					}	
				}
			
			
		}
	}
}
echo json_encode($result);
////
?>