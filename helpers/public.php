<?php

/**
 * Function helpNumeric
 * Fungsi ini digunakan untuk mengecek apakah sebuah variabel berisi nilai
   yang bersifat numeric (int, float, double).
 * @access public
 * @param (any) $var
 * @param (int) $res
 * @return (int) {0}
 */
function helpNumeric($var, $res = 0)
{
	return is_numeric($var) ? $var : $res;
}

/**
 * Function helpRoman
 * Fungsi ini digunakan untuk merubah angka menjadi bilangan romawi
 * @access public
 * @param (int) $var
 * @return (string) {''}
 */
function helpRoman($var)
{
	$n = intval($var);
	$result = '';
	$lookup = array(
		'M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400,
		'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40,
		'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1
	);
	foreach ($lookup as $roman => $value) {
		$matches = intval($n / $value);
		$result .= str_repeat($roman, $matches);
		$n = $n % $value;
	}
	return $result;
}

/**
 * Function helpSecSql
 * Fungsi ini digunakan untuk merubah variabel menjadi aman sebelum dimasukkan ke dalam database
 * @access public
 * @param (string) $var
 * @return (string)
 */
function helpSecSql($var)
{
	return addslashes(strtolower($var));
}

/**
 * Function helpTerbilang
 * Fungsi ini digunakan untuk merubah angka yang dimasukkan menjadi ejaan
 * @access public
 * @param (int) $var
 * @return (string)
 */
function helpTerbilang($num)
{
	$digits = array(
		0 => "nol",
		1 => "satu",
		2 => "dua",
		3 => "tiga",
		4 => "empat",
		5 => "lima",
		6 => "enam",
		7 => "tujuh",
		8 => "delapan",
		9 => "sembilan"
	);
	$orders = array(
		0 => "",
		1 => "puluh",
		2 => "ratus",
		3 => "ribu",
		6 => "juta",
		9 => "miliar",
		12 => "triliun",
		15 => "kuadriliun"
	);

	$is_neg = $num < 0;
	$num = "$num";

	$int = "";
	if (preg_match("/^[+-]?(\d+)/", $num, $m)) $int = $m[1];
	$mult = 0;
	$wint = "";

	while (preg_match('/(\d{1,3})$/', $int, $m)) {

		$s = $m[1] % 10;
		$p = ($m[1] % 100 - $s) / 10;
		$r = ($m[1] - $p * 10 - $s) / 100;

		if ($r == 0) $g = "";
		elseif ($r == 1) $g = "se$orders[2]";
		else $g = $digits[$r] . " $orders[2]";

		if ($p == 0) {
			if ($s == 0);
			elseif ($s == 1) $g = ($g ? "$g " . $digits[$s] : ($mult == 0 ? $digits[1] : "se"));
			else $g = ($g ? "$g " : "") . $digits[$s];
		} elseif ($p == 1) {
			if ($s == 0) $g = ($g ? "$g " : "") . "se$orders[1]";
			elseif ($s == 1) $g = ($g ? "$g " : "") . "sebelas";
			else $g = ($g ? "$g " : "") . $digits[$s] . " belas";
		} else {
			$g = ($g ? "$g " : "") . $digits[$p] . " puluh" .
				($s > 0 ? " " . $digits[$s] : "");
		}

		$wint = ($g ? $g . ($g == "se" ? "" : " ") . $orders[$mult] : "") .
			($wint ? " $wint" : "");

		$int = preg_replace('/\d{1,3}$/', '', $int);
		$mult += 3;
	}
	if (!$wint) $wint = $digits[0];
	$frac = "";
	if (preg_match("/\.(\d+)/", $num, $m)) $frac = $m[1];
	$wfrac = "";
	for ($i = 0; $i < strlen($frac); $i++) {
		$wfrac .= ($wfrac ? " " : "") . $digits[substr($frac, $i, 1)];
	}
	$hasil = ($is_neg ? "minus " : "") . $wint . ($wfrac ? " koma $wfrac" : "");
	$hasil = str_replace("sejuta", "satu juta", $hasil);
	return $hasil;
}

