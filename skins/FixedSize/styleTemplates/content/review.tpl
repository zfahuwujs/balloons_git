<!-- BEGIN: review -->
<div class="boxContent">
<h1>{REVIEW_TITLE}</h1>
<br /><p>
<strong>{PROD_DESC}</strong><br />{REVIEW_DESC}<br/>
<strong><font color="#07C30B">{SUCCESS}</font></strong>
<strong><font color="#FF0000">{NEED_REG}</font></strong>
<strong><font color="#FF0000">{ERROR_MESS}</font></strong>
<strong><font color="#FF0000">{IP_ERROR}</font></strong>
<br /><br />
  <div align="left">
  
      <form action="index.php?act=review&amp;productId={PRODUCT_ID}" method="post" enctype="multipart/form-data" name="form1" target="_self">
      <input type="hidden" name="productId" value="{PRODUCT_ID}" />  
       <input type="hidden" name="productname" value="{FORM_NAME}" />
	 <div style="float: left; width: 420px;"> 
   <strong>{CUSTOMER_NAME}<font color="#FF0000"> *</font></strong> 
    <br />
<input name="name" class="textbox"  type="text"  size="30" maxlength="30" value="{NAME}"><br/>
    <br />
	 <strong>Enter your Location<font color="#FF0000"> *</font></strong> <br />
    <br />
<input name="location" class="textbox"  type="text"  size="30" maxlength="30" value="{LOCATION}"><br /><br />
<strong>{NEW_COMMENT}<font color="#FF0000"> *</font></strong><br />
 <textarea name="message" cols="40" rows="5" id="message">{MESSAGE}</textarea><br />
</div>
<div style="float: left;" >
<b>{STAR_RATING}<font color="#FF0000"> *</font></b> <br /><br />


<img src="/images/stars-1.gif" border="0" align="left" style="vertical-align:middle;" /><input type="radio" name="stars" value="0" ><br /><br />
<img src="/images/stars-2.gif" border="0" align="left" style="vertical-align:middle;" /><input type="radio" name="stars" value="1"><br /><br />
<img src="/images/stars-3.gif" border="0" align="left" style="vertical-align:middle;" /><input type="radio" name="stars" value="2"><br /><br />
<img src="/images/stars-4.gif" border="0" align="left" style="vertical-align:middle;" /><input type="radio" name="stars" value="3"><br /><br />
<img src="/images/stars-5.gif" border="0" align="left" style="vertical-align:middle;" /><input type="radio" name="stars" value="4"> <br /><br />

		  <br />
<input type="submit" name="submit" class="submit" value="{SUBMIT_REVIEW}">
</div>
	<br clear="all" />
</form>
        </div>
        
    </div>
<!-- END: review -->

