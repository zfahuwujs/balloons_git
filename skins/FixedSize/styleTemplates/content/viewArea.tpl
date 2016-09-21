<!-- BEGIN: account -->
<div class="boxContent">
	
	<h1>{LANG_YOUR_ACCOUNT}</h1>
	
	<!-- BEGIN: session_true -->
	<div align="center">
<table border="0" width="463" height="350">
<tr>
<td width="226" height="175" align="center" valign="bottom" background="images/Contacts.png">
<p><a href="index.php?act=profile" class="txtDefault">{TXT_PERSONAL_INFO}</a></td>
<td width="227" height="175" align="center" valign="bottom" background="images/Inbox.png">
<p><a href="cart.php?act=viewOrders" class="txtDefault">{TXT_ORDER_HISTORY}</a></td>
</tr>
<tr>
<td width="226" height="175" align="center" valign="bottom" background="images/Locker.png">
<p><a href="index.php?act=changePass" class="txtDefault">{TXT_CHANGE_PASSWORD}</a></td>
<td width="227" height="175" align="center" valign="bottom" background="images/All-mail.png">
<p><a href="index.php?act=newsletter" class="txtDefault">{TXT_NEWSLETTER}</td>
</tr>
</table>

</div>
	<!-- END: session_true -->
	
	<!-- BEGIN: session_false -->
	<p>{LANG_LOGIN_REQUIRED}</p>
	<!-- END: session_false -->
			
</div>
<!-- END: account -->