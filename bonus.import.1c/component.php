<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$APPLICATION->RestartBuffer();

header("Content-Type: application/xml; charset=UTF-8");
$RETURN_XML = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>".PHP_EOL;

CModule::IncludeModule('highloadblock');
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;

function to_array(&$input) {	//Переделаем все элементы из объектов в массивы
	if (is_object($input)) $input = (array) $input;
	foreach ($input AS $key => $value) {
		if (is_object($value)) $input[$key] = (array) $value;
		if (is_array($input[$key])) to_array($input[$key]);
	}
}
function arrItemZero(&$arr) {	//Если документ всего один, приведём его к массиву с нулевым итемом
	if (!is_array($arr[0])){
		$tmp = $arr;
		$arr = array();
		$arr[0] = $tmp;
	}
}


//---Откроем XML-файл
$xml = '';
if ($_GET['xml_file'] == 'post') {
	if (is_array($_FILES['xml'])) {
		$xml = file_get_contents($_FILES['xml']['tmp_name']);
		//$xml = str_replace("windows-1251", "utf-8", $xml);
		//$xml = iconv("CP1251", "UTF-8", $xml);
	}
} else {
	$data = file_get_contents("php://input");
	$file_name = 'bonus_'.date('d.m.Y H:i:s', time()).'.xml';
	$dir_name = date('d.m.Y', time());
	if (!is_dir($_SERVER['DOCUMENT_ROOT']."/upload/1c_bonus/{$dir_name}")) mkdir($_SERVER['DOCUMENT_ROOT']."/upload/1c_bonus/{$dir_name}/", 0755);
	
	file_put_contents($_SERVER['DOCUMENT_ROOT']."/upload/1c_bonus/{$dir_name}/{$file_name}", $data);
	$xml = file_get_contents($_SERVER['DOCUMENT_ROOT']."/upload/1c_bonus/{$dir_name}/{$file_name}");
}
//---
if (strstr($xml, 'partners')) {
	$data = new SimpleXMLElement($xml);
	
	to_array($data);
	arrItemZero($data['Документ']);
	
	$return_orders = array();
	
	//-Достанем HLBlock
	$hlblock = HL\HighloadBlockTable::getById(1)->fetch();
	$entity = HL\HighloadBlockTable::compileEntity($hlblock);
	$entity_data_class = $entity->getDataClass();
	//-
	
	foreach ($data['partner'] as $partner) {
		$attr = is_array($partner['@attributes']) ? $partner['@attributes'] : $partner;
		
		$result = '';
		$arFields = array(
				'UF_UID' => $attr['ID'],
				'UF_PAID' => $attr['ПродажиЗаРасчетныйПериод'],
				'UF_PAID_BONUS' => $attr['СписаноБонусов'],
				'UF_SAVED' => $attr['ПолученоСкидки'],
				'UF_ACCRUED' => $attr['НачисленоБонусов'],
				'UF_SCORE' => $attr['БонусовНаСчету'],
				'UF_DISCOUNT_CURRENT' => $attr['ТекущийУровеньСкидок'],
				'UF_BONUS_CURRENT' => $attr['ТекущийУровеньБонусов'],
				'UF_DISCOUNT_NEXT' => $attr['СледующийУровеньСкидок'],
				'UF_BONUS_NEXT' => $attr['СледующийУровеньБонусов'],
		);
		if ($user = $entity_data_class::getList(array('filter' => array('UF_UID' => $attr['ID'])))->Fetch()) {
			$result = $entity_data_class::update($user['ID'], $arFields);
		} else {
			$result = $entity_data_class::add($arFields);
		}
	}
	
	//---Составим XML с ответом
	$RETURN_XML .= "<status>OK</status>";
	echo $RETURN_XML;
	
	die();
}

die();