<?php
class basket{
	var $basketData;
	//returns basket contents
	function getContents(){
		global $db, $glob, $ccUserData;
		$data = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_basket WHERE sessId = '.$db->mySQLSafe($ccUserData[0]['sessId']).' AND customerIp = '.$db->mySQLsafe($_SERVER['REMOTE_ADDR']));
		if($data==true){
			$this->basketData = $data;
			return $this->basketData;
		}else{
			/*$findSession = $db->select('SELECT DISTINCT(sessId), customerIp, userAgent FROM '.$glob['dbprefix'].'CubeCart_basket WHERE customerIp = '.$db->mySQLsafe($_SERVER['REMOTE_ADDR']));
			if($findSession==true){
				if(count($findSession)==1){
					if($findSession[0]['customerIp']==$_SERVER['REMOTE_ADDR'] && $findSession[0]['userAgent']==$_SERVER['HTTP_USER_AGENT'] && !empty($findSession[0]['userAgent'])){
						if($findSession[0]['sessId']!=$ccUserData[0]['sessId']){
							
							$oldSession = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_sessions WHERE sessId = '.$db->mySQLSafe($findSession[0]['sessId']));
							if($oldSession==true){
							
								$record['sessId'] = $db->mySQLSafe($ccUserData[0]['sessId']);
								$where = 'sessId = '.$db->mySQLSafe($findSession[0]['sessId']).' AND customerIp = '.$db->mySQLSafe($_SERVER['REMOTE_ADDR']).' AND userAgent = '.$db->mySQLSafe($_SERVER['HTTP_USER_AGENT']).' AND startTime = '. $db->mySQLSafe($oldSession[0]['timeStart']);;
								$update = $db->update($glob['dbprefix'].'CubeCart_basket',$record,$where);
								if($update==true){
									$text = 'Session ID missmatch detected (SESSION IDENTIFIED):'."\r\n".'Browser: '.$_SERVER['HTTP_USER_AGENT']."\r\n".'User IP: '.$_SERVER['REMOTE_ADDR']."\r\n".'Date/Time: '.date('d/m/Y H:i:s')."\r\n".'Original Session ID: '.$findSession[0]['sessId']."\r\n".'Current Session ID: '.$ccUserData[0]['sessId'];
									$this->logError($text);
									return $this->getContents();
								}else{
									$text = 'Session ID missmatch detected (SESSION NOT IDENTIFIED):'."\r\n".'Browser: '.$_SERVER['HTTP_USER_AGENT']."\r\n".'User IP: '.$_SERVER['REMOTE_ADDR']."\r\n".'Date/Time: '.date('d/m/Y H:i:s')."\r\n".'Current Session ID: '.$ccUserData[0]['sessId'];
									$this->logError($text);
									return $this->getContents();
								}
							}else{
								$text = 'Session ID missmatch detected (OLD SESSION NOT FOUND):'."\r\n".'Browser: '.$_SERVER['HTTP_USER_AGENT']."\r\n".'User IP: '.$_SERVER['REMOTE_ADDR']."\r\n".'Date/Time: '.date('d/m/Y H:i:s')."\r\n"."\r\n".'Current Session ID: '.$ccUserData[0]['sessId'];
								$this->logError($text);
								return $this->getContents();
							}
						}
					}
				}else{
					$text = 'Session ID missmatch detected (MULTIPLE SESSIONS DETECTED):'."\r\n".'Browser: '.$_SERVER['HTTP_USER_AGENT']."\r\n".'User IP: '.$_SERVER['REMOTE_ADDR']."\r\n".'Date/Time: '.date('d/m/Y H:i:s')."\r\n".'Current Session ID: '.$ccUserData[0]['sessId'];
					$this->logError($text);
				}
			}*/
			return false;
		}
	}
	//adds a product to the pasket, also performs all required calculations
	function add($product,$qty=1,$options=array()){
		global $db, $glob, $ccUserData;
		if($qty < 1){
			$qty = 1;
		}elseif(is_double($qty)){
			$qty = round($qty);
		}
		if($product > 0){
			$productInfo = $db->select('SELECT price, sale_price, cat_id FROM CubeCart_inventory WHERE productId = '.$db->mySQLSafe($product));
			if($productInfo==true){
				$record['productId'] = $db->mySQLSafe($product);
				$record['productCat'] = $db->mySQLSafe($productInfo[0]['cat_id']);
				$record['productQty'] = $db->mySQLSafe($qty);
				if(!empty($options) && count($options) > 0){
					if(is_array($options)){
						$optionsString = implode("|",$options);
						if(strlen($optionsString) > 0){
							$record['productOptions'] = $db->mySQLSafe($optionsString);
							$optionsIf = ' AND productOptions = '.$db->mySQLSafe($optionsString);
						}
					}else{
						$record['productOptions'] = $db->mySQLSafe($options);
						$optionsIf = ' AND productOptions = '.$db->mySQLSafe($options);
					}
				}
				$record['productPrice'] = $db->mySQLSafe($this->productPrice($product,$options));
				$record['totalPrice'] = $db->mySQLSafe(($qty * $this->productPrice($product,$options)));
				$record['totalTax'] = $db->mySQLSafe($this->productTax($product,($qty * $this->productPrice($product,$options))));
				$record['totalWeight'] = $db->mySQLSafe(($qty * $this->productWeight($product)));
				$record['timeAdded'] = $db->mySQLSafe(time());
				$record['sessId'] = $db->mySQLSafe($ccUserData[0]['sessId']);
				$record['customerIp'] = $db->mySQLSafe($_SERVER['REMOTE_ADDR']);
				$record['userAgent'] = $db->mySQLSafe($_SERVER['HTTP_USER_AGENT']);
				$record['startTime'] = $db->mySQLSafe($ccUserData[0]['timeStart']);
				$record['itemType'] = $db->mySQLSafe('product');
				$inBasket = $db->select('SELECT id, productQty FROM '.$glob['dbprefix'].'CubeCart_basket WHERE productId = '.$db->mySQLSafe($product).' AND sessId = '.$db->mySQLSafe($ccUserData[0]['sessId']).' AND customerIp = '.$db->mySQLSafe($_SERVER['REMOTE_ADDR']).$optionsIf);
				if($inBasket==true){
					$qty = $qty + $inBasket[0]['productQty'];
					$record['productQty'] = $db->mySQLSafe($qty);
					$record['totalPrice'] = $db->mySQLSafe(($qty * $this->productPrice($product,$options)));
					$record['totalTax'] = $db->mySQLSafe($this->productTax($product,($qty * $this->productPrice($product,$options))));
					$record['totalWeight'] = $db->mySQLSafe(($qty * $this->productWeight($product)));
					$where = 'id = '.$db->mySQLSafe($inBasket[0]['id']);
					$insert = $db->update($glob['dbprefix'].'CubeCart_basket',$record,$where);
				}else{
					$insert = $db->insert($glob['dbprefix'].'CubeCart_basket',$record);
				}
				if($insert==true){
					$this->setDiscount($this->getDiscountCode());
					return $this->getContents();
				}else{
					$this->logError('Baket was not updated');
					return false;
				}
			}
		}
	}
	//removes basket Rows
	function removeItem($itemKey){
		global $db, $glob, $ccUserData;
		$inBasket = $db->select('SELECT id FROM '.$glob['dbprefix'].'CubeCart_basket WHERE id = '.$db->mySQLSafe($itemKey).' AND sessId = '.$db->mySQLSafe($ccUserData[0]['sessId']).' AND customerIp = '.$db->mySQLSafe($_SERVER['REMOTE_ADDR']));
		if($inBasket==true){
			$delete = $db->delete($glob['dbprefix'].'CubeCart_basket','id = '.$db->mySQLSafe($inBasket[0]['id']),1);
			if($delete==true){
				$this->setDiscount($this->getDiscountCode());
				return $this->getContents();
			}else{
				$this->logError('Item Could not be deleted');
				return false;
			}
		}else{
			return false;
		}
	}
	//updates qty prices tax and total weight of the product in the basket row
	function update($key,$qty){
		global $db, $glob, $ccUserData;
		if(round($qty) > 0){
			$record['productQty'] = $db->mySQLSafe($qty);
			$record['totalPrice'] = $db->mySQLSafe(($qty * $this->productPrice($this->keyData($key,'productId'),$this->keyData($key,'productOptions'))));
			$record['totalTax'] = $db->mySQLSafe($this->productTax($this->keyData($key,'productId'),($qty * $this->productPrice($this->keyData($key,'productId'),$this->keyData($key,'productOptions')))));
			$record['totalWeight'] = $db->mySQLSafe(($qty * $this->productWeight($this->keyData($key,'productId'))));
			$where = 'id = '.$db->mySQLSafe($key);
			$update = $db->update($glob['dbprefix'].'CubeCart_basket',$record,$where);
			if($update==true){
				$this->setDiscount($this->getDiscountCode());
				return $this->getContents();
			}
		}else{
			return $this->removeItem($key);
		}
	}
	//calculates product price including options
	function productPrice($product,$options=array()){
		global $db, $glob, $ccUserData;
		if(!empty($options) && !is_array($options)){
			$options = explode('|',$options);
		}
		$productInfo = $db->select('SELECT price, sale_price FROM CubeCart_inventory WHERE productId = '.$db->mySQLSafe($product));
		if($productInfo==true){
			$price = $this->salePrice($productInfo[0]['price'],$productInfo[0]['sale_price']);
			if(!empty($options) && is_array($options)){
				for($i = 0; $i < count($options); $i++){
					$optionInfo = $db->select('SELECT option_price, option_symbol FROM '.$glob['dbprefix'].'CubeCart_inventory
					INNER JOIN '.$glob['dbprefix'].'CubeCart_options_bot
					ON '.$glob['dbprefix'].'CubeCart_inventory.productId = '.$glob['dbprefix'].'CubeCart_options_bot.product
					INNER JOIN '.$glob['dbprefix'].'CubeCart_options_mid
					ON '.$glob['dbprefix'].'CubeCart_options_bot.value_id = '.$glob['dbprefix'].'CubeCart_options_mid.value_id
					INNER JOIN '.$glob['dbprefix'].'CubeCart_options_top
					ON '.$glob['dbprefix'].'CubeCart_options_mid.father_id = '.$glob['dbprefix'].'CubeCart_options_top.option_id
					WHERE '.$glob['dbprefix'].'CubeCart_inventory.productId = '.$db->mySQLSafe($product).' AND assign_id = '.$db->mySQLSafe($options[$i]));
					if($optionInfo==true){
						if($optionInfo[0]['option_symbol']=='+'){
							$price = $price + $optionInfo[0]['option_price'];
						}elseif($optionInfo[0]['option_symbol']=='-'){
							$price = $price - $optionInfo[0]['option_price'];
						}elseif($optionInfo[0]['option_symbol']=='='){
							$price = $optionInfo[0]['option_price'];
						}
					}
				}
			}
			return $price;
		}else{
			$this->logError('Price not found');
			return false;
		}
	}
	//returns product weight
	function productWeight($product,$options=array()){
		global $db, $glob, $ccUserData;
		if(!empty($options) && !is_array($options)){
			$options = explode('|',$options);
		}
		$productInfo = $db->select('SELECT prodWeight FROM CubeCart_inventory WHERE productId = '.$db->mySQLSafe($product));
		if($productInfo==true){
			if(!empty($options) && is_array($options)){
				for($i = 0; $i < count($options); $i++){
					$optionInfo = $db->select('SELECT option_weight, weight_symbol FROM '.$glob['dbprefix'].'CubeCart_inventory
					INNER JOIN '.$glob['dbprefix'].'CubeCart_options_bot
					ON '.$glob['dbprefix'].'CubeCart_inventory.productId = '.$glob['dbprefix'].'CubeCart_options_bot.product
					INNER JOIN '.$glob['dbprefix'].'CubeCart_options_mid
					ON '.$glob['dbprefix'].'CubeCart_options_bot.value_id = '.$glob['dbprefix'].'CubeCart_options_mid.value_id
					INNER JOIN '.$glob['dbprefix'].'CubeCart_options_top
					ON '.$glob['dbprefix'].'CubeCart_options_mid.father_id = '.$glob['dbprefix'].'CubeCart_options_top.option_id
					WHERE '.$glob['dbprefix'].'CubeCart_inventory.productId = '.$db->mySQLSafe($product).' AND assign_id = '.$db->mySQLSafe($options[$i]));
					if($optionInfo==true){
						if($optionInfo[0]['weight_symbol']=='+'){
							$productInfo[0]['prodWeight'] = $productInfo[0]['prodWeight'] + $optionInfo[0]['option_weight'];
						}elseif($optionInfo[0]['weight_symbol']=='-'){
							$productInfo[0]['prodWeight'] = $productInfo[0]['prodWeight'] - $optionInfo[0]['option_weight'];
						}
					}
				}
			}
			return $productInfo[0]['prodWeight'];
		}else{
			$this->logError('Weight not found');
			return false;
		}
	}
	//calculates tax on the given product/price
	function productTax($product,$price){
		global $db, $glob, $ccUserData, $config;
		$productInfo = $db->select('SELECT taxType FROM CubeCart_inventory WHERE productId = '.$db->mySQLSafe($product));
		
		if($productInfo==true){
			//var_dump($productInfo[0]['taxType']);
			$taxInfo = $db->select('SELECT * FROM CubeCart_taxes WHERE id = '.$db->mySQLSafe($productInfo[0]['taxType']));
			if($taxInfo==true){
				if($config['priceIncTax']==1){
					return (($price/(($taxInfo[0]['percent']/100)+1)) * ($taxInfo[0]['percent']/100));
				}else{
					return ($price * ($taxInfo[0]['percent']/100));
				}
			}else{
				$this->logError('Tax not found');
				return false;
			}
		}else{
			$this->logError('Tax product not found');
			return false;
		}
	}
	//check if sale prices are used and are available returns sale price if available if not will return normal price
	function salePrice($normPrice, $salePrice){
		global $config;
		if($config['saleMode']==1){
			if($salePrice<$normPrice && $salePrice>0){
				return $salePrice;
			} else {
				return $normPrice;
			} 
		} elseif($config['saleMode']==2) {
			$saleValue = $normPrice * ((100-$config['salePercentOff'])/100);
			if($saleValue<$normPrice && $saleValue>0){ 
				return $saleValue; 
			} else { 
				return $normPrice; 
			}
		} else {
			return $normPrice;
		}
	}
	//returns total count of items in the basket
	function noItems(){
		global $db, $glob, $ccUserData;
		$count = $db->select('SELECT SUM(productQty) AS totalItems FROM '.$glob['dbprefix'].'CubeCart_basket WHERE itemType = "product" AND sessId = '.$db->mySQLSafe($ccUserData[0]['sessId']).' AND customerIp = '.$db->mySQLsafe($_SERVER['REMOTE_ADDR']));
		return $count[0]['totalItems'];
	}
	//returns total of products
	function productTotal(){
		global $db, $glob, $ccUserData;
		$count = $db->select('SELECT (SUM(totalPrice) - SUM(productDiscount * productQty)) AS totalItems FROM '.$glob['dbprefix'].'CubeCart_basket WHERE itemType = "product" AND sessId = '.$db->mySQLSafe($ccUserData[0]['sessId']).' AND customerIp = '.$db->mySQLsafe($_SERVER['REMOTE_ADDR']));
		if($ccUserData[0]['tax'] == 1){//no tax
			if($ccUserData[0]['customer_id'] > 0 && $ccUserData[0]['trade'] > 1){
				$tradeDiscount = $db->select("SELECT discount FROM ".$glob['dbprefix']."CubeCart_trade_accounts WHERE tradeAccId = ".$db->mySQLSafe($ccUserData[0]['trade']));
				$discount = $tradeDiscount[0]['discount']/100;
				return $count[0]['totalItems'] - ($count[0]['totalItems'] * $discount);
			}else{
				//no trader
				return $count[0]['totalItems'];
			}
		}
		if($ccUserData[0]['tax'] == 2){//with tax
			if($ccUserData[0]['customer_id'] > 0 && $ccUserData[0]['trade'] > 1){
				$tradeDiscount = $db->select("SELECT discount FROM ".$glob['dbprefix']."CubeCart_trade_accounts WHERE tradeAccId = ".$db->mySQLSafe($ccUserData[0]['trade']));
				$discount = $tradeDiscount[0]['discount']/100;
				$out = $count[0]['totalItems'] + $this->taxTotal();
				$out -= $out * $discount;
				return $out;
				
			}else{
				//no trader
				return $count[0]['totalItems'] + $this->taxTotal();
			}
		}
	}
	function taxTotal(){
		global $db, $glob, $ccUserData;
		$count = $db->select('SELECT (SUM(totalTax) - SUM(discountTax * productQty)) AS totalItems FROM '.$glob['dbprefix'].'CubeCart_basket WHERE (itemType = "product" OR itemType = "shipping") AND productId != 0 AND sessId = '.$db->mySQLSafe($ccUserData[0]['sessId']).' AND customerIp = '.$db->mySQLsafe($_SERVER['REMOTE_ADDR']));
		return $count[0]['totalItems'];
	}
	//calculate grand total
	function grandTotal(){
		global $db, $glob, $ccUserData, $config;
		$count = $db->select('SELECT (SUM(totalPrice) - SUM((productDiscount * productQty))) AS totalItems FROM '.$glob['dbprefix'].'CubeCart_basket WHERE sessId = '.$db->mySQLSafe($ccUserData[0]['sessId']).' AND productId != 0 AND customerIp = '.$db->mySQLsafe($_SERVER['REMOTE_ADDR']));
		$deliveryPrice = $db->select('SELECT totalPrice FROM '.$glob['dbprefix'].'CubeCart_basket WHERE sessId = '.$db->mySQLSafe($ccUserData[0]['sessId']).' AND productId = 0 AND customerIp = '.$db->mySQLsafe($_SERVER['REMOTE_ADDR']));
	
		if($count==true){
			if($ccUserData[0]['tax'] == 1){
				if($ccUserData[0]['customer_id'] > 0 && $ccUserData[0]['trade'] > 1){
					$tradeDiscount = $db->select("SELECT discount FROM ".$glob['dbprefix']."CubeCart_trade_accounts WHERE tradeAccId = ".$db->mySQLSafe($ccUserData[0]['trade']));
					$discount = $tradeDiscount[0]['discount']/100;
					return ($count[0]['totalItems'] - ($count[0]['totalItems'] * $discount))+$deliveryPrice[0]['totalPrice'];
				}else{
					//no trader
					return $count[0]['totalItems'] + $deliveryPrice[0]['totalPrice'];
				}
			}
			if($ccUserData[0]['tax'] == 2){
				if($ccUserData[0]['customer_id'] > 0 && $ccUserData[0]['trade'] > 1){
					$tradeDiscount = $db->select("SELECT discount FROM ".$glob['dbprefix']."CubeCart_trade_accounts WHERE tradeAccId = ".$db->mySQLSafe($ccUserData[0]['trade']));
					$discount = $tradeDiscount[0]['discount']/100;
					$out = $count[0]['totalItems'] + $this->taxTotal();
					$out -= $out * $discount;
					$out += $deliveryPrice[0]['totalPrice'];
					return $out;
					
				}else{
					//no trader
					return $count[0]['totalItems'] + $this->taxTotal() + $deliveryPrice[0]['totalPrice'];
				}
			}
		}else{
			$this->logError('Grand Total Not Found');
			return false;
		}
	}
	//returns total of products
	function productFullTotal(){
		global $db, $glob, $ccUserData;
		$count = $db->select('SELECT SUM(totalPrice) AS totalItems FROM '.$glob['dbprefix'].'CubeCart_basket WHERE itemType = "product" AND sessId = '.$db->mySQLSafe($ccUserData[0]['sessId']).' AND customerIp = '.$db->mySQLsafe($_SERVER['REMOTE_ADDR']));
		return $count[0]['totalItems'];
	}
	//list of product currently in the basket
	function productList(){
		global $db, $glob, $ccUserData;
		$products = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_basket WHERE itemType = "product" AND sessId = '.$db->mySQLSafe($ccUserData[0]['sessId']).' AND customerIp = '.$db->mySQLsafe($_SERVER['REMOTE_ADDR']));
		if($products==true){
			return $products;
		}else{
			return false;
		}
	}
	//sets basket variables
	function setVar($data,$varName){
		global $db, $glob, $ccUserData;
		$record['otherData'] = $db->mySQLSafe(serialize($data));
		$record['varName'] = $db->mySQLSafe($varName);
		$record['timeAdded'] = $db->mySQLSafe(time());
		$record['sessId'] = $db->mySQLSafe($ccUserData[0]['sessId']);
		$record['customerIp'] = $db->mySQLSafe($_SERVER['REMOTE_ADDR']);
		$record['userAgent'] = $db->mySQLSafe($_SERVER['HTTP_USER_AGENT']);
		$record['startTime'] = $db->mySQLSafe($ccUserData[0]['timeStart']);
		$record['itemType'] = $db->mySQLSafe('otherInfo');
		$check = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_basket WHERE itemType = "otherInfo" AND sessId = '.$db->mySQLSafe($ccUserData[0]['sessId']).' AND customerIp = '.$db->mySQLsafe($_SERVER['REMOTE_ADDR']));
		if($check==true){
			$where = 'id = '.$db->mySQLSafe($check[0]['id']);
			$update = $db->update($glob['dbprefix'].'CubeCart_basket',$record,$where);
		}else{
			$update = $db->insert($glob['dbprefix'].'CubeCart_basket',$record);
			if($update==false){
				$this->logError('Unable to set Variable '.$varName);
			}
		}
		if($update==true){
			return true;
		}else{
			return false;
		}
	}
	//get data from variable
	function getVar($varName){
		global $db, $glob, $ccUserData;
		$info = $db->select('SELECT otherData FROM '.$glob['dbprefix'].'CubeCart_basket WHERE itemType = "otherInfo" AND sessId = '.$db->mySQLSafe($ccUserData[0]['sessId']).' AND customerIp = '.$db->mySQLsafe($_SERVER['REMOTE_ADDR']).' AND varName = '.$db->mySQLSafe($varName));
		if($info==true){
			return unserialize($info[0]['otherData']); 
		}else{
			return false;
		}
	}
	//destroy variable
	function varDestroy($varName){
		global $db, $glob, $ccUserData;
		$where = 'itemType = "otherInfo" AND sessId = '.$db->mySQLSafe($ccUserData[0]['sessId']).' AND customerIp = '.$db->mySQLsafe($_SERVER['REMOTE_ADDR']).' AND varName = '.$db->mySQLSafe($varName);
		$delete = $db->delete($glob['dbprefix'].'CubeCart_basket',$where,1);
		if($delete==true){
			return true;
		}else{
			$this->logError('Unable to destroy Variable');
			return false;
		}
	}
	//get product data will return array if no column specified or will return column data if exists
	function productData($productId,$column=null){
		global $db, $glob, $ccUserData;
		$productInfo = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_inventory WHERE productId = '.$db->mySQLSafe($productId));
		if($productInfo==true){
			if(isset($column) && !empty($column)){
				return $productInfo[0][$column];
			}else{
				return $productInfo[0];
			}
		}else{
			$this->logError('Product Data Not Found');
			return false;
		}
	}
	//get option data will return array if no column specified or will return column data if exists
	function optionData($productId,$assignId,$column=null){
		global $db, $glob, $ccUserData;
		$optionData = $db->select('SELECT * FROM CubeCart_options_bot
		INNER JOIN CubeCart_options_mid
		ON CubeCart_options_bot.value_id = CubeCart_options_mid.value_id
		INNER JOIN CubeCart_options_top
		ON CubeCart_options_mid.father_id = CubeCart_options_top.option_id
		WHERE CubeCart_options_bot.assign_id = '.$db->mySQLSafe($assignId).' AND CubeCart_options_bot.product = '.$db->mySQLSafe($productId));
		if($optionData==true){
			if(isset($column) && !empty($column)){
				return $optionData[0][$column];
			}else{
				return $optionData[0];
			}
		}else{
			$this->logError('Option Data Not Found');
			return false;
		}
	}
	//outputs htm with option names for display on cart page
	function displayOptions($productId,$optionsArray){
		global $db, $glob, $ccUserData;
		$echo = null;
		for($i = 0; $i < count($optionsArray); $i++){
			$echo .= '<br/>'.$this->optionData($productId,$optionsArray[$i],'value_name');
		}
		return $echo;
	}
	//get data for the specific cart row
	function keyData($key,$column=null){
		global $db, $glob, $ccUserData;
		$data = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_basket WHERE id = '.$db->mySQLSafe($key).' AND sessId = '.$db->mySQLSafe($ccUserData[0]['sessId']).' AND customerIp = '.$db->mySQLsafe($_SERVER['REMOTE_ADDR']));
		if($data==true){
			if(isset($column) && !empty($column)){
				return $data[0][$column];
			}else{
				return $data[0];
			}
		}else{
			$this->logError('Key Data Not Found');
			return false;
		}
	}
	//will remove all cart data
	function emptyCart(){
		global $db, $glob, $ccUserData;
		$where = 'sessId = '.$db->mySQLSafe($ccUserData[0]['sessId']).' AND customerIp = '.$db->mySQLsafe($_SERVER['REMOTE_ADDR']);
		$empty = $db->delete($glob['dbprefix'].'CubeCart_basket',$where);
		return $this->getContents();
	}
	
	function noShipTotal(){
		global $db, $glob, $ccUserData;
		$count = $db->select('SELECT (SUM(totalPrice) - SUM((productDiscount * productQty))) AS totalItems FROM '.$glob['dbprefix'].'CubeCart_basket WHERE itemType = "product" AND sessId = '.$db->mySQLSafe($ccUserData[0]['sessId']).' AND customerIp = '.$db->mySQLsafe($_SERVER['REMOTE_ADDR']));
		if($count==true){
			return $count[0]['totalItems'];
		}else{
			$this->logError('No Shipping Total Not Found');
			return false;
		}
	}
	//set shipping variables
	function setShipping($price,$tax=0,$data=null){
		global $db, $glob, $ccUserData;
		$check = $db->select('SELECT id FROM '.$glob['dbprefix'].'CubeCart_basket WHERE itemType = "shipping" AND sessId = '.$db->mySQLSafe($ccUserData[0]['sessId']).' AND customerIp = '.$db->mySQLsafe($_SERVER['REMOTE_ADDR']));
		
		$record['totalPrice'] = $db->mySQLSafe($price);
		$record['totalTax'] = $db->mySQLSafe($price * ($tax/100));
		$record['otherData'] = $db->mySQLSafe(serialize($data));
		$record['timeAdded'] = $db->mySQLSafe(time());
		$record['sessId'] = $db->mySQLSafe($ccUserData[0]['sessId']);
		$record['customerIp'] = $db->mySQLSafe($_SERVER['REMOTE_ADDR']);
		$record['userAgent'] = $db->mySQLSafe($_SERVER['HTTP_USER_AGENT']);
		$record['startTime'] = $db->mySQLSafe($ccUserData[0]['timeStart']);
		$record['itemType'] = $db->mySQLSafe('shipping');
		
		if($check==true){
			$where = 'id = '.$db->mySQLSafe($check[0]['id']);
			$update = $db->update($glob['dbprefix'].'CubeCart_basket',$record,$where);
		}else{
			$update = $db->insert($glob['dbprefix'].'CubeCart_basket',$record);
		}
		return $this->getContents();
	}
	//get shipping price
	function getShipping(){
		global $db, $glob, $ccUserData;
		$check = $db->select('SELECT totalPrice FROM '.$glob['dbprefix'].'CubeCart_basket WHERE itemType = "shipping" AND sessId = '.$db->mySQLSafe($ccUserData[0]['sessId']).' AND customerIp = '.$db->mySQLsafe($_SERVER['REMOTE_ADDR']));
		if($check==true){
			return $check[0]['totalPrice'];
		}else{
			return false;
		}
	}
	function removeShipping(){
		global $db, $glob, $ccUserData;
		return $db->delete($glob['dbprefix'].'CubeCart_basket','itemType = "shipping" AND sessId = '.$db->mySQLSafe($ccUserData[0]['sessId']).' AND customerIp = '.$db->mySQLsafe($_SERVER['REMOTE_ADDR']));
	}
	//get shipping method
	function getShipMethod(){
		global $db, $glob, $ccUserData;
		$check = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_basket WHERE itemType = "shipping" AND sessId = '.$db->mySQLSafe($ccUserData[0]['sessId']).' AND customerIp = '.$db->mySQLsafe($_SERVER['REMOTE_ADDR']));
		return unserialize($check[0]['otherData']);
	}
	//remove discount
	function removeDiscount(){
		global $db, $glob, $ccUserData;
		$discount = $db->select('SELECT id FROM '.$glob['dbprefix'].'CubeCart_basket WHERE itemType = "discount" AND sessId = '.$db->mySQLSafe($ccUserData[0]['sessId']).' AND customerIp = '.$db->mySQLsafe($_SERVER['REMOTE_ADDR']));
		if($discount==true){
			$this->removeItem($discount[0]['id']);
		}
		$items = $this->getContents();
		if($items){
			$record['discountTax'] = $db->mySQLSafe(0);
			$record['productDiscount'] = $db->mySQLSafe(0);
			$where = 'itemType = "product" AND sessId = '.$db->mySQLSafe($ccUserData[0]['sessId']).' AND customerIp = '.$db->mySQLsafe($_SERVER['REMOTE_ADDR']);
			$update = $db->update($glob['dbprefix'].'CubeCart_basket',$record,$where);
			return $this->getContents();
		}
	}
	//set discount
	function setDiscount($code=null){
		global $db, $glob, $ccUserData;
		
		$applyDiscount = false;
		if(isset($code) && $code != ""){
			$check = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_discount WHERE discountCode = '.$db->mySQLSafe($code).' AND (customerId = 0 OR customerId = '.$db->mySQLSafe($ccUserData[0]['customer_id']).')');
			
		}
		$time = false;
		if($check[0]['expDate'] > time()){
			$time = true;
		}
		if($check==true && $time){
			$productTotal = $this->productFullTotal();
			if($check[0]['discountType']==1){
				//general discount
				if($check[0]['discountFormat']==1){
					//ammount off
					
					$ammount = $check[0]['discountAmount'];
				}else{
					//percentage off
					$ammount = $productTotal * ($check[0]['discountAmount']/100);
				}
				if($ammount > 0){
					
					$discountedTotal = $productTotal - $ammount;
					$discountPercentage = 100 - (($discountedTotal/$productTotal)*100);
					$productList = $this->productList();
					if(is_array($productList)){
						for($i = 0; $i < count($productList); $i++){
							$record['productDiscount'] = $db->mySQLSafe($this->productPrice($productList[$i]['productId'],$productList[$i]['productOptions']) * ($discountPercentage/100));
							$newPrice = $this->productPrice($productList[$i]['productId'],$productList[$i]['productOptions']) - ($this->productPrice($productList[$i]['productId'],$productList[$i]['productOptions']) * ($discountPercentage/100));
							//var_dump($newPrice);
							
							$newTax = $this->productTax($productList[$i]['productId'],$newPrice);
							$oldTax = ($productList[$i]['totalTax']/$productList[$i]['productQty']);
							$record['discountTax'] = $db->mySQLSafe($oldTax - $newTax);
							$where = 'id = '.$db->mySQLSafe($productList[$i]['id']);
							$update = $db->update($glob['dbprefix'].'CubeCart_basket',$record,$where);
							if($update==true){
								$applyDiscount = true;
								//var_dump($update);
								//exit;
							}
						}
					}
				}
			}elseif($check[0]['discountType']==2){
				//category discount
				$productList = $this->productList();
				if(is_array($productList)){
					for($i = 0; $i < count($productList); $i++){
						if($this->productWithinCat($productList[$i]['productId'],$check[0]['discountId'])==true){
							if($check[0]['discountFormat']==1){
								//ammount off
								$record['productDiscount'] = $db->mySQLSafe($check[0]['discountAmount']);
								$newPrice = $this->productPrice($productList[$i]['productId'],$productList[$i]['productOptions']) - $check[0]['discountAmount'];
								$newTax = $this->productTax($productList[$i]['productId'],$newPrice);
								$oldTax = ($productList[$i]['totalTax']/$productList[$i]['productQty']);
								$record['discountTax'] = $db->mySQLSafe($oldTax - $newTax);

							}else{
								//percentage off
								$record['productDiscount'] = $db->mySQLSafe($this->productPrice($key[$i]['productId'],$key[$i]['productOptions']) * ($check[0]['discountAmount']/100));
								$newPrice = $this->productPrice($productList[$i]['productId'],$productList[$i]['productOptions']) - $this->productPrice($productList[$i]['productId'],$productList[$i]['productOptions']) * ($check[0]['discountAmount']/100);
								$newTax = $this->productTax($productList[$i]['productId'],$newPrice);
								$oldTax = ($productList[$i]['totalTax']/$productList[$i]['productQty']);
								$record['discountTax'] = $db->mySQLSafe($oldTax - $newTax);
							}
							$where = 'id = '.$db->mySQLSafe($key[$i]['id']);
							$update = $db->update($glob['dbprefix'].'CubeCart_basket',$record,$where);
							if($update==true){
								$applyDiscount = true;
							}
						}
					}
				}
			}elseif($check[0]['discountType']==3){
				//product discount
				$key = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_basket WHERE productId = '.$db->mySQLSafe($check[0]['discountId']));
				if($key==true){
					for($i = 0; $i < count($key); $i++){
						if($check[0]['discountFormat']==1){
							//ammount off
							$record['productDiscount'] = $db->mySQLSafe($check[0]['discountAmount']);
							$newPrice = $this->productPrice($productList[$i]['productId'],$productList[$i]['productOptions']) - $check[0]['discountAmount'];
							$newTax = $this->productTax($productList[$i]['productId'],$newPrice);
							$oldTax = ($productList[$i]['totalTax']/$productList[$i]['productQty']);
							$record['discountTax'] = $db->mySQLSafe($oldTax - $newTax);
						}else{
							//percentage off
							$record['productDiscount'] = $db->mySQLSafe($this->productPrice($key[$i]['productId'],$key[$i]['productOptions']) * ($check[0]['discountAmount']/100));
							$newPrice = $this->productPrice($productList[$i]['productId'],$productList[$i]['productOptions']) - $this->productPrice($productList[$i]['productId'],$productList[$i]['productOptions']) * ($check[0]['discountAmount']/100);
							$newTax = $this->productTax($productList[$i]['productId'],$newPrice);
							$oldTax = ($productList[$i]['totalTax']/$productList[$i]['productQty']);
							$record['discountTax'] = $db->mySQLSafe($oldTax - $newTax);
						}
						$where = 'id = '.$db->mySQLSafe($key[$i]['id']);
						$update = $db->update($glob['dbprefix'].'CubeCart_basket',$record,$where);
						if($update==true){
							$applyDiscount = true;
						}
					}
				}
			}
			unset($record,$where,$update);
			if($ccUserData[0]['discount_group_id'] > 0){
				$groupDiscount = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_adg_groups_mod WHERE group_status = 1 AND group_id = '.$db->mySQLSafe($ccUserData[0]['discount_group_id']));
				if($groupDiscount==true){
					$discountPercentage = $groupDiscount[0]['group_discount'];
					$productList = $this->productList();
					if(is_array($productList)){
						for($i = 0; $i < count($productList); $i++){
							$record['productDiscount'] = $db->mySQLSafe($this->productPrice($productList[$i]['productId'],$productList[$i]['productOptions']) * ($discountPercentage/100));
							$newPrice = $this->productPrice($productList[$i]['productId'],$productList[$i]['productOptions']) - ($this->productPrice($productList[$i]['productId'],$productList[$i]['productOptions']) * ($discountPercentage/100));
							$newTax = $this->productTax($productList[$i]['productId'],$newPrice);
							$oldTax = ($productList[$i]['totalTax']/$productList[$i]['productQty']);
							$record['discountTax'] = $db->mySQLSafe($oldTax - $newTax);
							$where = 'id = '.$db->mySQLSafe($productList[$i]['id']);
							$update = $db->update($glob['dbprefix'].'CubeCart_basket',$record,$where);
							if($update==true){
								$applyDiscount = true;
							}
						}
					}
				}
			}
			$applied = $db->select('SELECT id FROM CubeCart_basket WHERE itemType = "discount" AND sessId = '.$db->mySQLSafe($ccUserData[0]['sessId']).' AND customerIp = '.$db->mySQLsafe($_SERVER['REMOTE_ADDR']));
			if($applyDiscount==true){
				$record['otherData'] = $db->mySQLSafe($code);
				$record['timeAdded'] = $db->mySQLSafe(time());
				$record['sessId'] = $db->mySQLSafe($ccUserData[0]['sessId']);
				$record['customerIp'] = $db->mySQLSafe($_SERVER['REMOTE_ADDR']);
				$record['userAgent'] = $db->mySQLSafe($_SERVER['HTTP_USER_AGENT']);
				$record['startTime'] = $db->mySQLSafe($ccUserData[0]['timeStart']);
				$record['itemType'] = $db->mySQLSafe("discount");
				if($applied==true){
					$where = 'id = '.$db->mySQLSafe($applied[0]['id']);
					$update = $db->update($glob['dbprefix'].'CubeCart_basket',$record,$where);
				}else{
					$update = $db->insert($glob['dbprefix'].'CubeCart_basket',$record);
				}
				if($update==true){
					return $this->getContents();
				}else{
					return $this->removeDiscount();
				}
			}else{
				return $this->removeDiscount();
			}
		}else{
			if($ccUserData[0]['discount_group_id'] > 0){
				$groupDiscount = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_adg_groups_mod WHERE group_status = 1 AND group_id = '.$db->mySQLSafe($ccUserData[0]['discount_group_id']));
				if($groupDiscount==true){
					$discountPercentage = $groupDiscount[0]['group_discount'];
					$productList = $this->productList();
					if(is_array($productList)){
						for($i = 0; $i < count($productList); $i++){
							$record['productDiscount'] = $db->mySQLSafe($this->productPrice($productList[$i]['productId'],$productList[$i]['productOptions']) * ($discountPercentage/100));
							$newPrice = $this->productPrice($productList[$i]['productId'],$productList[$i]['productOptions']) - ($this->productPrice($productList[$i]['productId'],$productList[$i]['productOptions']) * ($discountPercentage/100));
							$newTax = $this->productTax($productList[$i]['productId'],$newPrice);
							$oldTax = ($productList[$i]['totalTax']/$productList[$i]['productQty']);
							$record['discountTax'] = $db->mySQLSafe($oldTax - $newTax);
							$where = 'id = '.$db->mySQLSafe($productList[$i]['id']);
							$update = $db->update($glob['dbprefix'].'CubeCart_basket',$record,$where);
							if($update==true){
								$applyDiscount = true;
							}
						}
					}
				}
				$applied = $db->select('SELECT id FROM CubeCart_basket WHERE itemType = "discount" AND sessId = '.$db->mySQLSafe($ccUserData[0]['sessId']).' AND customerIp = '.$db->mySQLsafe($_SERVER['REMOTE_ADDR']));
				if($applyDiscount==true){
					$record['otherData'] = $db->mySQLSafe($code);
					$record['timeAdded'] = $db->mySQLSafe(time());
					$record['sessId'] = $db->mySQLSafe($ccUserData[0]['sessId']);
					$record['customerIp'] = $db->mySQLSafe($_SERVER['REMOTE_ADDR']);
					$record['userAgent'] = $db->mySQLSafe($_SERVER['HTTP_USER_AGENT']);
					$record['startTime'] = $db->mySQLSafe($ccUserData[0]['timeStart']);
					$record['itemType'] = $db->mySQLSafe("discount");
					if($applied==true){
						$where = 'id = '.$db->mySQLSafe($applied[0]['id']);
						$update = $db->update($glob['dbprefix'].'CubeCart_basket',$record,$where);
					}else{
						$update = $db->insert($glob['dbprefix'].'CubeCart_basket',$record);
					}
					if($update==true){
						return $this->getContents();
					}else{
						return $this->removeDiscount();
					}
				}else{
					return $this->removeDiscount();
				}
			}else{
				return $this->removeDiscount();
			}
		}
	}
	//discount total
	function discountTotal(){
		global $db, $glob, $ccUserData;
		$count = $db->select('SELECT SUM(productDiscount * productQty) AS totalDiscount FROM '.$glob['dbprefix'].'CubeCart_basket WHERE sessId = '.$db->mySQLSafe($ccUserData[0]['sessId']).' AND customerIp = '.$db->mySQLsafe($_SERVER['REMOTE_ADDR']));
		
		
		if($ccUserData[0]['tax'] == 1){//no tax
				return $count[0]['totalDiscount'];
			
		}
		if($ccUserData[0]['tax'] == 2){//with tax
				return $count[0]['totalDiscount']+($count[0]['totalDiscount']*0.2);
			
		}
		
		
		
		
		
		
		
		
		return $count[0]['totalDiscount'];
	}
	//subcategories
	function allSubcats($catId,$catArray=array()){
		global $db, $glob, $ccUserData;
		$catArray[] = $db->mySQLSafe($catId);
		$cats = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_category WHERE cat_father_id = '.$db->mySQLSafe($catId));
		if($cats==true){
			for($i = 0; $i < count($cats); $i++){
				$catArray = array_merge($catArray,$this->allSubcats($cats[$i]['cat_id']));
			}
		}
		return $catArray;
	}
	//product categories
	function productCats($productId){
		global $db, $glob, $ccUserData;
		$catArray = array();
		$idx = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_cats_idx WHERE productId = '.$db->mySQLSafe($productId));
		if($idx==true){
			for($i = 0; $i < count($idx); $i++){
				$catArray[] = $idx[$i]['cat_id'];
			}
		}
		return $catArray;
	}
	//check if product listed withing category array
	function productWithinCat($productId,$catId){
		global $db, $glob, $ccUserData;
		$catsArray = $this->allSubcats($catId);
		$productCats = $this->productCats($productId);
		$inarray = false;
		if(is_array($catsArray) && is_array($productCats) && !empty($catsArray) && !empty($productCats)){
			for($i = 0; $i < count($productCats); $i++){
				if(in_array($productCats[$i],$catsArray)){
					$inarray = true;
				}
			}
			return $inarray;
		}else{
			return false;
		}
	}
	//get discount code;
	function getDiscountCode(){
		global $db, $glob, $ccUserData;
		$applied = $db->select('SELECT otherData FROM CubeCart_basket WHERE itemType = "discount" AND sessId = '.$db->mySQLSafe($ccUserData[0]['sessId']).' AND customerIp = '.$db->mySQLsafe($_SERVER['REMOTE_ADDR']));
		if($applied==true){
			return $applied[0]['otherData'];
		}else{
			return false;
		}
	}
	//sets invoice address variables
	function setInvAddr($data){
		global $db, $glob, $ccUserData;
		$record['otherData'] = $db->mySQLSafe(serialize($this->encript($data)));
		$record['timeAdded'] = $db->mySQLSafe(time());
		$record['sessId'] = $db->mySQLSafe($ccUserData[0]['sessId']);
		$record['customerIp'] = $db->mySQLSafe($_SERVER['REMOTE_ADDR']);
		$record['userAgent'] = $db->mySQLSafe($_SERVER['HTTP_USER_AGENT']);
		$record['startTime'] = $db->mySQLSafe($ccUserData[0]['timeStart']);
		$record['itemType'] = $db->mySQLSafe('invAddr');
		$check = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_basket WHERE itemType = "invAddr" AND sessId = '.$db->mySQLSafe($ccUserData[0]['sessId']).' AND customerIp = '.$db->mySQLsafe($_SERVER['REMOTE_ADDR']));
		if($check==true){
			$where = 'id = '.$db->mySQLSafe($check[0]['id']);
			$update = $db->update($glob['dbprefix'].'CubeCart_basket',$record,$where);
		}else{
			$update = $db->insert($glob['dbprefix'].'CubeCart_basket',$record);
		}
		if($update==true){
			return true;
		}else{
			$this->logError('Unable to set invoice address');
			return false;
		}
	}
	//get invoice address variable
	function getInvAddr(){
		global $db, $glob, $ccUserData;
		$check = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_basket WHERE itemType = "invAddr" AND sessId = '.$db->mySQLSafe($ccUserData[0]['sessId']).' AND customerIp = '.$db->mySQLsafe($_SERVER['REMOTE_ADDR']));
		if($check==true){
			$data['invInf'] = $this->decript(unserialize($check[0]['otherData']));
			return $data;
		}else{
			return false;
		}
	}
	//destroy invoice address
	function varDestroyInvAdr($varName){
		global $db, $glob, $ccUserData;
		$where = 'itemType = "invAddr" AND sessId = '.$db->mySQLSafe($ccUserData[0]['sessId']).' AND customerIp = '.$db->mySQLsafe($_SERVER['REMOTE_ADDR']);
		$delete = $db->delete($glob['dbprefix'].'CubeCart_basket',$where,1);
		if($delete==true){
			return true;
		}else{
			$this->logError('Unable to remove invoice address');
			return false;
		}
	}
	//sets delivery address variables
	function setDelAddr($data){
		global $db, $glob, $ccUserData;
		$record['otherData'] = $db->mySQLSafe(serialize($this->encript($data)));
		$record['timeAdded'] = $db->mySQLSafe(time());
		$record['sessId'] = $db->mySQLSafe($ccUserData[0]['sessId']);
		$record['customerIp'] = $db->mySQLSafe($_SERVER['REMOTE_ADDR']);
		$record['userAgent'] = $db->mySQLSafe($_SERVER['HTTP_USER_AGENT']);
		$record['startTime'] = $db->mySQLSafe($ccUserData[0]['timeStart']);
		$record['itemType'] = $db->mySQLSafe('dellAddr');
		$check = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_basket WHERE itemType = "dellAddr" AND sessId = '.$db->mySQLSafe($ccUserData[0]['sessId']).' AND customerIp = '.$db->mySQLsafe($_SERVER['REMOTE_ADDR']));
		if($check==true){
			$where = 'id = '.$db->mySQLSafe($check[0]['id']);
			$update = $db->update($glob['dbprefix'].'CubeCart_basket',$record,$where);
		}else{
			$update = $db->insert($glob['dbprefix'].'CubeCart_basket',$record);
		}
		if($update==true){
			return true;
		}else{
			$this->logError('Unable to set delivery address');
			return false;
		}
	}
	//get delivery address variable
	function getDelAddr(){
		global $db, $glob, $ccUserData;
		$check = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_basket WHERE itemType = "dellAddr" AND sessId = '.$db->mySQLSafe($ccUserData[0]['sessId']).' AND customerIp = '.$db->mySQLsafe($_SERVER['REMOTE_ADDR']));
		if($check==true){
			$data['delInf'] = $this->decript(unserialize($check[0]['otherData']));
			return $data;
		}else{
			return false;
		}
	}
	//destroy delivery address
	function varDestroyDelAddr($varName){
		global $db, $glob, $ccUserData;
		$where = 'itemType = "dellAddr" AND sessId = '.$db->mySQLSafe($ccUserData[0]['sessId']).' AND customerIp = '.$db->mySQLsafe($_SERVER['REMOTE_ADDR']);
		$delete = $db->delete($glob['dbprefix'].'CubeCart_basket',$where,1);
		if($delete==true){
			return true;
		}else{
			$this->logError('Unable to remove delivery address');
			return false;
		}
	}
	//get stock level
	function getStock($productId,$options=array()){
		global $db, $glob, $ccUserData;
		if(empty($options)){
			$product = $db->select('SELECT stock_level FROM '.$glob['dbprefix'].'CubeCart_inventory WHERE productId = '.$db->mySQLSafe($productId));
			if($product==true){
				return $product[0]['stock_level'];
			}else{
				return false;
			}
		}else{
			if(!empty($options) && !is_array($options)){
				$options = explode('|',$options);
			}
			for($i = 0; $i < count($options); $i++){
				if(!empty($options[$i])){
					if(isset($where)){
						$where .= ' AND options LIKE "%'.$db->mySQLSafe($options[$i],null).'%"';
					}else{
						$where .= ' options LIKE "%'.$db->mySQLSafe($options[$i],null).'%"';
					}
				}
			}
			if(!empty($where)){
				$data = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_options_stock WHERE '.$where);
				if($data==true){
					return $data[0]['stock'];
				}
			}else{
				return false;
			}
		}
	}
	//set payment gateway variable
	function setGateway($gateWay){
		global $db, $glob, $ccUserData;
		$record['otherData'] = $db->mySQLSafe(serialize($gateWay));
		$record['timeAdded'] = $db->mySQLSafe(time());
		$record['sessId'] = $db->mySQLSafe($ccUserData[0]['sessId']);
		$record['customerIp'] = $db->mySQLSafe($_SERVER['REMOTE_ADDR']);
		$record['userAgent'] = $db->mySQLSafe($_SERVER['HTTP_USER_AGENT']);
		$record['startTime'] = $db->mySQLSafe($ccUserData[0]['timeStart']);
		$record['itemType'] = $db->mySQLSafe('gateway');
		$check = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_basket WHERE itemType = "gateway" AND sessId = '.$db->mySQLSafe($ccUserData[0]['sessId']).' AND customerIp = '.$db->mySQLsafe($_SERVER['REMOTE_ADDR']));
		if($check==true){
			$where = 'id = '.$db->mySQLSafe($check[0]['id']);
			$update = $db->update($glob['dbprefix'].'CubeCart_basket',$record,$where);
		}else{
			$update = $db->insert($glob['dbprefix'].'CubeCart_basket',$record);
			if($update==false){
				$this->logError('Unable to set gateway');
			}
		}
		if($update==true){
			return true;
		}else{
			return false;
		}
	}
	//get payment gateway variable value
	function getGateway(){
		global $db, $glob, $ccUserData;
		$check = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_basket WHERE itemType = "gateway" AND sessId = '.$db->mySQLSafe($ccUserData[0]['sessId']).' AND customerIp = '.$db->mySQLsafe($_SERVER['REMOTE_ADDR']));
		if($check==true){
			return unserialize($check[0]['otherData']);
		}else{
			return false;
		}
	}
	//validate basket, check if all required information is present
	function validateBasket(){
		global $db, $glob, $ccUserData;
		$cartValid = true;
		$products = $db->numrows('SELECT id FROM '.$glob['dbprefix'].'CubeCart_basket WHERE itemType = "product" AND sessId = '.$db->mySQLSafe($ccUserData[0]['sessId']).' AND customerIp = '.$db->mySQLsafe($_SERVER['REMOTE_ADDR']));
		$shipping = $db->numrows('SELECT id FROM '.$glob['dbprefix'].'CubeCart_basket WHERE itemType = "shipping" AND sessId = '.$db->mySQLSafe($ccUserData[0]['sessId']).' AND customerIp = '.$db->mySQLsafe($_SERVER['REMOTE_ADDR']));
		$invAddr = $db->numrows('SELECT id FROM '.$glob['dbprefix'].'CubeCart_basket WHERE itemType = "invAddr" AND sessId = '.$db->mySQLSafe($ccUserData[0]['sessId']).' AND customerIp = '.$db->mySQLsafe($_SERVER['REMOTE_ADDR']));
		$dellAddr = $db->numrows('SELECT id FROM '.$glob['dbprefix'].'CubeCart_basket WHERE itemType = "dellAddr" AND sessId = '.$db->mySQLSafe($ccUserData[0]['sessId']).' AND customerIp = '.$db->mySQLsafe($_SERVER['REMOTE_ADDR']));
		$gateway = $db->numrows('SELECT id FROM '.$glob['dbprefix'].'CubeCart_basket WHERE itemType = "gateway" AND sessId = '.$db->mySQLSafe($ccUserData[0]['sessId']).' AND customerIp = '.$db->mySQLsafe($_SERVER['REMOTE_ADDR']));
		if($products < 1 || $this->productList()==false){
			$cartValid = false;
			$this->logError('Missing Product List');
		}
		if($shipping < 1 || $this->getShipping()==false){
			$cartValid = false;
			$this->logError('Missing Shipping');
		}
		if($invAddr < 1 || $this->getInvAddr()==false){
			$cartValid = false;
			$this->logError('Missing Invoice Address');
		}
		if($dellAddr < 1 || $this->getDelAddr()==false){
			$cartValid = false;
			$this->logError('Missing Delivery Address');
		}
		if($gateway < 1 || $this->getGateway()==false){
			$cartValid = false;
			$this->logError('Missing Gateway');
		}
		return $cartValid;
	}
	//set cart order id
	function setOrderId($orderId){
		global $db, $glob, $ccUserData;
		$record['otherData'] = $db->mySQLSafe(serialize($orderId));
		$record['timeAdded'] = $db->mySQLSafe(time());
		$record['sessId'] = $db->mySQLSafe($ccUserData[0]['sessId']);
		$record['customerIp'] = $db->mySQLSafe($_SERVER['REMOTE_ADDR']);
		$record['userAgent'] = $db->mySQLSafe($_SERVER['HTTP_USER_AGENT']);
		$record['startTime'] = $db->mySQLSafe($ccUserData[0]['timeStart']);
		$record['itemType'] = $db->mySQLSafe('cart_order_id');
		$check = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_basket WHERE itemType = "cart_order_id" AND sessId = '.$db->mySQLSafe($ccUserData[0]['sessId']).' AND customerIp = '.$db->mySQLsafe($_SERVER['REMOTE_ADDR']));
		if($check==true){
			$where = 'id = '.$db->mySQLSafe($check[0]['id']);
			$update = $db->update($glob['dbprefix'].'CubeCart_basket',$record,$where);
		}else{
			$update = $db->insert($glob['dbprefix'].'CubeCart_basket',$record);
		}
		if($update==true){
			return true;
		}else{
			$this->logError('Order ID Not Set');
			return false;
		}
	}
	//get cart order id
	function getOrderId(){
		global $db, $glob, $ccUserData;
		$check = $db->select('SELECT * FROM '.$glob['dbprefix'].'CubeCart_basket WHERE itemType = "cart_order_id" AND sessId = '.$db->mySQLSafe($ccUserData[0]['sessId']).' AND customerIp = '.$db->mySQLsafe($_SERVER['REMOTE_ADDR']));
		if($check==true){
			return unserialize($check[0]['otherData']);
		}else{
			return false;
		}
	}
	//get total weight
	function getTotalWeight(){
		global $db, $glob, $ccUserData;
		$check = $db->select('SELECT SUM(totalWeight) AS basketWeight FROM '.$glob['dbprefix'].'CubeCart_basket WHERE itemType = "product" AND sessId = '.$db->mySQLSafe($ccUserData[0]['sessId']).' AND customerIp = '.$db->mySQLsafe($_SERVER['REMOTE_ADDR']));
		if($check==true){
			return $check[0]['basketWeight'];
		}else{
			return false;
		}
	}
	function logError($text){
		global $db, $glob, $ccUserData;
		$record['orderId'] = $db->mySQLSafe($this->getOrderId());
		$record['errorText'] = $db->mySQLSafe($text."\r\n".' Current session data : '.$_SESSION['ccUser']);
		$record['errorTime'] = $db->mySQLSafe(time());
		$record['errorBrowser'] = $db->mySQLSafe($_SERVER['HTTP_USER_AGENT']);
		$record['errorIp'] = $db->mySQLSafe($_SERVER['REMOTE_ADDR']);
		$repoortexists = $db->select('SELECT id FROM '.$glob['dbprefix'].'cart_error_log WHERE errorBrowser = '.$db->mySQLSafe($_SERVER['HTTP_USER_AGENT']).' AND errorIp = '.$db->mySQLSafe($_SERVER['REMOTE_ADDR']).' AND errorText = '.$db->mySQLSafe($text));
		if($repoortexists==true){
			$where = 'id = '.$db->mySQLSafe($repoortexists[0]['id']);
			$insert = $db->update($glob['dbprefix'].'cart_error_log',$record,$where);
		}else{
			$insert = $db->insert($glob['dbprefix'].'cart_error_log',$record);
		}
		if($insert==true){
			return true;
		}else{
			return false;
		}
	}
	function encript($data){
		if(is_array($data)){
			$keys = array_keys($data);
			if(is_array($keys) && !empty($keys)){
				for($i = 0; $i < count($keys); $i++){
					$encoded[base64_encode($keys[$i])] = base64_encode($this->treatGet($data[$keys[$i]]));
				}
			}
		}else{
			$encoded = base64_encode($data);
		}
		return $encoded;
	}
	function decript($data){
		if(is_array($data)){
			$keys = array_keys($data);
			if(is_array($keys) && !empty($keys)){
				for($i = 0; $i < count($keys); $i++){
					$decoded[base64_decode($keys[$i])] = htmlspecialchars_decode(base64_decode($data[$keys[$i]]));
				}
			}
		}else{
			$decoded = base64_decode($data);
		}
		return $decoded;
	}
	function treatGet($text){
		$text = htmlspecialchars($text);
		return $text;
	}
}
?>