<!-- BEGIN: profile -->
<div class="boxContent">

	<h1>{LANG_PERSONAL_INFO_TITLE}</h1>
	<div>
    <a href="index.php?act=account" class="btnDefault">Back to Menu</a>
  </div>
  <br />
	<!-- BEGIN: session_true -->
	<!-- BEGIN: no_error -->
	<p>{LANG_PROFILE_DESC}</p>
	<!-- END: no_error -->
	<!-- BEGIN: error -->
	<p class="txtError">{VAL_ERROR}</p>
	<!-- END: error -->
	
		<form action="index.php?act=profile{VAL_EXTRA_GET}" target="_self" method="post">
			<table border="0" cellspacing="0" cellpadding="3" align="center" width="100%">
            	<tr>
					<td align="right"><strong>Account Type:</strong> </td>
					<td><strong>{ACCOUNT_TYPE}</strong></td>
				</tr>
				<tr>
					<td align="right"><strong>{TXT_TITLE}</strong> </td>
					<td><input name="title" type="text" class="accountTextBox" id="title" value="{VAL_TITLE}" size="7" maxlength="30" /></td>
				</tr>
				<tr>
					<td align="right"><strong>{TXT_FIRST_NAME}</strong> </td>
					<td><input name="firstName" type="text" class="accountTextBox" id="firstName" value="{VAL_FIRST_NAME}" maxlength="100" /></td>
				</tr>
				<tr>
					<td align="right"><strong>{TXT_LAST_NAME}</strong> </td>
					<td><input name="lastName" type="text" class="accountTextBox" id="lastName" value="{VAL_LAST_NAME}" maxlength="100" /></td>
				</tr>
				<tr>
					<td align="right"><strong>{TXT_EMAIL}</strong> </td>
					<td><input name="email" type="text" class="accountTextBox" id="email" value="{VAL_EMAIL}" maxlength="100" /></td>
				</tr>
				<tr>
				  <td align="right" valign="top"><strong>{TXT_ADD_1}</strong> </td>
				  <td><input name="add_1" type="text" class="accountTextBox" id="add_1" value="{VAL_ADD_1}" maxlength="100" /></td>
			  	</tr>
				<tr>
				  <td align="right" valign="top"><strong>{TXT_ADD_2}</strong> </td>
				  <td><input name="add_2" type="text" class="accountTextBox" id="add_2" value="{VAL_ADD_2}" maxlength="100" /></td>
			  	</tr>
				<tr>
				  <td align="right" valign="top"><strong>{TXT_TOWN}</strong> </td>
				  <td><input name="town" type="text" class="accountTextBox" id="town" value="{VAL_TOWN}" maxlength="100" /></td>
			  	</tr>
				<tr>
				  <td align="right" valign="top"><strong>{TXT_COUNTY}</strong> </td>
				  <td><input name="county" type="text" class="accountTextBox" id="county" value="{VAL_COUNTY}" maxlength="100" /></td>
			  	</tr>
				<tr>
				  <td align="right" valign="top"><strong>{TXT_POSTCODE}</strong> </td>
				  <td><input name="postcode" type="text" class="accountTextBox" id="postcode" value="{VAL_POSTCODE}" maxlength="100" /></td>
			  	</tr>
				<tr>
				  <td align="right" valign="top"><strong>{TXT_COUNTRY}</strong> </td>
				  <td>
				  	<select name="country" id="country" class="accountSelectBox">
				  		<!-- BEGIN: country_opts -->
						<option value="{VAL_COUNTRY_NAME}" {COUNTRY_SELECTED}>{VAL_COUNTRY_NAME}</option>
						<!-- END: country_opts -->
					</select>
				  </td>
			  	</tr>
				<tr>
					<td align="right"><strong>{TXT_PHONE}</strong> </td>
					<td><input name="phone" type="text" class="accountTextBox" id="phone" value="{VAL_PHONE}" maxlength="100" /></td>
				</tr>
				<tr>
				  <td align="right"><strong>{TXT_MOBILE}</strong> </td>
				  <td><input name="mobile" type="text" class="accountTextBox" id="mobile" value="{VAL_MOBILE}" maxlength="100" /></td>
			  	</tr>
				<tr>
				  <td align="right"><strong>{TXT_COMPANY}</strong> </td>
				  <td><input name="company" type="text" class="accountTextBox" id="company" value="{VAL_COMPANY}" maxlength="100" /></td>
			  	</tr>
				<tr>
				  <td>&nbsp;</td>
				  <td><input name="submit" type="submit" value="Update" class="btnDefault accBtnMove" /></td>
			  </tr>
		</table>
	</form>
	<!-- END: session_true -->
	
	<!-- BEGIN: session_false -->
	<p>{LANG_LOGIN_REQUIRED}</p>
	<!-- END: session_false -->

</div>
<!-- END: profile -->