<!-- BEGIN: language -->
<div class="languageSurround">

	<!-- BEGIN: cubie -->
	<select name="lang" class="dropDown" onchange="jumpMenu('parent',this,0)">
		<!-- BEGIN: option -->
		<option value="switch.php?r={VAL_CURRENT_PAGE}&amp;lang={LANG_VAL}" {LANG_SELECTED} onmouseover="javascript:getImage('language/{LANG_VAL}/flag.gif');">{LANG_NAME}</option> 
		<!-- END: option -->
	</select>
	<img src="language/{ICON_FLAG}" alt="" width="21" height="14" id="img" title="" />
	<!-- END: cubie -->
    
    
    <!-- BEGIN: google -->
    <div class="googleTranslateContainer">
    	<div id="google_translate_element"></div><script>
			function googleTranslateElementInit() {
			  new google.translate.TranslateElement({
				pageLanguage: 'en'
			  }, 'google_translate_element');
			}
			</script>
			<script src="http://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
    </div>
    <!-- END: google -->	
</div>
<!-- END: language -->	