<!-- BEGIN: view_cart -->
<script>
$(document).ready(function(){
	updateTotal();
	calculateShipping();
	{CURRENT_SHIPPING}
});
function changeShipping(shipKey){
	$.ajax({
	  type: "GET",
	  cache : false,
	  url: '/shippingPrice.ajax.php',
	  data:  "ccUser={SESSIONID}&shipKey="+shipKey,
	  success: function(data) {
		$('.shippingCharge').html(data);
		updateTotal();
	  }
	});
}
function getShipPrice(){
	$.ajax({
	  type: "GET",
	  cache : false,
	  url: '/shippingPriceOnly.ajax.php',
	  data:  "ccUser={SESSIONID}",
	  success: function(data) {
		$('.shippingCharge').html(data);
		updateTotal();
	  }
	});
}
function calculateShipping(){
	$.ajax({
	  type: "GET",
	  url: '/shippingCalculator.php',
	  cache : false,
	  data:  "ccUser={SESSIONID}&country="+$('#delInf_country').val(),
	  success: function(data) {
		  if(data=='No shipping available'){
			  $('#paynow').attr('disabled','disabled');
			  window.location = "/cart.php?act=noShip";
		  }else if(data=='overWeight'){
			  $('#paynow').attr('disabled','disabled');
			  alert('The total weight of your order it too high. Please contact us to see if we can make an alternative arrangement or reduce the amount of items in your basket.');
			  //window.location = "/cart.php?act=overWeight";
		  }else{
			$('.shippingClick').html(data);
			updateTotal();
			nonformatedSHipping();
		  }
	  }
	});
}
function updateTotal(){
	var totalz = $('#grandtotal').val();
	$.ajax({
	  type: "GET",
	  cache : false,
	  url: '/totalPrice.ajax.php',
	  data:  "ccUser={SESSIONID}&totalz="+totalz,
	  success: function(data) {
		$('.grandTotal').html(data);
	  }
	});
}
function nonformatedSHipping(){
	$.ajax({
	  type: "GET",
	  cache : false,
	  url: '/shippingPrice.ajax.php',
	  data:  "ccUser={SESSIONID}&priceonly=true",
	  success: function(data) {
		$('.shippingCharge').val(data);
		$('.shippingCharge').html(data);
	  }
	});
}
function nonformatedTotal(){
	var totalz = $('#grandtotal').val();
	$.ajax({
	  type: "GET",
	  cache : false,
	  url: '/totalPrice.ajax.php',
	  data:  "ccUser={SESSIONID}&totalz="+totalz+"&priceonly=true",
	  success: function(data) {
		$('#grandtotalz').val(data)
	  }
	});
}
function copyAddress(){
	if($('#copyaddress').attr('checked')){
		$('#delInf_firstName').val($('#invif_firstName').val());
		$('#delInf_lastName').val($('#invinf_lastName').val());
		$('#delInf_country').val($('#invInf_country').val());
		$('#delInf_add_1').val($('#invInf_add_1').val());
		$('#delInf_add_2').val($('#invInf_add_2').val());
		$('#delInf_town').val($('#invInf_town').val());
		$('#delInf_county').val($('#invInf_county').val());
		$('#delInf_postcode').val($('#invInf_postcode').val());
		$('#delInf_phone').val($('#invInf_phone').val());
		calculateShipping();
	}else{
		$('#delInf_firstName').val('');
		$('#delInf_lastName').val('');
		$('#delInf_country').val('');
		$('#delInf_add_1').val($(''));
		$('#delInf_add_2').val('');
		$('#delInf_town').val('');
		$('#delInf_county').val('');
		$('#delInf_postcode').val('');
		$('#delInf_phone').val('');
	}
}
function validateDelivery(){
	if($('#delInf_firstName').val()=='' || $('#invInf_postcode').val()=='undefined'){
		$('.fnameError').remove();
		$('.deliveryEror').append('<div class="fnameError">Please enter your first name</div>');
		$('#delInf_firstName').css('border','1px solid #f00');
		var deliveryEror = true;
	}else{
		$('#delInf_firstName').css('border','1px solid #a8a8a8');
		$('.fnameError').remove();
	}
	if($('#delInf_lastName').val()==''){
		$('.lnameErrord').remove();
		$('.deliveryEror').append('<div class="lnameErrord">Please enter your last name</div>');
		var deliveryEror = true;
		$('#delInf_lastName').css('border','1px solid #f00');
	}else{
		$('#delInf_lastName').css('border','1px solid #a8a8a8');
		$('.lnameErrord').remove();
	}
	if($('#delInf_country').val()==''){
		$('.countryErrord').remove();
		$('#delInf_country').css('border','1px solid #f00');
		$('.deliveryEror').append('<div class="countryErrord">Please enter your delivery country</div>');
		var deliveryEror = true;
	}else{
		$('#delInf_country').css('border','1px solid #a8a8a8');
		$('.countryErrord').remove();
	}
	if($('#delInf_add_1').val()==''){
		$('.addressErrord').remove();
		$('#delInf_add_1').css('border','1px solid #f00');
		$('.deliveryEror').append('<div class="addressErrord">Please enter your delivery address</div>');
		var deliveryEror = true;
	}else{
		$('#delInf_add_1').css('border','1px solid #a8a8a8');
		$('.addressErrord').remove();
	}
	if($('#delInf_town').val()==''){
		$('.townErrord').remove();
		$('#delInf_town').css('border','1px solid #f00');
		$('.deliveryEror').append('<div class="townErrord">Please enter your delivery town/city</div>');
		var deliveryEror = true;
	}else{
		$('#delInf_town').css('border','1px solid #a8a8a8');
		$('.townErrord').remove();
	}

	if($('#delInf_postcode').val()==''){
		$('.postcodeErrord').remove();
		$('#delInf_postcode').css('border','1px solid #f00');
		$('.deliveryEror').append('<div class="postcodeErrord">Please enter your delivery postcode</div>');
		var deliveryEror = true;
	}else{
		$('#delInf_postcode').css('border','1px solid #a8a8a8');
		$('.postcodeErrord').remove();
	}
	if(deliveryEror!=true){
		$('.generalError').remove();
		Accordion1.openNextPanel();
	}else{
		$('.generalError').remove();
		$('.deliveryEror').prepend('<div class="generalError">Please select same as billing address or</div>');
	}
}
function validateBilling(){
	if($('#invif_firstName').val()=='' || $('#invif_firstName').val()=='undefined'){
		$('.fnameError').remove();
		$('#invif_firstName').css('border','1px solid #f00');
		$('.billingEror').append('<div class="fnameError">Please enter your first name</div>');
		var billingError = true;
	}else{
		$('#invif_firstName').css('border','1px solid #a8a8a8');
		$('.fnameError').remove();
	}
	if($('#invinf_lastName').val()==''){
		$('.lnameError').remove();
		$('#invinf_lastName').css('border','1px solid #f00');
		$('.billingEror').append('<div class="lnameError">Please enter your last name</div>');
		var billingError = true;
	}else{
		$('#invinf_lastName').css('border','1px solid #a8a8a8');
		$('.lnameError').remove();
	}
	if($('#invInf_email').val()==''){
		$('.emailError').remove();
		$('#invInf_email').css('border','1px solid #f00');
		$('.billingEror').append('<div class="emailError">Please enter your email address</div>');
		var billingError = true;
	}else{
		$('#invInf_email').css('border','1px solid #a8a8a8');
		$('.emailError').remove();
	}
	if($('#invInf_country').val()==''){
		$('.countryError').remove();
		$('#invInf_country').css('border','1px solid #f00');
		$('.billingEror').append('<div class="countryError">Please enter your billing country</div>');
		var billingError = true;
	}else{
		$('#invInf_country').css('border','1px solid #a8a8a8');
		$('.countryError').remove();
	}
	if($('#invInf_add_1').val()==''){
		$('.addressError').remove();
		$('#invInf_add_1').css('border','1px solid #f00');
		$('.billingEror').append('<div class="addressError">Please enter your billing address</div>');
		var billingError = true;
	}else{
		$('#invInf_add_1').css('border','1px solid #a8a8a8');
		$('.addressError').remove();
	}
	if($('#invInf_town').val()==''){
		$('.townError').remove();
		$('#invInf_add_1').css('border','1px solid #f00');
		$('.billingEror').append('<div class="townError">Please enter your billing town/city</div>');
		var billingError = true;
	}else{
		$('#invInf_town').css('border','1px solid #a8a8a8');
		$('.townError').remove();
	}

	if($('#invInf_postcode').val()==''){
		$('.postcodeError').remove();
		$('#invInf_postcode').css('border','1px solid #f00');
		$('.billingEror').append('<div class="postcodeError">Please enter your billing postcode</div>');
		var billingError = true;
	}else{
		$('#invInf_postcode').css('border','1px solid #a8a8a8');
		$('.postcodeError').remove();
	}
	if($('#invInf_phone').val()==''){
		$('.phoneError').remove();
		$('#invInf_phone').css('border','1px solid #f00');
		$('.billingEror').append('<div class="phoneError">Please enter your phone number - "We collect your phone number just in case we need to contact you regards your delivery"</div>');
		var billingError = true;
	}else{
		$('#invInf_phone').css('border','1px solid #a8a8a8');
		$('.phoneError').remove();
	}

	if(billingError!=true){
		Accordion1.openNextPanel();
	}
}
function nextTab(){
	Accordion1.openNextPanel();
}
</script>
<div class="boxContent checkoutPage">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="50%" align="left" valign="top" style="padding-right:5px;"><h1 class="bothLines">CHECKOUT</h1>
      <!-- BEGIN: login -->
        <form name="form1" method="post" action="{PAGE_ACTION}">
        <div>If you have an account, please login and we'll fill in your details...</div>
        {LOGIN_STATUS}
          <table width="100%" border="0" cellspacing="0" cellpadding="5">
            <tr>
              <td colspan="2">Username</td>
            </tr>
            <tr>
              <td colspan="2"><label for="username"></label>
              <input type="text" name="username" id="username" class="loField"></td>
            </tr>
            <tr>
              <td colspan="2">Password</td>
            </tr>
            <tr>
              <td colspan="2"><label for="password"></label>
              <input type="password" name="password" id="password" class="loField"></td>
            </tr>
            <tr>
              <td width="100"><input type="submit" name="logmeIn" id="logmeIn" class="submit" value="Login"></td>
              <td><a href="/index.php?act=forgotPass" style="text-decoration:none;">Forgotten your password?</a></td>
            </tr>
          </table>
      </form>
      <h1 class="bothLines">NEW CUSTOMER</h1>
     <div> No account? no problem! Just fill in your details below to continue...</div>
     <!-- END: login -->
     {REGERROR}
      <form name="form3" method="post" action="{PAGE_ACTION}">
