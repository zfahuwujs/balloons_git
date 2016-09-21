<!-- BEGIN: change_pass -->
<div class="boxContent">

	<h1>{LANG_CHANGE_PASS_TITLE}</h1>
    <div>
    <a href="index.php?act=account" class="btnDefault">Back to Menu</a>
  </div>
  <br />
	
	<!-- BEGIN: session_true -->
	
	<!-- BEGIN: no_error -->
	<p>{LANG_PASS_DESC}</p>
	<!-- END: no_error -->
	
	<!-- BEGIN: error -->
	<p class="txtError">{VAL_ERROR}</p>
	<!-- END: error -->
	
		<!-- BEGIN: form -->
		<form action="index.php?act=changePass" target="_self" method="post">
			<table border="0" cellspacing="0" cellpadding="3" align="center" width="100%">
				<tr>
					<td align="right"><strong>{TXT_OLD_PASS}</strong></td>
					<td><input name="oldPass" type="password" class="accountTextBox" id="oldPass" maxlength="30" /></td>
				</tr>
				<tr>
					<td align="right"><strong>{TXT_NEW_PASS}</strong></td>
					<td><input name="newPass" type="password" class="accountTextBox" id="newPass" maxlength="100" /></td>
				</tr>
				<tr>
					<td align="right"><strong>{TXT_NEW_PASS_CONF}</strong></td>
					<td><input name="newPassConf" type="password" class="accountTextBox" id="newPassConf" maxlength="100" /></td>
				</tr>
				<tr>
				  <td>&nbsp;</td>
				  <td><input name="submit" type="submit" value="Update" class="btnDefault accBtnMove" /></td>
			  </tr>
		</table>
	</form>
	<!-- END: form -->
	<!-- END: session_true -->
	
	<!-- BEGIN: session_false -->
	<p>{LANG_LOGIN_REQUIRED}</p>
	<!-- END: session_false -->

</div>
<!-- END: change_pass -->