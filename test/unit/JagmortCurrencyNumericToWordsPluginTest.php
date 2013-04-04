<?php
/**
 * @see http://stackoverflow.com/questions/2532729/daylight-saving-time-and-timezone-best-practices
 * @see https://bugs.php.net/bug.php?id=51051
 */

// setup testing framework
$sf_root_dir = realpath(dirname(__FILE__)).'/../../../..';
require_once ($sf_root_dir.'/test/bootstrap/unit.php');

$plan = 59;
$t = new lime_test($plan);

$jagmort_tz_root = realpath(dirname(__FILE__).'/../..') === sfConfig::get('sf_root_dir') ? sfConfig::get('sf_root_dir') : sfConfig::get('sf_plugins_dir').'/JagmortTimezonePlugin';

$autoload = sfSimpleAutoload::getInstance(sfConfig::get('sf_cache_dir').'/project_autoload.cache');
$autoload->addDirectory(sfConfig::get('sf_symfony_lib_dir'));
$autoload->addDirectory($jagmort_tz_root.'/lib');

$CurrencyNumberInWord = JagmortCurrencyNumericToWords::createInstance();

/**
 * test
 */
$t->diag('->convert()');
$t->is($CurrencyNumberInWord->convert('0.01'), '00 рублей 1 копейка', '->convert() should convert numeric string to proper words');
$t->is($CurrencyNumberInWord->convert('0.02'), '00 рублей 2 копейки', '->convert() should convert numeric string to proper words');
$t->is($CurrencyNumberInWord->convert('0.05'), '00 рублей 5 копеек', '->convert() should convert numeric string to proper words');
$t->is($CurrencyNumberInWord->convert('1'), 'один рубль 0 копеек', '->convert() should convert numeric string to proper words');
$t->is($CurrencyNumberInWord->convert('1.00'), 'один рубль 0 копеек', '->convert() should convert numeric string to proper words');
$t->is($CurrencyNumberInWord->convert('1.01'), 'один рубль 1 копейка', '->convert() should convert numeric string to proper words');
$t->is($CurrencyNumberInWord->convert('1.02'), 'один рубль 2 копейки', '->convert() should convert numeric string to proper words');
$t->is($CurrencyNumberInWord->convert('1.05'), 'один рубль 5 копеек', '->convert() should convert numeric string to proper words');
$t->is($CurrencyNumberInWord->convert('2.00'), 'два рубля 0 копеек', '->convert() should convert numeric string to proper words');
$t->is($CurrencyNumberInWord->convert('2.01'), 'два рубля 1 копейка', '->convert() should convert numeric string to proper words');
$t->is($CurrencyNumberInWord->convert('2.02'), 'два рубля 2 копейки', '->convert() should convert numeric string to proper words');
$t->is($CurrencyNumberInWord->convert('2.05'), 'два рубля 5 копеек', '->convert() should convert numeric string to proper words');
$t->is($CurrencyNumberInWord->convert('5.00'), 'пять рублей 0 копеек', '->convert() should convert numeric string to proper words');
$t->is($CurrencyNumberInWord->convert('5.01'), 'пять рублей 1 копейка', '->convert() should convert numeric string to proper words');
$t->is($CurrencyNumberInWord->convert('5.02'), 'пять рублей 2 копейки', '->convert() should convert numeric string to proper words');
$t->is($CurrencyNumberInWord->convert('5.05'), 'пять рублей 5 копеек', '->convert() should convert numeric string to proper words');

/**
 * test
 */
$test = array(10 => 'десять', 11 => 'одиннадцать', 12 => 'двенадцать', 13 => 'тринадцать', 14 => 'четырнадцать', 15 => 'пятнадцать', 16 => 'шестнадцать', 17 => 'семнадцать', 18 => 'восемнадцать', 19 => 'девятнадцать');
foreach ($test as $k => $v) {
	$t->is($CurrencyNumberInWord->convert($k . '.00'), $v . ' рублей 0 копеек', '->convert() should convert numeric string to proper words');
}

