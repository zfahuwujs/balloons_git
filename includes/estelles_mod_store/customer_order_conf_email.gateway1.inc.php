<?php
/*-----------------------------------------------------------------------------
 * Customer Order Confirmation Email
 *-----------------------------------------------------------------------------
 * customer_order_conf_email.gateway1.inc.php
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


// uses: $basket['invArray']
// sets up: $prodtext_cust

phpExtension();

$config_name = "Customer_Order_Conf_Email";
$coce = fetchDbConfig($config_name);

if ($coce && $coce['status'])
{
	if (!isset($prodtext_cust)) $prodtext_cust = "";

	$prodtext_cust .= sprintf($lang['front']['gateway']['order_email_body_4'],
				$basket['invArray'][$i+1]["name"]);

	if (!empty($basket['invArray'][$i+1]["prodOptions"]))
	{
		$prodtext_cust .= sprintf($lang['front']['gateway']['order_email_body_5'],
				str_replace(array("\r","\n")," ",$basket['invArray'][$i+1]["prodOptions"]));
	}

	// start mod: Text Input Fields for Products, by Estelle
	if (isset($textinput_config) && $textinput_config['status']) {
		for ($j=0; $j<count($basket['textinputArray']); $j++) {
			if ($basket['textinputArray'][$j]['inv_index'] == ($i+1) ) {
				$prodtext_cust .= $basket['textinputArray'][$j]['title']." ".$basket['textinputArray'][$j]['value']."\n";
			}
		}
	}
	// end mod: Text Input Fields for Products, by Estelle

	$prodtext_cust .= sprintf($lang['front']['gateway']['order_email_body_6'],
				$basket['invArray'][$i+1]["quantity"],
				$basket['invArray'][$i+1]["productCode"],
				priceFormat($basket['invArray'][$i+1]["price"]));
}

?>
