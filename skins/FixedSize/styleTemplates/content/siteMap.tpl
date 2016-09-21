<!-- BEGIN: view_map -->
<div class="boxContent">
<h1>Site Map</h1>
<p />
<table>
<tr><th style="text-align: left;">Categories</th></tr>
<tr><td>
<!-- BEGIN: cat_list -->
{CAT_LIST}
<!-- END: cat_list -->
</td></tr>
<tr><th style="text-align: left;">Site Documents</th></tr>
<tr><td>
<ul class="sitemap">
<!-- BEGIN: doc_list -->
<li class="sitemap"><a href="index.php?act=viewDoc&amp;docId={DATA.doc_id}" class="txtDefault">{DATA.doc_name}</a></li>
<!-- END: doc_list -->
</ul>
</td></tr>
</table>
</div>
<!-- END: view_map -->