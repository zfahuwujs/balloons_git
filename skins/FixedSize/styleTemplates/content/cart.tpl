<!-- BEGIN: view_cart -->
<script type="text/javascript">
	$(document).ready(function(){
		$("#fill").click(function(){
			if($("#fill").attr("checked") == true){
				$("#title2").val($("#title1").val());
				$("#firstName2").val($("#firstName1").val());
				$("#lastName2").val($("#lastName1").val());
				$("#add_12").val($("#add_11").val());
				$("#add_22").val($("#add_21").val());
				$("#town2").val($("#town1").val());
				$("#county2").val($("#county1").val());
				$("#postcode2").val($("#postcode1").val());
				$("#country2").val($("#country1").val());
			}
							
			if($("#fill").attr("checked") == false){
				$("#title2").val('');
				$("#firstName2").val('');
				$("#lastName2").val('');
				$("#add_12").val('');
				$("#add_22").val('');
				$("#town2").val('');
				$("#county2").val('');
				$("#postcode2").val('');
				$("#country2").val("1");
			}
		});
		
	});
	
	
	function validateForm(){
		if($("#firstName1").val() == "" || $("#lastName1").val() == "" || $("#add_11").val() == "" || $("#town1").val() == "" || $("#county1").val() == "" || $("#postcode1").val() == "" || $("#country1").val() == "" || $("#telephone1").val() == "" || $("#email1").val() == ""){
			alert('Please ensure that you complete all fields with an asterisk (*)');
			return false;
		}else{
			return true;
		}
	}				
</script>

