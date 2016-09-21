<?php
phpExtension();
$box_content = new XTemplate ("skins/".$config['skinDir']."/styleTemplates/boxes/prouctModuleSide.tpl");

// START HOMPAGE PRODUCT MODULES
//check which poduct modules to use and count how many are in use
$modCount=0;
$latest=0;
$featured=0;
$popular=0;
$sale=0;
$bestseller=0;
if($config['latestProdsSide']==1){$latest=1; $modCount=$modCount+1;}
if($config['featuredProdsSide']==1){$featured=1; $modCount=$modCount+1;}
if($config['popularProdsSide']==1){$popular=1; $modCount=$modCount+1;}
if($config['saleProdsSide']==1){$sale=1; $modCount=$modCount+1;}
if($config['bestsellerProdsSide']==1){$bestseller=1; $modCount=$modCount+1;}

//repeat output for each module
if($modCount>0){
	for($m=0; $m<$modCount; $m++){
		//get title, db column name and number of products
		if($latest==1){
			$numProds=$config['noLatestProds']; 
			$module=$latest; 
			$latest=0; 
			$box_content->assign("PROD_MOD_TITLE",'Latest Products');
			$products = $db->select("SELECT * FROM ".$glob['dbprefix']."CubeCart_inventory WHERE showProd = 1 AND latest = 1 ORDER BY lastModified DESC LIMIT ".$numProds);
		}elseif($featured==1){
			$numProds=$config['noFeaturedProds']; 
			$module=$featured; 
			$featured=0; 
			$box_content->assign("PROD_MOD_TITLE",'Featured Products');
			$products = $db->select("SELECT * FROM ".$glob['dbprefix']."CubeCart_inventory WHERE showProd = 1 AND featured = 1 ORDER BY name ASC LIMIT ".$numProds);
		}elseif($popular==1){
			$numProds=$config['noFeaturedProds']; 
			$module=$popular; 
			$popular=0; 
			$box_content->assign("PROD_MOD_TITLE",'Popular Products');
			$products = $db->select("SELECT * FROM ".$glob['dbprefix']."CubeCart_inventory WHERE showProd = 1 AND popular = 1 ORDER BY popularity DESC LIMIT ".$numProds);
		}elseif($sale==1){
			$numProds=$config['noSaleProds']; 
			$module=$sale; 
			$sale=0; 
			$box_content->assign("PROD_MOD_TITLE",'Sale Products');
			$products = $db->select("SELECT * FROM ".$glob['dbprefix']."CubeCart_inventory WHERE showProd = 1 AND sale = 1 AND sale_price > 0 ORDER BY name ASC LIMIT ".$numProds);
		}elseif($bestseller==1){
			$numProds=$config['noBestsellerProds']; 
			$module=$bestseller; 
			$bestseller=0; 
			$box_content->assign("PROD_MOD_TITLE",'Best Seller Products');
			$products = $db->select("SELECT * FROM ".$glob['dbprefix']."CubeCart_inventory WHERE showProd = 1 AND bestseller = 1 ORDER BY salesCount DESC LIMIT ".$numProds);
		}
		
		if($products==TRUE){
			for($i=0;$i<count($products);$i++){
				//get trade or standard prices
				if($ccUserData[0]['customer_id'] > 0 && $ccUserData[0]['trade'] == 1){
					$price=$products[$i]['tradePrice'];
					$sale_price=$products[$i]['tradeSale'];
				}else{
					$price=$products[$i]['price'];
					$sale_price=$products[$i]['sale_price'];
				}
				
				if(($val = prodAltLang($products[$i]['productId'])) == TRUE){
						$products[$i]['name'] = $val['name'];
				}
		
				if(file_exists($GLOBALS['rootDir']."/images/uploads/thumbs/thumb_".$products[$i]['image'])){
					$box_content->assign("VAL_IMG_SRC",$GLOBALS['rootRel']."images/uploads/thumbs/thumb_".$products[$i]['image']);
				} else {
					$box_content->assign("VAL_IMG_SRC",$GLOBALS['rootRel']."skins/".$config['skinDir']."/styleImages/thumb_nophoto.gif");
				}
		
				if(salePrice($products[$i]['price'], $products[$i]['sale_price'])==FALSE){
						$box_content->assign("TXT_PRICE",priceFormat($price));
						$box_content->assign("TXT_SALE_PRICE", "");
				} else {
						$box_content->assign("TXT_PRICE","<span class='txtOldPrice'>".priceFormat($price)."</span>");
						$salePrice = salePrice($price, $sale_price);
						$box_content->assign("TXT_SALE_PRICE", priceFormat($salePrice));
				}
			
				$box_content->assign("VAL_WIDTH", $config['gdthumbSize']);
				if($config['outofstockPurchase']==1){
					$box_content->assign("BTN_BUY",$lang['front']['viewCat']['buy']);
					$box_content->assign("PRODUCT_ID",$products[$i]['productId']);
					$box_content->parse("sideProds.prod_mod.module.prods.buy_btn");
				} elseif($products[$i]['useStockLevel']==1 && $products[$i]['stock_level']>0){
					$box_content->assign("BTN_BUY",$lang['front']['viewCat']['buy']);
					$box_content->assign("PRODUCT_ID",$products[$i]['productId']);
					$box_content->parse("sideProds.prod_mod.module.prods.buy_btn");
				} elseif($products[$i]['useStockLevel']==0){
					$box_content->assign("BTN_BUY",$lang['front']['viewCat']['buy']);
					$box_content->assign("PRODUCT_ID",$products[$i]['productId']);
					$box_content->parse("sideProds.prod_mod.module.prods.buy_btn");
				}
				$box_content->assign("BTN_MORE",$lang['front']['viewCat']['more']);
				$box_content->assign("VAL_PRODUCT_ID",$products[$i]['productId']);
				$box_content->assign("TXT_DESC",substr(strip_tags($products[$i]['short_description']),0,$config['productPrecis'])."&hellip;");
				$box_content->assign("VAL_PRODUCT_NAME",validHTML($products[$i]['name']));
				$box_content->parse("sideProds.prod_mod.module.prods");
			}	
		}
		$box_content->parse("sideProds.prod_mod.module");
	}
	$box_content->parse("sideProds.prod_mod");
}
// END HOMEPAGE PRODUCTS MODUILE

$box_content->parse("sideProds");

$box_content = $box_content->text("sideProds");

?>