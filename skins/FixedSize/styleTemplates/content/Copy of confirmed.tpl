<!-- BEGIN: confirmation -->
<div class="boxContent">
	
	<h1>{LANG_CONFIRMATION_SCREEN}</h1>

    <!-- BEGIN: gat_header -->
    <script type="text/javascript">
    var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
    document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
    </script>
    
    <script type="text/javascript">
    try {
    var pageTracker = _gat._getTracker("{GAT_TRACKING_ID}");
    pageTracker._trackPageview();
    } catch(err) {} </script>
    <!-- END: gat_header -->

<!-- BEGIN: gat_body -->

        <script type="text/javascript">
		var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
		document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
		</script>

		<script type="text/javascript">
        
        try {
        
          var pageTracker = _gat._getTracker("{GAT_TRACKING_ID}");
          pageTracker._initData();
          pageTracker._trackPageview();
          
          {GAT_TRANS_CODE}
        
          {GAT_ITEMS_CODE}
        
          pageTracker._trackTrans();
        
        } catch(err) {}
        
        </script>

<!-- END: gat_body -->

	<!-- BEGIN: session_true -->
	<div>
		<div style="text-align: center; height: 25px;">
			<div class="cartProgress">
			{LANG_CART} &raquo; {LANG_ADDRESS} &raquo; {LANG_PAYMENT} &raquo; <span class='txtcartProgressCurrent'>{LANG_COMPLETE}</span>
			</div>
		</div>
		<!-- BEGIN: order_success -->
		<p>{LANG_ORDER_SUCCESSFUL}</p>
		<!-- BEGIN: aff_track -->
		{AFFILIATE_IMG_TRACK}
		<!-- END: aff_track -->
		<!-- END: order_success -->
		
		<!-- BEGIN: order_failed -->
		<p>{LANG_ORDER_FAILED}</p>
		<p>{LANG_ORDER_RETRY}</p>
		<div style="text-align: center; padding: 10px;"><a href="cart.php?act=step4"  class="txtCheckout">{LANG_RETRY_BUTTON}</a></div>
		<!-- END: order_failed -->
	</div>
	<!-- END: session_true -->
	
	<!-- BEGIN: session_false -->
	<p>{LANG_LOGIN_REQUIRED}</p>
	<!-- END: session_false -->
			
</div>
<!-- END: confirmation -->