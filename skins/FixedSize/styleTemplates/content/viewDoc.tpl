<!-- BEGIN: view_doc -->
<div class="boxContent">
<p class="directions"><a href="/">Home</a> &raquo;&nbsp;<span style="color:#000;">{DOC_NAME}</span></p>
<h1 class="moduleTitle">{DOC_NAME}</h1>
<!-- BEGIN: content -->
{DOC_CONTENT}
<!-- END: content -->
{404}

<!-- BEGIN: the_form -->
<div style="float:left;width:385px;">
<h3 style="color:#0E99DA;font-size:18px">Connect with us</h3>
{DOC_CONTENT}
	<table cellpadding="0" cellspacing="0" border="0">
  	<tr>
    	<td><a target="_blank" href="{FB_LINK}"><img src="skins/FixedSize/styleImages/backgrounds/fb.png" alt="Facebook"/></a></td>
      <td><a target="_blank" href="{FB_LINK}" style="color:#0E99DA;text-decoration:underline;margin-left:10px;line-height:32px;">Connect with us on Facebook</a></td>
    </tr>
    <tr>
    	<td colspan="2" height="5">&nbsp;</td>
    </tr>
    <tr>
    	<td><a target="_blank" href="{TWITER_LINK}"><img src="skins/FixedSize/styleImages/backgrounds/twitter.png" alt="Twitter"/></a></td>
      <td><a target="_blank" href="{TWITER_LINK}" style="color:#0E99DA;text-decoration:underline;margin-left:10px;line-height:32px;">Follow us on Twitter</a></td>
    </tr>
  </table>
		
  	
  </div>
</div>
<div style="float:left;width:385px;margin-left:10px;">
<h3 style="color:#0E99DA;font-size:18px">Get in touch</h3>
{ERROR}

<script type="text/JavaScript">
<!--
function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_validateForm() { //v4.0
  var i,p,q,nm,test,num,min,max,errors='',args=MM_validateForm.arguments;
  for (i=0; i<(args.length-2); i+=3) { test=args[i+2]; val=MM_findObj(args[i]);
    if (val) { nm=val.name; if ((val=val.value)!="") {
      if (test.indexOf('isEmail')!=-1) { p=val.indexOf('@');
        if (p<1 || p==(val.length-1)) errors+='- '+nm+' must contain an e-mail address.\n';
      } else if (test!='R') { num = parseFloat(val);
        if (isNaN(val)) errors+='- '+nm+' must contain a number.\n';
        if (test.indexOf('inRange') != -1) { p=test.indexOf(':');
          min=test.substring(8,p); max=test.substring(p+1);
          if (num<min || max<num) errors+='- '+nm+' must contain a number between '+min+' and '+max+'.\n';
    } } } else if (test.charAt(0) == 'R') errors += '- '+nm+' is required.\n'; }
  } if (errors) alert('The following error(s) occurred:\n'+errors);
  document.MM_returnValue = (errors == '');
}
//-->
</script>

<form action="#" method="post" name="form1" id="form1" class="contactForm" onsubmit="MM_validateForm('from','','RisEmail','subject','','R','verif_box','','R','message','','R');return document.MM_returnValue">

<!-- BEGIN: departmentOFFFFFFFFFFFFFFF -->
Choose a Department: <br />
<select name="department" class="contactSelectbox textbox">
	<option value="">Please Choose One</option>
    <!-- BEGIN: departments -->
    	<option value="{DEP_EMAIL}" {DEP_SELECTED}}>{DEP_NAME}</option>
    <!-- END: departments -->
</select>
<br />
<br />
<!-- END: department -->

Your Name*<br />
<input name="name" type="text" id="from" class="textboxContactUs" value="{VAL_NAME}"/>
<br />
<br />

Your Email Address*<br />
<input name="from" type="text" id="from" class="textboxContactUs" value="{VAL_EMAIL}"/>
<br />
<br />

Subject<br />
<input name="subject" type="text" id="subject" class="textboxContactUs" value="{VAL_SUBJECT}"/>
<br />
<br />

Message*<br />
<textarea name="message" cols="6" rows="5" id="message" class="contactTextArea textboxContactUs">{VAL_MESSAGE}</textarea>
<br />
<br />

Type verification image*<br />
<input name="verif_box" type="text" id="verif_box" class="textboxContactUs" style="width:50px !important;"/>
<img src="verificationimage.php?{RAND_NUM}" alt="verification image, type it in the box" width="50" height="24" class="verifImage" />
<input name="Submit" type="submit" class="sendMsgBtn" value="Send Message"  />
<br />
* required fields
<!-- BEGIN: wrong_code -->
<div style="border:1px solid #990000; background-color:#D70000; color:#FFFFFF; padding:4px; padding-left:6px;width:295px;">Wrong verification code</div><br /> 
<!-- END: wrong_code -->



</form>
<div style="clear:both;height:30px;"></div>
</div>
<!-- END: the_form -->

</div>
<!-- END: view_doc -->