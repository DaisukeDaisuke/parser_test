<?php
//php test in copilot
$test = [11,2,3,4,5,6,100];
$test2 = [11,2,3,4,5,6,100];

for($i = 0; $i < count($test); $i++){
	if($test[$i] == $test2[$i]){
		echo "true";
	}
	else{
		echo "false";
	}
}
