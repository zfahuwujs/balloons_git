<!-- BEGIN: quote -->
<div class="boxContent">
	<h1>Get a Quote</h1>
    <!-- BEGIN: sent -->
    <p>Thank you, Your quotation has been successfully sent.</p>
    <!-- END: sent -->
    <!-- BEGIN: not_sent -->
	<p>Please fill out the form below to request a quote for our product: {PRODUCT_NAME} ({PRODUCT_CODE})</p>
    
	<form action="index.php?act=quote&amp;productId={PRODUCT_ID}" target="_self" method="post">
		<table border="0" cellspacing="0" cellpadding="3" align="center">
        	<tr>
				<td>&nbsp;</td>
				<td>
                	<!-- BEGIN: errors -->
                        <div style="border:1px solid #990000; background-color:#D70000; color:#FFFFFF; padding:4px; padding-left:6px;width:295px;">{ERROR}</div>
                    <!-- END: errors -->
                </td>
			</tr>
			<tr>
				<td align="right">Your Name: </td>
				<td><input type="text" name="name" class="contactTextBox" value="{VAL_NAME}" /></td>
			</tr>
			<tr>
				<td align="right">Your Email: </td>
				<td><input type="text" name="from" class="contactTextBox" value="{VAL_EMAIL}" /></td>
			</tr>
			<tr>
		    	<td align="right">Image Verification: </td>
				<td>
                  	<input name="verif_box" type="text" id="verif_box" class="contactTextBox2"/>
                    <img src="verificationimage.php?{RAND_NUM}" alt="verification image, type it in the box" width="50" height="24" align="absbottom" />
                    <!-- BEGIN: wrong_code -->
                  	  <div style="border:1px solid #990000; background-color:#D70000; color:#FFFFFF; padding:4px; padding-left:6px;width:295px;">Wrong verification code</div>
                    <!-- END: wrong_code -->
                </td>
            </tr>
			<tr>
				<td align="right" valign="top">Message: </td>
				<td><textarea name="message" cols="30" rows="5" class="contactTextArea">{VAL_MESSAGE}</textarea></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td align="center"><input name="submit" type="submit" value="Request Quote" class="btnDefault" /></td>
			</tr>
		</table>
	</form>
	<!-- END: not_sent -->
</div>
<!-- END: quote -->