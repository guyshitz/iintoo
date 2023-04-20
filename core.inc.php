<?php
class Core
{
    const CLASS_FILE_SUFFIX = ".class.php",
        CLASS_PATH = "class" . DIRECTORY_SEPARATOR,
        REQUEST_PATH = "request" . DIRECTORY_SEPARATOR,
        REQUEST_ERROR = 500,
        REQUEST_SUCCESS = 200,
        PRODUCT_IMG_UPLOAD_DIR = "../iintoo_data/product_theme_uploads",
        PRODUCTS_PER_PAGE = 12, // Should be at least 4 to avoid CSS width change for the class "product-card"
        PRODUCTS_NAVIGATION_PADDING_PAGES = 2;

    private Database $db;

    public function __construct()
    {
        // Sets the root folder as main in order to operate from core
        chdir(__DIR__);

        // Class autoloader
        spl_autoload_register([$this, 'autoload']);
    }

    /**
     *  Create Database Connection
     *
     * @return void
     */
    public function initDbConn(): void {
        // Requiring database config file.
        require_once("db.config.php");
        /** @var array $db_config */

        // Creates a PDO connection
        $this->db = new Database($db_config['host'], $db_config['db_name'], $db_config['username'], $db_config['password']);
    }


    /**
     *
     * Loading classes from the class dir automatically by PHP autoloader
     *
     * @param string $classname
     * @return void
     */
    public function autoload(string $classname): void
    {
        // Adding the initial class path in order to get the complete class file path
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