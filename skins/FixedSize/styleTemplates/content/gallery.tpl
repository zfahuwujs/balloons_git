<!-- BEGIN: view_gallery -->

<div class="boxContent">

  <h1>Gallery</h1>

	<!-- BEGIN: lightbox -->
	<div class="lightbox">
  	<!-- BEGIN: images -->
  		<div class="lightboxImg"><a href="{IMG_SRC}" class="galleryImg"><img src="{IMG_SRC_THUMB}" border="0" /></a></div>
  	<!-- END: images -->
    <br clear="all" />
  	</div>
    <!-- END: lightbox -->

 	<!-- BEGIN: galleria -->
	<div id="galleria">
  	<!-- BEGIN: images -->
  		<img src="{IMG_SRC}" />
  	<!-- END: images -->
  	</div>
	<script>
        Galleria.loadTheme('/js/themes/classic/galleria.classic.js');
        $('#galleria').galleria();
    </script>
    <!-- END: galleria -->

</div>
<!-- END: view_gallery -->
