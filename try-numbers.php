<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title>asfadfasdf</title>
	</head>
	<body>
	
	<?php
	//echo "asdfasdfasd<br/>";
	
	/*
	for ($i=11; $i<1000000; $i++){
		$a_2=floor($i/1000000);
		$a_1=floor($i/100000) - $a_2*10;
		$a0=floor($i/10000) - $a_2*100 - $a_1*10;
		$a1=floor($i/1000) - $a_2*1000 - $a_1*100 - $a0*10;
		$a2=floor($i/100) - $a_2*10000 - $a_1*1000 - $a0*100 - $a1*10;
		$a3=floor($i/10) - $a_2*100000 - $a_1*10000 - $a0*1000 - $a1*100 - $a2*10;
		$a4=$i - $a_2*1000000 - $a_1*100000 - $a0*10000 - $a1*1000 - $a2*100 - $a3*10;
		
		for ($p=1; $p<10; $p++){
			$result=pow($a_2,$p)+pow($a_1,$p)+pow($a0,$p)+pow($a1,$p)+pow($a2,$p)+pow($a3,$p)+pow($a4,$p);
			
			//echo "///".$result."--";
			if ($result==$i) {echo "found ".$i."powered to ".$p.".<br/>";}
			//echo "<br/>";
			
		}
	}*/
	//$result = pow(7,38)%187;
	//echo "result is ".$result;
	
	$x = new SplFileObject("example.txt","r");
	foreach ($x as $lineno => $val) {
    	if (!empty($val)) {print "$lineno:\t$val<br/>"; }
	}
	
	?>
	</body>
</html>