<div class="boxContent">
  <h1>{LANG_VIEW_CART}</h1>
  <div style="text-align: center;">
    <div class="cartProgress"> <span {CLASS_STEP2}>{LANG_CART}</span> &raquo; <span {CLASS_STEP3}>{LANG_ADDRESS}</span> &raquo; <span {CLASS_STEP4}>Shipping</span> &raquo; {LANG_PAYMENT} &raquo; {LANG_COMPLETE}<br />
      <div><p><a href='/' class='btnDefault'>Back to Shop</a></p></div>
    </div>
  </div>
  <!--<form action="{VAL_FORM_ACTION}" method="post" class="quickBuy" style="padding: 4px;">
    {LANG_ADD_PRODCODE}
    <input name="productCode" type="text" size="5" class="textbox" />
    <input name="submit" type="submit" class="submit" value="{LANG_ADD}" />
  </form>-->
  <!-- BEGIN: cart_false -->
  <p>{LANG_CART_EMPTY}</p>
  <!-- END: cart_false -->
  <!-- BEGIN: cart_true -->
  <form name="cart" method="post" id="cart" action="{VAL_FORM_ACTION}">
    <!-- BEGIN: step_3 -->
    <!-- BEGIN: login_true -->
    <table width="100%"  border="0" cellspacing="0" cellpadding="3" style="margin-bottom: 10px;">
      <tr>
        <td width="50%" class="tdcartTitle">{LANG_INVOICE_ADDRESS}</td>
        <td colspan="2" class="tdcartTitle">{LANG_DELIVERY_ADDRESS}</td>
      </tr>
      <tr>
        <td width="50%">{VAL_INF_TITLE} {VAL_INF_FIRST_NAME} {VAL_INF_LAST_NAME}</td>
        <td><strong>{TXT_TITLE}</strong></td>
        <td><input name="delInf[title]" type="text" class="textbox" id="title" value="{VAL_DEL_TITLE}" size="7" maxlength="30" /></td>
      </tr>
      <tr>
        <td>{VAL_INF_ADD_1}</td>
        <td><strong>{TXT_FIRST_NAME}</strong></td>
        <td><input name="delInf[firstName]" type="text" class="textbox" id="firstName" value="{VAL_DEL_FIRST_NAME}" maxlength="100" /></td>
      </tr>
      <tr>
        <td>{VAL_INF_ADD_2}</td>
        <td><strong>{TXT_LAST_NAME}</strong></td>
        <td><input name="delInf[lastName]" type="text" class="textbox" id="lastName" value="{VAL_DEL_LAST_NAME}" maxlength="100" /></td>
      </tr>
      <tr>
        <td width="50%">{VAL_INF_TOWN}</td>
        <td><strong>{TXT_ADD_1}</strong></td>
        <td><input name="delInf[add_1]" type="text" class="textbox" id="add_1" value="{VAL_DEL_ADD_1}" maxlength="100" /></td>
      </tr>
      <tr>
        <td width="50%">{VAL_INF_COUNTY}, {VAL_INF_POSTCODE}</td>
        <td><strong>{TXT_ADD_2}</strong></td>
        <td><input name="delInf[add_2]" type="text" class="textbox" id="add_2" value="{VAL_DEL_ADD_2}" maxlength="100" /></td>
      </tr>
      <tr>
        <td width="50%">{VAL_INF_COUNTRY}</td>
        <td><strong>{TXT_TOWN}</strong></td>
        <td><input name="delInf[town]" type="text" class="textbox" id="town" value="{VAL_DEL_TOWN}" maxlength="100" /></td>
      </tr>
      <tr>
        <td width="50%" rowspan="3" align="left" valign="bottom"><a href="index.php?act=profile&amp;f={VAL_BACK_TO}" class="btnDefault">{LANG_CHANGE_INV_ADD}</a></td>
        <td><strong>{TXT_INF_COUNTY}</strong></td>
        <td><input name="delInf[county]" type="text" class="textbox" id="county" value="{VAL_DEL_COUNTY}" maxlength="100" /></td>
      </tr>
      <tr>
        <td><strong>{TXT_POSTCODE}</strong></td>
        <td><input name="delInf[postcode]" type="text" class="textbox" id="postcode" value="{VAL_DEL_POSTCODE}" maxlength="100" /></td>
      </tr>
      <tr>
        <td><strong>{TXT_COUNTRY}</strong></td>
        <td><select name="delInf[country]" id="country" class="textbox">
            <!-- BEGIN: country_opts -->
            <option value="{VAL_DEL_COUNTRY_ID}" {COUNTRY_SELECTED}>{VAL_DEL_COUNTRY_NAME}</option>
            <!-- END: country_opts -->
          </select></td>
      </tr>
    </table>
    <!-- END: login_true -->
    <!-- BEGIN: login_false -->
    <table width="100%"  border="0" cellspacing="0" cellpadding="3" style="margin-bottom: 10px;">
      <tr>
        <td colspan="2" width="50%" class="tdcartTitle">{LANG_INVOICE_ADDRESS}</td>
        <td colspan="2" width="50%" class="tdcartTitle">{LANG_DELIVERY_ADDRESS}</td>
      </tr>
      <!-- BEGIN: error_msg -->
      <tr>
        <td colspan="4" align="center"><font color="red">Please make sure you fill in all required fields</font></td>
      </tr>
      <!-- END: error_msg -->
      <!-- BEGIN: error_msg_email -->
      <tr>
        <td colspan="4" align="center"><font color="red">Please make sure you fill in a correct email address</font></td>
      </tr>
      <!-- END: error_msg_email -->
      <!-- BEGIN: error_msg_phone -->
      <tr>
        <td colspan="4" align="center"><font color="red">Please make sure you fill in a correct phone number</font></td>
      </tr>
      <!-- END: error_msg_phone -->
      <tr>
        <td colspan="2">{LANG_INVOICE_ADDRESS}</td>
        <td colspan="2">Deliver to invoice/billing address?
          <input type="checkbox" name="fill" id="fill" />
          <br />
          Please Note: If you do not tick this you will have to enter in the delivery address details below: </td>
      </tr>
      <tr>
        <td><strong>{TXT_TITLE}</strong></td>
        <td width="50%"><input name="invInf[title]" type="text" class="textbox" id="title1" value="{VAL_INF_TITLE}" size="7" maxlength="30" tabindex="1" /></td>
        <td><strong>{TXT_TITLE}</strong></td>
        <td><input name="delInf[title]" type="text" class="textbox" id="title2" value="{VAL_DEL_TITLE}" size="7" maxlength="30" tabindex="10" /></td>
      </tr>
      <tr>
        <td><strong>{TXT_FIRST_NAME}</strong></td>
        <td><input name="invInf[firstName]" type="text" class="textbox" id="firstName1" value="{VAL_INF_FIRST_NAME}" maxlength="100" tabindex="2" />
          *</td>
        <td><strong>{TXT_FIRST_NAME}</strong></td>
        <td><input name="delInf[firstName]" type="text" class="textbox" id="firstName2" value="{VAL_DEL_FIRST_NAME}" maxlength="100" tabindex="11" />
          *</td>
      </tr>
      <tr>
        <td><strong>{TXT_LAST_NAME}</strong></td>
        <td><input name="invInf[lastName]" type="text" class="textbox" id="lastName1" value="{VAL_INF_LAST_NAME}" maxlength="100" tabindex="3" />
          *</td>
        <td><strong>{TXT_LAST_NAME}</strong></td>
        <td><input name="delInf[lastName]" type="text" class="textbox" id="lastName2" value="{VAL_DEL_LAST_NAME}" maxlength="100" tabindex="12" />
          *</td>
      </tr>
      <tr>
        <td><strong>{TXT_ADD_1}</strong></td>
        <td><input name="invInf[add_1]" type="text" class="textbox" id="add_11" value="{VAL_INF_ADD_1}" maxlength="100" tabindex="4" />
          *</td>
        <td><strong>{TXT_ADD_1}</strong></td>
        <td><input name="delInf[add_1]" type="text" class="textbox" id="add_12" value="{VAL_DEL_ADD_1}" maxlength="100" tabindex="13" />
          *</td>
      </tr>
      <tr>
        <td><strong>{TXT_ADD_2}</strong></td>
        <td><input name="invInf[add_2]" type="text" class="textbox" id="add_21" value="{VAL_INF_ADD_2}" maxlength="100" tabindex="5" /></td>
        <td><strong>{TXT_ADD_2}</strong></td>
        <td><input name="delInf[add_2]" type="text" class="textbox" id="add_22" value="{VAL_DEL_ADD_2}" maxlength="100" tabindex="14" /></td>
      </tr>
      <tr>
        <td><strong>{TXT_TOWN}</strong></td>
        <td><input name="invInf[town]" type="text" class="textbox" id="town1" value="{VAL_INF_TOWN}" maxlength="100" tabindex="6" />
          *</td>
        <td><strong>{TXT_TOWN}</strong></td>
        <td><input name="delInf[town]" type="text" class="textbox" id="town2" value="{VAL_DEL_TOWN}" maxlength="100" tabindex="15" />
          *</td>
      </tr>
      <tr>
        <!--<td width="50%" rowspan="3" align="left" valign="bottom"><a href="index.php?act=profile&amp;f={VAL_BACK_TO}" class="txtUpdate">{LANG_CHANGE_INV_ADD}</a></td>-->
        <td><strong>{TXT_COUNTY}</strong></td>
        <td><input name="invInf[county]" type="text" class="textbox" id="county1" value="{VAL_INF_COUNTY}" maxlength="100" tabindex="7" />
          *</td>
        <td><strong>{TXT_COUNTY}</strong></td>
        <td><input name="delInf[county]" type="text" class="textbox" id="county2" value="{VAL_DEL_COUNTY}" maxlength="100" tabindex="16" />
          *</td>
      </tr>
      <tr>
        <td><strong>{TXT_POSTCODE}</strong></td>
        <td><input name="invInf[postcode]" type="text" class="textbox" id="postcode1" value="{VAL_INF_POSTCODE}" maxlength="100" tabindex="8" />
          *</td>
        <td><strong>{TXT_POSTCODE}</strong></td>
        <td><input name="delInf[postcode]" type="text" class="textbox" id="postcode2" value="{VAL_DEL_POSTCODE}" maxlength="100" tabindex="17" />
          *</td>
      </tr>
      <tr>
        <td><strong>{TXT_COUNTRY}</strong></td>
        <td><select name="invInf[country]" id="country1" class="textbox" tabindex="9">
            <!-- BEGIN: country_opts1 -->
            <option value="{VAL_INV_COUNTRY_ID}" {INV_COUNTRY_SELECTED}>{VAL_INV_COUNTRY_NAME}</option>
            <!-- END: country_opt1 -->
          </select>
          *</td>
        <td><strong>{TXT_COUNTRY}</strong></td>
        <td><select name="delInf[country]" id="country2" class="textbox" tabindex="18">
            <!-- BEGIN: country_opts2 -->
            <option value="{VAL_DEL_COUNTRY_ID}" {DEL_COUNTRY_SELECTED}>{VAL_DEL_COUNTRY_NAME}</option>
            <!-- END: country_opts2 -->
          </select>
          *</td>
      </tr>
      <tr>
        <td colspan="4" class="tdcartTitle">Contact Details</td>
      </tr>
      <tr>
        <td><strong>Telephone:</strong></td>
        <td width="50%"><input name="invInf[telephone]" type="text" class="textbox" id="telephone1" value="{VAL_INF_TELEPHONE}" maxlength="30" tabindex="19" />
          *</td>
        <td><strong>Mobile:</strong></td>
        <td><input name="invInf[mobile]" type="text" class="textbox" id="mobile1" value="{VAL_INF_MOBILE}" maxlength="30" tabindex="20" /></td>
      </tr>
      <tr>
        <td><strong>Email:</strong></td>
        <td><input name="invInf[email]" type="text" class="textbox" id="email1" value="{VAL_INF_EMAIL}" maxlength="100" tabindex="21" />
          *</td>
        <td colspan="2">* - required fields</td>
      </tr>
    </table>
    <!-- END: login_false -->
    <!-- END: step_3 -->
    <!-- BEGIN: step_4 -->
    <!-- BEGIN: login_true -->
    <table width="100%"  border="0" cellspacing="0" cellpadding="3" style="margin-bottom: 10px;">
      <tr>
        <td width="50%" class="tdcartTitle">{LANG_INVOICE_ADDRESS}</td>
        <td class="tdcartTitle">{LANG_DELIVERY_ADDRESS}</td>
      </tr>
      <tr>
        <td width="50%">{VAL_INF_TITLE} {VAL_INF_FIRST_NAME} {VAL_INF_LAST_NAME}</td>
        <td>{VAL_DEL_TITLE} {VAL_DEL_FIRST_NAME} {VAL_DEL_LAST_NAME}</td>
      </tr>
      <tr>
        <td>{VAL_INF_ADD_1}</td>
        <td>{VAL_DEL_ADD_1}</td>
      </tr>
      <tr>
        <td>{VAL_INF_ADD_2}</td>
        <td>{VAL_DEL_ADD_2}</td>
      </tr>
      <tr>
        <td width="50%">{VAL_INF_TOWN}</td>
        <td>{VAL_DEL_TOWN}</td>
      </tr>
      <tr>
        <td width="50%">{VAL_INF_COUNTY}, {VAL_INF_POSTCODE}</td>
        <td>{VAL_DEL_COUNTY}, {VAL_DEL_POSTCODE}</td>
      </tr>
      <tr>
        <td width="50%">{VAL_INF_COUNTRY}</td>
        <td>{VAL_DEL_COUNTRY}</td>
      </tr>
      <tr>
        <td width="50%"><a href="index.php?act=profile&amp;f={VAL_BACK_TO}" class="btnDefault">{LANG_CHANGE_INV_ADD}</a></td>
        <td><!-- BEGIN: edit_btn -->
          <a href="cart.php?act=step3" class="btnDefault">{LANG_CHANGE_DEL_ADD}</a>
          <!-- END: edit_btn -->
        </td>
      </tr>
    </table>
    <!-- END: login_true -->
    <!-- BEGIN: login_false -->
    <table width="100%"  border="0" cellspacing="0" cellpadding="3" style="margin-bottom: 10px;">
      <tr>
        <td width="50%" class="tdcartTitle">{LANG_INVOICE_ADDRESS}</td>
        <td class="tdcartTitle">{LANG_DELIVERY_ADDRESS}</td>
      </tr>
      <tr>
        <td width="50%">{VAL_INF_TITLE} {VAL_INF_FIRST_NAME} {VAL_INF_LAST_NAME}</td>
        <td>{VAL_DEL_TITLE} {VAL_DEL_FIRST_NAME} {VAL_DEL_LAST_NAME}</td>
      </tr>
      <tr>
        <td>{VAL_INF_ADD_1}</td>
        <td>{VAL_DEL_ADD_1}</td>
      </tr>
      <tr>
        <td>{VAL_INF_ADD_2}</td>
        <td>{VAL_DEL_ADD_2}</td>
      </tr>
      <tr>
        <td width="50%">{VAL_INF_TOWN}</td>
        <td>{VAL_DEL_TOWN}</td>
      </tr>
      <tr>
        <td width="50%">{VAL_INF_COUNTY}, {VAL_INF_POSTCODE}</td>
        <td>{VAL_DEL_COUNTY}, {VAL_DEL_POSTCODE}</td>
      </tr>
      <tr>
        <td width="50%">{VAL_INF_COUNTRY}</td>
        <td>{VAL_DEL_COUNTRY}</td>
      </tr>
      <tr>
        <td width="50%"><a href="cart.php?act=step3" class="btnDefault">{LANG_CHANGE_INV_ADD}</a></td>
        <td><!-- BEGIN: edit_btn -->
          <a href="cart.php?act=step3" class="btnDefault">{LANG_CHANGE_DEL_ADD}</a>
          <!-- END: edit_btn -->
        </td>
      </tr>
    </table>
    <!-- END: login_false -->
    <!-- END: step_4 -->
    <table width="100%" border="0" cellpadding="3" cellspacing="0" {BASKET_HIDE}>
      <tr>
        <td class="tdcartTitle">&nbsp;</td>
        <td align="center" class="tdcartTitle">{LANG_QTY}</td>
        <td align="center" class="tdcartTitle">&nbsp;</td>
        <td class="tdcartTitle">{LANG_PRODUCT}</td>
        <td align="center" class="tdcartTitle">{LANG_CODE}</td>
        <td align="center" class="tdcartTitle">{LANG_STOCK}</td>
        <td class="tdcartTitle" align="right">{LANG_PRICE}</td>
        <td width="80" align="right" nowrap='nowrap' class="tdcartTitle">{LANG_LINE_PRICE}</td>
      </tr>
      <!-- BEGIN: repeat_cart_contents -->
      <tr>
        <td align="center" class="{TD_CART_CLASS}"><a href="cart.php?act={VAL_CURRENT_STEP}&amp;remove={VAL_PRODUCT_KEY}"><img src="skins/{VAL_SKIN}/styleImages/del.gif" alt="{LANG_DELETE}" width="12" height="12" border="0" title="{LANG_DELETE}" /></a></td>
        <td align="center" class="{TD_CART_CLASS}"><input name="quan[{VAL_PRODUCT_KEY}]" type="text" value="{VAL_QUANTITY}" size="2" class="{TEXT_BOX_CLASS}" style="text-align:center;" {QUAN_DISABLED} /></td>
        <td align="center" class="{TD_CART_CLASS}"><img src="{VAL_IMG_SRC}" alt="" title="" width="100" /></td>
        <td class="{TD_CART_CLASS}"> {VAL_PRODUCT_NAME}
          <!-- BEGIN: options -->
          <br />
          <strong>{VAL_OPT_NAME}</strong>: {VAL_OPT_VALUE}
          <!-- END: options -->
        </td>
        <td align="center" class="{TD_CART_CLASS}">{VAL_PRODUCT_CODE}</td>
        <td align="center" class="{TD_CART_CLASS}">{VAL_INSTOCK}</td>
        <td align="right" class="{TD_CART_CLASS}">{VAL_IND_PRICE}</td>
        <td width="80" align="right" class="{TD_CART_CLASS}">{VAL_LINE_PRICE}</td>
      </tr>
      <!-- BEGIN: stock_warn -->
      <tr>
        <td align="center" class="{TD_CART_CLASS}">&nbsp;</td>
        <td colspan="7" align="left" class="{TD_CART_CLASS}"><span class="txtStockWarn">{VAL_STOCK_WARN}</span></td>
      </tr>
      <!-- END: stock_warn -->
      <!-- END: repeat_cart_contents -->
      <tr>
        <td align="center" class="tdCartSubTotal"><img src="skins/{VAL_SKIN}/styleImages/del.gif" alt="{LANG_DELETE}" width="12" height="12" title="{LANG_DELETE}" /></td>
        <td colspan="5" class="tdCartSubTotal">- {LANG_REMOVE_ITEM}</td>
        <td align="right" class="tdCartSubTotal">{LANG_SUBTOTAL}</td>
        <td width="80" align="right" class="tdCartSubTotal">{VAL_SUBTOTAL}</td>
      </tr>
      <tr>
        <td colspan="7" align="right"><span {STEP2OFFTAX}>{LANG_TAX}</span></td>
        <td width="80" align="right"><span {STEP2OFFTAX}>{VAL_TAX}</span> </td>
      </tr>
    </table>
    <table width="100%"  border="0" cellspacing="0" cellpadding="3" {BASKET_HIDE}>
      <tr>
        <td align="right"><span {STEP2OFF}>{LANG_SHIPPING}</span></td>
        <td width="80" align="right"><span {STEP2OFF}>{VAL_SHIPPING}</td>
      </tr>
    </table>
     <!-- BEGIN: show_discount -->
    <table width="100%"  border="0" cellspacing="0" cellpadding="3" {BASKET_HIDE}>
      <tr {DISCOUNT_HIDE_PT1}>
        <td align="right"><span style="color: red;">Discount{SHOW_DISCOUNT_CODE}:</span></td>
        <td width="80" align="right"><span style="color: red;">{VAL_DISCOUNT}</td>
      </tr>
    </table>
     <!-- END: show_discount -->
    <table width="100%"  border="0" cellspacing="0" cellpadding="3" {BASKET_HIDE}>
      <tr>
        <td align="right"><strong>{LANG_CART_TOTAL}</strong></td>
        <td width="80" align="right"><strong>{VAL_CART_TOTAL}</strong></td>
      </tr>
    </table>
    <div style="float: left; line-height: 22px; margin-bottom: 3px; {UPDATE_HIDE}"><a href="javascript:submitDoc('cart');" class="btnDefault">{LANG_UPDATE_CART}</a> <p>{LANG_UPDATE_CART_DESC}</p></div>
    <div style="text-align: right; margin-top: 4px; margin-bottom: 3px;"><a href="{CONT_VAL}" class="btnDefault" {STEP_3}>{LANG_CHECKOUT}</a></div>
  </form>
   <!-- BEGIN: showCouponForm -->
  <div align="center" {DISCOUNT_HIDE_PT2}>
    <form action="/cart.php?act={NEXT_STEP}" name="coupon" method="post" style="padding: 4px;">
      <table border="0" id="table1" width="100%" {BASKET_HIDE}>
        <tr>
          <td colspan="2"><p align="center" style="color:red;"><b>{COUPON_ERR_MSG}</b></p></td>
        </tr>
        <tr>
          <td nowrap="nowrap" align="right"><input name="couponCode" type="text" size="20" class="textbox" />
            <a href="javascript:submitDoc('coupon');" class="btnDefault">Add</a><br />
            <p style="font-size:18px; margin:10px;"><b>VOUCHER CODE</b></p></td>
        </tr>
      </table>
    </form>
  </div>
  <!-- END: showCouponForm -->
  <!-- END: cart_true -->
</div>
<!-- END: view_cart -->
