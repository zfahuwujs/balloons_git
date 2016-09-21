<?php
$file = "test.txt";
$gzfile = "zip/test.gz";
$fp = gzopen ($gzfile, 'wb'); // w9 == highest compression
if(gzwrite($fp, file_get_contents($file))){
	echo 'Ok';
}else{
	echo 'Error';
}
gzclose($fp);

?>