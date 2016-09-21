<?php
$module = fetchDbConfig("By_Price");
if($module['status']==1){
	$i=0;
	while((isset($module['level_'.$i]) && $module['level_'.$i]!='') || $i==0){ 
		if($subTotal<=$module['level_'.$i]){
			$sum = $module['amount_'.$i];
			$shippingPrice .= "<option value='".$shipKey."'";
				if($shipKey ==$basket['shipKey']){
					$shippingPrice .= " selected='selected'";
					$basket = $cart->setVar($lang['misc']['freeForOrdOver']." ".$module['level_'.$i],"shipMethod");
					$basket = $cart->setVar(sprintf("%.2f",$sum),"shipCost");
				}
			$shippingPrice .= ">".priceFormat($sum)."</option>\r\n";
			$shippingAvailable = TRUE;
			$shipKey++;
			break;
		}
		$i++;
	}
	if($shippingAvailable!=true){
		$sum = 0.00;
		$shippingPrice .= "<option value='".$shipKey."'";
			if($shipKey == $basket['shipKey']){
				$shippingPrice .= " selected='selected'";
				$basket = $cart->setVar($lang['misc']['freeForOrdOver']." ".$module['level_'.$i],"shipMethod");
				$basket = $cart->setVar(sprintf("%.2f",$sum),"shipCost");
			}
		$shippingPrice .= ">".$lang['misc']['freeShipping']."</option>\r\n";
		$shippingAvailable = TRUE;
		$shipKey++;
	}
	unset($module, $taxVal);
}
?>