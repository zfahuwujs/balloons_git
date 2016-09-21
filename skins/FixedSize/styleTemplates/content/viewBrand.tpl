<!-- BEGIN: view_brand -->

<h1>{TXT_CAT_TITLE}</h1>

<div class="boxContent">
  <!-- start mod - Category Descriptions -->
  
  <!-- BEGIN: cat_img -->
  <div style="float: left; margin-right: 5px; margin-bottom: 5px;"> <img src="{IMG_CURENT_CATEGORY}" alt="{TXT_CURENT_CATEGORY}" border="0" title="{TXT_CURENT_CATEGORY}" /> </div>
  <!-- END: cat_img -->
  
  <div>{TXT_CAT_TITLE}</div>
  <!-- end mod - Category Descriptions -->
  
  <br clear="all" />

  <!-- BEGIN: prod_sort -->
  <form action="index.php" method="get" name="sort" id="sort">
    <input type="hidden" name="act" value="viewBrand">
    <input type="hidden" name="brandId" value="{LINK_BRANDID}">
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td><strong>{LANG_CURRENT_DIR}</strong> {CURRENT_DIR}
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
  <table border="0" width="100%" cellspacing="0" cellpadding="3">
    <!-- BEGIN: products -->
    <tr>
      <td align="center"><a href="index.php?act=viewProd&amp;productId={PRODUCT_ID}" target="_self"><img src="{SRC_PROD_THUMB}" alt="{TXT_TITLE}" border="0" title="{TXT_TITLE}" /></a> </td>
      <td valign="top"><strong>{TXT_TITLE}</strong> - <a href="index.php?act=viewProd&amp;productId={PRODUCT_ID}" target="_self" class="txtDefault"><strong>{TXT_OUTOFSTOCK}</strong></a> <br />
        {TXT_DESC} <br />
        <form action="{CURRENT_URL}" method="post" name="prod{PRODUCT_ID}">
          <div class="txtSale">{TXT_SALE_PRICE}</div>
          <strong>{TXT_PRICE}</strong> <a href="index.php?act=viewProd&amp;productId={PRODUCT_ID}" target="_self"><img src="skins/FixedSize/styleImages/backgrounds/more.jpg" alt="more" align="absmiddle" /></a>
          <!-- BEGIN: buy_btn -->
          <input type="hidden" name="add" value="{PRODUCT_ID}" />
          <input type="hidden" name="quan" value="1" />
          <a href="javascript:submitDoc('prod{PRODUCT_ID}');" target="_self"><img src="skins/FixedSize/styleImages/backgrounds/buy.jpg" alt="buy" align="absmiddle" /></a>
          <!-- END: buy_btn -->
        </form></td>
    </tr>
    <!-- END: products -->
  </table>
  <!-- END: productTable -->
  
  <!-- BEGIN: noProducts -->
  <div>{TXT_NO_PRODUCTS}</div>
  <!-- END: noProducts -->
  
  <div class="pagination">{PAGINATION}</div>
  
</div>
<!-- END: view_brand -->
