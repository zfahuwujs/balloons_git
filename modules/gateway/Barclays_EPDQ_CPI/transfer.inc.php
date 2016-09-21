<?php
/*
+--------------------------------------------------------------------------
|   CubeCart v3
|   ========================================
|   by Alistair Brookbanks
|	CubeCart is a Trade Mark of Devellion Limited
|   Copyright Devellion Limited 2005 - 2006. All rights reserved.
|   Devellion Limited,
|   5 Bridge Street,
|   Bishops Stortford,
|   HERTFORDSHIRE.
|   CM23 2JU
|   UNITED KINGDOM
|   http://www.devellion.com
|	UK Private Limited Company No. 5323904
|   ========================================
|   Web: http://www.cubecart.com
|   Date: Tuesday, 17th July 2007
|   Email: sales (at) cubecart (dot) com
|	License Type: CubeCart is NOT Open Source Software and Limitations Apply 
|   Licence Info: http://www.cubecart.com/site/faq/license.php
+--------------------------------------------------------------------------
|	transfer.php
|   ========================================
|	Core functions for the Barclays EPDQ CPI Gateway	
+--------------------------------------------------------------------------
*/

/*
//////////////////////////
// IN THE REPEATED REGION
//////
$orderInv['productId']						- product id as an integer
$orderInv['name']							- product name as a varchar
$orderInv['price']							- price of each product (inc options)
$orderInv['quantity']						- quantity of products as an integer
$orderInv['product_options']				- products attributes as test
$orderInv['productCode']					- product code as a varchar
$i											- This is the current incremented integer starting at 0

/////////////////////////
// FIXED VARS
///////
$cart_order_id							- cart order id as a varchar
$ccUserData[0]['email']						- Customers email address
$ccUserData[0]['title']						- Customers title (Mr Miss etc...)
$ccUserData[0]['firstName']					- Customers first name
$ccUserData[0]['lastName']					- Customers last name 
$ccUserData[0]['add_1']						- Invoice Address line 1
$ccUserData[0]['add_2']						- Invoice Address line 1
$ccUserData[0]['town']						- Invoice Town or city
$ccUserData[0]['county']					- Invoice County or state
$ccUserData[0]['postcode']					- Invoice Post/Zip Code
$ccUserData[0]['country']					- Invoice country Id we can look up the country name like this
										countryName($ccUserData[0]['country']);
$ccUserData[0]['phone']						- Contact phone no
$ccUserData[0]['mobile']					- Mobile/Cell phone number

$basket['delInf']['title']				- Delivery title (Mr Miss etc...)
$basket['delInf']['firstName']			- Delivery customers first name
$basket['delInf']['lastName']			- Delivery customers last name 
$basket['delInf']['add_1']				- Delivery Address line 1
$basket['delInf']['add_2']				- Delivery Address line 1
$basket['delInf']['town']				- Delivery Town or city
$basket['delInf']['county']				- Delivery County or state
$basket['delInf']['postcode']			- Delivery Post/Zip Code
$basket['delInf']['country']			- Delivery  country Id we can look up the country name like this	
									countryName($basket['delInf']['country']);


$basket['subTotal'] 					- Order Subtotal (exTax and Shipping)
$basket['grandTotal']					- Basket total which has to be paid (inc Tax and Shipping).
$basket['tax']							- Total tax to pay
$basket['shipCost']						- Shipping price
////////////////////////////////////////////////////////
*/

$module = fetchDbConfig("Barclays_EPDQ_CPI");

#the following function performs a HTTP Post and returns the whole response
function pullpage( $host, $usepath, $postdata = "" ) {

# open socket to filehandle(epdq encryption cgi)
 $fp = fsockopen( $host, 80, $errno, $errstr, 60 );

#check that the socket has been opened successfully
 if( !$fp ) {
    print "$errstr ($errno)<br>\n";
 }
 else {

    #write the data to the encryption cgi
    fputs( $fp, "POST $usepath HTTP/1.0\n");
    $strlength = strlen( $postdata );
    fputs( $fp, "Content-type: application/x-www-form-urlencoded\n" );
    fputs( $fp, "Content-length: ".$strlength."\n\n" );
    fputs( $fp, $postdata."\n\n" );

    #clear the response data
   $output = "";


    #read the response from the remote cgi
    #while content exists, keep retrieving document in 1K chunks
    while( !feof( $fp ) ) {
        $output .= fgets( $fp, 1024);
    }

    #close the socket connection
    fclose( $fp);
 }

#return the response
 return $output;
}

