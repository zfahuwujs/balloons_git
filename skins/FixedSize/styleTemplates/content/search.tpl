<!-- BEGIN: search -->

<div class="boxContent">
  <h1>Advanced Search</h1>
  <form action="index.php" method="get" name="sort" id="sort">
    <input type="hidden" name="act" value="search">
    <input type="hidden" name="catId" value="{LINK_CATID}">
    <table border="0" width="100%" cellspacing="5" cellpadding="0">
      <tr>
        <td width="100"><strong>Keyword: </strong></td>
        <td><input type="text" name="name" value="{NAME}" /></td>
      </tr>
      <tr>
        <td width="100"><strong>Category: </strong></td>
        <td><select name="cat_id">
            <option value="0">All Categories</option>
            <!-- BEGIN: cats -->
            <option value="{CAT_ID}">{CAT_NAME}</option>
            <!-- END: cats -->
          </select></td>
      </tr>
      <tr>
        <td width="100"><strong>Minimum Price: </strong></td>
        <td><input type="text" name="min_price" value="{MIN_PRICE}" /></td>
      </tr>
       <tr>
        <td width="100"><strong>Maximum Price: </strong></td>
        <td><input type="text" name="max_price" value="{MAX_PRICE}" /></td>
      </tr>
      <tr height="40">
        <td align="center"><input type="submit" class="submit" name="submit" value="Search" /></td>
      </tr>
    </table>
  </form>
  {SEARCH_QUERY}
  <br />
  <!-- BEGIN: productTable -->
  
  <table border="0" width="100%" cellspacing="0" cellpadding="3" class="tblList">
    <tr>
      <td class="tdListTitle"><strong>{LANG_IMAGE}</strong></td>
      <td class="tdListTitle"><strong>{LANG_DESC}</strong></td>
      <td class="tdListTitle"><strong>{LANG_PRICE}</strong></td>
      <td class="tdListTitle">&nbsp;</td>
    </tr>
    <!-- BEGIN: products -->
    <tr>
      <td align="center" class="{CLASS}"><a href="index.php?act=viewProd&amp;productId={PRODUCT_ID}" target="_self"><img src="{SRC_PROD_THUMB}" alt="{TXT_TITLE}" border="0" title="{TXT_TITLE}" /></a></td>
      <td valign="top" class="{CLASS}"><a href="index.php?act=viewProd&amp;productId={PRODUCT_ID}" target="_self" class="txtDefault"><strong>{TXT_TITLE}</strong></a><br />
        {TXT_DESC}
        <div class="txtOutOfStock">{TXT_OUTOFSTOCK}</div></td>
      <td align="center" class="{CLASS}">{TXT_PRICE}
        <div class="txtSale">{TXT_SALE_PRICE}</div></td>
      <td align="right" nowrap='nowrap' class="{CLASS}">
      
      <form action="{CURRENT_URL}" method="post" name="prod{PRODUCT_ID}">
      	<!-- BEGIN: buy_btn -->
          <input type="hidden" name="add" value="{PRODUCT_ID}" />
          <input type="hidden" name="quan" value="1" />
          <a href="javascript:submitDoc('prod{PRODUCT_ID}');" target="_self" class="txtButton">{BTN_BUY}</a>
          <!-- END: buy_btn --> 
          <a href="index.php?act=viewProd&amp;productId={PRODUCT_ID}" target="_self" class="txtButton">{BTN_MORE}</a>
        </form>
        </td>
    </tr>
    <!-- END: products -->
  </table>
  <!-- END: productTable -->
</div>
<!-- END: search -->
