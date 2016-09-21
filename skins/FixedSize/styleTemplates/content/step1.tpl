<!-- BEGIN: session_page -->
<div class="boxContent">
			<!-- BEGIN: cart_false -->
            <p>{LANG_CART_EMPTY}</p>
			<!-- END: cart_false -->
			<!-- BEGIN: cart_true -->
            <form action="index.php?act=login&amp;redir={VAL_SELF}" target="_self" method="post">
			<table width="50%" align="left">
				<tr>
					<td>
					<h1>{LANG_LOGIN_TITLE}</h1>
					</td>
				</tr>
				<tr>
					<td height="120px">
						<p>{LANG_LOGIN_BELOW}</p>
					
						<table border="0" cellspacing="0" cellpadding="3">
							<tr>
								<td align="right"><strong>{LANG_USERNAME}</strong></td>
								<td><input type="text" name="username" class="textbox" value="{VAL_USERNAME}" /></td>
							</tr>
							<tr>
								<td align="right"><strong>{LANG_PASSWORD}</strong></td>
								<td><input type="password" name="password" class="textbox" /></td>
							</tr>
							<tr>
								<td align="right">{LANG_REMEMBER}</td>
								<td><input name="remember" type="checkbox" value="1" {CHECKBOX_STATUS} /></td>
							</tr>
					  	</table>
					
					</td>
				</tr>
				<tr>
					<td>
						<input name="submit" style="float: left;" type="submit" class="submit" value="Login" />
                        <a style="margin-left: 5px; float:left;" href="cart.php?act=reg" class="btnDefault">Register Now</a>
					</td>
				</tr>
				<tr>
					<td>
						<a href="index.php?act=forgotPass" class="txtDefault">{LANG_FORGOT_PASS}</a>
					</td>
				</tr>
			</table>
            <!-- BEGIN: no_reg -->
            <table width="50%" align="right">
				<tr>
					<td>
					<h1>I want to just pay and go.</h1>
					</td>
				</tr>
				<tr>
					<td height="120px">
						<div class="regSepContentHeight">Use this option to place an order without registering with us. You will be given the option to register during the checkout process. The advantage of registering is that you will be able to track and monitor your order.</div>
					</td>
				</tr>
				<tr>
					<td>
						<a href="cart.php?act=step2" class="btnDefault">Pay and Go</a>
                        <a href="index.php" class="btnDefault">Continue Shopping</a>
					</td>
				</tr>
			</table>
            <!-- END: no_reg -->
			</form>
			<!-- END: cart_true -->
            <div class = "clearing"></div>
</div>
<!-- END: session_page -->