<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    
if (isset($_GET["query"])) {
    run_query();
}

function collect_data() {
    require_once 'dbh.php';
    $filter = $_GET["filter"];
    $results = $conn -> query("SELECT * FROM retail_items WHERE ".$filter);
    echo json_encode($results -> fetch_all(MYSQLI_ASSOC));
}
function run_query() {    
    require_once 'dbh.php';
    require_once 'database_utility.php';
    require_once 'database_functions.php';

    $query = $_GET["query"];

    $conn = new DatabaseConnection();
    $database_utility = new DatabaseUtility($conn);
    $retail_items_database = new RetailItemsDatabase($database_utility);
    $image_locations_database = new ImageLocationsDatabase($database_utility);

    $conn->connect();
    switch ($query) {
        case "categories":
            $results = $retail_items_database->get_categories();
            echo json_encode($results);
            break;

        case "items-category":
            $category = urldecode($_GET["filter"]);
            $results = $retail_items_database->get_items_from_category($category);
            echo json_encode($results);
            break;

        case "top-products":
            $limit = urldecode($_GET['filter']);
            $results = $retail_items_database->get_top_products($limit);
            echo json_encode($results);
            break;

        case "home-slideshow":
            $results = $image_locations_database->get_home_slideshow_images();
            echo json_encode($results);
            break;

        case "home-signup":
            $results = $image_locations_database->get_home_signup_image();
            echo json_encode($results);
            break;

        case "subcategories":
            $results = $conn->query("SELECT GROUP_CONCAT(DISTINCT sub_category SEPARATOR ', ') as sub_categories FROM retail_items GROUP BY category");
            echo handle_data("ALL", $results, "sub_categories");
            break;

        case "featured":
            $results = $conn -> query("SELECT i.item_name AS item_name, i.stock_code AS stock_code, ri.image_file_name FROM retail_items AS ri INNER JOIN items AS i ON ri.item_id = i.id WHERE featured = 'Yes'");
            echo handle_data("ASSOC", $results, null);
            break;

        case "stockcode":
            $stock_code = $_GET["filter"];
            $stmt = $conn->prepare('SELECT *, i.item_name AS item_name, i.list_price AS price FROM retail_items AS ri INNER JOIN items AS i ON ri.item_id = i.id WHERE i.stock_code = ?');
            $stmt->bind_param("s", $stock_code);
            $stmt->execute();
            $results = $stmt->get_result();
            echo handle_data("ASSOC", $results, null);
            break;

        case "image_locations":
            $stock_code = $_GET["filter"];
            $stmt = $conn->prepare("SELECT image_location FROM image_locations AS il INNER JOIN retail_items AS ri ON il.retail_item_id = ri.id INNER JOIN items AS i ON ri.item_id = i.id WHERE i.stock_code = ?");
            $stmt->bind_param("s", $stock_code);
            $stmt->execute();
            $results = $stmt->get_result();
            echo handle_data("ASSOC", $results, null);
            break;

        case "item-category":
            $item_category = urldecode($_GET["filter"]);
            $stmt = $conn -> prepare("SELECT ri.category AS category FROM retail_items AS ri INNER JOIN items AS i ON ri.item_id = i.id WHERE i.stock_code = ?");
            $stmt -> bind_param("s", $item_category);
            $stmt -> execute();
            $results = $stmt->get_result();
            echo handle_data("ASSOC", $results, "category");
            break;


        case "items-sub-category":
            $item_sub_category = urldecode($_GET["filter"]);
            $stmt = $conn -> prepare("SELECT ri.*, i.item_name AS item_name, i.list_price AS price, i.stock_code AS stock_code FROM retail_items AS ri INNER JOIN items AS i ON ri.item_id = i.id WHERE sub_category = ?");
            $stmt -> bind_param("s", $item_sub_category);
            $stmt -> execute();
            $results = $stmt->get_result();
            echo handle_data("ASSOC", $results, null);
            break;

        case "allergens":
            $stock_code = urldecode($_GET["filter"]);
            $stmt = $conn->prepare("SELECT celery AS 'Celery', cereals_containing_gluten AS 'Cereals Containing Gluten', crustaceans AS 'Curstaceans', eggs AS 'Eggs', fish AS 'Fish', lupin AS 'Lupin', milk AS 'Milk', molluscs AS 'Moulluscs', mustard AS 'Mustard', peanuts AS 'Peanuts', sesame AS 'Sesame', soybeans AS 'Soybeans', sulphur_dioxide_and_sulphites AS 'Sulphur Dioxide and Sulphites', tree_nuts AS 'Tree Nuts' FROM allergen_information
            AS ai INNER JOIN retail_items as ri ON ai.retail_item_id = ri.id INNER JOIN items as i ON ri.item_id = i.id WHERE i.stock_code = ?");
            $stmt->bind_param("s", $stock_code);
            $stmt->execute();
            $results = $stmt->get_result();
            echo handle_data("ASSOC", $results, null);
            break;

        case "popular-5":
            $results = $conn->query("SELECT i.item_name AS item_name, i.stock_code AS stock_code, i.list_price AS price, ri.image_file_name AS image_location FROM retail_items AS ri INNER JOIN items AS i ON ri.item_id = i.id ORDER BY i.total_sold DESC LIMIT 0, 5");
            echo handle_data("ASSOC", $results, null);
            break;

        case "current_offers":
            $results = $conn->query("SELECT i.item_name AS item_name, i.stock_code AS stock_code, i.list_price AS price, ri.image_file_name AS image_location, ri.offer_end AS offer_end FROM retail_items AS ri INNER JOIN items AS i ON ri.item_id = i.id WHERE ri.offer_id IS NOT NULL AND ((curdate()) >= ri.offer_start AND curdate() <= ri.offer_end) LIMIT 0,5");
            echo handle_data("ASSOC", $results, null);
            break;

        case "upcoming_offers":
            $results = $conn->query("SELECT i.item_name AS item_name, i.stock_code AS stock_code, i.list_price AS price, ri.image_file_name AS image_location, ri.offer_start AS offer_start FROM retail_items AS ri INNER JOIN items AS i ON ri.item_id = i.id WHERE ri.offer_id IS NOT NULL AND (curdate()) <= ri.offer_start LIMIT 0,5");
            echo handle_data("ASSOC", $results, null);
            break;

        default:
            echo "No Match!";
        break;
    }
}
function handle_data($data_type, $results, $offset) {
    if ($data_type == "ASSOC") {
        if ($offset != null && $results->num_rows > 0) {
            $data = $results -> fetch_array(MYSQLI_ASSOC)[$offset];
            if ($data == null) {
                return "0";
            }
            return $data;
        }
        return json_encode($results -> fetch_all(MYSQLI_ASSOC));
    }
    elseif ($data_type == "ALL") {
        return json_encode($results -> fetch_all());
    }
    elseif ($data_type == "NUM") {
        return $results -> fetch_array(MYSQLI_NUM);
    }
    elseif ($data_type == "ROW_COUNT") {
        return $results -> num_rows;
    }
}
?>