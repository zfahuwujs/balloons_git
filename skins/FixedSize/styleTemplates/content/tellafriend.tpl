<!-- BEGIN: tellafriend -->
<div class="boxContent">

	<h1>{TAF_TITLE}</h1>
	<!-- BEGIN: error -->
	<p class="txtError">{VAL_ERROR}</p>
	<!-- END: error -->
	<p>{TAF_DESC}</p>
	
		<form action="index.php?act=taf&amp;productId={PRODUCT_ID}" target="_self" method="post">
			<table border="0" cellspacing="0" cellpadding="3" align="center">
				<tr>
					<td align="right"><strong>{TXT_RECIP_NAME}</strong></td>
					<td><input type="text" name="recipName" class="contactTextBox" /></td>
				</tr>
				<tr>
					<td align="right"><strong>{TXT_RECIP_EMAIL}</strong></td>
					<td><input type="text" name="recipEmail" class="contactTextBox" /></td>
				</tr>
				<tr>
					<td align="right"><strong>{TXT_SENDER_NAME}</strong></td>
					<td><input type="text" name="senderName" class="contactTextBox" value="{VAL_SENDER_NAME}" /></td>
				</tr>
				<tr>
					<td align="right"><strong>{TXT_SENDER_EMAIL}</strong></td>
					<td><input type="text" name="senderEmail" class="contactTextBox" value="{VAL_SENDER_EMAIL}" /></td>
				</tr>
				<!-- BEGIN: spambot -->
				<tr>
				  <td align="right" valign="bottom"><strong>{TXT_SPAMBOT}</strong></td>
				  <td>
                    <input name="verif_box" type="text" id="verif_box" class="contactTextBox2"/>
					<img src="verificationimage.php?{RAND_NUM}" alt="verification image, type it in the box" width="50" height="24" align="absbottom" />
                  </td>
                </tr>
			  	<!-- END: spambot -->
				<tr>
					<td align="right" valign="top"><strong>{TXT_MESSAGE}</strong></td>
					<td><textarea name="message" cols="30" rows="5" class="contactTextArea">{VAL_MESSAGE}</textarea></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td align="center"><input name="submit" type="submit" value="Send Message" class="btnSnd" /></td>
				</tr>
		</table>
	<input name="ESC" type="hidden" value="{VAL_ESC}" />
	</form>

</div>
<!-- END: tellafriend -->