<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

class CL0gicSelectCity extends CBitrixComponent
{
	const DEFAULT_CITY = 79575;
	const COUNT_FIND_CITIES = 12;
	
	private $currentCityId;
	private $currentCityName;
	
	public function executeComponent()
	{
		if (!CModule::IncludeModule('statistic')) die(__CLASS__.": Не установлен модуль статистики");
		global $APPLICATION;
		
		if ($_REQUEST['ajax_city'] == 1)						//Аякс-запрос на поиск города
		{
			global $APPLICATION;
			$APPLICATION->RestartBuffer();
			echo $this->getCitiesJSON($_REQUEST['q']);
			die();
		}
		elseif (!empty($_REQUEST['change_city']))				//Если запрос на изменение города
		{
			$this->setCity($_REQUEST['change_city']);
			$this->saveCity();	//сразу сохраним и переадресуем на страницу без параметра change_city
			LocalRedirect($APPLICATION->GetCurPageParam('', array('change_city')));
		}
		elseif (is_numeric($_COOKIE['CITY']))					//Если город в куках, то выставляем его
		{
			$this->setCity($_COOKIE['CITY']);
		}
		else													//Иначе определяем по IP
		{
			$city_id = $this->getCityByIP();
			$this->setCity($city_id);
		}
		
		if (!$this->currentCityId)								//Если по каким-то причинам город не определился, выставляем по умолчанию
		{
			$city_id = getDefaultCity();
			$this->setCity($city_id);
		}
		
		$this->saveCity();

		$this->includeComponentTemplate();
    }
    
    private function setCity($city_id)
    {
    	$arOrder = array('CITY_NAME' => 'DESC');
    	$arFilter = array('COUNTRY_ID' => 'RU', 'CITY_ID' => $city_id);
    	if ($res = CCity::GetList($arOrder, $arFilter))
    	{
    		$city = $res->Fetch();
    		$this->currentCityId = $city['CITY_ID'];
    		$this->currentCityName = $city['CITY_NAME'];
    	}
    }
    
    private function saveCity()
    {
    	$this->arResult['CITY_ID'] = $this->currentCityId;
    	$this->arResult['CITY_NAME'] = $this->currentCityName;
    	setcookie("CITY", $this->currentCityId, time() + 60 * 60 * 24 * 30 * 12 * 2, '/');
    	$GLOBALS['CITY_ID'] = $this->currentCityId;
    	$GLOBALS['CITY_NAME'] = $this->currentCityName;
    }
    
    private function getCityByIP()
    {
    	$city_obj = new CCity();
    	$this_city = $city_obj->GetCityID();
    	return $this_city;
    }
    
    private function getDefaultCity()
    {
    	return $arParams['DEFAULT_CITY'] ? $arParams['DEFAULT_CITY'] : self::DEFAULT_CITY;
    }
    
    private function getCitiesJSON($q)
    {
    	$json_cities = '';
    	if (strlen($q) >= 3)
    	{
	    	$cities = array();
	    	$arOrder = array('CITY_NAME' => 'ASC');
	    	$arFilter = array('COUNTRY_ID' => 'RU', 'CITY_NAME' => "%{$q}%");
	    	$res = CCity::GetList($arOrder, $arFilter);
	    	$i = 0;
	    	while ($el = $res->Fetch())
	    	{
	    		$i++;
	    		$cities[] = array('CITY_ID' => $el['CITY_ID'], 'CITY_NAME' => $el['CITY_NAME']);
	    		if ($i > self::COUNT_FIND_CITIES) break;
	    	}
	    	//$json_cities = json_encode($cities);
	    	
	    	$i = 0;
	    	$json_cities = '[';
	    	foreach ($cities as $key=>$city)
	    	{
	    		if($i != 0) $json_cities .= ',';
	    		$json_cities .= '{"CITY_ID":"'.$city['CITY_ID'].'","CITY_NAME":"'.$city['CITY_NAME'].'"}';
	    		$i++;
	    	}
	    	$json_cities .= ']';
    	}
    	return $json_cities;
    }
}