<div id="Accordion1" class="Accordion" tabindex="0">
  <div class="AccordionPanel">
    <div class="AccordionPanelTab"><span class="panelNumber">1</span> Billing Address</div>
    <div class="AccordionPanelContent">
    
      <table width="100%" border="0" cellspacing="0" cellpadding="5">
        <tr>
          <td colspan="2">First Name</td>
        </tr>
        <tr>
          <td colspan="2"><label for="invif[]"></label>
            <input name="invInf[firstName]" type="text" class="reFields" id="invif_firstName" value="{VAL_INF_FIRST_NAME}"></td>
        </tr>
        <tr>
          <td colspan="2">Last Name</td>
        </tr>
        <tr>
          <td colspan="2"><label for="invinf[]"></label>
            <input name="invInf[lastName]" type="text" class="reFields" id="invinf_lastName" value="{VAL_INF_LAST_NAME}"></td>
        </tr>
        <tr>
          <td colspan="2">Email Address </td>
        </tr>
        <tr>
          <td colspan="2"><label for="invInf[]"></label>
            <input name="invInf[email]" type="text" class="reFields" id="invInf_email" value="{VAL_INF_EMAIL}" /></td>
        </tr>
        <tr>
          <td colspan="2">Country </td>
        </tr>
        <tr>
          <td colspan="2">
          <select name="invInf[country]" id="invInf_country" class="reFields" tabindex="9">
          <!-- BEGIN: displayUK1 -->
          <optgroup label="United Kingdom">
            <!-- BEGIN: uk_opts1 -->
            <option value="{VAL_INV_COUNTRY_ID}" {INV_COUNTRY_SELECTED}>{VAL_INV_COUNTRY_NAME}</option>
            <!-- END: uk_opts1 -->
          </optgroup>  
           <!-- END: displayUK1 -->
            <optgroup label="Non UK">
              <!-- BEGIN: country_opts1 -->
              <option value="{VAL_INV_COUNTRY_ID}" {INV_COUNTRY_SELECTED}>{VAL_INV_COUNTRY_NAME}</option>
              <!-- END: country_opts1 -->
             </optgroup>
          </select></td>
        </tr>
        <tr>
          <td colspan="2">Street Address 1 </td>
        </tr>
        <tr>
          <td colspan="2"><label for="invInf[]"></label>
            <input name="invInf[add_1]" type="text" class="reFields" id="invInf_add_1" value="{VAL_INF_ADD_1}" /></td>
        </tr>
        <tr>
          <td colspan="2">Street Address 2 (optional)</td>
        </tr>
        <tr>
          <td colspan="2"><label for="invInf[]"></label>
            <input name="invInf[add_2]" type="text" class="reFields" id="invInf_add_2" value="{VAL_INF_ADD_2}" /></td>
        </tr>
        <tr>
          <td colspan="2">City </td>
        </tr>
        <tr>
          <td colspan="2"><label for="invInf[]"></label>
            <input name="invInf[town]" type="text" class="reFields" id="invInf_town" value="{VAL_INF_TOWN}" /></td>
        </tr>
        <tr>
          <td colspan="2">County </td>
        </tr>
        <tr>
          <td colspan="2"><label for="invInf[]"></label>
            <input name="invInf[county]" type="text" class="reFields" id="invInf_county" value="{VAL_INF_COUNTY}" /></td>
        </tr>
        <tr>
          <td colspan="2">Postcode </td>
        </tr>
        <tr>
          <td colspan="2"><label for="invInf[]"></label>
            <input name="invInf[postcode]" type="text" class="reFields" id="invInf_postcode" value="{VAL_INF_POSTCODE}" /></td>
        </tr>
        <tr>
          <td colspan="2">Telephone Number &quot;We collect your phone number just in case we need to contact you regards your delivery&quot;</td>
        </tr>
        <tr>
          <td colspan="2"><label for="invInf[]"></label>
            <input name="invInf[phone]" type="text" class="reFields" id="invInf_phone" value="{VAL_INF_TELEPHONE}" />
            <div class="billingEror"></div></td>
        </tr>
        <tr>
          <td width="100"><span class="submit" onclick="validateBilling()">Continue</span></td>
          <td>&nbsp;</td>
        </tr>
      </table>
    </div>
  </div>
  <div class="AccordionPanel">
    <div class="AccordionPanelTab"><span class="panelNumber">2</span> Delivery Address</div>
    <div class="AccordionPanelContent">
    
      <table width="100%" border="0" cellspacing="0" cellpadding="5">
        <tr>
          <td colspan="2">Same as billing address 
            <input type="checkbox" name="copyaddress" id="copyaddress" onclick="copyAddress()" />
            <label for="copyaddress"></label></td>
        </tr>
        <tr>
          <td colspan="2">First Name</td>
        </tr>
        <tr>
          <td colspan="2"><label for="delInf[]"></label>
            <input name="delInf[firstName]" type="text" class="reFields" id="delInf_firstName" value="{VAL_DEL_FIRST_NAME}"></td>
        </tr>
        <tr>
          <td colspan="2">Last Name</td>
        </tr>
        <tr>
          <td colspan="2"><label for="delInf[]"></label>
            <input name="delInf[lastName]" type="text" class="reFields" id="delInf_lastName" value="{VAL_DEL_LAST_NAME}"></td>
        </tr>
        <tr>
          <td colspan="2">Country </td>
        </tr>
        <tr>
          <td colspan="2">
          <select name="delInf[country]" id="delInf_country" tabindex="18" class="reFields" onchange="calculateShipping()">
          <!-- BEGIN: displayUK2 -->
          <optgroup label="United Kingdom">
            <!-- BEGIN: uk_opts2 -->
            <option value="{VAL_DEL_COUNTRY_ID}" {DEL_COUNTRY_SELECTED}>{VAL_DEL_COUNTRY_NAME}</option>
            <!-- END: uk_opts2 -->
          </optgroup>  
           <!-- END: displayUK2 -->
            <optgroup label="Non UK">
              <!-- BEGIN: country_opts2 -->
              <option value="{VAL_DEL_COUNTRY_ID}" {DEL_COUNTRY_SELECTED}>{VAL_DEL_COUNTRY_NAME}</option>
              <!-- END: country_opts2 -->
             </optgroup>
          </select>
          </td>
        </tr>
        <tr>
          <td colspan="2">Street Address 1 </td>
        </tr>
        <tr>
          <td colspan="2"><label for="delInf[]"></label>
            <input name="delInf[add_1]" type="text" class="reFields" id="delInf_add_1" value="{VAL_DEL_ADD_1}" /></td>
        </tr>
        <tr>
          <td colspan="2">Street Address 2 (optional)</td>
        </tr>
        <tr>
          <td colspan="2"><label for="delInf[]"></label>
            <input name="delInf[add_2]" type="text" class="reFields" id="delInf_add_2" value="{VAL_DEL_ADD_2}" /></td>
        </tr>
        <tr>
          <td colspan="2">City </td>
        </tr>
        <tr>
          <td colspan="2"><label for="delInf[]"></label>
            <input name="delInf[town]" type="text" class="reFields" id="delInf_town" value="{VAL_DEL_TOWN}" /></td>
        </tr>
        <tr>
          <td colspan="2">County </td>
        </tr>
        <tr>
          <td colspan="2"><label for="delInf[]"></label>
            <input name="delInf[county]" type="text" class="reFields" id="delInf_county" value="{VAL_DEL_COUNTY}" /></td>
        </tr>
        <tr>
          <td colspan="2">Postcode </td>
        </tr>
        <tr>
          <td colspan="2"><label for="delInf[]"></label>
            <input name="delInf[postcode]" type="text" class="reFields" id="delInf_postcode" value="{VAL_DEL_POSTCODE}" /></td>
        </tr>
        <tr>
          <td colspan="2">Mobile Number (optional)</td>
        </tr>
        <tr>
          <td colspan="2"><label for="delInf[]"></label>
            <input name="delInf[phone]" type="text" class="reFields" id="delInf_phone" value="{VAL_INF_MOBILE}" />
            <div class="deliveryEror"></div>
            </td>
        </tr>
        <tr>
          <td width="100"><span class="submit" onclick="validateDelivery()">Continue</span></td>
          <td>&nbsp;</td>
        </tr>
      </table>
    </div>
  </div>
  <div class="AccordionPanel">
    <div class="AccordionPanelTab"><span class="panelNumber">3</span> Shipping Options</div>
    <div class="AccordionPanelContent">
      <table width="100%" border="0" cellspacing="0" cellpadding="5">
        <tr>
          <td colspan="2" class="shippingClick">{VAL_SHIPPING}</td>
          </tr>
        <tr>
          <td><span class="submit" onclick="Accordion1.openNextPanel()">Continue</span></td>
          <td>&nbsp;</td>
        </tr>
      </table>
    </div>
  </div>
  <div class="AccordionPanel">
    <div class="AccordionPanelTab"><span class="panelNumber">4</span> Payment</div>
    <div class="AccordionPanelContent">
    <!-- BEGIN: choose_gate -->
    <p>{LANG_CHOOSE_GATEWAY}</p>
    <input name="shipPricez" id="shipPricez" type="hidden" value="" />
    <input type="hidden" name="grandtotalz" id="grandtotalz" value="{TOTAL_UNFORMATED}" />
        <table width="150" border="0" align="center" cellspacing="0" cellpadding="3">
            <!-- BEGIN: gateways_true -->
            <tr>
                <td class="{TD_CART_CLASS}">{VAL_GATEWAY_DESC}</td>
                <td width="50" align="center" class="{TD_CART_CLASS}">
                <input name="gateway" type="radio" value="{VAL_GATEWAY_FOLDER}" {VAL_CHECKED} />
                </td>
            </tr>
            <!-- END: gateways_true -->
            <tr>
                <td colspan="2" align="left">{LANG_COMMENTS}</td>
            </tr>
            <tr align="right">
              <td colspan="2"><textarea name="customer_comments" cols="35" rows="3" class="textbox">{VAL_CUSTOMER_COMMENTS}</textarea></td>
            </tr>
            <!-- BEGIN: gateways_false -->
            <tr>
                <td>{LANG_GATEWAYS_FALSE}</td>
            </tr>
            <!-- END: gateways_false -->
        </table>
        <div align="center" style="padding-bottom:10px;"><input type="submit" name="paynow" id="paynow" class="submit" value="Continue" /></div>
    <!-- END: choose_gate -->
    </div>
  </div>