#define the remote cgi in readiness to call pullpage function 
$server="secure2.epdq.co.uk";
$url="/cgi-bin/CcxBarclaysEpdqEncTool.e";

$currency['GBP']="826";
$currency['EUR']="978";
$currency['USD']="840";

#the following parameters have been obtained earlier in the merchant's webstore
#clientid, passphrase, oid, currencycode, total
$params="clientid=".$module['clientid'];
$params.="&password=".$module['passphrase'];
$params.="&oid=".$cart_order_id;
$params.="&chargetype=Auth";
$params.="&amp;currencycode=".$currency[$cCode];
$params.="&total=".$basket['grandTotal'];

#perform the HTTP Post
$response = pullpage( $server,$url,$params );

#split the response into separate lines
$response_lines=explode("\n",$response);

#for each line in the response check for the presence of the string 'epdqdata'
#this line contains the encrypted string
$response_line_count=count($response_lines);


for ($i=0;$i<$response_line_count;$i++){

    if (preg_match('/epdqdata/',$response_lines[$i])){
        $strEPDQ=$response_lines[$i];
    }
}


function repeatVars(){

		return FALSE;
	
}

function fixedVars(){
	
	global $glob,$module, $basket, $ccUserData, $cart_order_id, $config, $GLOBALS,$strEPDQ;

	$hiddenVars =  "<input type='hidden' name='returnurl' value='".$glob['storeURL']."/confirmed.php'>";
	$hiddenVars .= "<input type='hidden' name='product[]' value='' />";
	$hiddenVars .= "<input type='hidden' name='merchantdisplayname' value='".$module['display']."'>";
	$hiddenVars .= "<input type='hidden' name='tax' value='".$basket['tax']."' />";
	$hiddenVars .= "<input type='hidden' name='shipping' value='".$basket['shipCost']."' />";
	$hiddenVars .= "<input type='hidden' name='baddr1' value='".$ccUserData[0]['add_1']	."' />";
	$hiddenVars .= "<input type='hidden' name='baddr2' value='".$ccUserData[0]['add_2']	."' />";
	$hiddenVars .= "<input type='hidden' name='bcity' value='".$ccUserData[0]['town']."' />";
	$hiddenVars .= "<input type='hidden' name='bcountyprovince' value='".$ccUserData[0]['county']."' />";
	$hiddenVars .= "<input type='hidden' name='bcountry' value='826' />";
	$hiddenVars .= "<input type='hidden' name='bpostalcode' value='".$ccUserData[0]['postcode']."' />";
	$hiddenVars .= "<input type='hidden' name='btelephonenumber' value='".$ccUserData[0]['phone']."' />";
	$hiddenVars .= "<input type='hidden' name='email' value='".$ccUserData[0]['email']."' />";
	$hiddenVars .= "<input type='hidden' name='saddr1' value='".$basket['delInf']['add_1']."' />";
	$hiddenVars .= "<input type='hidden' name='saddr2' value='".$basket['delInf']['add_2']."' />";
	$hiddenVars .= "<input type='hidden' name='scity' value='".$basket['delInf']['town']."' />";
	$hiddenVars .= "<input type='hidden' name='scountyprovince' value='".$basket['delInf']['county']."' />";
	$hiddenVars .= "<input type='hidden' name='scountry' value='826' />";
	$hiddenVars .= "<input type='hidden' name='spostalcode' value='".$basket['delInf']['postcode']."' />";
	$hiddenVars .= $strEPDQ;

	return $hiddenVars;
	
}

function success(){
	global $db, $glob, $module, $basket;
	

		$result = $db->select("SELECT status  FROM ".$glob['dbprefix']."CubeCart_order_sum WHERE cart_order_id = ".$db->mySQLSafe($_GET['oid']) );
		
		if($result[0]['status']==2){
			return TRUE;
		} else {
			return FALSE;
		}
	
}

///////////////////////////
// Other Vars
////////

	$formAction = "https://secure2.epdq.co.uk/cgi-bin/CcxBarclaysEpdq.e";
	$formMethod = "post";
	$formTarget = "_self";

	$transfer = "auto";
	$stateUpdate = TRUE;

?>
