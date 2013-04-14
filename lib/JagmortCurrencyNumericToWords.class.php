<?php

/**
 * JagmortCurrencyNumericToWords
 *
 * Converting currency into words for billing in Russia.
 * Class used only integer operations to avoid floating point misunderstandings.
 *
 * Usage:
 *
 *   JagmortCurrencyNumericToWords::createInstance()->convert('12345.67') => двенадцать тысяч триста сорок пять рублей 67 копеек
 *
 * @author frost-nzcr4 <frost.nzcr4@jagmort.com>
 * @version 0.2
 */
class JagmortCurrencyNumericToWords {
	const GENDER_MALE   = 0;
	const GENDER_FEMALE = 1;

	private
		$options = array(
			'mainNull'        => '00',
			'withLeadingZero' => false);

	protected static
		$instances = array(),
		$current   = 'default';

	/**
	 * Creates a new JagmortCurrencyNumericToWords instance.
	 *
	 * @param string  $name  An instance name.
	 *
	 * @return JagmortCurrencyNumericToWords
	 */
	public static function createInstance($name = null) {
		if (null === $name) {
			$name = count(self::$instances);
		}

		self::$current = $name;
		$class = __CLASS__;
		self::$instances[$name] = new $class();

		return self::$instances[$name];
	}

	/**
	 * Retrieves the singleton instance of this class.
	 *
	 * @param  string  $name  An instance name.
	 *
	 * @return JagmortCurrencyNumericToWords  An JagmortCurrencyNumericToWords implementation instance.
	 */
	public static function getInstance($name = null) {
		if (null === $name) {
			$name = self::$current;
		}

		return self::$instances[$name];
	}

	/**
	 * Configure options.
	 *
	 * @param array $options
	 *
	 * @return JagmortCurrencyNumericToWords
	 */
	public function configure(array $options) {
		$this->options = array_merge($this->options, $options);

		return $this;
	}

	/**
	 * Convert numeric string to words.
	 *
	 * @param  string $numeric
	 *
	 * @return string
	 */
	public function convert($numeric) {
		$numeric_parts = explode('.', $numeric);

		if (1 === count($numeric_parts)) {
			$numeric_parts[1] = '00';
		}
		foreach ($numeric_parts as $k => $v) {
			$numeric_parts[$k] = intval($v);
		}

		$main_cur_unit = $this->mainCurrencyUnit($numeric_parts[0]);        // banknote.
		$frac_cur_unit = $this->fractionalCurrencyUnit($numeric_parts[1]);  // coin.

		if ($main_cur_unit) {
			$out = $main_cur_unit . ' ' . $frac_cur_unit;
		} else {
			$out = $frac_cur_unit;
		}

		return $out;
	}

	/**
	 * Convert fractional currency unit part to words.
	 *
	 * @param integer $amount
	 * @param string  $withLeadingZero  Customize how unit is printed. If amount is 6 when setting this option to true will print '06', otherwise '6'.
	 * @return string
	 */
	protected function fractionalCurrencyUnit($amount, $withLeadingZero = null) {
		$withLeadingZero = (null === $withLeadingZero ? $this->options['withLeadingZero'] : $withLeadingZero);
		if ($withLeadingZero) {
			$amount = sprintf('%02d', $amount);
		}
		return $amount . ' ' . $this->morphology($amount, array('копейка', 'копейки', 'копеек'));
	}

	/**
	 * Convert main currency unit part to words.
	 *
	 * @param integer $amount
	 * @param string  $mainNull  Customize how main currency unit is printed when it equal to 0. Typically, '', 'ноль', '0', and '00' for billing in Russia.
	 *
	 * @return string
	 */
	protected function mainCurrencyUnit($amount, $mainNull = null) {
		$parts = array();

		if (0 === $amount) {
			$mainNull = (null === $mainNull ? $this->options['mainNull'] : $mainNull);
			return '' !== $mainNull ? $mainNull . ' рублей' : $mainNull;
		}

		/*
		 * if $amount = 123456789 then $parts will be [789, 456, 123]
		 */
		$am = $amount;
		while ($am !== 0) {
			$mod = $am % 1000;
			$am = ($am - $mod) / 1000;
			$parts[] = $mod;
		}

		$parts_names = array(
			array('forms' => array('рубль', 'рубля', 'рублей'), 'gender' => self::GENDER_MALE),             // 0-999
			array('forms' => array('тысяча', 'тысячи', 'тысяч'), 'gender' => self::GENDER_FEMALE),          // 1000-999999
			array('forms' => array('миллион', 'миллиона', 'миллионов'), 'gender' => self::GENDER_MALE),     // 1000000-999999999
			array('forms' => array('миллиард', 'милиарда', 'миллиардов'), 'gender' => self::GENDER_MALE));  // 1000000000-999999999999

		$out = array();
		$len = count($parts);
		for ($i = 0; $i < $len; $i++) {
			$amount_to_str = $this->amountToString($parts[$i], $parts_names[$i]['gender']);
			/*
			 * $i === 0 is used for 'рубль', when $parts[$i] == 0 will return 'рублей' otherwise return $parts[$i] + ' рублей',
			 * $i === 1 is used for 'тысяча', when $parts[$i] == 0 will do nothing otherwise return $parts[$i] + ' тысяч',
			 * and so on
			 */
			if (0 === $i || 0 < $parts[$i]) {
				$morphology = $this->morphology($parts[$i] < 20 ? $parts[$i] : $parts[$i] % 10, $parts_names[$i]['forms']);
				$out[] = $amount_to_str ? $amount_to_str . ' ' . $morphology : $morphology;
			}
		}

		$out = array_reverse($out);
		return implode(' ', $out);
	}

