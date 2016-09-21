<!-- BEGIN: sideProds -->
<!-- BEGIN: prod_mod -->
<!-- BEGIN: module -->
<h3>{PROD_MOD_TITLE}</h3>
<div class="boxContentRight">

<marquee scrollamount="2" onMouseOver="this.scrollAmount='0'" onMouseOut="this.scrollAmount='2'" direction="up" loop="true" width="100%">     
     <ul>
	<!-- BEGIN: prods -->
    <li>
    	<div class="homeProds">
			
				<table width="100%" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td class="homeProdsImageHeight" colspan="2"><a href="index.php?act=viewProd&amp;productId={VAL_PRODUCT_ID}"><img src="{VAL_IMG_SRC}" alt="{VAL_PRODUCT_NAME}" border="0" title="{VAL_PRODUCT_NAME}" /></a></td>
					</tr>
					<tr>
						<td colspan="2"><a href="index.php?act=viewProd&amp;productId={VAL_PRODUCT_ID}" class="txtDefault">{VAL_PRODUCT_NAME}</a></td>
					</tr><tr>
						<td colspan="2">{TXT_DESC}</td>
					</tr>
					<tr>
						<td>{TXT_PRICE} <br /><span class="txtSale">{TXT_SALE_PRICE}</span></td>
						<td>
							<form action="{CURRENT_URL}" method="post" name="prod{PRODUCT_ID}">
								<!-- BEGIN: buy_btn -->
									<input type="hidden" name="add" value="{PRODUCT_ID}" />
									<input type="hidden" name="quan" value="1" />
									<a href="javascript:submitDoc('prod{PRODUCT_ID}');" target="_self" class="txtButton">{BTN_BUY}</a><br />
								<!-- END: buy_btn -->
							</form> 
							<a href="index.php?act=viewProd&amp;productId={PRODUCT_ID}" target="_self" class="txtButton">{BTN_MORE}</a>
						</td>
					</tr>
				</table>
			</div>
        </li>
		<!-- END: prods -->
     </ul>
</marquee>

</div>
<!-- END: module -->
<!-- END: prod_mod -->
<!-- END: sideProds -->
