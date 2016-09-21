<?php
/*-----------------------------------------------------------------------------
 * Customer Order Confirmation Email
 *-----------------------------------------------------------------------------
 * customer_order_conf_email.gateway2.inc.php
 *-----------------------------------------------------------------------------
 * Author:   Estelle Winterflood
 * Email:    cubecart@expandingbrain.com
 * Store:    http://cubecart.expandingbrain.com
 *
 * Date:     January 23, 2007
 * Updated:  November 01, 2007
 * For CubeCart Version:  3.0.x
 *-----------------------------------------------------------------------------
 * DISCLAIMER:
 * The modification is provided on an "AS IS" basis, without warranty of
 * any kind, including without limitation the warranties of merchantability,
 * fitness for a particular purpose and non-infringement. The entire risk
 * as to the quality and performance of the Software is borne by you.
 * Should the modification prove defective, you and not the author assume 
 * the entire cost of any service and repair. 
 *-----------------------------------------------------------------------------
 */


// uses: $basket['mailSent'], $basket['mailResend']
// may send/resend email to customer

phpExtension();

$config_name = "Customer_Order_Conf_Email";
$coce_mod = fetchDbConfig($config_name);

if ($coce_mod && $coce_mod['status'])
{
	$email_gateway = 0;

	if ($coce_mod['gateway_1'] == $_POST['gateway']) {
		$email_gateway = 1;
	} elseif ($coce_mod['gateway_2'] == $_POST['gateway']) {
		$email_gateway = 2;
	} elseif ($coce_mod['gateway_3'] == $_POST['gateway']) {
		$email_gateway = 3;
	}

	if ($basket['mailSent']==0) {
		$email_format_string = $lang['front']['gateway']['order_email_body_1a'];
	} elseif ($basket['mailResend']==1) {
		$email_format_string = $lang['front']['gateway']['order_email_body_1a_updated_order'];
		$basket = $cart->setVar(0,"mailResend");
	}

	if ($email_gateway > 0)
	{
		include_once("classes/htmlMimeMail.php");
		$mail = new htmlMimeMail();

		$text_cust = sprintf($email_format_string,
					$ccUserData[0]['title']." ".$ccUserData[0]['firstName']." ".$ccUserData[0]['lastName'],
					$cart_order_id,
					formatTime(time()));

		if (!empty($lang['front']['gateway']['order_email_gateway_'.$email_gateway.'_instructions'])) {
			$text_cust .= $lang['front']['gateway']['order_email_gateway_'.$email_gateway.'_instructions'];
		}

		$text_cust .= sprintf($lang['front']['gateway']['order_email_body_1b'],
					$ccUserData[0]['title']." ".$ccUserData[0]['firstName']." ".$ccUserData[0]['lastName'],
					priceFormat($basket['subTotal']));

		$coupon_module = fetchDbConfig("Coupon_Manager");
		if ($coupon_module && $coupon_module['status']) {
			$text_cust .= sprintf($lang['front']['gateway']['order_email_body_1c'], priceFormat($basket['coupon_savings']));
		}

		$giftcard_module = fetchDbConfig("Gift_Card_Manager");
		if ($giftcard_module && $giftcard_module['status']) {
			$text_cust .= sprintf($lang['front']['gateway']['order_email_body_1d'], priceFormat($basket['giftcard_savings']));
		}

		$text_cust .= sprintf($lang['front']['gateway']['order_email_body_1e'],
					priceFormat($basket['shipCost'],true),
					priceFormat($basket['tax']),
					priceFormat($basket['grandTotal']),
					$ccUserData[0]['title']." ".$ccUserData[0]['firstName']." ".$ccUserData[0]['lastName'],
					$ccUserData[0]['add_1'],
					$ccUserData[0]['add_2'],
					$ccUserData[0]['town'],
					$ccUserData[0]['county'],
					$ccUserData[0]['postcode'],
					countryName($ccUserData[0]['country']),
					$basket['delInf']['title']." ".$basket['delInf']['firstName']." ".$basket['delInf']['lastName'],
					$basket['delInf']['add_1'],
					$basket['delInf']['add_2'],
					$basket['delInf']['town'],
					$basket['delInf']['county'],
					$basket['delInf']['postcode'],
					countryName($basket['delInf']['country']),
					str_replace("_"," ",$_POST['gateway']),
					str_replace("_"," ",$basket['shipMethod']));
	
		if(!empty($_POST['customer_comments'])){
			$text_cust .= sprintf($lang['front']['gateway']['order_email_body_2'],
						$_POST['customer_comments']);
		}
	
		$text_cust .= $lang['front']['gateway']['order_email_body_3'];
	
		$text_cust .= $prodtext_cust;
	
		$mail->setText($text_cust);
	
		$mail->setReturnPath($config['masterEmail']);
		$mail->setFrom($config['masterName'].' <'.$config['masterEmail'].'>');
	
		$mail->setSubject($lang['front']['gateway']['order_email_subject'].$cart_order_id);
		$mail->setHeader('X-Mailer', 'CubeCart Mailer');
		$send = $mail->send(array($ccUserData[0]['email']), $config['mailMethod']);

		//echo nl2br($text_cust);
	}
}

?>
