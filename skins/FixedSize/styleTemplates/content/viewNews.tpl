<!-- BEGIN: view_news -->
    <!-- BEGIN: one -->
        <h1>{DOC_NAME}</h1>
        <div class="boxContent">
      		<p class="added">Added: {ADDED}</p>
            <div class="newsArcItemL">
	      		{DOC_CONTENT}
            </div>
            <div class="newsArcItemR">
            	<!-- BEGIN: image -->
            	<img src="images/uploads/{IMAGE}" alt="{DOC_NAME}" />
                <!-- END: image -->
            </div>
            <br clear="all" />
        </div>
    <!-- END: one -->
    <!-- BEGIN: all -->
    <div class="newsAllL">
    	<h1>Latest News</h1>
        <!-- BEGIN: all_one -->
    	<h3 class="newsTitle"><a href="/index.php?act=viewNews&amp;docId={NEWS_ID}">{DOC_NAME}</a></h3>
        <div class="boxContent">
      		<p class="added">Added: {ADDED}</p>
            <div class="newsArcItemL">
	      		{DOC_CONTENT}
            </div>
            <div class="newsArcItemR">
            	<!-- BEGIN: image -->
            	<img src="images/uploads/{IMAGE}" alt="{DOC_NAME}" width="100" />
                <!-- END: image -->
            </div>
            <br clear="all" />
        </div>
        <!-- END: all_one -->
    </div>
    <div class="newsAllR">
    	<h1>News Archive</h1>
        <div class="boxContent">
        	<div class="pagination">{PAGINATION}</div>
            <!-- BEGIN: item -->
            	<div class="newsArcItem">
           			<h3 class="newsTitle"><a href="/index.php?act=viewNews&amp;docId={NEWS_ID}">{DOC_NAME}</a></h3>
                	<p class="added">Added: {ADDED}</p>
                </div>
            <!-- END: item -->
            <div class="pagination">{PAGINATION}</div>
        </div>
    </div>
    <br clear="all" />
    <!-- END: all -->
<!-- END: view_news -->