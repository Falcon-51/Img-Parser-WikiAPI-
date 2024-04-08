<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>WikiApiParser</title>
        <link rel="stylesheet" href='static/css/dataTables.bootstrap5.css'>
        <link rel="stylesheet" href='static/css/bootstrap.min.css'>
        <link rel="stylesheet" href='static/css/style.css'>
        <script type="text/javascript" language="javascript" src='static/js/jquery-3.7.1.js'></script>
        <script type="text/javascript" language="javascript" src='static/js/dataTables.js'></script>
        <script type="text/javascript" language="javascript" src='static/js/dataTables.bootstrap5.js'></script>
        <script type="text/javascript" language="javascript" src='static/js/bootstrap.bundle.min.js'></script>
        <script type="text/javascript" language="javascript" src='static/js/load_table.js'></script>
        <script src="https://cdn.plot.ly/plotly-2.30.0.min.js" charset="utf-8"></script>
</head>


<body>


        <div class="wrapper">
                <div id="content">
                        <h1>Result</h1>

                        <?php

                                if (isset($_POST["done"])) {


                                        if (empty($_POST["topic"])){
                                                $pageTitle = "Cats";
                                        } else{
                                                $pageTitle = $_POST["topic"];
                                        }



                                $endPoint = "https://en.wikipedia.org/w/api.php";
                                $params = [
                                "action" => "query",
                                "list" => "categorymembers",
                                "cmtitle" => "Category:".$pageTitle,
                                "cmtype" => "page",
                                "cmlimit" => 1,
                                "format" => "json"
                                ];

                                $url = $endPoint . "?" . http_build_query( $params );

                                $ch = curl_init( $url );
                                curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
                                $output = curl_exec( $ch );
                                curl_close( $ch );

                                $result = json_decode( $output, true );

                                // Вывод начала таблицы
                                echo "<table id='example' class='table table-striped' style='width:100%'>";
                                echo "<thead>
                                                        <tr>
                                                                <th>User</th>
                                                                <th>Timestamp</th>
                                                                <th>Link</th>
                                                                <th>Image</th>
                                                        </tr>
                                                </thead>";
                                echo "<tbody>";
                                foreach( $result["query"]["categorymembers"] as $cat ) {
                                                $params = [
                                                "action" => "query",
                                                "prop" => "images",
                                                "titles" => $cat["title"],
                                                "format" => "json"
                                        ];

                                        $url = $endPoint . "?" . http_build_query( $params );

                                        $ch = curl_init( $url );
                                        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
                                        $output = curl_exec( $ch );
                                        curl_close( $ch );

                                        $result = json_decode( $output, true );

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

                                                                                echo "<tr>";
                                                                                        echo '<td>'.( $v['title'] . ' is uploaded by User:' . $v['imageinfo'][0]['user']).'</td>';
                                                                                        echo '<td>'. $v['imageinfo'][0]['timestamp'].'</td>';
                                                                                        echo "<td><a href='" .  $v["imageinfo"][0]["descriptionurl"] . "'>Link on page with description</a><br></td>";
                                                                                        echo  '<td>'. '<img src="' .  $v["imageinfo"][0]["responsiveUrls"]["2"] . '" alt="Image">'.'</td>';
                                                                                echo "</tr>";
                                                                                //echo '<li>' . ( $v["title"] . " is uploaded by User:" . $v["imageinfo"][0]["user"] . " " ) . '</li>';
                                                                                //echo '<li>' . 'timestamp:'  .  $v["imageinfo"][0]["timestamp"] . '</li> ';
                                                                                //echo "<a href='" .  $v["imageinfo"][0]["descriptionurl"] . "'>Link on page with description</a><br>";
                                                                                //echo '<img src="' .  $v["imageinfo"][0]["responsiveUrls"]["2"] . '" alt="Image">';
                                                                        }
                                                }
                                        }
                                }
                                }
                                        echo "</tbody>";
                                        echo "</table>";
                                }

                        ?>


                </div>
        </div>
        
</body>
</html>
