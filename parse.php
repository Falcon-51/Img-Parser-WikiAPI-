<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>MediaWikiParser</title>
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

                <h1>MediaWikiParser</h1>
                <form action="parse.php" method="post">
                        <p>Category: <input type="text" name="category" value="Mountains"></p>
                        <p>Year: <input type="number" name="year" min="1970" max="<?php echo date('Y'); ?>" step="1" value="2024"></p>
                        <p><input type="Submit" value="Confirm" name="done"></p>
                </form>
                <div id="plot"></div>

           <?php
                if (isset($_POST["done"])) {
                   echo '<h1>'. 'Result' . '</h1>';

                   if (empty($_POST["category"])){
                          $pageTitle = "Mountains";
                   } else{
                          $pageTitle = $_POST["category"];
                   }

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
                                'gcmtitle' => 'Category:'.$pageTitle, // Замените на название вашей категории
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
                        // Вывод начала таблицы
                        echo "<table id='example' class='table table-striped' style='width:100%'>";
                        echo "<thead>
                                                <tr>
                                                        <th>Title</th>
                                                        <th>Image</th>
                                                        <th>Author</th>
                                                        <th>Link</th>
                                                        <th>Timestamp</th>
                                                        <th>Metadata</th>
                                                </tr>
                                </thead>";
                        echo "<tbody>";


                        // Проверяем наличие данных и выводим результат
                        if (isset($data['query']['pages'])) {
                                foreach ($data['query']['pages'] as $pageId => $page) {
                                        if (isset($page['imageinfo'])) {
                                                foreach ($page['imageinfo'] as $image) {


                                                        echo "<tr>";
                                                                echo '<td>'. $page['title'] . '</td>';
                                                                echo  '<td>'. '<img src="' .  $image['thumburl']  . '" alt="Image">'.'</td>';

                                                                if (isset($image['user'])) {
                                                                        echo '<td>'. $image['user'] . '</td>';
                                                                } else {
                                                                        echo '<td>'. 'None' . '</td>';
                                                                }

                                                                echo "<td><a href='" .  $image["descriptionurl"] . "'>Link on page with description</a></td>";

                                                                echo '<td>' . $image["timestamp"] . '</td>';
                                                                $date = new DateTime($image["timestamp"]);
                                                                $year = $date->format("Y");
                                                                $month = $date->format("m");
                                                                if ($year == $_POST["year"]){
                                                                        $ImageData[$month] += 1;
                                                                }


                                                                echo '<td>';
                                                                foreach ($image['metadata'] as $item) {

                                                                        echo '<li>'. $item["name"] . ": " . $item["value"] . '</li>';
                                                                }
                                                                echo '</td>';

                                                        echo "</tr>";


                                                }
                                        }
                                }
                        } else {
                                echo 'No data!';
                        }

                 echo "</tbody>";
                 echo "</table>";


                //$months = array_keys($ImageData);
                $months = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
                $counts = array_values($ImageData);

                $plotly_data = [
                [
                        'x' => $months,
                        'y' => $counts,
                        'type' => 'bar'
                ]
                ];

                $plotly_json = json_encode($plotly_data);
          }

          ?>



        </div>
</div>




<script>
// JavaScript код для построения диаграммы с помощью Plotly
var data = <?php echo $plotly_json; ?>;


var layout = {
    title: 'Number of Images by Month',
    xaxis: {
        title: 'Month'
    },
    yaxis: {
        title: 'Count of Images'
    }
};

Plotly.newPlot('plot', data, layout);
</script>


</body>
</html>