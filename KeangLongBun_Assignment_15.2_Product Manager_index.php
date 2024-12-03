<?php
require('../model/database.php');
require('../model/category.php');
require('../model/category_db.php');
require('../model/product.php');
require('../model/product_db.php');
require('../model/fields.php');
require('../model/validate.php');

// Initialize validation
$validate = new Validate();
$fields = $validate->getFields();

// Add fields for validation
$fields->addField('code');
$fields->addField('name');
$fields->addField('price');

$action = filter_input(INPUT_POST, 'action');
if ($action === NULL) {
    $action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
    if ($action === NULL) {
        $action = 'list_products';
    }
}

switch ($action) {
    case 'list_products':
        $category_id = filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT);
        if ($category_id === NULL || $category_id === FALSE) {
            $category_id = 1; // Default category ID
        }

        // Fetch data
        $current_category = CategoryDB::getCategory($category_id);
        $categories = CategoryDB::getCategories();
        $products = ProductDB::getProductsByCategory($category_id);

        // Load product list view
        include('product_list.php');
        break;

    case 'show_add_form':
        // Fetch categories for the dropdown
        $categories = CategoryDB::getCategories();
        include('product_add.php');
        break;

    case 'add_product':
        $category_id = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);
        $code = filter_input(INPUT_POST, 'code', FILTER_SANITIZE_STRING);
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);

        // Validate fields
        $validate->text('code', $code, 1, 10); // Code should be 1-10 characters
        $validate->text('name', $name); // Name is required
        $validate->number('price', $price); // Price must be a valid number

        // If validation fails, reload the form with error messages
        if ($fields->hasErrors()) {
            $categories = CategoryDB::getCategories();
            include('product_add.php');
        } else {
            // Create a new product and add to the database
            $current_category = CategoryDB::getCategory($category_id);
            $product = new Product($current_category, $code, $name, $price);
            ProductDB::addProduct($product);
            header("Location: .?category_id=$category_id"); // Redirect to product list
        }
        break;

    case 'delete_product':
        $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
        $category_id = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);

        // Delete the product and redirect
        ProductDB::deleteProduct($product_id);
        header("Location: .?category_id=$category_id");
        break;

    default:
        // Default case if an unknown action is encountered
        $category_id = 1; // Default category ID
        $current_category = CategoryDB::getCategory($category_id);
        $categories = CategoryDB::getCategories();
        $products = ProductDB::getProductsByCategory($category_id);

        include('product_list.php');
        break;
}
