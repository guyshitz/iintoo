<?php
if(isset($_GET['id']) && is_numeric($_GET['id']) && $_GET['id'] > 0) {
    require_once ("../../core.inc.php");
    global $core;
    $core = new Core();

    $products_factory = new ProductDbFactory();
    if($product = $products_factory->selectByID($_GET['id'], false)) {
        // An instance of Product was created by given id data
        $file_path = Core::PRODUCT_IMG_UPLOAD_DIR . DIRECTORY_SEPARATOR . $product->getThemeImgFile();

        // Image Headers
        header("Content-Type: " . $product->getThemeImgMimeType());
        header('Content-Length: ' . filesize($file_path));

        // Reading & presenting image contents
        readfile($file_path);
    }
}