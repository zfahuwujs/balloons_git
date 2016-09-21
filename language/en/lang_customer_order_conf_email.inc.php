<?php
/*-----------------------------------------------------------------------------
 * Customer Order Confirmation Email
 *-----------------------------------------------------------------------------
 * lang_customer_order_confirmation_email.inc.php
 *-----------------------------------------------------------------------------
 * Author:   Estelle Winterflood
 * Email:    cubecart@expandingbrain.com
 * Store:    http://cubecart.expandingbrain.com
 *
 * Date:     December 2, 2005
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


$new_lang['front']['gateway'] = array(


////// START OF PAYMENT INSTRUCTIONS //////
////// Carefully enable these and customize them if you wish...

// Suggested payment instructions for the Manual Credit Card mod:
/*
Your order has been recorded.  If you entered your credit card
details we will charge your credit card within 1 business day.
If you did not enter your credit card details, please phone us
to complete your order.
*/


/* TO ENABLE PAYMENT INSTRUCTIONS FOR GATEWAY 1: REMOVE THIS LINE THEN CUSTOMIZE BELOW

'order_email_gateway_1_instructions' => "
~~~~~~~~~~~~~~~~~~~~~~~~~~
Payment Instructions

Please deposit money into the following bank account:
Bank: xxxx
Account Number: xxxx
Account Name: xxxx
~~~~~~~~~~~~~~~~~~~~~~~~~~
",

// DON'T TOUCH THIS LINE! */


/* TO ENABLE PAYMENT INSTRUCTIONS FOR GATEWAY 2: REMOVE THIS LINE THEN CUSTOMIZE BELOW

'order_email_gateway_2_instructions' => "
~~~~~~~~~~~~~~~~~~~~~~~~~~
Payment Instructions

Please post money order to:
xxxx
xxxx
xxxx
~~~~~~~~~~~~~~~~~~~~~~~~~~
",

// DON'T TOUCH THIS LINE! */


/* TO ENABLE PAYMENT INSTRUCTIONS FOR GATEWAY 3: REMOVE THIS LINE THEN CUSTOMIZE BELOW

'order_email_gateway_3_instructions' => "
~~~~~~~~~~~~~~~~~~~~~~~~~~
Payment Instructions

...
~~~~~~~~~~~~~~~~~~~~~~~~~~
",

// DON'T TOUCH THIS LINE! */


////// END OF PAYMENT INSTRUCTIONS //////


'order_email_body_1a' => "Dear %s,

Thank you for your order no: %s placed on %s

As soon as your payment is received we will process your order and dispatch your goods.
",


'order_email_body_1a_updated_order' => "Dear %s,

We have received an update to your order no: %s placed on %s.  The new order details are shown below.

As soon as your payment is received we will process your order and dispatch your goods.
",


'order_email_body_1b' => "
~~~~~~~~~~~~~~~~~~~~~~~~~~
Name: %s
Subtotal: %s<br>",

'order_email_body_1c' => "Coupon Discount: %s<br>",

'order_email_body_1d' => "Giftcard Amount: %s<br>",

'order_email_body_1e' => "Postage & Packaging: %s
Tax: %s
Grand Total: %s
~~~~~~~~~~~~~~~~~~~~~~~~~~

Invoice Address:
%s
%s
%s
%s
%s
%s
%s

Shipping Address:
%s
%s
%s
%s
%s
%s
%s

Payment Method: %s
Shipping Method: %s",

'order_email_body_2' => "<br>Your comments: %s<br>",

'order_email_body_3' => "<br>~~~~~~~~~~~~~~~~~~~~~~~~~~<br>

Order Inventory:<br>",

'order_email_body_4' =>"Product: %s<br>",

'order_email_body_5' => "Options: %s<br>",

'order_email_body_6' => "Quantity: %s
Product Code: %s
Price: %s<br><br>",

'order_email_subject' => "Order No: ",

);



$lang['front']['gateway'] = array_merge($lang['front']['gateway'], $new_lang['front']['gateway']);

?>
