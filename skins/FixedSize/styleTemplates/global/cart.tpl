<!-- BEGIN: body -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset={VAL_ISO}" />
<title>{META_TITLE}</title>
<meta name="description" content="{META_DESC}" />
<meta name="keywords" content="{META_KEYWORDS}" />
<link rel="shortcut icon" href="images/favicon.ico" />
<link href="skins/{VAL_SKIN}/styleSheets/layout.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="js/jslibrary.js" type="text/javascript"></script>
<script language="javascript" src="js/css_browser_selector.js" type="text/javascript"></script>
<script language="javascript" src="js/jquery-1.7.2.js" type="text/javascript"></script>
<script type="text/javascript" src="js/infinitecarousel/jquery.infinitecarousel2.js"></script>
<script language="javascript" src="js/jquery.lightbox.min.js" type="text/javascript"></script>
<link href="skins/{VAL_SKIN}/styleSheets/jquery.lightbox.css" rel="stylesheet" type="text/css" />

<!--!!!!!DELETE ALL JAVASCRIPT COMPONENTS NOT BEING USED!!!!!-->

<!--jQuery UI Main Components-->
<script type="text/javascript" src="js/ui/jquery.ui.core.js"></script>
<script type="text/javascript" src="js/ui/jquery.ui.widget.js"></script>
<script type="text/javascript" src="js/ui/jquery.ui.position.js"></script>

<!--Datepicker - Requires: Core
<script type="text/javascript" src="js/ui/jquery.ui.datepicker.js"></script>
<link href="js/ui/themes/base/jquery.ui.datepicker.css" rel="stylesheet" type="text/css" />-->

<script src="/SpryAssets/SpryAccordion.js" type="text/javascript"></script>
<script type="text/javascript" src="SpryAssets/SpryURLUtils.js"></script>
<link href="/SpryAssets/SpryAccordion.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
var params = Spry.Utils.getLocationParamsAsObject();
</script>

<!--Accordion - Requires: Core, Widget-->
<script type="text/javascript" src="js/ui/jquery.ui.accordion.js"></script>
<link href="js/ui/themes/base/jquery.ui.accordion.css" rel="stylesheet" type="text/css" />

<!--Tabs - Requires: Core, Widget
<script type="text/javascript" src="js/ui/jquery.ui.tabs.js"></script>
<link href="js/ui/themes/base/jquery.ui.tabs.css" rel="stylesheet" type="text/css" />-->

<!--Auto Complete - Requires: Core, Widget, Position-->
<script type="text/javascript" src="js/ui/jquery.ui.autocomplete.js"></script>
<link href="js/ui/themes/ui-lightness/jquery.ui.autocomplete.css" rel="stylesheet" type="text/css" />
<link href="js/ui/themes/ui-lightness/jquery.ui.theme.css" rel="stylesheet" type="text/css" />

<!--Select Menu - Requires: Core, Widget, Position
<script type="text/javascript" src="js/ui/jquery.ui.selectmenu.js"></script>
<link href="js/ui/themes/base/jquery.ui.selectmenu.css" rel="stylesheet" type="text/css" />
-->

<!--Galleria - Requires: Nothing
<script src="js/galleria.js"></script>-->

<!--Cloud Zoom - Requires: Nothing
<script type="text/javascript" src="js/cloudzoom/cloud-zoom.1.0.2.js"></script>
<link href="js/cloudzoom/cloud-zoom.css" rel="stylesheet" type="text/css" />-->

<!--Coda Slider - Requires: Nothing
<script type="text/javascript" src="js/codaslider/jquery.easing.1.3.js"></script>
<script type="text/javascript" src="js/codaslider/jquery.coda-slider-2.0.js"></script>
<link href="js/codaslider/coda-slider-2.0.css" rel="stylesheet" type="text/css" />-->

<!--Innerfade - Requires: Nothing
<script type="text/javascript" src="js/innerfade/jquery.innerfade.js"></script>-->

<!--jCarousel - Requires: Nothing
<script type="text/javascript" src="js/jcarousel/jquery.jcarousel.js"></script>
<link href="js/jcarousel/tango/skin.css" rel="stylesheet" type="text/css" />-->

<!--Superfish - Requires: Nothing-->
<script type="text/javascript" src="js/superfish/superfish.js"></script>
<script type="text/javascript" src="js/superfish/hoverIntent.js"></script>
<link href="js/superfish/superfish.css" rel="stylesheet" type="text/css" />

<!-- BEGIN: font_resize_js -->
<script type="text/javascript" src="js/fontResize.js"></script>
<!-- END: font_resize_js -->

<script type="text/javascript">
$(document).ready(function() {         

	$('a.galleryImg').lightBox();
	
	$('#carousel').infiniteCarousel({
		displayThumbnails: false,
		showControls: false
	});
	
	$(function() {
		$( "#accordion" ).accordion({
			collapsible: true , 
			autoHeight: false, 
			active: false,
		});
	});	
	

	$('ul.sf-menu').superfish({
		dropShadows:false,
		delay: 800
	});
	
});
</script>

