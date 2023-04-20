<?php
// Including the project core
require_once("core.inc.php");
global $core;
$core = new Core();

// Preparing to view products from database

$products_factory = new ProductDbFactory();

// Products Pagination
$total_products_amount = $products_factory->count();
$total_products_pages = ceil($total_products_amount / Core::PRODUCTS_PER_PAGE);
$products_page = 1;

// Determines the current page number
if(isset($_GET['pg']) && filter_input(INPUT_GET, 'pg', FILTER_VALIDATE_INT) && $_GET['pg'] > 0)
    $products_page = $_GET['pg'];

if($products_page > $total_products_pages)
    $products_page = 1;

// Getting products according to page number and limitations

$products_page_offset = ($products_page - 1) * Core::PRODUCTS_PER_PAGE;
$products = $products_factory->selectAll($products_page_offset);

// Determines previous, next, start, ending pages for navigation

$start_page = ($products_page - Core::PRODUCTS_NAVIGATION_PADDING_PAGES);
if($start_page < 1)
    $start_page = 1;

$end_page = ($products_page + Core::PRODUCTS_NAVIGATION_PADDING_PAGES);
if($end_page > $total_products_pages)
    $end_page = $total_products_pages;

$next_page = $products_page + 1;
$next_flag = ($next_page > $total_products_pages);

