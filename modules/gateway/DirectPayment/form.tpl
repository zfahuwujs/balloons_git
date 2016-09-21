<!-- BEGIN: form -->
<table width="100%" cellpadding="3" cellspacing="0" border="0">
	<tr align="left">
		<td colspan="4" class="tdcartTitle"><strong>{LANG_CC_INFO_TITLE}</strong></td>
	</tr>
	<tr align="left">
		<td><strong>{LANG_FIRST_NAME}</strong></td>
	    <td><input type="text" name="firstName" value="{VAL_FIRST_NAME}" class="textbox" /></td>
		<td><strong>{LANG_LAST_NAME}</strong></td>
	    <td><input type="text" name="lastName" value="{VAL_LAST_NAME}" class="textbox" /></td>
	</tr>
	<tr align="left">
		<td><strong>{LANG_CARD_TYPE}</strong></td>
		<td>
			<select name="cardType" class="textbox">
				<!-- BEGIN: repeat_cards -->
				<option value="{VAL_CARD_TYPE}" {CARD_SELECTED}>{VAL_CARD_NAME}</option>
				<!-- END: repeat_cards -->
			</select>		</td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	</tr>
	<tr align="left">
		<td><strong>{LANG_CARD_NUMBER}</strong>
	  <td><input type="text" name="cardNumber" value="" size="18" maxlength="18" class="textbox" /></td>
	  <td>&nbsp;</td>
	  <td>&nbsp;</td>
	</tr>
	<tr align="left">
		<td><strong>{LANG_EXPIRES}</strong></td>
  <td><input type="text" name="expirationMonth" value="" size="2" maxlength="2" class="textbox" /> / <input type="text" name="expirationYear" value="" size="4" maxlength="4" class="textbox" /> {LANG_MMYYYY}    
  <td colspan="2" rowspan="2" align="right">
  <a href="javascript:;" onclick="javascript:window.open('https://www.paypal.com/uk/cgi-bin/webscr?cmd=xpt/popup/OLCWhatIsPayPal-outside','olcwhatispaypal','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, width=400, height=350');"><img  src="https://www.paypal.com/en_US/i/logo/PayPal_mark_60x38.gif" alt="Acceptance Mark" border="0" title=""></a>
  </tr>
	<tr align="left">
		<td><strong>{LANG_SECURITY_CODE}</strong>
	  <td><input type="text" name="cvc2" value="" size="4" maxlength="4" class="textbox" /></td>
    </tr>
	<tr>
		<td colspan="4">&nbsp;</td>
	</tr>
	<tr align="left">
		<td colspan="4" class="tdcartTitle"><strong>{LANG_CUST_INFO_TITLE}</strong></td>
	</tr>				
	<tr align="left">
		<td><strong>{LANG_EMAIL}</strong>
	  <td colspan="3"><input type="text" name="emailAddress" value="{VAL_EMAIL_ADDRESS}" size="50" class="textbox" /></td>
	</tr>
	<tr align="left">
		<td><strong>{LANG_ADDRESS}</strong></td>
	  <td colspan="3"><input type="text" name="addr1" value="{VAL_ADD_1}" size="50" class="textbox" /></td>
	</tr>
	<tr align="left">
		<td>&nbsp;</td>
	  <td colspan="3"><input type="text" name="addr2" value="{VAL_ADD_2}" size="50" class="textbox" /> {LANG_OPTIONAL}</td>
	</tr>
	<tr align="left">
		<td>
		<strong>{LANG_CITY}</strong>		</td>
		<td colspan="3">
		<input type="text" name="city" value="{VAL_CITY}" class="textbox" />	  </td>
  </tr>
		<tr align="left">
		<td>
		<strong>{LANG_STATE}</strong>		</td>
		<td colspan="3">
		<select id="state" name="state" class="textbox">
			<option value="{VAL_COUNTY}">{VAL_COUNTY}</option>
			<option value="AK">AK</option>
			<option value="AL">AL</option>
			<option value="AR">AR</option>
			<option value="AZ">AZ</option>
			<option value="CA">CA</option>
			<option value="CO">CO</option>
			<option value="CT">CT</option>
			<option value="DC">DC</option>
			<option value="DE">DE</option>
			<option value="FL">FL</option>
			<option value="GA">GA</option>
			<option value="HI">HI</option>
			<option value="IA">IA</option>
			<option value="ID">ID</option>
			<option value="IL">IL</option>
			<option value="IN">IN</option>
			<option value="KS">KS</option>
			<option value="KY">KY</option>
			<option value="LA">LA</option>
			<option value="MA">MA</option>
			<option value="MD">MD</option>
			<option value="ME">ME</option>
			<option value="MI">MI</option>
			<option value="MN">MN</option>
			<option value="MO">MO</option>
			<option value="MS">MS</option>
			<option value="MT">MT</option>
			<option value="NC">NC</option>
			<option value="ND">ND</option>
			<option value="NE">NE</option>
			<option value="NH">NH</option>
			<option value="NJ">NJ</option>
			<option value="NM">NM</option>
			<option value="NV">NV</option>
			<option value="NY">NY</option>
			<option value="OH">OH</option>
			<option value="OK">OK</option>
			<option value="OR">OR</option>
			<option value="PA">PA</option>
			<option value="RI">RI</option>
			<option value="SC">SC</option>
			<option value="SD">SD</option>
			<option value="TN">TN</option>
			<option value="TX">TX</option>
			<option value="UT">UT</option>
			<option value="VA">VA</option>
			<option value="VT">VT</option>
			<option value="WA">WA</option>
			<option value="WI">WI</option>
			<option value="WV">WV</option>
			<option value="WY">WY</option>
			<option value="AA">AA</option>
			<option value="AE">AE</option>
			<option value="AP">AP</option>
			<option value="AS">AS</option>
			<option value="FM">FM</option>
			<option value="GU">GU</option>
			<option value="MH">MH</option>
			<option value="MP">MP</option>
			<option value="PR">PR</option>
			<option value="PW">PW</option>
			<option value="VI">VI</option>
		</select>		</td>
		</tr>
		<tr align="left">
		<td>
		<strong>{LANG_ZIPCODE}</strong>		</td>
		<td colspan="3">
		<input type="text" name="postalCode" value="{VAL_POST_CODE}" size="10" maxlength="10" class="textbox" />	  </td>
	</tr>
	<tr align="left">
		<td><strong>{LANG_COUNTRY}</strong>
		<td colspan="3">
			<select name="country" class="textbox">
				<!-- BEGIN: repeat_countries -->
				<option value="{VAL_COUNTRY_ISO}" {COUNTRY_SELECTED}>{VAL_COUNTRY_NAME}</option>
				<!-- END: repeat_countries -->
			</select>	  </td>
	</tr>
</table>
<input type="hidden" name="cart_order_id" value="{VAL_CART_ORDER_ID}" />
<input type="hidden" name="order_total" value="{VAL_ORDER_TOTAL}" />
<input type="hidden" name="item_total" value="{VAL_ITEM_TOTAL}" />
<input type="hidden" name="tax_total" value="{VAL_TAX_TOTAL}" />
<input type="hidden" name="shipping_total" value="{VAL_SHIPPING_TOTAL}" />
<input type="hidden" name="currency_id" value="{VAL_CURRENCY_ID}" />
<input type="hidden" name="gateway" value="DirectPayment" />
<!-- END: form -->
