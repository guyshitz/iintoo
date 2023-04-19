<?php
if(isset($_GET['id']) && is_numeric($_GET['id'])) {
    require_once ("../../core.inc.php");
    global $core;
    $core = new Core();

    $products_factory = new ProductDbFactory();
    if($product = $products_factory->selectByID($_GET['id'], false)) {
        $file_path = Core::PRODUCT_IMG_UPLOAD_DIR . DIRECTORY_SEPARATOR . $product->getThemeImgFile();

        //image headers
        header("Content-Type: " . $product->getThemeImgMimeType());
        header('Content-Length: ' . filesize($file_path));

        readfile($file_path);
    }
}