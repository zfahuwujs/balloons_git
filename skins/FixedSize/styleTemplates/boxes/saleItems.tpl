<!-- BEGIN: sale_items -->
<h3>{LANG_SALE_ITEMS_TITLE}</h3>
<div class="boxContentRight">
	<marquee scrollamount="2" onMouseOver="this.scrollAmount='0'" onMouseOut="this.scrollAmount='2'" direction="up" loop="true" width="100%">     
     <ol>
	<!-- BEGIN: li -->
    	
		<div class="PopProdEa" align="center">
        	<a title="{DATA.name}" href="index.php?act=viewProd&amp;productId={DATA.productId}"><img title="{DATA.name}" src="{PROD_IMG_SRC}" alt="{DATA.name}" border="0" /></a>
	
    		<br />
    		<a title="{DATA.name}" href="index.php?act=viewProd&amp;productId={DATA.productId}" class="txtDefault">{DATA.name}</a><br />
            now only: <span style="font-size: 14px">{PRICE}</span>
            <br />
            
    		<span style="color: #FF0000;">{LANG_SAVE} {SAVING}</span>
        </div>
       
		<!-- END: li -->
     </ol>
</marquee>
</div>
<!-- END: sale_items -->