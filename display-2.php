<!DOCTYPE html>

<?php
include_once 'utils.php';
include_once 'database_init.php';
$image_table = "test_graph";
$paper_table = "test_paper";
$num_per_page = default_val($_REQUEST["num_per_page"], 50);


if ($_REQUEST["search"] != NULL) {
    $request_search = $_REQUEST["search"];
    $search_arr = explode(" & ", $request_search);
    print_r($search_arr);

    $where_stmt = join(" AND ", array_map("search_term_to_like", $search_arr));

    if ($_REQUEST["year"] != NULL) {
        $year = $_REQUEST["year"];
        $where_stmt = $where_stmt . "AND $paper_table.date = $year";
    }

    var_dump($where_stmt);
    // $where_stmt = "page_text LIKE '%glacier%'";

    $get_stmt = $pdo->prepare("SELECT * FROM $image_table 
    INNER JOIN $paper_table ON $image_table.paper_id = $paper_table.id 
    WHERE ($where_stmt)
    LIMIT ?
    ");
    // $get_stmt->execute(["where" => $where_stmt, "limit" => $num_per_page]);
    $get_stmt->execute([$num_per_page]);
} else {
    $where_stmt = "";
    if ($_REQUEST["year"] != NULL) {
        $year = $_REQUEST["year"];
        $where_stmt = $where_stmt . "WHERE ($paper_table.date = $year)";
    }


    $get_stmt = $pdo->prepare("SELECT * FROM $image_table 
    INNER JOIN $paper_table ON $image_table.paper_id = $paper_table.id 
    $where_stmt LIMIT ?
    ");
    $get_stmt->execute([$num_per_page]);
}

// echo $get_stmt->debugDumpParams();
$rows = $get_stmt->fetchAll();
?>

<html>

<head>
    <title>Jean Laherrere Papers</title>
</head>

<body>
    <form method="get">
        <label for="search">Search:</label>
        <input type="text" name="search" <?php if ($_REQUEST["search"] != NULL) {
                                                echo 'value="' . $_REQUEST["search"] . '"';
                                            } ?>>
        <label for="year">Year:</label>
        <input type="text" name="year" <?php if ($_REQUEST["year"] != NULL) {
                                            echo 'value="' . $_REQUEST["year"] . '"';
                                        } ?>>
        <label for="num_per_page">Items per page:</label>
        <input type="text" name="num_per_page" <?php if ($_REQUEST["num_per_page"] != NULL) {
                                                    echo 'value="' . $_REQUEST["num_per_page"] . '"';
                                                } ?>>
        <button type="submit">submit</button>
    </form>
    <form method="get"><button type="submit">clear</button></form>

    <table>
        <tr>
            <td>ID</td>
            <td>image</td>
            <td>pdf page</td>
            <td>paper year</td>
        </tr>
        <?php foreach ($rows as $db_row) {
            // var_dump($db_row);
            $id = $db_row["id"];
            $image_path = $db_row["image_path"];
            $img_tag = "<img src='$image_path' width='768'>";
            $paper_link = $db_row["paper_url"];
            $page_num = $db_row["page_num"];
            $page_tag = "<a href='$paper_link#page=$page_num' target=”_blank”>paper link</a>";
            $paper_year = $db_row["date"];
            $html_row = "<tr><td>$id</td><td>$img_tag</td><td>$page_tag</td><td>$paper_year</td></tr>";
            echo $html_row;
        }
        ?>
    </table>
</body>

</html>