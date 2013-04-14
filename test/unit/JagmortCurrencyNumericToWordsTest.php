<?php
/**
 * @see http://stackoverflow.com/questions/2532729/daylight-saving-time-and-timezone-best-practices
 * @see https://bugs.php.net/bug.php?id=51051
 */

// setup testing framework
$sf_root_dir = realpath(dirname(__FILE__)).'/../../../..';
require_once ($sf_root_dir.'/test/bootstrap/unit.php');

$plan = 167;
$t = new lime_test($plan);

$jagmort_tz_root = realpath(dirname(__FILE__).'/../..') === sfConfig::get('sf_root_dir') ? sfConfig::get('sf_root_dir') : sfConfig::get('sf_plugins_dir').'/JagmortTimezonePlugin';

$autoload = sfSimpleAutoload::getInstance(sfConfig::get('sf_cache_dir').'/project_autoload.cache');
$autoload->addDirectory(sfConfig::get('sf_symfony_lib_dir'));
$autoload->addDirectory($jagmort_tz_root.'/lib');

$CurrencyNumberInWord = JagmortCurrencyNumericToWords::createInstance();

$t->diag('->convert()');
/**
 * test
 */
$test = array(
	'0.01' => '00 рублей 1 копейка',
	'0.02' => '00 рублей 2 копейки',
	'0.05' => '00 рублей 5 копеек',
	'1' => 'один рубль 0 копеек',
	'1.00' => 'один рубль 0 копеек',
	'1.01' => 'один рубль 1 копейка',
	'1.02' => 'один рубль 2 копейки',
	'1.05' => 'один рубль 5 копеек',
	'2.00' => 'два рубля 0 копеек',
	'2.01' => 'два рубля 1 копейка',
	'2.02' => 'два рубля 2 копейки',
	'2.05' => 'два рубля 5 копеек',
	'5.00' => 'пять рублей 0 копеек',
	'5.01' => 'пять рублей 1 копейка',
	'5.02' => 'пять рублей 2 копейки',
	'5.05' => 'пять рублей 5 копеек');
foreach ($test as $k => $v) {
	$num_str = $k;
	$words = $v;
	$t->is($CurrencyNumberInWord->convert($num_str), $words, '->convert("' . $num_str . '") should convert numeric string to proper words "' . $words . '"');
}

/**
 * test
 */
$test = array_fill_keys(range(0, 99), 'ек');
foreach ($test as $k => &$v) {
	if ($k > 5 && $k < 21) {
		continue;
	}
	$mod = $k % 10;
	switch ($mod) {
		case 1: $v = 'йка'; break;
		case 2:
		case 3:
		case 4: $v = 'йки'; break;
	}
}

foreach ($test as $k => $v) {
	$num_str = '0.' . $k;
	$words = '00 рублей ' . $k . ' копе' . $v;
	$t->is($CurrencyNumberInWord->convert($num_str), $words, '->convert("' . $num_str . '") should convert numeric string to proper words "' . $words . '"');
}

/**
 * test
 */
$test = array(10 => 'десять', 11 => 'одиннадцать', 12 => 'двенадцать', 13 => 'тринадцать', 14 => 'четырнадцать', 15 => 'пятнадцать', 16 => 'шестнадцать', 17 => 'семнадцать', 18 => 'восемнадцать', 19 => 'девятнадцать');
foreach ($test as $k => $v) {
	$num_str = $k . '.00';
	$words = $v . ' рублей 0 копеек';
	$t->is($CurrencyNumberInWord->convert($num_str), $words, '->convert("' . $num_str . '") should convert numeric string to proper words "' . $words . '"');
}

/**
 * test
 */
$test = array(20 => 'двадцать', 30 => 'тридцать', 40 => 'сорок', 50 => 'пятьдесят', 60 => 'шестьдесят', 70 => 'семьдесят', 80 => 'восемьдесят', 90 => 'девяносто');
foreach ($test as $k => $v) {
	$num_str = $k . '.00';
	$words = $v . ' рублей 0 копеек';
	$t->is($CurrencyNumberInWord->convert($num_str), $words, '->convert("' . $num_str . '") should convert numeric string to proper words "' . $words . '"');
}

