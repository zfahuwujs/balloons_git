<!-- BEGIN: popular_products -->
<div class="boxTitleRight">{LANG_POPULAR_PRODUCTS_TITLE}</div>
<div class="boxContentRight">

<marquee scrollamount="2" onMouseOver="this.scrollAmount='0'" onMouseOut="this.scrollAmount='2'" direction="up" loop="true" width="100%">     
     <ol>
	<!-- BEGIN: li -->
		<div class="PopProdEa" align="center"><a title="{DATA.name}" href="index.php?act=viewProd&amp;productId={DATA.productId}"><img title="{DATA.name}" src="{PROD_IMG_SRC}" alt="{DATA.name}" border="0" /></a>
	<br /><a title="{DATA.name}" href="index.php?act=viewProd&amp;productId={DATA.productId}" class="txtDefault">{DATA.name}</a><p></p></li></div>
		<!-- END: li -->
     </ol>
</marquee>

</div>
<!-- END: popular_products -->
