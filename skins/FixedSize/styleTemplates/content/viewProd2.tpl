<!-- BEGIN: view_prod -->

<div class="boxContent">
	<!-- BEGIN: prod_true -->
    <div class="breadCrums">
	<a href="/" class="txtLocation">Home</a> {CURRENT_DIR}
    </div>
<form action="{CURRENT_URL}" method="post" name="addtobasket" target="_self">
	
	
	
    <div style="margin-top:10px;">
        <!-- start mod Related Items -->
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr valign="top">
        <td width="300">
        <div class="mainImage">
        	<a href='{LRG_SRC}' class='cloud-zoom' id="zoom1" rel="">
            <img src="{IMG_SRC}" alt="{TXT_PRODTITLE}" id="img_preview" name="preview" style="border: 3px solid #D3DBD8;" />
            </a>
        </div>
            <div style="text-align: center;">
            <!-- BEGIN: thumbs -->
            <div class="productThumbs"{LAST_THUMB}>
        	<a href='{VALUE_ZOOM_SRC}' class='cloud-zoom-gallery' rel="useZoom: 'zoom1', smallImage:'{VALUE_IMG_SRC}'">
            <img src="{VALUE_THUMB_SRC}" alt="{TXT_PRODTITLE}" name="preview" border="0" id="img_preview" />
            </a>
            </div>
            <!-- END: thumbs -->
            </div>
        </td>
        <td align="left" style="padding-left:5px;">

<div>
<h1 class="txtContentTitle"><strong>{TXT_PRODTITLE}</strong></h1>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="priceBox">
  <tr>
    <td class="prevProduct">{PREV}</td>
    <td class="nextProduct" align="right">{NEXT}</td>
    </tr>
  <tr>
    <td><strong>{TXT_PRICE}</strong> <span class="txtSale">{TXT_SALE_PRICE}</span></td>
    <td align="right"><strong>{TXT_INSTOCK}{TXT_OUTOFSTOCK}</strong></td>
  </tr>
  <tr>
    <td colspan="2">{REVIEWHTML}</td>
    </tr>
</table>

        	<!--{RRP_PRICE}<br />
			
            <br />{SAVING}-->
            
        <!-- BEGIN: prod_opts -->
            <br />
            <strong>{TXT_PROD_OPTIONS}</strong>
            <table border="0" cellspacing="0" cellpadding="3">
                <!-- BEGIN: repeat_options -->
                <tr>
                    <td><strong>{VAL_OPTS_NAME}</strong></td>
                    <td>
                        <select name="productOptions[]">
                            <!-- BEGIN: repeat_values -->
                            <option value="{VAL_ASSIGN_ID}">
                            {VAL_VALUE_NAME}
                            <!-- BEGIN: repeat_price -->
                            ({VAL_OPT_SIGN}{VAL_OPT_PRICE})
                            <!-- END: repeat_price -->
                            </option>
                            <!-- END: repeat_values -->
                        </select>
                    </td>
                </tr>
            	<!-- END: repeat_options -->
            </table>
        <!-- END: prod_opts -->
        <br />
        
        <!--<strong>{LANG_PRODCODE}</strong> {TXT_PRODCODE} -->

		<div>
			
	
            <!-- BEGIN: buy_btn -->
            <div style="position: relative; text-align: right;"><strong>{LANG_QUAN}</strong>
              <input name="quan" type="text" value="1" size="2" class="textbox" style="text-align:center;" />
            <br />
            <table align="right"><tr><td>
            <a href="javascript:submitDoc('addtobasket');"><img src="/skins/FixedSize/styleImages/backgrounds/add_to_basket.png" alt="Add to Basket" width="159" height="35" border="0" /></a></td></tr><tr><td><a href="{WISHLINK}">{WISHTEXT}</a></td></tr></table>
            </div>
            <!-- END: buy_btn -->
		</div>  
    </div>

        </td>
        </tr>
        </table>
    	<!-- end mod Related Items -->
	</div>
    
    <br clear="all" />
<input type="hidden" name="add" value="{PRODUCT_ID}" />
</form>	    
    
