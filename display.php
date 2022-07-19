<!DOCTYPE html>

<?php
include_once 'utils.php';

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


$page_num = default_val($_REQUEST["page"], 1);

$num_per_page = 50;
// var_dump($_REQUEST);
// var_dump($_REQUEST["search"]);


if ($_REQUEST["search"] == NULL) {
  $get_stmt = $pdo->prepare("SELECT * FROM jlh_papers LIMIT ?, $num_per_page");
  $get_stmt->execute([($page_num - 1) * $num_per_page]);
} else {
  $start = ($page_num - 1) * $num_per_page;
  $search_term = $_REQUEST["search"];
  $get_stmt = $pdo->prepare("SELECT * FROM jlh_papers WHERE (page_text LIKE '%$search_term%') LIMIT ?, $num_per_page");
  // $get_stmt->debugDumpParams();

  $get_stmt->execute([$start]);
}

$rows = $get_stmt->fetchAll();
?>
<html>

<head>
  <title>Jean Laherrere Papers</title>
</head>

<body>
  <a href="?page=<?php echo $page_num - 1 ?><?php if ($_REQUEST["search"] != NULL) {
                                              echo "&search=" . $_REQUEST["search"];
                                            } ?>" <?php if ($page_num < 2) {
                                                    echo 'style="pointer-events: none"';
                                                  } ?>>Previous</a>
  <a href="?page=<?php echo $page_num + 1 ?><?php if ($_REQUEST["search"] != NULL) {
                                              echo "&search=" . $_REQUEST["search"];
                                            } ?>">Next</a>
  <form method="get">
    <input type="text" name="search">
    <button type="submit">submit</button>
  </form>
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
      $img_tag = "<img src=\"$image_path\" width=\"512\">";
      $page_text = $db_row["page_text"];
      $img_text = $db_row["img_text"];
      $html_row = "<tr><td>$id</td><td>$img_tag</td><td>$page_text</td><td>$img_text</td></tr>";
      echo $html_row;
    }
    ?>
  </table>
</body>

</html>