/**
 * Function helpResponse
 * Fungsi ini digunakan untuk mengambil response restful
 * @access public
 * @param (string) $code
 * @param (array) $data
 * @param (string) $msg
 * @param (string) $status
 * @return (array)
 */
function helpResponse($code, $data = NULL, $msg = '', $status = '')
{
	switch ($code) {
		case '200':
			$s = 'OK';
			$m = 'Sukses';
			break;
		case '201':
		case '202':
			$s = 'Saved';
			$m = 'Data berhasil disimpan';
			break;
		case '204':
			$s = 'No Content';
			$m = 'Data tidak ditemukan';
			break;
		case '304':
			$s = 'Not Modified';
			$m = 'Data gagal disimpan';
			break;
		case '400':
			$s = 'Bad Request';
			$m = 'Fungsi tidak ditemukan';
			break;
		case '401':
			$s = 'Unauthorized';
			$m = 'Silahkan login terlebih dahulu';
			break;
		case '403':
			$s = 'Forbidden';
			$m = 'Anda tidak dapat mengakses halaman ini, silahkan hubungi Administrator';
			// $m = 'Sesi anda telah berakhir';
			break;
		case '404':
			$s = 'Not Found';
			$m = 'Halaman tidak ditemukan';
			break;
		case '414':
			$s = 'Request URI Too Long';
			$m = 'Data yang dikirim terlalu panjang';
			break;
		case '500':
			$s = 'Internal Server Error';
			$m = 'Maaf, terjadi kesalahan dalam mengolah data';
			break;
		case '502':
			$s = 'Bad Gateway';
			$m = 'Tidak dapat terhubung ke server';
			break;
		case '503':
			$s = 'Service Unavailable';
			$m = 'Server tidak dapat diakses';
			break;
		default:
			$s = 'Undefined';
			$m = 'Undefined';
			break;
	}

	$status = ($status != '') ? $status : $s;
	$msg = ($msg != '') ? $msg : $m;
	$result = array(
		"status" => array(
			"code" => $code,
			"message" => $msg
		),
		"data" => $data
	);

	setHeader($code, $status);

	return $result;
}

function rand_str($length = 10, $token = false)
{
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

	if ($token == true) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_';
	}
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return $randomString;
}

// start by: dimas

function helpCurrency($nominal = '', $start = '', $pemisah = '.', $cent = true)
{
	if (empty($nominal)) {
		$hasil = '-';
	} else {
		$nominal = empty($nominal) ? 0 : $nominal;
		$angka_belakang = ',00';
		$temp_rp = explode('.', $nominal);

		if (count($temp_rp) > 1) {
			$nominal = $temp_rp[0];
			$angka_belakang = ',' . $temp_rp[1];
		}

		if ($cent == false) {
			$angka_belakang = '';
		}

		$hasil = $start . number_format($nominal, 0, "", $pemisah) . $angka_belakang;
	}

	return $hasil;
}

function setHeader($code = '200', $status = '')
{
	header($_SERVER['SERVER_PROTOCOL'] . ' ' . $code . ' ' . $status);
}

function helpToNum($data)
{
	$alphabet = array(
		'a', 'b', 'c', 'd', 'e',
		'f', 'g', 'h', 'i', 'j',
		'k', 'l', 'm', 'n', 'o',
		'p', 'q', 'r', 's', 't',
		'u', 'v', 'w', 'x', 'y',
		'z'
	);
	$alpha_flip = array_flip($alphabet);
	$return_value = -1;
	$length = strlen($data);
	for ($i = 0; $i < $length; $i++) {
		$return_value +=
			($alpha_flip[$data[$i]] + 1) * pow(26, ($length - $i - 1));
	}
	return $return_value;
}

function toNum($data)
{
	$alphabet = array(
		'a', 'b', 'c', 'd', 'e',
		'f', 'g', 'h', 'i', 'j',
		'k', 'l', 'm', 'n', 'o',
		'p', 'q', 'r', 's', 't',
		'u', 'v', 'w', 'x', 'y',
		'z'
	);
	$alpha_flip = array_flip($alphabet);
	$return_value = -1;
	$length = strlen($data);
	for ($i = 0; $i < $length; $i++) {
		$return_value +=
			($alpha_flip[$data[$i]] + 1) * pow(26, ($length - $i - 1));
	}
	return $return_value;
}

