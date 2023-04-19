<?php
//define('__ROOT__', dirname(dirname(__FILE__)));

class Core
{
    const CLASS_FILE_SUFFIX = ".class.php",
        CLASS_PATH = "class" . DIRECTORY_SEPARATOR,
        REQUEST_PATH = "request" . DIRECTORY_SEPARATOR,
        REQUEST_ERROR = 500,
        REQUEST_SUCCESS = 200,
        PRODUCT_IMG_UPLOAD_DIR = "../iintoo_data/product_theme_uploads";

    private Database $db;

    public function __construct()
    {
        chdir(__DIR__);
        //class autoloader
        spl_autoload_register([$this, 'autoload']);
    }

    public function initDbConn(): void {
        require_once("db.config.php");
        /** @var array $config */
        $this->db = new Database($config['db']['host'], $config['db']['username'], $config['db']['password'], $config['db']['db_name']);
    }

    public function fetchProducts(): bool|mysqli_result {
        if(isset($this->db))
            return $this->db->exec("SELECT * FROM io_products");
        else
            die("No database connection.");
    }

    public function autoload($classname): void
    {
        //adding the initial class path in order to get the complete class file path
        $filepath = self::CLASS_PATH . $classname . self::CLASS_FILE_SUFFIX;

        if (file_exists($filepath))
            require_once $filepath;
        else
            die("Class not found: " . $classname . " at $filepath");
    }

    public function getDbConn(): Database {
        return $this->db;
    }
}