<div id="TabbedPanels1" class="TabbedPanels">
  <ul class="TabbedPanelsTabGroup">
    <li class="TabbedPanelsTab" tabindex="0">Product Description</li>
    <li class="TabbedPanelsTab" tabindex="0">Specifications</li>
    <li class="TabbedPanelsTab" tabindex="0">Delivery and Returns</li>
    <li class="TabbedPanelsTab" tabindex="0">Customer Reviews</li>
  </ul>
  <div class="TabbedPanelsContentGroup">
	<div class="TabbedPanelsContent">{TXT_DESCRIPTION}</div>
    <div class="TabbedPanelsContent">{TXT_FEATURES}</div>
    <div class="TabbedPanelsContent">{TXT_DELIVERY}</div>
    <div class="TabbedPanelsContent">
    <h3>Customer Reviews</h3>
    <!-- BEGIN: reviewItem -->
    <div class="revirewItem">
    <table border="0" cellpadding="0" cellspacing="0"><tr><td>By: {USERNAME}</td><td>{REV_STARS}</td></tr></table>
    <div>
    {REVIEWTEXT}
    </div>
    {REWVIEWDATE}
    </div>
    <!-- END: reviewItem -->
    <h3>Add a review</h3>
    <script>
	function oneStarHover(){
		$('.star1').attr('src','/skins/FixedSize/styleImages/backgrounds/fullStar.png');
		$('.star2').attr('src','/skins/FixedSize/styleImages/backgrounds/emptyStar.png');
		$('.star3').attr('src','/skins/FixedSize/styleImages/backgrounds/emptyStar.png');
		$('.star4').attr('src','/skins/FixedSize/styleImages/backgrounds/emptyStar.png');
		$('.star5').attr('src','/skins/FixedSize/styleImages/backgrounds/emptyStar.png');
	}
	function twoStarHover(){
		$('.star1').attr('src','/skins/FixedSize/styleImages/backgrounds/fullStar.png');
		$('.star2').attr('src','/skins/FixedSize/styleImages/backgrounds/fullStar.png');
		$('.star3').attr('src','/skins/FixedSize/styleImages/backgrounds/emptyStar.png');
		$('.star4').attr('src','/skins/FixedSize/styleImages/backgrounds/emptyStar.png');
		$('.star5').attr('src','/skins/FixedSize/styleImages/backgrounds/emptyStar.png');
	}
	function threeStarHover(){
		$('.star1').attr('src','/skins/FixedSize/styleImages/backgrounds/fullStar.png');
		$('.star2').attr('src','/skins/FixedSize/styleImages/backgrounds/fullStar.png');
		$('.star3').attr('src','/skins/FixedSize/styleImages/backgrounds/fullStar.png');
		$('.star4').attr('src','/skins/FixedSize/styleImages/backgrounds/emptyStar.png');
		$('.star5').attr('src','/skins/FixedSize/styleImages/backgrounds/emptyStar.png');
	}
	function fourStarHover(){
		$('.star1').attr('src','/skins/FixedSize/styleImages/backgrounds/fullStar.png');
		$('.star2').attr('src','/skins/FixedSize/styleImages/backgrounds/fullStar.png');
		$('.star3').attr('src','/skins/FixedSize/styleImages/backgrounds/fullStar.png');
		$('.star4').attr('src','/skins/FixedSize/styleImages/backgrounds/fullStar.png');
		$('.star5').attr('src','/skins/FixedSize/styleImages/backgrounds/emptyStar.png');
	}
	function fiveStarHover(){
		$('.star1').attr('src','/skins/FixedSize/styleImages/backgrounds/fullStar.png');
		$('.star2').attr('src','/skins/FixedSize/styleImages/backgrounds/fullStar.png');
		$('.star3').attr('src','/skins/FixedSize/styleImages/backgrounds/fullStar.png');
		$('.star4').attr('src','/skins/FixedSize/styleImages/backgrounds/fullStar.png');
		$('.star5').attr('src','/skins/FixedSize/styleImages/backgrounds/fullStar.png');
	}
	function clearStars(){
		if($('#rating').val()==0){
			$('.star1').attr('src','/skins/FixedSize/styleImages/backgrounds/emptyStar.png');
			$('.star2').attr('src','/skins/FixedSize/styleImages/backgrounds/emptyStar.png');
			$('.star3').attr('src','/skins/FixedSize/styleImages/backgrounds/emptyStar.png');
			$('.star4').attr('src','/skins/FixedSize/styleImages/backgrounds/emptyStar.png');
			$('.star5').attr('src','/skins/FixedSize/styleImages/backgrounds/emptyStar.png');
		}else if($('#rating').val()==1){
			$('.star1').attr('src','/skins/FixedSize/styleImages/backgrounds/fullStar.png');
			$('.star2').attr('src','/skins/FixedSize/styleImages/backgrounds/emptyStar.png');
			$('.star3').attr('src','/skins/FixedSize/styleImages/backgrounds/emptyStar.png');
			$('.star4').attr('src','/skins/FixedSize/styleImages/backgrounds/emptyStar.png');
			$('.star5').attr('src','/skins/FixedSize/styleImages/backgrounds/emptyStar.png');
		}else if($('#rating').val()==2){
			$('.star1').attr('src','/skins/FixedSize/styleImages/backgrounds/fullStar.png');
			$('.star2').attr('src','/skins/FixedSize/styleImages/backgrounds/fullStar.png');
			$('.star3').attr('src','/skins/FixedSize/styleImages/backgrounds/emptyStar.png');
			$('.star4').attr('src','/skins/FixedSize/styleImages/backgrounds/emptyStar.png');
			$('.star5').attr('src','/skins/FixedSize/styleImages/backgrounds/emptyStar.png');
		}else if($('#rating').val()==3){
			$('.star1').attr('src','/skins/FixedSize/styleImages/backgrounds/fullStar.png');
			$('.star2').attr('src','/skins/FixedSize/styleImages/backgrounds/fullStar.png');
			$('.star3').attr('src','/skins/FixedSize/styleImages/backgrounds/fullStar.png');
			$('.star4').attr('src','/skins/FixedSize/styleImages/backgrounds/emptyStar.png');
			$('.star5').attr('src','/skins/FixedSize/styleImages/backgrounds/emptyStar.png');
		}else if($('#rating').val()==4){
			$('.star1').attr('src','/skins/FixedSize/styleImages/backgrounds/fullStar.png');
			$('.star2').attr('src','/skins/FixedSize/styleImages/backgrounds/fullStar.png');
			$('.star3').attr('src','/skins/FixedSize/styleImages/backgrounds/fullStar.png');
			$('.star4').attr('src','/skins/FixedSize/styleImages/backgrounds/fullStar.png');
			$('.star5').attr('src','/skins/FixedSize/styleImages/backgrounds/emptyStar.png');
		}else if($('#rating').val()==5){
			$('.star1').attr('src','/skins/FixedSize/styleImages/backgrounds/fullStar.png');
			$('.star2').attr('src','/skins/FixedSize/styleImages/backgrounds/fullStar.png');
			$('.star3').attr('src','/skins/FixedSize/styleImages/backgrounds/fullStar.png');
			$('.star4').attr('src','/skins/FixedSize/styleImages/backgrounds/fullStar.png');
			$('.star5').attr('src','/skins/FixedSize/styleImages/backgrounds/fullStar.png');
		}

	}
	function setStars(stars){
		$('#rating').val(stars);
	}
	function submitReview(){
		var postName = $('#name').val();
		var heading = $('#heading').val();
		var comments = $('#comments').val();
		if(postName==''){
			$('#name').addClass('errorText');
		}else{
			$('#name').removeClass('errorText');
		}
		if(heading==''){
			$('#heading').addClass('errorText');
		}else{
			$('#heading').removeClass('errorText');
		}
		if(comments==''){
			$('#comments').addClass('errorText');
		}else{
			$('#comments').removeClass('errorText');
		}
		if(postName!='' && heading!='' && comments!=''){
			$.ajax({
			  type: "POST",
			  url: "/addReview.ajax.php",
			  data: { postName: postName, heading: heading, comments: comments, stars: $('#rating').val(), product: $('#product').val() }
			}).done(function(msg) {
				if(msg=='ok'){
					$('#form1').html('<p>Thank you for your comments</p>')
				}else{
					alert('Please complete all required fields');
				}
			});
		}
	}
	</script>
    <form id="form1" name="form1" method="post" action="">
      <table width="100%" border="0" cellspacing="0" cellpadding="5">
        <tr>
          <td>Rating </td>
        </tr>
        <tr>
          <td class="starBox">
          <img src="/skins/FixedSize/styleImages/backgrounds/emptyStar.png" width="12" height="12" alt="Star" class="star1" onmouseover="oneStarHover()" onmouseout="clearStars()" onclick="setStars(1)" style="cursor:pointer;" />
          <img src="/skins/FixedSize/styleImages/backgrounds/emptyStar.png" width="12" height="12" alt="Star" class="star2" onmouseover="twoStarHover()" onmouseout="clearStars()" onclick="setStars(2)" style="cursor:pointer;" />
          <img src="/skins/FixedSize/styleImages/backgrounds/emptyStar.png" width="12" height="12" alt="Star" class="star3" onmouseover="threeStarHover()" onmouseout="clearStars()" onclick="setStars(3)" style="cursor:pointer;" />
          <img src="/skins/FixedSize/styleImages/backgrounds/emptyStar.png" width="12" height="12" alt="Star" class="star4" onmouseover="fourStarHover()" onmouseout="clearStars()" onclick="setStars(4)" style="cursor:pointer;" />
          <img src="/skins/FixedSize/styleImages/backgrounds/emptyStar.png" width="12" height="12" alt="Star" class="star5" onmouseover="fiveStarHover()" onmouseout="clearStars()" onclick="setStars(5)" style="cursor:pointer;" />
          </td>
        </tr>
        <tr>
          <td>Posted By</td>
        </tr>
        <tr>
          <td><label for="name"></label>
            <input type="text" name="name" id="name" class="contactTextBox" /></td>
        </tr>
        <tr>
          <td>Heading </td>
        </tr>
        <tr>
          <td><label for="heading"></label>
            <input type="text" name="heading" id="heading" class="contactTextBox" /></td>
        </tr>
        <tr>
          <td>Comments </td>
        </tr>
        <tr>
          <td><label for="comments"></label>
            <textarea name="comments" id="comments" cols="45" rows="5" class="contactTextArea"></textarea></td>
        </tr>
        <tr>
          <td><img src="/skins/FixedSize/styleImages/backgrounds/addReview.png" style="cursor:pointer;" onclick="submitReview()" />
            <input name="rating" type="hidden" id="rating" value="0" />
            <input name="product" type="hidden" id="product" value="{PRODUCT_ID}" /></td>
        </tr>
      </table>
    </form>
    </div>
  </div>
