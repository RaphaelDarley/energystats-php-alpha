<!DOCTYPE html>

<?php
include_once 'utils.php';
include_once 'database_init.php';
$image_table = "test_graph";
$paper_table = "test_paper";
$default_per_page = 200;
$num_per_page = default_val($_REQUEST["num_per_page"], $default_per_page);
$default_img_size = 768;
$img_size = default_val($_REQUEST["img_size"], $default_img_size);



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
<style>
    table,
    th,
    td {
        border: 1px solid black;
        border-collapse: collapse;
    }
</style>

<head>
    <title>Jean Laherrere Papers</title>
</head>

<body>
    <form method="get">
        <label for="search">Search:</label>
        <input type="text" name="search" <?php persist_value($_REQUEST["search"]) ?>>

        <label for="year">Year:</label>
        <input type="text" name="year" <?php persist_value($_REQUEST["year"]) ?>>

        <label for="num_per_page">Displayed items:</label>
        <input type="text" name="num_per_page" <?php persist_value($num_per_page) ?>>

        <label for="img_size">Image size:</label>
        <input type="text" name="img_size" <?php persist_value($img_size) ?>>

        <label for="show_text">Show text:</label>
        <input type="checkbox" name="show_text" <?php persist_checked($_REQUEST["show_text"]) ?>>
        <button type="submit">submit</button>
    </form>
    <form method="get"><button type="submit">clear</button></form>

    <?php
    $result_num = count($rows);
    echo "$result_num results found";
    ?>

    <table>
        <tr>
            <th>ID</th>
            <!-- <th>pdf page</th> -->
            <th>year</th>
            <th>image</th>
            <?php $disp_text = $_REQUEST["show_text"];
            if ($disp_text) {
                echo "<th>page text</th>";
            } ?>
        </tr>
        <?php foreach ($rows as $db_row) {
            // var_dump($db_row);
            $id = $db_row["id"];

            $paper_link = $db_row["paper_url"];
            $page_num = $db_row["page_num"];
            // $page_tag = "<a href='$paper_link#page=$page_num' target=”_blank”>paper link</a>";

            $image_path = $db_row["image_path"];
            $img_tag = "<img src='$image_path' width='$img_size'><br><i>$paper_link</i>";

            $image_link_tag = "<a href='$paper_link#page=$page_num' target=”_blank”>$img_tag</a>";

            $paper_year = $db_row["date"];

            if ($disp_text) {
                $page_text = $db_row["page_text"];

                foreach ($search_arr as $term) {
                    $page_text = str_replace($term, "<mark>$term</mark>", $page_text);
                }

                $html_row = "<tr><td>$id</td><td>$paper_year</td><td>$image_link_tag</td><td>$page_text</td></tr>";
            } else {
                $html_row = "<tr><td>$id</td><td>$paper_year</td><td>$image_link_tag</td></tr>";
            }

            echo $html_row;
        }
        ?>
    </table>
</body>

</html>