	/**
	 * Convert amount to words.
	 *
	 * @param integer $amount
	 * @param integer $gender
	 *
	 * @return string
	 */
	protected function amountToString($amount, $gender = self::GENDER_MALE) {
		$from1to9m    = array(1 => 'один', 2 => 'два', 3 => 'три', 4 => 'четыре', 5 => 'пять', 6 => 'шесть', 7 => 'семь', 8 => 'восемь', 9 => 'девять');
		$from1to9f    = array(1 => 'одна', 2 => 'две') + $from1to9m;
		$from10to19   = array(10 => 'десять', 11 => 'одиннадцать', 12 => 'двенадцать', 13 => 'тринадцать', 14 => 'четырнадцать', 15 => 'пятнадцать', 16 => 'шестнадцать', 17 => 'семнадцать', 18 => 'восемнадцать', 19 => 'девятнадцать');
		$from10to90   = array(2 => 'двадцать', 3 => 'тридцать', 4 => 'сорок', 5 => 'пятьдесят', 6 => 'шестьдесят', 7 => 'семьдесят', 8 => 'восемьдесят', 9 => 'девяносто');
		$from100to900 = array(1 => 'сто', 2 => 'двести', 3 => 'триста', 4 => 'четыреста', 5 => 'пятьсот', 6 => 'шестьсот', 7 => 'семьсот', 8 => 'восемьсот', 9 => 'девятьсот');

		if (0 === $amount) {
			return '';
		}
		if ($amount < 10) {
			if (self::GENDER_MALE === $gender) {
				return $from1to9m[$amount];
			} else {
				return $from1to9f[$amount];
			}
		}
		if ($amount < 20) {
			return $from10to19[$amount];
		}
		if ($amount < 100) {
			$_1  = $amount % 10;
			$_10 = ($amount - $_1) / 10;

			$out = $this->amountToString($_1, $gender);
			return $from10to90[$_10] . ($out ? ' ' . $out : $out);
		}

		$_10  = $amount % 100;
		$_100 = ($amount - $_10) / 100;
		$out = $this->amountToString($_10, $gender);
		return $from100to900[$_100] . ($out ? ' ' . $out : $out);
	}

	/**
	 * Morphology rules for currency amount.
	 *
	 * Examples:
	 *
	 *   morphology(1, array('dollar', 'dollars', 'dollars')) => 1 dollar
	 *   morphology(2, array('dollar', 'dollars', 'dollars')) => 2 dollars
	 *
	 * @param integer $amount
	 * @param array $forms Array of morphology forms for one, some, and many items. Short form is array('for one item', 'for some items', 'for many items')
	 *
	 * @return string
	 */
	protected function morphology($amount, array $forms) {
		// Convert from short form to extended with keys [one => ..., some => ..., many => ...].
		if (!isset($forms['one'])) {
			$keys = array('one', 'some', 'many');
			$len = count($keys);
			for ($i = 0; $i < $len; $i++) {
				$forms[$keys[$i]] = $forms[$i];
			}
		}

		if (1 === $amount) {
			return $forms['one'];
		}
		if ($amount > 1 && $amount < 5) {
			return $forms['some'];
		}
		if ($amount > 20) {
			$mod = $amount % 10;
			switch ($mod) {
				case 1:
					return $forms['one'];
				case 2:
				case 3:
				case 4:
					return $forms['some'];
			}
		}

		return $forms['many'];
	}
}
