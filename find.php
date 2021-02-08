<?php

require_once('config.php');

function returnResponse($data, $responce_code) {
	http_response_code($responce_code);
	header('Content-Type: application/json');

	exit(json_encode($data));
}

//Find fibonacci n-1, n, n+1
function fibonacci($n) {
	//Matrix A
	$a = 1;
	$b = 1;
	$c = 1;
	$d = 0;
	//Vector R
	$rc = 0;
	$rd = 1;
	while ($n > 0) {
		if ($n % 2 != 0) {
			//R * A
			$tc = $rc;
			$rc = $rc * $a + $rd * $c;
			$rd = $tc * $b + $rd * $d;
		}
		//A * A
		$ta = $a;
		$tb = $b;
		$tc = $c;
		$a = $a * $a + $b * $c;
		$b = $ta * $b + $b * $d;
		$c = $ta * $c + $c * $d;
		$d = $tc * $tb + $d * $d;
		$n = intdiv($n, 2);
	}
	//Fn-1, Fn, Fn+1
	$res = [$rd, $rc, $rd + $rc];
	return $res;
}

//Get number from form
if ((!isset($_POST['number'])) or (!is_numeric($_POST['number']))) {
	returnResponse([
		'message' => 'Number is empty or entered data is not number!'
	], 422);
}

//DB Connect
$mysqli = new mysqli($host, $user, $pas, $table);
if (!$mysqli) {
    returnResponse([
		'message' => 'Do not connect to DB'
	], 422);
}	

$number = $_POST['number'];
if ($number <= 0) {
	$fibonacci_number = '0';

	//Add number to table
	if (!$mysqli->query("INSERT INTO fibonacci_number (number) VALUES (".$fibonacci_number.")")) {
		returnResponse([
			'message' => 'Do not INSERT fibonacci number to DB'
		], 422);
	}

	returnResponse([
		'message' => $fibonacci_number
	], 200);
}

//Get n from Binne
$n = round(log($number * sqrt(5) + 1/2) / log((1 + sqrt(5)) / 2));
$res = fibonacci($n);

//Find nearly fibonacci
if ($number > $res[1]) {
	if (($res[2] - $number) < ($number - $res[1])) {
		$fibonacci_number = $res[2];
	} else {
		$fibonacci_number = $res[1];
	}
} else {
	if (($res[1] - $number) < ($number - $res[0])) {
		$fibonacci_number = $res[1];
	} else {
		$fibonacci_number = $res[0];
	}
}

//Add number to table
if (!$mysqli->query("INSERT INTO fibonacci_number (number) VALUES (".$fibonacci_number.")")) {
	returnResponse([
		'message' => 'Do not INSERT fibonacci number to DB'
	], 422);
}

returnResponse([
	'message' => $fibonacci_number
], 200);