<!-- BEGIN: google_analytics -->
<script type="text/javascript">
var _gaq = _gaq || [];
_gaq.push(['_setAccount', '{GOOGLE_ANALYTICS_UID}']);
_gaq.push(['_trackPageview']);
_gaq.push(['_trackPageLoadTime']);
<!-- BEGIN: gat_body -->
_gaq.push(['_addTrans', {GAT_TRANS_CODE} ]); 
// add item might be called for every item in the shopping cart
// where your ecommerce engine loops through each item in the cart and
// prints out _addItem for each
<!-- BEGIN: gat_body_item_repeat -->
_gaq.push(['_addItem', {GAT_ITEMS_CODE} ]);
<!-- END: gat_body_item_repeat -->
_gaq.push(['_trackTrans']); //submits transaction to the Analytics servers
<!-- END: gat_body -->
(function() {
	var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();
</script>
<!-- END: google_analytics -->
</head>
<body>
<!-- BEGIN: disclaimer -->
<div class="disclaimer">
  <p>This website is best viewed in Internet Explorer 8 or above.  You are currently using an old version of Internet Explorer.  Please click on this <a href="http://www.microsoft.com/windows/internet-explorer/worldwide-sites.aspx" title="Internet Explorer 8">link</a> to update your browser.</p>
</div>
<!-- END: disclaimer -->


<div id="wrapper">

    <div style="width:960px;margin:0 auto;height:30px;">
    	<table width="960" cellpadding="0" cellspacing="0" border="0">
      <tr>
      	<td width="130"><span style="color:#097cb3;">Display All Prices:</span></td>
        <td width="150">
        	<form action="switch.php" method="get" name="changeTax" id="changeTax">
            <select name="tax" id="tax">
              <option value="1" {VAT_SEL_NO}>Without VAT/TAX</option>
              <option value="2" {VAT_SEL_YES}>Including VAT/TAX</option>
            </select>
            <input type="hidden" name="r" value="{CUR_URL}" />
          </form>
          <script>
          $(document).ready(function(e) {
            $('#tax').change(function(e) {
              $('#changeTax').submit();
            });
          });
          </script>
        </td>
        <td width="90"><a href="/contact-us/info_2.html">Contact Us</a></td>
        <td>|</td>
       <!-- <td width="25"><img src="/skins/FixedSize/styleImages/backgrounds/liveChat.png" /></td>
        <td width="70">Live Chat</td>
        <td>|</td>-->
        <td>Tel: <strong>{PHONE}</strong></td>
        <td><div style="float:right">{SESSION}</div></td>
      </tr>
      </table>
    </div>
    <div id="topHeader">
    	
        <div class="blankheader">
            <a href="/">
                <img src="blankheader.gif" width="342" height="110" border="0" />
            </a>
        </div>
        <div class="headerContent"> 
            {SHOPPING_CART}
        </div>
    </div>
    
    <div class = "hasShadow">
    	<div id = "topNavCont">
	        {TOP_NAV}
          <div style="clear:both;height:0;"></div>
          <div style="float:left;margin-left:50px;margin-top:15px;">
          	<span style="text-transform:uppercase;color:#334d5c;font-size:14px;">FREE DELIVERY ON UK ORDERS OVER &pound;100</span>
            <span style="margin-left:120px;color:#53AFE0;text-decoration:underline;font-weight:bold;">Advanced Search</span>
          </div>
          <div>
          	{SEARCH_FORM}
          </div>
            
            <div class = "clearing"></div>
        </div>
        
        <div id="subSurround">
        
            <div class="colMid">
                {PAGE_CONTENT}
            </div>
            
            <div class="colRight"> 
                {PRODUCTS_MODULE_SIDE}
                <!-- BEGIN: recent_prods -->
                {RECENT_PRODS}
                <!-- END: recent_prods -->
            </div>
            <div class = "clearing"></div>
        
        </div><!-- end subsurround -->


    
        <div class="footer">
        	<div style="width:960px;margin-left:30px;margin-top:30px;">
          	{MAIL_LIST}
            <div style="margin-left:20px;float:left;">
              <table cellpadding="0" cellspacing="0" border="0">
              	<tr>
                	<td><img src="skins/FixedSize/styleImages/backgrounds/secureLocker.png" alt="Payment Secure" height="31" width="28" /></td>
                	<td style="padding-left:10px"><strong style="font-size:13px;">Secure Payment</strong></td>
                  <td style="padding-left:10px;"><img src="skins/FixedSize/styleImages/backgrounds/cards.png" alt="Payment Methods" style="width: 255px;"/></td>
              	</tr>
              </table>
            </div>
          </div>
          <div style="clear:both;height:0;"></div>
          <div style="margin-left:30px;margin-top:20px;">
          	{FOOTER}
            <div style="float:left;">
            	<span class="footerCatTxt">Connect</span><br />
            	{FACEBOOK} {TWITTER}
            </div>
          </div>
          <div style="clear:both;height:0px;"></div>
          <div id = "evo">
          	&copy; {COMPANY_NAME} - 
           </div>
        	<div class = "clearing"></div>
          </div><!-- footer -->       
	</div><!-- end of shadow -->
</div><!-- end wrapper -->
    
    
<!--

-->


<!-- BEGIN: tabs -->
<script type="text/javascript">
<!--var TabbedPanels1 = new Spry.Widget.TabbedPanels("TabbedPanels1");-->
var Accordion1 = new Spry.Widget.Accordion("Accordion1", { defaultPanel: params.panel ? params.panel: 0 });
</script>
<!-- END: tabs -->




</body>
</html>
<!-- END: body -->