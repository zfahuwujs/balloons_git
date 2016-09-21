<!-- BEGIN: index -->




<style>
.c2242{height:320px;}
</style>
<!-- SLIDER -->
<div id="carousel">
        <ul>
        	<!-- BEGIN: slider_images -->
            <li class="c2242">
            {SLIDER_IMG_LINK}
            	<img src="{SLIDER_IMG_PATH}" width="960" height="320" />
				<!-- BEGIN: para -->				<p><span class="sliderText">{SLIDER_IMG_TEXT}</span></p>				<!-- END: para -->
            {SLIDER_IMG_END}
            </li>  
            <!-- END: slider_images -->
        </ul>
</div>

<!--/ SLIDER -->



<div id="categoriesWrapper">
	{CAT_WITH_IMAGES}
</div>
<div style="clear:both;height:0px;"></div>

<!-- BEGIN: prod_mod -->
<!-- BEGIN: module -->
	<div style="clear:both;height:0px;"></div>
	<div class="boxContent">
	<h1 class="moduleTitle">{PROD_MOD_TITLE}</h1>
    
		<!-- BEGIN: prods -->
			<div class="homeProds">
      	<table width="100%" cellpadding="0" cellspacing="0" border="0">
        	<tr>
          	<td colspan="2" align="center" valign="middle" style="border:1px solid #c3ccc9;height:108px;">
            	<a href="index.php?act=viewProd&amp;productId={VAL_PRODUCT_ID}">
              	<img src="{VAL_IMG_SRC}" alt="{VAL_PRODUCT_NAME}" border="0" height="106" title="{VAL_PRODUCT_NAME}" />
            	</a>
            </td>
          </tr>
          <tr>
          	<td colspan="2" height="35">
            	<div style="width:100%;height:35px;overflow:hidden;"><h4><a href="index.php?act=viewProd&amp;productId={VAL_PRODUCT_ID}">{VAL_PRODUCT_NAME}</a></h4></div>
            </td>
          </tr>
         <!-- <tr>
          	<td colspan="2" style="padding:0px 5px"><p>Part No: {PART_NO}</p></td>
          </tr>-->
          <tr>
          	<td colspan="2">
            	<p class="prodPrice">Starts at {TXT_PRICE} <span class="txtSale">{TXT_SALE_PRICE}</span></p>
            </td>
          </tr>
        </table>
                
			</div>
		<!-- END: prods -->
        </div>
        <div class = "clearing"></div>
	
    <!-- END: module -->
<!-- END: prod_mod -->



 <!-- BEGIN: brands -->
    <div class="boxContent">
    <h1 class="moduleTitle">FEATURED BRANDS</h1>
    <ul id="mycarouselBrands" class="jcarousel-skin-tangoBrands">
    	<!-- BEGIN: item -->
        <li><!--<a href="/index.php?act=viewBrand&amp;brandId={BRAND_ID}">--><img src="{BRAND_IMAGE}" alt="{BRAND_NAME}" title="{BRAND_NAME}" width="186" height="71" /></li>
    	<!-- END: item -->
    </ul>
    </div>
<!-- END: brands -->



<!-- BEGIN: home_content -->
<div class="boxContent">
	{HOME_CONTENT}
</div>
<!-- END: home_content -->







<!-- END: index -->



















<!-- Starting HomePage Categories -->
<!-- BEGIN: homepage_cats -->
<div class="boxContent">
<h2>Browse Our Categories</h2>
	<div>
	<!-- BEGIN: homepage_cats_loop -->
		<div class="homeCats">
		<a href="index.php?act=viewCat&amp;catId={TXT_LINK_CATID}"><img src="{IMG_CATEGORY}" alt="{TXT_CATEGORY}" border="0" title="{TXT_CATEGORY}" /></a><br />
		<a href="index.php?act=viewCat&amp;catId={TXT_LINK_CATID}">{TXT_CATEGORY}<!--{TXT_CATDESC}--></a>
		</div>
	<!-- END: homepage_cats_loop -->
	<br clear="all" />
	</div>
</div>
<!-- END: homepage_cats -->
<!-- Ending HomePage Categories -->

 <tr>
          	<td colspan="2" style="padding:5px">
            	<form action="{CURRENT_URL}" method="post" name="prod{PRODUCT_ID}_{MOD_NUM}">
              	<!-- BEGIN: buy_btn -->
                <input type="hidden" name="add" value="{PRODUCT_ID}" />
                <input type="hidden" name="quan" value="1" />
                <a href="javascript:submitDoc('prod{PRODUCT_ID}_{MOD_NUM}');" target="_self" class="btnDefault2">Add to Basket</a><br />
                <!-- END: buy_btn -->
              </form>
            </td>
          </tr>