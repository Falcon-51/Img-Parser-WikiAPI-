<?php

	$login = $_POST["login"];
	$password = $_POST["password"];
	
	if (($login != "beaver") or ($password != "root123")){
			echo json_encode("Authorization is failed...");
	}else{

		$pageTitle = $_POST["category"];

		$ImageData = array(
			"01" => 0,
			"02" => 0,
			"03" => 0,
			"04" => 0,
			"05" => 0,
			"06" => 0,
			"07" => 0,
			"08" => 0,
			"09" => 0,
			"10" => 0,
			"11" => 0,
			"12" => 0
		);


		// Задаем параметры запроса
		$params = array(
			'action' => 'query',
			'format' => 'json',
			'generator' => 'categorymembers',
			'gcmtitle' => 'Category:'.$pageTitle,
			'gcmlimit' => 'max', // Получить максимальное количество элементов
			'prop' => 'imageinfo|categories|revisions',
			'iiprop' => 'timestamp|url|user|metadata', // Запрашиваем URL изображения, автора и метаданные
			'iilimit' => '1', // Получить только одно изображение
			'iiurlwidth' => 400,
			'rvprop' => 'user', // Получить автора
		);

		// Формируем URL для запроса
		$url = 'https://commons.wikimedia.org/w/api.php?' . http_build_query($params);
		// Получаем результат через API
		$result = file_get_contents($url);

		// Декодируем JSON-ответ
		$data = json_decode($result, true);

		$dateParts = explode("-", $_POST["yearr"]);
		$year_send = $dateParts[0];

		$meta_data = array();
		$summary_img = array();
		$summary_data = array();

		// Проверяем наличие данных и выводим результат
		if (isset($data['query']['pages'])) {
			foreach ($data['query']['pages'] as $pageId => $page) {
				if (isset($page['imageinfo'])) {
					foreach ($page['imageinfo'] as $image) {

						$date = new DateTime($image["timestamp"]);
						$year = $date->format("Y");
						$month = $date->format("m");

						if ($year == $year_send && $image['user'] == $_POST["author"]){
							
								$ImageData[$month] += 1;
								foreach ($image['metadata'] as $item) {

										$meta_data[]='<li>'. $item["name"] . ": " . $item["value"] . '</li>';
								}

								$summary_img[]=array(
										"title"=>$page['title'],
										"thumburl"=>$image['thumburl'],
										"user"=>$image['user'],
										"descriptionurl"=>$image["descriptionurl"],
										"timestamp"=>$image["timestamp"],
										"metadata"=>$meta_data
								);
						}
						$meta_data = array();
					}
				}
			}
		}

		$months = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
		$counts = array_values($ImageData);

		$plotly_data = [
		[
						'x' => $months,
						'y' => $counts,
						'type' => 'bar'
		]
		];

		$summary_data[]=$summary_img;
		$summary_data[]=$plotly_data;

		echo json_encode($summary_data);
		
	}
?>
