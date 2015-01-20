<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<?
$arCities = array(
	array(
		array('ID' => '79575', 'NAME' => 'Москва'),
		array('ID' => '79631', 'NAME' => 'Санкт-Петербург'),
		array('ID' => '79531', 'NAME' => 'Нижний Новгород'),
		array('ID' => '79327', 'NAME' => 'Ростов-на-Дону'),
		array('ID' => '79629', 'NAME' => 'Самара'),
		array('ID' => '79348', 'NAME' => 'Казань'),
		array('ID' => '79763', 'NAME' => 'Екатеринбург'),
		array('ID' => '79726', 'NAME' => 'Тольятти'),
		array('ID' => '79574', 'NAME' => 'Омск'),
	),
	array(
		array('ID' => '79234', 'NAME' => 'Волгоград'),
		array('ID' => '79659', 'NAME' => 'Ставрополь'),
		array('ID' => '79383', 'NAME' => 'Краснодар'),
		array('ID' => '79230', 'NAME' => 'Воронеж'),
		array('ID' => '79736', 'NAME' => 'Уфа'),
		array('ID' => '79840', 'NAME' => 'Ярославль'),
		array('ID' => '79623', 'NAME' => 'Саратов'),
		array('ID' => '79472', 'NAME' => 'Ижевск'),
		array('ID' => '79819', 'NAME' => 'Челябинск'),
	),
);
?>


<div class="lsc-container">
	<span class="lsc-city">Город:</span> <a class="lsc-link"><?=$arResult['CITY_NAME']?></a>
	<div class="lsc-popup">
		<div class="lsc-popup-header">Выберите город</div>
		<div class="lsc-q"><input type="text" value=""></div>
		<div class="lsc-cities">
			<?foreach($arCities as $rowCities):?>
				<div>
					<?foreach($rowCities as $city):?>
						<a class="<?=($arResult['CITY_ID'] == $city['ID'] ? 'lsc-link-selected' : '')?>" href="<?=$APPLICATION->GetCurPageParam("change_city={$city['ID']}", array('change_city'))?>"><?=$city['NAME']?></a>
					<?endforeach;?>
				</div>
			<?endforeach;?>
		</div>
	</div>
</div>