<!-- BEGIN: mail_list -->
<div style="float:left;width:500px;">
	<!-- BEGIN: form -->
  <form action="{FORM_METHOD}" method="post">
	<table cellpadding="0" cellspacing="0" border="0">
  	<tr>
    	<td><strong>Sign up to our free newsletter</strong></td>
      <td rowspan="2">
      	 <input name="email" type="text" size="14" maxlength="255" class="textbox" style="margin-left:10px;width:230px;" value="Email Address..." onblur="if (this.value=='') this.value = this.defaultValue" onfocus="if (this.value==this.defaultValue) this.value = ''" /> 
         <input type="hidden" name="act" value="mailList" />
      </td>
      <td rowspan="2"><input name="submit" type="submit" value="Submit" class="mailBtn" /></td>
    </tr>
    <tr>
    	<td>for the latest news and updates</td>
    </tr>
  </table>
	</form>
	<!-- END: form -->
</div>
<!-- END: mail_list -->