
<!-- BEGIN: view_prod -->

<div class="boxContent">
  <!-- BEGIN: prod_true -->
  <form action="{CURRENT_URL}" method="post" name="addtobasket" target="_self">
  <p class="directions2"><a href="/">Home</a> {CURRENT_DIR} &raquo; <span style="color:#000 !important">{TXT_PRODTITLE}</span></p>
   <h1 class="moduleTitle">{TXT_PRODTITLE}</h1>
  <div class="prodImg">
  <table width="100%" cellpadding="0" cellspacing="0" border="0" style="min-height:480px;">
  	<tr>
    	<td align="center">
      	<div id="gallery" class="ad-gallery">
      		<div class="ad-image-wrapper"></div>
      		<div class="ad-controls"></div>
      		<div class="ad-nav">
        	<div class="ad-thumbs">
          	<ul class="ad-thumb-list">
            	<li>
              	<a href="{IMG_SRC}">
                	<img src="{IMG_SRC_THUMB}" class="image0" title="&nbsp;" height="100">
              	</a>
            	</li>
              <!-- BEGIN: more_images -->
              	<!-- BEGIN: thumbs -->
            	<li>
              	<a href="{VALUE_SRC}">
                	<img src="{VALUE_THUMB_SRC}" title="&nbsp;" alt="" height="100">
              	</a>
            	</li>
              	<!-- END: thumbs -->
              <!-- END: more_images -->
            </ul>
           </div>
          </div>
         </div>
      </td>
    </tr>
  </table>
  </div>
  
  <div class="prodInfo">
  <table cellpadding="0" cellspacing="0" border="0" width="100%">
  <tr>
  	<td align="left" style="border-bottom:1px solid #E3E3E3;border-top:1px solid #E3E3E3;padding:5px 0;">
    	<p class = "in_stock">In stock</p>
      <p class = "out_of_stock">Out of stock</p>
      <p id="emailMeWhenInStock">Email me when in stock</p>
      <input type="hidden" id="prodOptions" value="">      
      <!-- BEGIN: in_stockOFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF -->
      <p class = "in_stock">In stock</p>
      <!-- END: in_stockOFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF -->
      
      <!-- BEGIN: out_of_stockOFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF -->
      <p class = "out_of_stock">Out of stock</p>            
      <!-- END: out_of_stockOFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF -->
    </td>
    <td align="right" style="border-bottom:1px solid #E3E3E3;border-top:1px solid #E3E3E3;padding:5px 0;">
    	<!-- BEGIN: price -->
    	<p>Price: <span id="productPrice" class="prodPrice">{TXT_PRICE} </span><span class="prodPriceSale">{TXT_SALE_PRICE}</span></p>
      <!-- END: price -->
    </td>
  </tr>
  <tr>
  	<td colspan="2" align="center" valign="middle" height="100" style="border-bottom:1px solid #E3E3E3;padding:5px 0;">
    	<img src='/images/uploads/{BRAND_IMG_SRC}' alt="{BRAND_NAME}" height="80" border="0">
    </td>
  </tr>
  <tr>
  	<td colspan="2">
    <!-- BEGIN: prod_opts -->
    <table border="0" cellspacing="0" cellpadding="3" style="padding-top:10px;">
    	<!-- BEGIN: repeat_options -->
      <tr>
      	<td><span class="prodOptionName">{VAL_OPTS_NAME}</span></td>
      </tr>
      <tr>
      	<td>
        	<select name="productOptions[]" class="productOptions">
          	<!-- BEGIN: repeat_values -->
            <option value="{VAL_ASSIGN_ID}"> {VAL_VALUE_NAME}
              <!-- BEGIN: repeat_priceOFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF -->
              ({VAL_OPT_SIGN}{VAL_OPT_PRICE})
              <!-- END: repeat_priceOFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF -->
            </option>
            <!-- END: repeat_values -->
          </select>
        </td>
      </tr>
    	<!-- END: repeat_options -->
    </table>
    <!-- END: prod_opts -->
    </td>
  </tr>
  </table>
  <script>
  $(document).ready(function(e) {
		$('.in_stock').hide();
		$('.out_of_stock').hide();
		$('#emailMeWhenInStock').hide();	
		var prodId = "{PRODUCT_ID}";
		var prodOpt = "";
		$('.productOptions').each(function() {
    	prodOpt += ($(this).val()) + "|";
    });
		checkPriceAndStock(prodId, prodOpt);
		
		
		//////////////////////////////////////////////////////////////////////		TU
		//getEmailMeTxt(prodId, prodOpt);
		
		
		
		$('.productOptions').change(function(e) {
			var prodId = "{PRODUCT_ID}";
			var prodOpt = "";
			$('.productOptions').each(function() {
    		prodOpt += ($(this).val()) + "|";
    	});
    	checkPriceAndStock(prodId, prodOpt);
			//$('.activeEmailMeLink').click(function() {
			//	alert('ok');
      //  getEmailMeTxt(prodId, prodOpt);
      //});
    });
		function checkPriceAndStock(prodId, prodOpt){
			$.ajax({
      	type: "POST",
				dataType: 'json', 
        url: "/checkPriceStock.ajax.php",
        data: "prodOpt="+prodOpt+"&prodId="+prodId,
        success: function(data) {
				//alert(data.emailMeMsg);
					if(!parseInt(data.stock)>0){
						$('.in_stock').hide();
						$('.out_of_stock').show();
						$('.prodPrice').html(data.price);
						$('#productPrice').removeClass("offPrice");
						$('.prodPriceSale').html("");
						if(data.emailMe>0){
							$('#emailMeWhenInStock').html(data.emailMeMsg).show();
							if(data.emailMe==1){
								$('#emailMeWhenInStock').addClass('activeEmailMeLink');
							}else{
								$('#emailMeWhenInStock').removeClass('activeEmailMeLink');
							}
						}
					}else{
						$('.in_stock').show();
						$('.out_of_stock').hide();
						$('#emailMeWhenInStock').hide();
						$('.prodPrice').html(data.price);
						$('#productPrice').removeClass("offPrice");
						if(data.salePrice != "NULL"){
							$('.prodPriceSale').html(data.salePrice);
							$('#productPrice').addClass("offPrice");
						}
					}
        },//end success
				error: function() {
					alert('An error has occurred in checkPriceAndStock');
				}
      });//end ajax call
		}
		//////////////////////////////////////////////////////////////////////		TU
		
  });
	$(document).on("click", ".activeEmailMeLink", function() {
		var prodId2 = "{PRODUCT_ID}";
		var prodOpt2 = "";
		$('.productOptions').each(function() {
    	prodOpt2 += ($(this).val()) + "|";
    });
		getEmailMeTxt(prodId2, prodOpt2);
	});
	function getEmailMeTxt(prodId, prodOpt){
		$.ajax({
    	type: "POST",
			dataType: 'json', 
      url: "/emailMeWhenInStock.ajax.php",
      data: "prodId="+prodId+"&prodOpt="+prodOpt,
      success: function(data) {
			//alert(data.prodOptions);
			if(data.emailMe>0){
				$('#emailMeWhenInStock').html(data.emailMeMsg).show();
					if(data.emailMe==1){
						$('#emailMeWhenInStock').addClass('activeEmailMeLink');
					}else{
						$('#emailMeWhenInStock').removeClass('activeEmailMeLink');
					}
				}
      },//end success
			error: function(data) {
				alert('An error has occurred in emailMeWhenInStock');
			}
    });//end ajax call
	}
  </script>
  <!-- BEGIN: buy_btn -->
  <table cellpadding="0" cellspacing="0" border="0" width="100%" style="margin-top:10px;">
  <tr>
  	<td width="50"><span class="prodOptionName">Quantity:</span></td>
    <td rowspan="2" align="right" valign="middle">
    	<!-- BEGIN: add_btn -->
      	<!-- BEGIN: add_btn2 -->
        <div id="backetBtnDiv">
          <a href="javascript:submitDoc('addtobasket');" class="buyBtn">Add to Basket</a> 
        </div>
        <input type="hidden" name="add" value="{PRODUCT_ID}" />
        <!-- END: add_btn2 -->
       <!-- END: add_btn --> 
    </td>
  </tr>
  <tr>
  	<td><input name="quan" type="text" value="1" size="2" class="textbox" id="quan" style="text-align:center;" /></td>
  </tr>
  </table>
  <!-- END: buy_btn -->
	   
  </div>
  <div style="clear:both;height:10px;"></div>
  <div class="tabContainer">
	<ul class="tabHeader">
  	<li>Description</li>
		<li>Customer Reviews</li>
    <li>Shipping information</li>
	</ul>
	<div class="contents">
  	<div class="tabContent">
    	{TXT_DESCRIPTION}
    </div>
		<div class="tabContent">
    	 <!-- BEGIN: customer_reviews -->
      <div class="prodRev">
        <h3>Product Reviews</h3>
        <div class="reviews"> 
        <!-- BEGIN: reviews -->
        <strong>Reviewer</strong> : <strong>{REV_NAME}</strong> from {REV_LOC} <br />
        <span style="font-size:10px;">Added: {REV_DATE}</span><br />
        {REV_STARS}<br />
          {REV_COMMENTS} <br/>
          <br />
           <!-- END: reviews -->
          <a href="index.php?act=review&amp;productId={PRODUCT_ID}" class="btnDefault">Add a Review</a>
        </div>
      </div>
      <!-- END: customer_reviews -->
    </div>
        <div class="tabContent">
            {SHIPPING_INFO}
        </div>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function (){
	$('.tabContent:gt(0)').hide();
	$('.tabHeader > li:eq(0)').addClass('active');
	$('.tabHeader > li').click(showHideTabs);
});

