<?php
$otchet_date = (isset($_POST['otchet_date']))?$_POST['otchet_date']:'';
$cel_znach = (isset($_POST['cel_znach']))?$_POST['cel_znach']:'';
$dop_otkl_down = (isset($_POST['dop_otkl_down']))?$_POST['dop_otkl_down']:'';
$dop_otkl_up = (isset($_POST['dop_otkl_up']))?$_POST['dop_otkl_up']:'';
$fakt = (isset($_POST['fakt']))?$_POST['fakt']:'';
define('CURRENT_DATE', date('Y-m-d'));
?>
<form method="POST">
	<p>дата <input type="text" name="otchet_date" value="<?=$otchet_date?>"/></p>
	<p>целевое значение <input type="text" name="cel_znach" value="<?=$cel_znach?>"/></p>
	<p>отклонение вниз <input type="text" name="dop_otkl_down" value="<?=$dop_otkl_down?>"/></p>
	<p>отклонение вверх <input type="text" name="dop_otkl_up" value="<?=$dop_otkl_up?>"/></p>
	<p>факт <input type="text" name="fakt" value="<?=$fakt?>"/></p>
	<input type="submit"/>
</form>
<?php

if (empty($_POST)) {
	exit();
}

define('LOW_IS_BEST', -99);
define('HIGH_IS_BEST', -999);
//расчет контрольных значений диапазона
if ($cel_znach > 0 && $dop_otkl_down > 0 && $dop_otkl_up > 0) {
	$min = $cel_znach - $dop_otkl_down;
	$mid = $cel_znach;
	$max = $cel_znach + $dop_otkl_up;
	if ($otchet_date < CURRENT_DATE) {
		$kod_diapazon = '0111';
	} else {
		$kod_diapazon = '1111';
	}
} elseif ($cel_znach > 0 && $dop_otkl_down == LOW_IS_BEST && $dop_otkl_up > 0) {
	$min = $cel_znach;
	$mid = $cel_znach;
	$max = $cel_znach + $dop_otkl_up;
	if ($otchet_date < CURRENT_DATE) {
		$kod_diapazon = '0211';
	} else {
		$kod_diapazon = '1211';
	}
} elseif ($cel_znach > 0 && $dop_otkl_down > 0 && $dop_otkl_up == LOW_IS_BEST) {
	$min = $cel_znach - $dop_otkl_down;
	$mid = $cel_znach;
	$max = $cel_znach;
	if ($otchet_date < CURRENT_DATE) {
		$kod_diapazon = '0112';
	} else {
		$kod_diapazon = '1112';
	}
} elseif ($cel_znach > 0 && $dop_otkl_down == LOW_IS_BEST && $dop_otkl_up == LOW_IS_BEST) {
	$min = $cel_znach;
	$mid = $cel_znach;
	$max = $cel_znach;
	if ($otchet_date < CURRENT_DATE) {
		$kod_diapazon = '0212';
	} else {
		$kod_diapazon = '1212';
	}
} elseif ($cel_znach > 0 && $dop_otkl_down == HIGH_IS_BEST && $dop_otkl_up > 0) {
	$min = $cel_znach;
	$mid = $cel_znach;
	$max = $cel_znach + $dop_otkl_up;
	if ($otchet_date < CURRENT_DATE) {
		$kod_diapazon = '0311';
	} else {
		$kod_diapazon = '1311';
	}
} elseif ($cel_znach > 0 && $dop_otkl_down > 0 && $dop_otkl_up == HIGH_IS_BEST) {
	$min = $cel_znach - $dop_otkl_down;
	$mid = $cel_znach;
	$max = $cel_znach;
	if ($otchet_date < CURRENT_DATE) {
		$kod_diapazon = '0113';
	} else {
		$kod_diapazon = '1113';
	}
} elseif ($cel_znach > 0 && $dop_otkl_down == HIGH_IS_BEST && $dop_otkl_up == HIGH_IS_BEST) {
	$min = $cel_znach;
	$mid = $cel_znach;
	$max = $cel_znach;
	if ($otchet_date < CURRENT_DATE) {
		$kod_diapazon = '0313';
	} else {
		$kod_diapazon = '1313';
	}
}

//расчет позиции в диапазоне
if ($mid == $fakt) {
	$position = 3;
} elseif ($mid < $fakt) {
	if ($max < $fakt) {
		$position = 5;
	} else {
		$position = 4;
	}
} else {
	if ($min < $fakt) {
		$position = 2;
	} else {
		$position = 1;
	}
}

define('VERY_LOW',1);
define('LOW',2);
define('NORM',3);
define('HIGH',4);
define('VERY_HIGH',5);
$diapazons = [
	'0111' => ['1'=>VERY_LOW,'2'=>LOW,'3'=>NORM,'4'=>HIGH,'5'=>VERY_HIGH],
	'1111' => ['1'=>LOW,'2'=>LOW,'3'=>NORM,'4'=>HIGH,'5'=>HIGH],
	'0211' => ['1'=>VERY_LOW,'2'=>VERY_LOW,'3'=>NORM,'4'=>HIGH,'5'=>VERY_HIGH],
	'1211' => ['1'=>LOW,'2'=>LOW,'3'=>NORM,'4'=>HIGH,'5'=>VERY_HIGH],
	'0112' => ['1'=>VERY_LOW,'2'=>LOW,'3'=>NORM,'4'=>VERY_HIGH,'5'=>VERY_HIGH],
	'1112' => ['1'=>VERY_LOW,'2'=>LOW,'3'=>NORM,'4'=>HIGH,'5'=>HIGH],
	'0212' => ['1'=>VERY_LOW,'2'=>VERY_LOW,'3'=>NORM,'4'=>VERY_HIGH,'5'=>VERY_HIGH],
	'1212' => ['1'=>LOW,'2'=>LOW,'3'=>NORM,'4'=>HIGH,'5'=>HIGH],
	'0311' => ['1'=>NORM,'2'=>NORM,'3'=>NORM,'4'=>HIGH,'5'=>VERY_HIGH],
	'1311' => ['1'=>LOW,'2'=>LOW,'3'=>NORM,'4'=>HIGH,'5'=>VERY_HIGH],
	'0113' => ['1'=>VERY_LOW,'2'=>LOW,'3'=>NORM,'4'=>NORM,'5'=>NORM],
	'1113' => ['1'=>VERY_LOW,'2'=>LOW,'3'=>NORM,'4'=>HIGH,'5'=>HIGH],
	'0313' => ['1'=>NORM,'2'=>NORM,'3'=>NORM,'4'=>NORM,'5'=>NORM],
	'1313' => ['1'=>LOW,'2'=>LOW,'3'=>NORM,'4'=>HIGH,'5'=>HIGH]
];

echo "целевое зачение:$cel_znach<br/>";
echo "допустимое отклонение: $dop_otkl_down / $dop_otkl_up<br/>";
echo "фактическое зачение:$fakt<br/>";
//вывод оценки
echo 'оценка='.$diapazons[$kod_diapazon][$position];
?>
