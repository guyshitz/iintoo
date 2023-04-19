<?php
header('Content-Type: application/json');

const MIN_TITLE_LENGTH = 3,
    MAX_TITLE_LENGTH = 100,
    MIN_DESCRIPTION_LENGTH = 3,
    MAX_DESCRIPTION_LENGTH = 500,
    MIN_FEATURE_NAME_LENGTH = 3,
    MAX_FEATURE_NAME_LENGTH = 500,
    MIN_FEATURE_VALUE_LENGTH = 3,
    MAX_FEATURE_VALUE_LENGTH = 500,
    SUCCESS_MSG = "The product has been added successfully.";

//required post params
if(isset($_POST['title']) && isset($_POST['description']) && isset($_POST['price'])) {
    require_once("../../core.inc.php");
    global $core;
    $core = new Core();

    //validation
    $errors = [];
    $title_len = strlen($_POST['title']);
    $desc_len = strlen($_POST['description']);
    $img_uploaded = isset($_FILES['theme_img']['tmp_name']) && !empty($_FILES['theme_img']['tmp_name']);

    if($title_len < MIN_TITLE_LENGTH || $title_len > MAX_TITLE_LENGTH)
        $errors[] = "The title's length has to be " . MIN_TITLE_LENGTH ." to " . MAX_TITLE_LENGTH . " chars";

    if($desc_len < MIN_DESCRIPTION_LENGTH || $desc_len > MAX_DESCRIPTION_LENGTH)
        $errors[] = "The description's length has to be " . MIN_DESCRIPTION_LENGTH ." to " . MAX_DESCRIPTION_LENGTH . " chars";

    if(!is_numeric($_POST['price']))
        $errors[] = "The product price must be numeric.";

    if(isset($_POST['sale_price']) && !empty($_POST['sale_price']) && !is_numeric($_POST['sale_price']))
        $errors[] = "The sale price must be numeric.";

    if($img_uploaded) {
        if(empty($_FILES['theme_img']) || $_FILES['theme_img']['error'] !== 0)
            $errors[] = "Image upload failed.";

        //ensuring upload image size
        $size_verification = getimagesize($_FILES['theme_img']['tmp_name']);

        //ensuring upload image file type
        $pattern = "#^(image/)[^\s\n<]+$#i";

        if(!preg_match($pattern, $size_verification['mime']))
            $errors[] = "Only image files are allowed for uploading.";
    }

    $min_feature_name_len = min(array_map('strlen', $_POST['feature_name']));
    $max_feature_name_len = max(array_map('strlen', $_POST['feature_name']));

    $min_feature_val_len = min(array_map('strlen', $_POST['feature_value']));
    $max_feature_val_len = max(array_map('strlen', $_POST['feature_value']));
    if((isset($_POST['feature_name']) && !is_array($_POST['feature_name']))
        || (isset($_POST['feature_value']) && !is_array($_POST['feature_value']))
        || count($_POST['feature_name']) != count($_POST['feature_value'])
        || ($min_feature_name_len > 0 && $min_feature_name_len < MIN_FEATURE_NAME_LENGTH)
        || ($max_feature_name_len > 0 && $max_feature_name_len > MAX_FEATURE_NAME_LENGTH)
        || ($min_feature_val_len > 0 && $min_feature_val_len < MIN_FEATURE_VALUE_LENGTH)
        || ($max_feature_val_len > 0 && $max_feature_val_len > MAX_FEATURE_VALUE_LENGTH)
    )
        $errors[] = "Each feature must have a name of " . MIN_FEATURE_NAME_LENGTH . " to " . MAX_FEATURE_NAME_LENGTH . " chars length, and matching value of " . MIN_FEATURE_VALUE_LENGTH . " to " . MAX_FEATURE_VALUE_LENGTH . " chars length.";

    if(!count($errors)) {
        //validated and ready for commit

        if($img_uploaded) {
            //ensuring upload
            do {
                $theme_img_path = Core::PRODUCT_IMG_UPLOAD_DIR . DIRECTORY_SEPARATOR . mt_rand(). ".tmp";
                $fp = @fopen($theme_img_path, 'x');
            }
            while(!$fp);

            fclose($fp);

            if(!move_uploaded_file($_FILES['theme_img']['tmp_name'], $theme_img_path)) {
                echo json_encode([
                    "code" => Core::REQUEST_ERROR,
                    "msg" => "The image could not be uploaded. The product was not created."
                ], true);
                return;
            }
        }

        $product = new Product(
            null,
            $_POST['title'],
            $_POST['description'],
            (float)$_POST['price'],
            (isset($_POST['sale_price']) ? (float)$_POST['sale_price'] : null),
            isset($_POST['on_sale']),
            $img_uploaded ? basename($theme_img_path) : null,
            isset($_FILES['theme_img']['name']) ? pathinfo(basename($_FILES['theme_img']['name']), PATHINFO_EXTENSION) : null,
            $_FILES['theme_img']['type'] ?? null,
            null,
            array_filter(array_combine($_POST['feature_name'], $_POST['feature_value']))
        );

        //inserting the product to database
        $db_factory = new ProductDbFactory();
        if($db_factory->insert($product))
            echo json_encode([
                "code" => Core::REQUEST_SUCCESS,
                "msg" => SUCCESS_MSG
            ], true);
        else {
            if($img_uploaded)
                unlink($theme_img_path);

            echo json_encode([
                "code" => Core::REQUEST_ERROR,
                "msg" => "The product could not be created."
            ], true);
        }
    }
    else //printing errors
        echo json_encode([
        "code" => Core::REQUEST_ERROR,
        "msg" => implode('<br>', $errors)
    ], true);
}