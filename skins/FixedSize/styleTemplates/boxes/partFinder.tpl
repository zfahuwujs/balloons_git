<!-- BEGIN: partFinder -->
<script type="text/javascript">
	$(document).ready(function() {
		$('#make').change(function() {
		
			//Finish off and validate
			if($(this).val() == 0){
				$('#model').find('option')
						.remove()
						.end()
						.append('<option value="0">select</option>')
						.val('0');
				//$('#model').text('select');
			}else{
				$.ajax({
					type: 'get',
					url: '/models.ajax.php',
					data: 'make_id=' + $('#make').val(), 
					success: function(msg) {
					
						$('#model').html(msg);
					}
				});
			}
		});
	});
</script>

<div id = "partFinder" class = "boxContentLeft">
	<table cellspacing="1" cellpadding="2" border="0" width="100%">
        <form name="partFinder" action="index.php" method="get">
        
		<tr>
	        <td colspan="2"><h2>Part Finder</h2></td>
        </tr>
        <tr>
        	<td colspan="2"><p>Select your Vehicle: </p></td>
        </tr>
        <tr>
        	<td><label for="make">Make: </label></td>
            <td>
            	<select name = "make" id="make" class = "textbox">
                    <option value="0">select</option>
                    <!-- BEGIN: makes -->
                    	{MAKES}
                    <!-- END: makes -->
                </select>
                
                
            </td>
        </tr>
        <tr>
        	<td><label for="model">Model: </label></td>
            <td>
            	<select name = "model" id="model" class = "textbox">
                    <option value="0">select</option>
                    <!-- BEGIN: models -->
                    	{MODELS}
                    <!-- END: models -->
                </select>
                
            </td>
        </tr>
        <!--
        <tr>
        	<td><label for="year">Year: </label></td>
            <td>
                <select name = "year" id="year" class = "textbox">
                    <option>select</option>
                </select>
            </td>
        </tr>
        -->
		<tr>
        	<td><label for="category">Category: </label></td>
            <td>
            	<select name = "category" id="category" class = "textbox">
                    <option value="select">all</option>
                    <!-- BEGIN: categories -->
                    	{CATEGORIES}
                    <!-- END: categories -->
                </select>
			</td>
        </tr>
    
    <tr>
        	<td colspan="2"><p>or Search Parts: </p></td>
        </tr>
		
        <tr>
        	<td colspan="2"><input type="text" name="keywords" id="keywords" class = "textbox" value="keyword search.." onblur="if (this.value=='') this.value = this.defaultValue" onfocus="if (this.value==this.defaultValue) this.value = ''" /></td>
        </tr>
        <tr><input type="hidden" name="act" value="viewCat" />
        	<td colspan="2"><input type="submit" id="submit" class = "btnDefault" name="partSearch" value="Search" /></td>
        </tr>
        
        </form>
	</table>        
</div>




<!-- END: partFinder -->