/**
 * test
 */
$test = array(20 => 'двадцать', 30 => 'тридцать', 40 => 'сорок', 50 => 'пятьдесят', 60 => 'шестьдесят', 70 => 'семьдесят', 80 => 'восемьдесят', 90 => 'девяносто');
foreach ($test as $k => $v) {
	$t->is($CurrencyNumberInWord->convert($k . '.00'), $v . ' рублей 0 копеек', '->convert() should convert numeric string to proper words');
}

/**
 * test
 */
$test = array(100 => 'сто', 200 => 'двести', 300 => 'триста', 400 => 'четыреста', 500 => 'пятьсот', 600 => 'шестьсот', 700 => 'семьсот', 800 => 'восемьсот', 900 => 'девятьсот');
foreach ($test as $k => $v) {
	$t->is($CurrencyNumberInWord->convert($k . '.00'), $v . ' рублей 0 копеек', '->convert() should convert numeric string to proper words');
}

/**
 * test
 */
$t->is($CurrencyNumberInWord->convert('123.00'), 'сто двадцать три рубля 0 копеек', '->convert() should convert numeric string to proper words');
$t->is($CurrencyNumberInWord->convert('709.00'), 'семьсот девять рублей 0 копеек', '->convert() should convert numeric string to proper words');

/**
 * test
 */
$t->is($CurrencyNumberInWord->convert('1000.01'), 'одна тысяча рублей 1 копейка', '->convert() should convert numeric string to proper words');
$t->is($CurrencyNumberInWord->convert('2000.02'), 'две тысячи рублей 2 копейки', '->convert() should convert numeric string to proper words');
$t->is($CurrencyNumberInWord->convert('5000.05'), 'пять тысяч рублей 5 копеек', '->convert() should convert numeric string to proper words');
$t->is($CurrencyNumberInWord->convert('10000'), 'десять тысяч рублей 0 копеек', '->convert() should convert numeric string to proper words');
$t->is($CurrencyNumberInWord->convert('1300.00'), 'одна тысяча триста рублей 0 копеек', '->convert() should convert numeric string to proper words');
$t->is($CurrencyNumberInWord->convert('2470.01'), 'две тысячи четыреста семьдесят рублей 1 копейка', '->convert() should convert numeric string to proper words');
$t->is($CurrencyNumberInWord->convert('5892.02'), 'пять тысяч восемьсот девяносто два рубля 2 копейки', '->convert() should convert numeric string to proper words');

/**
 * test
 */
$t->is($CurrencyNumberInWord->convert('95648125.3'), 'девяносто пять миллионов шестьсот сорок восемь тысяч сто двадцать пять рублей 3 копейки', '->convert() should convert numeric string to proper words');
$t->is($CurrencyNumberInWord->convert('2000000.00'), 'два миллиона рублей 0 копеек', '->convert() should convert numeric string to proper words');
$t->is($CurrencyNumberInWord->convert('1000000000.00'), 'один миллиард рублей 0 копеек', '->convert() should convert numeric string to proper words');

/**
 * test
 */
$t->diag('->configure()->convert()');
$t->is($CurrencyNumberInWord->configure(array('mainNull' => ''))->convert('0.01'), '1 копейка', '->configure()->convert() should convert numeric string to proper words');
$t->is($CurrencyNumberInWord->configure(array('mainNull' => '0'))->convert('0.02'), '0 рублей 2 копейки', '->configure()->convert() should convert numeric string to proper words');
$t->is($CurrencyNumberInWord->configure(array('mainNull' => '0', 'withLeadingZero' => true))->convert('0.05'), '0 рублей 05 копеек', '->configure()->convert() should convert numeric string to proper words');
$t->is($CurrencyNumberInWord->configure(array('withLeadingZero' => true))->convert('1'), 'один рубль 00 копеек', '->configure()->convert() should convert numeric string to proper words');
