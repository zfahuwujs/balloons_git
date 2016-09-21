<!-- BEGIN: contact_form -->
<div class="boxContent">
<p class="directions"><a href="/">Home</a> &raquo;&nbsp;{DOC_NAME}</p>
<h1>{DOC_NAME}</h1>
<br />
{DOC_CONTENT}

<!-- BEGIN: the_form -->
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

Your e-mail:<br />
<input name="from" type="text" id="from" class="contactTextBox" value="{VAL_EMAIL}"/>
<br />
<br />

Subject:<br />
<input name="subject" type="text" id="subject" class="contactTextBox" value="{VAL_SUBJECT}"/>
<br />
<br />

Type verification image:<br />
<input name="verif_box" type="text" id="verif_box" class="contactTextBox"/>
<img src="verificationimage.php?{RAND_NUM}" alt="verification image, type it in the box" width="50" height="24" align="absbottom" /><br />
<br />

<!-- BEGIN: wrong_code -->
<div style="border:1px solid #990000; background-color:#D70000; color:#FFFFFF; padding:4px; padding-left:6px;width:295px;">Wrong verification code</div><br /> 
<!-- END: wrong_code -->

Message:<br />
<textarea name="message" cols="6" rows="5" id="message" class="contactTextArea">{VAL_MESSAGE}</textarea>

<input name="Submit" type="submit" style="margin-top:10px; display:block; border:1px solid #000000; width:100px; height:20px;font-family:Verdana, Arial, Helvetica, sans-serif; font-size:11px; padding-left:2px; padding-right:2px; padding-top:0px; padding-bottom:2px; line-height:14px; background-color:#EFEFEF;" value="Send Message"/>
</form>

<!-- END: the_form -->

</div>
<!-- END: contact_form -->