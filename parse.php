<?php


//Заголовок статьи
//$pageTitle = "Tank";

if (isset($_POST["done"])) {


        if (empty($_POST["topic"])){
                $pageTitle = "Tank";
        } else{
                $pageTitle = $_POST["topic"];
        }


//Задаём endpoint Wiki и параметры для запроса
$endPoint = "https://en.wikipedia.org/w/api.php";
$params = [
    "action" => "query",
    "prop" => "images",
    "titles" => $pageTitle,
    "format" => "json"
];

//Формируем URL
$url = $endPoint . "?" . http_build_query( $params );

$ch = curl_init( $url ); //Инициализируем curl
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true ); //Устанавливаем опцию для возврата результата запроса в виде строки
$output = curl_exec( $ch ); // Выполняем запрос и сохраняем результат.
curl_close( $ch ); // Закрываем соединение

$result = json_decode( $output, true ); // Декодируем полученный JSON ответ в ассоциативный массив.

foreach ($result["query"]["pages"] as $page) {          // Перебираем страницы из результата запроса
            if (isset($page["images"])) {               // Проверяем наличие изображений на странице.
                foreach ($page["images"] as $image) {   // Перебираем изображения на странице.
                                        $params = [     // Задаем параметры для нового запроса, чтобы получить информацию о каждом изображении.
                                                "action" => "query",
                                                "format" => "json",
                                                "prop" => "imageinfo",
                                                "titles" => $image["title"],
                                                "iilimit" => 2,
                                                "iiprop" => "timestamp|user|url",
                                                "iiurlwidth" => 200
                                         ];

                                        // Формируем URL для нового запроса.
                                        $url = $endPoint . "?" . http_build_query( $params );

                                        $ch = curl_init( $url );
                                        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
                                        $output = curl_exec( $ch );
                                        curl_close( $ch );

                                        $result = json_decode( $output, true );

                                        // Перебираем информацию об изображениях.
                                        foreach( $result["query"]["pages"] as $k => $v ) {
                                                echo '<li>' . ( $v["title"] . " is uploaded by User:" . $v["imageinfo"][0]["user"] . " " ) . '</li>';
                                                echo '<li>' . 'timestamp:'  .  $v["imageinfo"][0]["timestamp"] . '</li> ';
                                                echo "<a href='" .  $v["imageinfo"][0]["descriptionurl"] . "'>Link on page with description</a><br>";
                                                echo '<img src="' .  $v["imageinfo"][0]["responsiveUrls"]["2"] . '" alt="Image">';
                                        }
                }
            }
       }

}
