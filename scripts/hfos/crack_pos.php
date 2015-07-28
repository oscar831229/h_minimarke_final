<?php

for($i=0;$i<=9999;$i++){
	if(sha1(sprintf("%04s", $i))==$_SERVER['argv'][1]){
		echo sprintf("%04s", $i).PHP_EOL;
	}
}
