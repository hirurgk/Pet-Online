$(function(){
	$('.lsc-link').click(function(){
		$('.lsc-popup').slideToggle(300);
	});
	
	$('.lsc-q input').keyup(function(){
		var q = $(this).val();
		if (q.length >= 3) {
			$.getJSON('/test_city.php', {'ajax_city':'1', 'q':q}, function(cities){
				$('.lsc-cities div a').hide();
				for (i in cities) {
					$('.lsc-cities div:eq(0)').append('<a class="lsc-link-find" href="?change_city='+cities[i].CITY_ID+'">'+cities[i].CITY_NAME+'</a>');
				}
			});
		} else {
			$('.lsc-link-find').remove();
			$('.lsc-cities div a').show();
		}
	});
});