function showHideTabs(){
	var allLi = $('.tabHeader > li').removeClass('active')
	$(this).addClass('active');
	var index = allLi.index(this);
	$('.tabContent:visible').hide();
	$('.tabContent:eq('+index+')').show();
}
</script>
  
<!--  END OF TABS				-->

<div style="clear:both"></div>

<!-- BEGIN: related_prods_true -->
  <div class="prodRel">
    <h1 class="moduleTitle">{TXT_RELATED_PRODUCTS}</h1>
    <!-- BEGIN: repeat_related_prods -->
    <div class="homeProds">
      	<table width="100%" cellpadding="0" cellspacing="0" border="0">
        	<tr>
          	<td colspan="2" align="center" valign="middle" style="border:1px solid #c3ccc9;height:108px;">
            	<a href="{VALUE_RELATED_LINK}">
              	<img src="{VALUE_RELATED_THUMB}" alt="{VALUE_RELATED_NAME}" border="0" height="106" title="{VALUE_RELATED_NAME}" />
            	</a>
            </td>
          </tr>
          <tr>
          	<td colspan="2" height="35">
            	<div style="width:100%;height:35px;overflow:hidden;"><h4><a href="{VALUE_RELATED_LINK}">{VALUE_RELATED_NAME}</a></h4></div>
            </td>
          </tr>
          <tr>
          	<td colspan="2">
            	<p class="prodPriceRel">Starts {REL_PRICE} <span class="txtSale">{REL_PRICE_SALE}</span></p>
            </td>
          </tr>
        </table>
                
			</div>
    <!-- END: repeat_related_prods -->
    <div class = "clearing"></div>
  </div>
  <!-- END: related_prods_true -->



        </form>
        <div class = "clearing"></div>

  
  <!-- END: prod_true -->
  <!-- BEGIN: prod_false -->
  <p>{LANG_PRODUCT_EXPIRED}</p>
  <!-- END: prod_false -->
</div>
<!-- END: view_prod -->