function toAlpha($data)
{
	$alphabet =   array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
	$alpha_flip = array_flip($alphabet);
	if ($data <= 25) {
		return $alphabet[$data];
	} elseif ($data > 25) {
		$dividend = ($data + 1);
		$alpha = '';
		$modulo = '';
		while ($dividend > 0) {
			$modulo = ($dividend - 1) % 26;
			$alpha = $alphabet[$modulo] . $alpha;
			$dividend = floor((($dividend - $modulo) / 26));
		}
		return $alpha;
	}
}

function helpRename($value = '', $replace_with = '')
{
	$pattern = '/[^a-zA-Z0-9 &.,-_]/u';
	$value = preg_replace($pattern, $replace_with, $value);

	return $value;
}

function help_forbidden_char($value = '', $replace_with = '')
{
	$forbidden_char = array('[', ']', '(', ')', '?', '\'', '′', '%');
	$value = str_replace($forbidden_char, $replace_with, $value);

	return $value;
}

function help_filename($value = '', $replace_with = '', $timestamp = true)
{
	$forbidden_char = array('[', ']', '(', ')', '?', '\'', '′', '%', ' ');
	$value = str_replace($forbidden_char, $replace_with, $value);
	$value = ($timestamp == true) ? date('YmdHis') . '_' . $value : $value;

	return $value;
}

function helpUsername($value = '', $replace_with = '', $lower = true)
{
	$pattern = '/[^a-zA-Z0-9.-_]/u';

	$arr_temp = explode(' ', $value);

	$result = preg_replace($pattern, $replace_with, $arr_temp[0]);
	$result = (strlen($result) >= 5) ? $result : (isset($arr_temp[1]) ? $result . preg_replace($pattern, $replace_with, $arr_temp[1]) : $result . rand_str(2, true));
	$result = ($lower == true) ? strtolower($result) : $result;

	return $result;
}

function helpEmpty($value = '', $replace_with = '-', $null = false)
{
	if ($null == false) {
		$result = (empty($value) && $value != '0') ? $replace_with : $value;
	} else {
		$result = (empty($value) && $value != '0') ? '' : $value;
	}

	return $result;
}

function helpText($value, $tags = true, $zalgo = true)
{
	$result = $value;

	if ($tags == true) {
		$result = htmlspecialchars($result, ENT_QUOTES, 'UTF-8');
	}

	if ($zalgo == true) {
		// $pattern = "~(?:[\p{M}]{1})([\p{M}])+?~uis";
		$pattern = "~[\p{M}]~uis";
		$result = preg_replace($pattern, "", $result);
	}

	return $result;
}

function slug($text)
{
	// replace non letter or digits by -
	$text = preg_replace('~[^\pL\d]+~u', '-', $text);

	// transliterate
	$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

	// remove unwanted characters
	$text = preg_replace('[^-\w]+', '', $text);

	// trim
	$text = trim($text, '-');

	// remove duplicate -
	$text = preg_replace('-+', '-', $text);

	// lowercase
	$text = strtolower($text);

	if (empty($text)) {
		return 'n-a';
	}

	return $text;
}

function generateAccessToken($group = 'dB528-')
{
	return $group . rand_str(5, true) . substr(date('Y'), 0, 2) . rand_str(5, true) . substr(date('Y'), 2, 2) . rand_str(6, true) . date('m') . rand_str(6, true) . date('d') . rand_str(6, true) . date('H') . rand_str(6, true) . date('i') . rand_str(6, true) . date('s');
}

function modelOptions($default, $options = [])
{
	if (is_array($default)) {
		foreach ($default as $key => $value) {
			if (empty($options) || !array_key_exists($key, $options)) {
				$options[$key] = $value;
			}
		}
	}

	return $options;
}

function customDecode($value, $find = ["{", "}"], $explodeWith = ","){
	$replaceValue = str_replace($find, '', $value);

	return explode($explodeWith, $replaceValue);
}
// end by: dimas