/**
 * test
 */
$test = array(100 => 'сто', 200 => 'двести', 300 => 'триста', 400 => 'четыреста', 500 => 'пятьсот', 600 => 'шестьсот', 700 => 'семьсот', 800 => 'восемьсот', 900 => 'девятьсот');
foreach ($test as $k => $v) {
	$num_str = $k . '.00';
	$words = $v . ' рублей 0 копеек';
	$t->is($CurrencyNumberInWord->convert($num_str), $words, '->convert("' . $num_str . '") should convert numeric string to proper words "' . $words . '"');
}

/**
 * test
 */
$test = array(21 => 'двадцать один', 31 => 'тридцать один', 41 => 'сорок один', 51 => 'пятьдесят один', 61 => 'шестьдесят один', 71 => 'семьдесят один', 81 => 'восемьдесят один', 91 => 'девяносто один');
foreach ($test as $k => $v) {
	$num_str = $k . '.00';
	$words = $v . ' рубль 0 копеек';
	$t->is($CurrencyNumberInWord->convert($num_str), $words, '->convert("' . $num_str . '") should convert numeric string to proper words "' . $words . '"');
}

/**
 * test
 */
$test = array('123.00' => 'сто двадцать три рубля 0 копеек', '709.00' => 'семьсот девять рублей 0 копеек');
foreach ($test as $k => $v) {
	$num_str = $k;
	$words = $v;
	$t->is($CurrencyNumberInWord->convert($num_str), $words, '->convert("' . $num_str . '") should convert numeric string to proper words "' . $words . '"');
}

/**
 * test
 */
$test = array(
	'1000.01' => 'одна тысяча рублей 1 копейка',
	'2000.02' => 'две тысячи рублей 2 копейки',
	'5000.05' => 'пять тысяч рублей 5 копеек',
	'10000' => 'десять тысяч рублей 0 копеек',
	'1300.00' => 'одна тысяча триста рублей 0 копеек',
	'2470.01' => 'две тысячи четыреста семьдесят рублей 1 копейка',
	'5892.02' => 'пять тысяч восемьсот девяносто два рубля 2 копейки');
foreach ($test as $k => $v) {
	$num_str = $k;
	$words = $v;
	$t->is($CurrencyNumberInWord->convert($num_str), $words, '->convert("' . $num_str . '") should convert numeric string to proper words "' . $words . '"');
}

/**
 * test
 */
$test = array(
		'95648125.3' => 'девяносто пять миллионов шестьсот сорок восемь тысяч сто двадцать пять рублей 3 копейки',
		'2000000.00' => 'два миллиона рублей 0 копеек',
		'1000000000.00' => 'один миллиард рублей 0 копеек');
foreach ($test as $k => $v) {
	$num_str = $k;
	$words = $v;
	$t->is($CurrencyNumberInWord->convert($num_str), $words, '->convert("' . $num_str . '") should convert numeric string to proper words "' . $words . '"');
}

$t->diag('->configure()->convert()');
/**
 * test
 */
$t->is($CurrencyNumberInWord->configure(array('mainNull' => ''))->convert('0.01'), '1 копейка', '->configure("0.01")->convert() should convert numeric string to proper words "1 копейка"');
$t->is($CurrencyNumberInWord->configure(array('mainNull' => '0'))->convert('0.02'), '0 рублей 2 копейки', '->configure("0.02")->convert() should convert numeric string to proper words "0 рублей 2 копейки"');
$t->is($CurrencyNumberInWord->configure(array('mainNull' => '0', 'withLeadingZero' => true))->convert('0.05'), '0 рублей 05 копеек', '->configure("0.05")->convert() should convert numeric string to proper words "0 рублей 05 копеек"');
$t->is($CurrencyNumberInWord->configure(array('withLeadingZero' => true))->convert('1'), 'один рубль 00 копеек', '->configure("1")->convert() should convert numeric string to proper words "один рубль 00 копеек"');
