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
    $page_sections_database = new PageSectionsDatabase($database_utility);
    $retail_user_database = new RetailUserDatabase($database_utility);

    $conn->connect();
    $results = null;
    switch ($query) {
        case 'categories':
            $results = $retail_items_database->get_categories();
            break;

        case 'subcategories':
            $results = $retail_items_database->get_subcategories();
            break;

        case 'items-category':
            $category = urldecode($_GET["filter"]);
            $results = $retail_items_database->get_items_from_category($category);
            break;

        case 'top-products':
            $limit = urldecode($_GET['filter']);
            $results = $retail_items_database->get_top_products($limit);
            break;

        case 'products-from-category':
            $category = urldecode($_GET['filter']);
            $results = $retail_items_database->get_products_from_category($category);
            break;

        case 'products':
            $results = $retail_items_database->get_products();
            break;
            
        case 'home-slideshow':
            $results = $image_locations_database->get_home_slideshow_images();
            break;

        case 'home-signup':
            $results = $image_locations_database->get_home_signup_image();
            break;

        case 'section-data':
            $section_name = urldecode($_GET['filter']);
            $results = $page_sections_database->get_section_data($section_name);
            break;

        case "section-image":
            $section_name = urldecode($_GET['filter']);
            $results = $page_sections_database->get_section_image($section_name);
            break;

        case "featured":
            $limit = urldecode($_GET['filter']);
            $results = $retail_items_database->get_featured($limit);
            break;

        case "product-view":
            $product_name = urldecode($_GET['filter']);
            $results = $retail_items_database->get_product_view($product_name);
            break;

        case "product-view-images":
            $retail_item_id = urldecode($_GET['filter']);
            $results = $retail_items_database->get_product_view_images($retail_item_id);
            break;

        case "product-view-details":
            $product_name = urldecode($_GET['filter']);
            $results = $retail_items_database->get_product_from_name($product_name);
            break;

        case 'login':
            $email = $_POST['email'];
            $password = $_POST['password'];
            login($retail_user_database, $email, $password);
            break;
    }
    echo json_encode($results);
}

function login($retail_user_database, $email, $password) {
    $password_hash = $retail_user_database->get_password($email, $password);
    if ($password_hash == null || !password_verify($password, $password_hash)) { 
        return 'Username or password is incorrect!';
    } else {
        return 'true';
    }
}
?>