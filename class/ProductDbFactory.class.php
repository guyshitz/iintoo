<?php

class ProductDbFactory
{
    private Database $db;

    function __construct()
    {
        global $core;
        $core->initDbConn();
        $this->db = $core->getDbConn();
    }

    /**
     * @param array $row
     * @param bool $init_features
     * @return Product instance after properties initialization
     */
    public function createProductByRowData(array $row, bool $init_features = true): Product {
        $row['creation_date'] = DateTime::createFromFormat("Y-m-d H:i:s", $row['creation_date']);
        $values = array_values($row);

        if($init_features) {
            $features = [];
            $features_data = $this->db->exec("SELECT `feature_name`, `feature_value` FROM `io_product_features` WHERE `product_id` = ?", [$row['ID']]);

            foreach($features_data as $f)
                $features[$f['feature_name']] = $f['feature_value'];

            $values[] = $features;
        }

        return new Product(...$values);
    }

    /**
     * @return array of Product object
     */
    public function selectAll(bool $include_features = true): array {
        $results = [];
        //query columns specification is important for the unpacking to come next
        $data = $this->db->exec("SELECT `ID`, `product_title`, `product_description`, `product_price`, `sale_price`, `on_sale`, `theme_img_file`, `theme_img_ext`, `theme_img_mimetype`, `creation_date` FROM `io_products` ORDER BY `creation_date` DESC");

        foreach($data as $row)
            $results[] = $this->createProductByRowData($row, $include_features);

        return $results;
    }

    /**
     * Returns a new instance of Product in case the record has been fond. Otherwise, returns false.
     * @param int $product_id
     * @param bool $include_features
     * @return Product|false
     */
    public function selectByID(int $product_id, bool $include_features = true): Product|false {
        $data = $this->db->exec("SELECT `ID`, `product_title`, `product_description`, `product_price`, `sale_price`, `on_sale`, `theme_img_file`, `theme_img_ext`, `theme_img_mimetype`, `creation_date` FROM `io_products` WHERE `ID` = ?", [$product_id]);
        foreach($data as $row)
            return $this->createProductByRowData($row, $include_features);

        return false;
    }

    /**
     * @param Product $product
     * @return bool for query status. True for success and False for failure.
     */
    public function insert(Product $product): bool {
        $db_conn = $this->db->getConn();

        try {
            $db_conn->begin_transaction();

            //inserting product
            $this->db->exec("INSERT INTO `io_products`(`product_title`, `product_description`, `product_price`, `sale_price`, `on_sale`, `theme_img_file`, `theme_img_ext`, `theme_img_mimetype`, `creation_date`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())", [
                $product->getTitle(),
                $product->getDescription(),
                $product->getPrice(),
                $product->getSalePrice(),
                $product->isOnSale(),
                $product->getThemeImgFile(),
                $product->getThemeImgExt(),
                $product->getThemeImgMimeType()
            ]);

            $last_id = mysqli_insert_id($db_conn);
            $feature_params = [];

            //inserting product features
            if($features = $product->getFeatures()) {
                foreach($features as $name => $val) {
                    $feature_params[] = $last_id;
                    $feature_params[] = $name;
                    $feature_params[] = $val;
                }
            }

            $features_count = count($feature_params) / 3;
            if($features_count)
                $this->db->exec("INSERT INTO `io_product_features`(`product_id`, `feature_name`, `feature_value`) VALUES " . rtrim(str_repeat("(?, ?, ?),", $features_count), ","), $feature_params);

            $db_conn->commit();
        } catch (\Throwable $e) {
            $db_conn->rollback();
            //die($e->getMessage());
            return false;
        }

        return true;
    }

    public function __destruct()
    {
        //finishes the work with mysql connection
        $this->db->close();
    }
}