</div>
      </form></td>
      <td width="50%" align="left" valign="top" style="padding-left:5px;">
      <div class="summaryBox">
      <h1 class="oneLines">ORDER SUMMARY</h1>
      <!-- BEGIN: order_summary -->
      <form name="form2" method="post" action="{PAGE_ACTION}">
        <table width="100%" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td>&nbsp;</td>
            <td>Qty</td>
            <td>Image</td>
            <td>Description</td>
            <td align="center">Stock</td>
            <td>Price</td>
            <td>Total</td>
            </tr>
          <!-- BEGIN: item -->  
          <tr>
            <td align="center" class="checkoutLine"><a href="cart.php?act=onePage&amp;remove={KEY}"><img src="/skins/FixedSize/styleImages/del.gif" width="12" height="12" alt="remove" border="0"></a></td>
            <td class="checkoutLine">
              <input name="qty[{KEY}]" type="text" id="qty[]" value="{QTY}" size="1"></td>
            <td class="checkoutLine"><a href="/index.php?act=viewProd&amp;productId={PRODUCT_ID}"><img src="{SRC_PROD_THUMB}" alt="{PRODUCT_NAME}" border="0" title="{PRODUCT_NAME}" width="40" /></a></td>
            <td class="checkoutLine"><a href="/index.php?act=viewProd&amp;productId={PRODUCT_ID}">{PRODUCT_NAME}{DISPLAY_OPTIONS}
            </a></td>
            <td align="center" class="checkoutLine">{VAL_INSTOCK}</td>
            <td class="checkoutLine">{PRICE}</td>
            <td class="checkoutLine">{PRICE_TOTAL}</td>
            </tr>
            <!-- BEGIN: stock_warn -->
          <tr>
            <td colspan="7" align="center" class="checkoutLine">{VAL_STOCK_WARN}</td>
            </tr>
            <!-- END: stock_warn -->
          <!-- END: item -->
          
          <!-- BEGIN: discount -->  
          <tr>
            <td class="checkoutLine"><a href="/cart.php?act=onePage&amp;removePromo"><img src="/skins/FixedSize/styleImages/del.gif" width="12" height="12" alt="remove" border="0"></a></td>
            <td colspan="4" class="checkoutLine">Remove Promotion Code</td>
            <td class="checkoutLine"><strong>Discount {SHOW_DISCOUNT_CODE}:</strong></td>
            <td class="checkoutLine">{DISCOUNT_AMOUNT}</td>
          </tr>
          <!-- END: discount -->
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td><strong>Cart Total:</strong></td>
            <td>{CART_TOTAL}</td>
            </tr>
          <tr>
            <td class="checkoutLine">&nbsp;</td>
            <td class="checkoutLine">&nbsp;</td>
            <td class="checkoutLine">&nbsp;</td>
            <td class="checkoutLine">&nbsp;</td>
            <td class="checkoutLine">&nbsp;</td>
            <td class="checkoutLine"><strong>Shipping Charge:</strong></td>
            <td class="shippingCharge checkoutLine">{SHIP_PRICE}</td>
            </tr>
          
          <tr>
            <td colspan="4"><input type="submit" name="imageField5" id="imageField5" class="submit" value="Update"></td>
            <td>&nbsp;</td>
            <td><strong>Total:</strong></td>
            <td><strong class="grandTotal">{GRAND_TOTAL}</strong>
              <input type="hidden" name="grandtotal" id="grandtotal" value="{TOTAL_UNFORMATED}" />
            </td>
          </tr>
        </table>
      <!-- {SHIP_PRICE} -->
      </form>
   
      <!-- END: order_summary -->
      <!-- BEGIN: empty -->
      <div align="center"><strong style="font-size:18px;">Please add a product to your cart before checking out</strong></div>
      <!-- END: empty -->
      </div>
      <!-- BEGIN: discount_Manager -->
      <form id="form4" name="form4" method="post" action="">
      <div class="summaryBox">
      <span style="color:#F00">{COUPON_ERR_MSG}</span>
        <table width="100%" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td>Do you have a promotion code?</td>
            <td><label for="couponCode"></label>
              <input name="couponCode" type="text" id="couponCode" size="10" /></td>
            <td><input type="submit" name="applycode" id="applycode" value="Apply Voucher" class="txtButton" /></td>
          </tr>
        </table>
      </div>
      </form>
      <!-- END: discount_Manager -->      
      <!--<h1 class="bothLines">NEED HELP?</h1>
      If you have any questions about your order, try our comprehensive FAQ section; alternatively, if you need a direct response, simply use our quick and easy contact form.-->
<!-- BEGIN: tabs -->  
<div id="TabbedPanels1" class="TabbedPanels">
  <ul class="TabbedPanelsTabGroup">
  <!-- BEGIN: title -->
  <li class="TabbedPanelsTab" tabindex="0">{TXT_TITLE}</li>
  <!-- END: title -->
  </ul>
  <div class="TabbedPanelsContentGroup">
  <!-- BEGIN: contents -->
  <div class="TabbedPanelsContent">{TXT_DESCRIPTION}</div>
  <!-- END: contents -->
  </div>
</div>
<!-- END: tabs -->
      </td>
    </tr>
  </table>
</div>
<!-- END: view_cart -->