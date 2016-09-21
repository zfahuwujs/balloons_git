<!-- BEGIN: view_cat -->

<div class="boxContent">
	<p class="directions"><a href="/">Home</a> {CURRENT_DIR}</p>
  <h1 class="moduleTitle">{TXT_CAT_TITLE}</h1>
 

  <div class = "clearing"></div>
  
  <div id="categoriesWrapper">
  {SUBCAT_WITH_IMAGES}
  </div>
  
  <div class = "clearing"></div>
  <!-- BEGIN: prod_sort -->
  <form action="index.php" method="get" name="sort" id="sort">
    <input type="hidden" name="act" value="viewCat">
    <input type="hidden" name="catId" value="{LINK_CATID}">
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td align="right"><strong>{SORT_BY_TEXT}</strong>
          <select name="sortOrder" onchange="submitDoc('sort');">
            {SORT_BY_OPTIONS}
          </select></td>
      </tr>
    </table>
  </form>
  <!-- END: prod_sort -->
  <div class="pagination">{PAGINATION}</div>
  <!-- BEGIN: productTable -->

    <!-- BEGIN: products -->
    
    <div class="homeProds">
      	<table width="320" cellpadding="0" cellspacing="0" border="0">
        	<tr>
          	<td colspan="2" align="center" style="height:320px;">
            	<a href="index.php?act=viewProd&amp;productId={PRODUCT_ID}">
              	<img src="{SRC_PROD_THUMB}" alt="{TXT_TITLE}" border="0" height="280" width="280" title="{TXT_TITLE}" />
            	</a>
            </td>
          </tr>
          <tr>
          	<td colspan="2" height="35">
            	<div style="width:100%;height:35px;overflow:hidden;"><h4><a href="index.php?act=viewProd&amp;productId={PRODUCT_ID}">{TXT_TITLE}</a></h4></div>
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
    
    <!-- END: products -->



    <div class = "clearing"></div>
    
  <!-- END: productTable -->
  
  
  
  <!-- BEGIN: noProducts -->
  <div>{TXT_NO_PRODUCTS}</div>
  <!-- END: noProducts -->
  <div class="pagination">{PAGINATION}</div>
  <div style="clear:both;height:0;"></div>
   <!-- start mod - Category Descriptions -->
  <div style="border-top:1px solid #d6d6d6">{TXT_CAT_DESC}</div>
  <!-- end mod - Category Descriptions -->
</div>
<!-- END: view_cat -->
