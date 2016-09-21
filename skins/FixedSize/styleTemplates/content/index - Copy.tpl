<!-- BEGIN: index -->

<!-- SLIDER -->
<div id="carousel">
        <ul>
        	<!-- BEGIN: slider_images -->
            <li>
            {SLIDER_IMG_LINK}
            	<img src="{SLIDER_IMG_PATH}" width="700" height="283" /><!-- BEGIN: para --><p><span class="sliderText">{SLIDER_IMG_TEXT}</span></p><!-- END: para -->
            {SLIDER_IMG_END}
            </li>  
            <!-- END: slider_images -->
        </ul>
</div>


<!-- BEGIN: home_content -->
<div class="boxContent">
{HOME_TITLE}
{HOME_CONTENT}
</div>
<!-- END: home_content -->



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








<!-- BEGIN: prod_mod -->
<!-- BEGIN: module -->
	<div class="boxContent">
	<h2>{PROD_MOD_TITLE}</h2>
    
    	<div class = "aModule">
		<!-- BEGIN: prods -->
			<div class="homeProds {COLOR}">
                
                <div class="homeProdsImg">
	                <a href="index.php?act=viewProd&amp;productId={VAL_PRODUCT_ID}">
                    	<img src="image_resize.php?im={VAL_IMG_SRC}&amp;size=118&amp;y_size=118" alt="{VAL_PRODUCT_NAME}" border="0" title="{VAL_PRODUCT_NAME}" />
                    </a>
                </div>
                
                
                <div class="homeProdsText">
                    <h4><a href="index.php?act=viewProd&amp;productId={VAL_PRODUCT_ID}">{VAL_PRODUCT_NAME}</a></h4>
                    {TXT_DESC}
                </div>
                
                
                <div class="homeProdsInfo">
                    <p>Price: <span>{TXT_PRICE} <span class="txtSale">{TXT_SALE_PRICE}</span></span></p>
					<p>Part No: {PART_NO}</p>
                    <p class = "in_stock">In stock</p>
                    
                    <a href="index.php?act=viewProd&amp;productId={PRODUCT_ID}" target="_self" class="special">View item</a>
                    <form action="{CURRENT_URL}" method="post" name="prod{PRODUCT_ID}_{MOD_NUM}">
                        <!-- BEGIN: buy_btn -->
                            <input type="hidden" name="add" value="{PRODUCT_ID}" />
                            <input type="hidden" name="quan" value="1" />
                            <a href="javascript:submitDoc('prod{PRODUCT_ID}_{MOD_NUM}');" target="_self" class="btnDefault2">Add to Basket</a><br />
                        <!-- END: buy_btn -->
                    </form> 
                </div>
		        <div class = "clearing"></div>
                
			</div>
		<!-- END: prods -->
        </div>
        <div class = "clearing"></div>
	</div>
    <!-- END: module -->
<!-- END: prod_mod -->














<!-- END: index -->