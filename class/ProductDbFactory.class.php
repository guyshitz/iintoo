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
     *
     * Create Product instance by row data array
     *
     * @param array $row
     * @param bool $init_features
     * @return Product instance after properties initialization
     */
    public function createProductByRowData(array $row, bool $init_features = true): Product {
        // Converting date format to PHP DateTime object
        $row['creation_date'] = DateTime::createFromFormat("Y-m-d H:i:s", $row['creation_date']);

        $values = array_values($row);

        if($init_features) {
            // Inits product feature from another table using the primary key
            $features = [];
            $features_data = $this->db->query("SELECT `feature_name`, `feature_value` FROM `io_product_features` WHERE `product_id` = ?", [$row['ID']]);

            foreach($features_data as $f)
                $features[$f['feature_name']] = $f['feature_value'];

            $values[] = $features;
        }

        return new Product(...$values);
    }

    /**
     *
     * Select all Product table records
     *
     * @return array of Product object
     */
    public function selectAll(int $offset, int $limit = Core::PRODUCTS_PER_PAGE, bool $include_features = true): array {
        $results = [];
        // Query columns specification is important for the unpacking to come next
        $data = $this->db->query("SELECT `ID`, `product_title`, `product_description`, `product_price`, `sale_price`, `on_sale`, `theme_img_file`, `theme_img_ext`, `theme_img_mimetype`, `creation_date` FROM `io_products` ORDER BY `creation_date` DESC LIMIT $offset, $limit");

        if(is_array($data) && $data)
            foreach($data as $row)
                $results[] = $this->createProductByRowData($row, $include_features);

        return $results;
    }

    /**
     *
     * Returns a new instance of Product in case the record has been fond. Otherwise, returns false.
     *
     * @param int $product_id
     * @param bool $include_features
     * @return Product|false
     */
    public function selectByID(int $product_id, bool $include_features = true) {
        $data = $this->db->query("SELECT `ID`, `product_title`, `product_description`, `product_price`, `sale_price`, `on_sale`, `theme_img_file`, `theme_img_ext`, `theme_img_mimetype`, `creation_date` FROM `io_products` WHERE `ID` = ?", [$product_id]);
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
            // Begins mysql transaction to keep the multi table inserting operation safe
            $db_conn->beginTransaction();

            // Inserting Product
            $this->db->query("INSERT INTO `io_products`(`product_title`, `product_description`, `product_price`, `sale_price`, `on_sale`, `theme_img_file`, `theme_img_ext`, `theme_img_mimetype`, `creation_date`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())", [
                $product->getTitle(),
                $product->getDescription(),
                $product->getPrice(),
                $product->getSalePrice(),
                $product->isOnSale(),
                $product->getThemeImgFile(),
                $product->getThemeImgExt(),
                $product->getThemeImgMimeType()
            ]);

            $last_id = $db_conn->lastInsertId();
            $feature_params = [];

            // Inserting Product Features
            if($features = $product->getFeatures()) {
                foreach($features as $name => $val) {
                    $feature_params[] = $last_id;
                    $feature_params[] = $name;
                    $feature_params[] = $val;
                }
            }

            $features_count = count($feature_params) / 3;
            if($features_count)
                $this->db->query("INSERT INTO `io_product_features`(`product_id`, `feature_name`, `feature_value`) VALUES " . rtrim(str_repeat("(?, ?, ?),", $features_count), ","), $feature_params);

            $db_conn->commit();
        } catch (\Throwable $e) {
            // Rolling back the transaction on failure
            //die($e->getMessage());
            $db_conn->rollback();
            return false;
        }

        return true;
    }

    public function count(): int
    {
        return (int)$this->db->read("SELECT COUNT(*) FROM `io_products`");
    }

    public function __destruct()
    {
        // Finishes the work with mysql connection
        $this->db->closeConnection();
    }
}