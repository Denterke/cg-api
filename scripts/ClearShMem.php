<?php
	$var = [
		'run' => [1, false],
		'cur' => [2, 0],
		'cnt' => [3, 0],
		'pid' => [4, -1],
		'res' => [5, false]
	];
	$astarotMemId = shm_attach(ftok(__DIR__ . "/astarot.php", 'A'));
	foreach($var as $key => $val) {
		if (shm_has_var($astarotMemId, $val[0])) {
			echo "Variable $key = " . shm_get_var($astarotMemId, $val[0]) . "\n";
		} else {
			echo "Variable $key does not exist\n";
		}
		shm_put_var($astarotMemId, $val[0], $val[1]);
	}
?>