$prev_page = $products_page - 1;
$prev_flag = ($prev_page < 1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="guyshitz">
    <title>MyStore - Online Store Management</title>
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<!-- SVG Icons -->
<svg display="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="768" zoomAndPan="magnify" viewBox="0 0 30 30.000001" height="40" preserveAspectRatio="xMidYMid meet"><defs><clipPath><path d="M 2.328125 4.222656 L 27.734375 4.222656 L 27.734375 24.542969 L 2.328125 24.542969 Z M 2.328125 4.222656 " clip-rule="nonzero"/></clipPath></defs><g id="icon-check" clip-path="url(#id1)"><path fill="rgb(13.729858%, 12.159729%, 12.548828%)" d="M 27.5 7.53125 L 24.464844 4.542969 C 24.15625 4.238281 23.65625 4.238281 23.347656 4.542969 L 11.035156 16.667969 L 6.824219 12.523438 C 6.527344 12.230469 6 12.230469 5.703125 12.523438 L 2.640625 15.539062 C 2.332031 15.84375 2.332031 16.335938 2.640625 16.640625 L 10.445312 24.324219 C 10.59375 24.472656 10.796875 24.554688 11.007812 24.554688 C 11.214844 24.554688 11.417969 24.472656 11.566406 24.324219 L 27.5 8.632812 C 27.648438 8.488281 27.734375 8.289062 27.734375 8.082031 C 27.734375 7.875 27.648438 7.679688 27.5 7.53125 Z M 27.5 7.53125 " fill-opacity="1" fill-rule="nonzero"/></g></svg>
<svg display="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="768" height="800" viewBox="0 0 768 800"><defs><g id="icon-close"><path class="path1" d="M31.708 25.708c-0-0-0-0-0-0l-9.708-9.708 9.708-9.708c0-0 0-0 0-0 0.105-0.105 0.18-0.227 0.229-0.357 0.133-0.356 0.057-0.771-0.229-1.057l-4.586-4.586c-0.286-0.286-0.702-0.361-1.057-0.229-0.13 0.048-0.252 0.124-0.357 0.228 0 0-0 0-0 0l-9.708 9.708-9.708-9.708c-0-0-0-0-0-0-0.105-0.104-0.227-0.18-0.357-0.228-0.356-0.133-0.771-0.057-1.057 0.229l-4.586 4.586c-0.286 0.286-0.361 0.702-0.229 1.057 0.049 0.13 0.124 0.252 0.229 0.357 0 0 0 0 0 0l9.708 9.708-9.708 9.708c-0 0-0 0-0 0-0.104 0.105-0.18 0.227-0.229 0.357-0.133 0.355-0.057 0.771 0.229 1.057l4.586 4.586c0.286 0.286 0.702 0.361 1.057 0.229 0.13-0.049 0.252-0.124 0.357-0.229 0-0 0-0 0-0l9.708-9.708 9.708 9.708c0 0 0 0 0 0 0.105 0.105 0.227 0.18 0.357 0.229 0.356 0.133 0.771 0.057 1.057-0.229l4.586-4.586c0.286-0.286 0.362-0.702 0.229-1.057-0.049-0.13-0.124-0.252-0.229-0.357z"></path></g></defs></svg>

<!-- Toast Alert -->
<div class="toast">
    <a href="javascript: void(0);" class="toast-close">
        <svg class="icon-close icon" style="width: 16px" viewBox="0 0 32 32"><use xlink:href="#icon-close"></use></svg>
    </a>
    <div class="toast-content">
        <svg class="icon-check icon" viewBox="0 0 32 32"><use xlink:href="#icon-check"></use></svg>

        <div class="message">
            <span class="text text-1 toast-title">Success</span>
            <span class="text text-2 toast-msg"></span>
        </div>
    </div>

    <div class="progress"></div>
</div>

<!-- Header Start -->
<div class="header">
    <a href="/"><h1>GlobalStore</h1></a>
    <div class="menu-buttons">
        <a href="#" class="btn btn-light product-btn">
            Add Product
        </a>
    </div>
</div>
<!-- Header End -->

<!-- Main Container Start -->
<div class="container">
    <h2 class="heading">Featured Products</h2>
    <ul class="product-list">
        <?php
        // Looping through all products in order to print them out
        foreach($products as $product)
            View::printProductItem($product);
        ?>
    </ul>
</div>
<!-- Main Container End -->

<!-- Footer Start -->
<div class="footer">
    <div class="w-100 text-center">
        <div class="pagination">
            <?php
            // Printing the pagination buttons according to the data lengths
            if(!$prev_flag)
                echo "<a href=\"?pg=$prev_page\">&laquo;</a>";

            for($i = $start_page; $i <= $end_page; $i++)
                echo '<a href="?pg=' . $i .'"' . ($i == $products_page ? ' class="active"' : "") . '>' . $i . '</a>';

            if(!$next_flag)
                echo "<a href=\"?pg=$next_page\">&raquo;</a>";
            ?>
        </div>
    </div>
</div>
<!-- Footer End -->

<!-- Product Form Modal Start  -->
<div class="modal">
    <div class="modal-overlay modal-toggle"></div>
    <div class="modal-wrapper">
        <div class="modal-header">
            <a href="javascript: void(0);" class="modal-close modal-toggle"><svg class="icon-close icon" viewBox="0 0 32 32"><use xlink:href="#icon-close"></use></svg></a>
            <h2 class="modal-heading">Create a product</h2>
        </div>

        <div class="modal-body">
            <div class="modal-content">
                <p class="mb-2 text-muted">Please fill the form below in order to add a product.</p>
                <div class="response-error"></div>

                <form method="post" class="mt-2 product-form" enctype="multipart/form-data">
                    <div class="input-group">
                        <label for="product-title">Product Title <span class="req"></span></label>
                        <input type="text" id="product-title" name="title" class="form-input w-100 mt-1" placeholder="Please enter the product title" />
                    </div>

                    <div class="input-group">
                        <label for="product-description">Product Description <span class="req"></span></label>
                        <textarea type="text" id="product-description" name="description" class="form-input w-100"></textarea>
                    </div>

                    <div class="input-groups-grid">
                        <div class="input-group">
                            <label for="product-price">Product Price <span class="req"></span></label>
                            <input type="number" min="1" step="any" id="product-price" name="price" class="form-input w-100 mt-1" placeholder="USD Price" />
                        </div>

                        <div class="input-group">
                            <label for="sale-price">Sale Price (Old Price)</label>
                            <input type="number" min="1" step="any" id="sale-price" name="sale_price" class="form-input w-100  mt-1" placeholder="USD Price" />
                        </div>


                        <div class="clearfix"></div>
                    </div>

                    <p class="mb-2">Additional Features (<a href="javascript: void(0);" class="clone-block-btn">Add Feature...</a>)</p>

                    <div class="input-groups-grid clone-block product-feature">
                        <a href="javascript: void(0);" class="btn feature-btn btn-dark remove-btn" disabled>-</a>
                        <div class="input-group">
                            <label for="product-feature">Product Feature</label>
                            <input type="text" min="1" step="any" id="product-feature" name="feature_name[]" class="form-input w-100 mt-1 feature-name" placeholder="Enter the feature name" />
                        </div>

                        <div class="input-group">
                            <label for="feature-value">Feature Value</label>
                            <input type="text" min="1" step="any" id="feature-value" name="feature_value[]" class="form-input w-100 mt-1 feature-value" placeholder="Enter the feature value" />
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="input-group">
                        <label for="product-on-sale">
                            <input type="checkbox" id="product-on-sale" name="on_sale" />
                            On Sale
                        </label>
                    </div>

                    <div class="input-group img-field">
                        <label for="product-theme-img">
                            Product Image (<a href="javascript: void(0);" class="choose-img">Choose...</a>)
                        </label>
                        <input type="file" accept="image/jpeg,image/jpg,image/png,image/gif" class="d-none" name="theme_img">
                        <a href="javascript: void(0);" class="d-block img-input">
                            <img src="assets/images/product-placeholder.jpg" class="product-img" alt="Product Placeholder">
                        </a>
                    </div>

                    <button type="submit" class="btn btn-dark">Submit Product</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Product Form Modal End  -->

<!-- Javascript Includes Start -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="assets/js/scripts.js?v=2"></script>
<!-- Javascript Includes End -->
</body>
</html>