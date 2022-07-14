<!DOCTYPE html>

<?php
$host = 'localhost';
$db   = 'energystats';
$user = 'energystats';
$pass = 'energystats';
$charset = 'utf8mb4';

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

// var_dump($_REQUEST["page"]);

if ($_REQUEST["page"] == NULL) {
  $page_num = 1;
} else {
  $page_num = $_REQUEST["page"];
}

$num_per_page = 50;

$get_stmt = $pdo->prepare("SELECT * FROM jlh_papers LIMIT ?, $num_per_page");
$get_stmt->execute([($page_num - 1) * $num_per_page]);
$rows = $get_stmt->fetchAll();
?>
<html>

<head>
  <title>Jean Laherrere Papers</title>
</head>

<body>
  <a href="?page=<?php echo $page_num - 1 ?>" <?php if ($page_num < 2) {
                                                echo 'style="pointer-events: none"';
                                              } ?>>Previous</a>
  <a href="?page=<?php echo $page_num + 1 ?>">Next</a>
  <table>
    <tr>
      <td>ID</td>
      <td>image path</td>
      <td>page text path</td>
      <td>image text path</td>
    </tr>
    <?php foreach ($rows as $db_row) {
      $id = $db_row["id"];
      $image_path = $db_row["image_path"];
      $img_tag = "<img src=\"$image_path\" height=\"128\">";
      $page_text = $db_row["page_text"];
      $img_text = $db_row["img_text"];
      $html_row = "<tr><td>$id</td><td>$img_tag</td><td>$page_text</td><td>$img_text</td></tr>";
      echo $html_row;
    }
    ?>
  </table>
</body>

</html>