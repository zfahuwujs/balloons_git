<!-- BEGIN: session -->
<div class="sessionSurround">
	<!-- BEGIN: session_false -->
	<img src="skins/FixedSize/styleImages/backgrounds/login.png" alt="loginImg" style="position:relative;top:5px;" />
  <a style="color:#0E99DA" href="index.php?act=login&amp;redir={VAL_SELF}">{LANG_LOGIN}</a> | <a href="cart.php?act=reg&amp;redir={VAL_SELF}" >{LANG_REGISTER}</a>
	<!-- END: session_false -->
	
	<!-- BEGIN: session_true -->
	<!--{TXT_USERNAME}--><a href="index.php?act=logout">{LANG_LOGOUT}</a> | <a href="index.php?act=account">{LANG_YOUR_ACCOUNT}</a>
	<!-- END: session_true -->
</div>
<!-- END: session -->