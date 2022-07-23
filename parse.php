<?php

$host = 'localhost';
$db   = 'energystats';
$charset = 'utf8mb4';

$user = 'energystats';
$pass = 'energystats';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

$papers = scandir("data");
$op_dir = getcwd() . "/";
// print_r($papers[2]);

foreach ($papers as $paper) {
    $rasters = glob("data/$paper/rasters/*.png");
    // print_r($rasters);
    $raster_acc = [];

    foreach ($rasters as $raster_path) {
        $page_text_path = substr($raster_path, 0, -3) . "page.txt";

        $page_text = file_get_contents($op_dir . $page_text_path);

        add_entry($pdo, $raster_path, $page_text, null);

        // print_r(file_get_contents($page_text_path));
        // echo "\n";
    }



    $drawings = glob("data/$paper/drawings/*.png");
    foreach ($drawings as $draw_path) {
        $page_text_path = substr($draw_path, 0, -3) . "page.txt";
        $img_text_path = substr($draw_path, 0, -3) . "img.txt";

        $page_text = file_get_contents($op_dir . $page_text_path);
        $img_text = file_get_contents($op_dir . $img_text_path);

        add_entry($pdo, $draw_path, $page_text, $img_text);
    }
    // print_r($drawings);


    // if (sizeof($rasters) != 0) {
    //     break;
    // }
}

function add_entry($pdo, $image_path, $page_text, $image_text)
{
    $add_stmt = $pdo->prepare('INSERT INTO jlh_papers (image_path, page_text, img_text) VALUES (:image_path, :page_text, :img_text)');
    $add_stmt->execute(['image_path' => $image_path, 'page_text' => $page_text, 'img_text' => $image_text]);
}