</div>

<!-- BEGIN: related_prods_true -->
<br clear="all" />

    <div style="margin: 5px 0;">{TXT_RELATED_PRODUCTS}</div>

    <!-- BEGIN: repeat_related_prods -->
	<div class="homeProds">
			
				<table width="100%" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td class="homeProdsImageHeight" colspan="2"><a href="index.php?act=viewProd&amp;productId={REL_PRODUCT_ID}"><img src="{VALUE_RELATED_THUMB}" alt="{VALUE_RELATED_NAME}" border="0" title="{VALUE_RELATED_NAME}" /></a></td>
					</tr>
			    <tr>
						<td colspan="2">
                        <table width="100%" border="0" cellspacing="0" cellpadding="3" class="productBg">
			            <tr>
				            <td colspan="2" align="left"><a href="index.php?act=viewProd&amp;productId={REL_PRODUCT_ID}" class="txtDefault">{VALUE_RELATED_NAME}</a></td>
				            </tr>
					        <tr>
					          <td colspan="2" align="left">{REL_DESC}</td>
		                </tr>
					        <tr>
					          <td align="left" class="productPrice">{REL_PRICE} 
				              <span class="txtSale">{REL_SALE_PRICE}</span></td>
					          <td align="right">
                                <form action="{CURRENT_URL}" method="post" name="prod{REL_PRODUCT_ID}">
 
                                        <input type="hidden" name="add" value="{REL_PRODUCT_ID}" />
                                        <input type="hidden" name="quan" value="1" />
                                        <a href="javascript:submitDoc('prod{REL_PRODUCT_ID}');" target="_self"><img src="/skins/FixedSize/styleImages/backgrounds/buy.png" alt="Buy" width="55" height="18" border="0" /></a><br />

                                </form> 
                              </td>
				            </tr>
              </table></td>
					</tr>
				</table>
				
				
			</div>

    <!-- END: repeat_related_prods -->

<!-- END: related_prods_true -->
	
<!-- END: prod_true -->
<!-- BEGIN: prod_false -->
<p>{LANG_PRODUCT_EXPIRED}</p>
<!-- END: prod_false -->

</div>
<!-- END: view_prod -->