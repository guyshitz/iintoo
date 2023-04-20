<?php

class Product
{
    // Initial properties the same as database table plus features array
    private int $id;
    private string $title, $description, $theme_img_file, $theme_img_ext, $theme_img_mime_type;
    private float $price;
    private ?float $sale_price;
    private bool $on_sale;
    private array $features;
    private DateTime $creation_date;

    function __construct(?int $id, string $title, string $description, float $price, ?float $sale_price = null, bool $on_sale = null, ?string $theme_img_file = null, ?string $theme_img_ext = null, ?string $theme_img_mime_type = null, ?DateTime $creation_date = null, array $features = []) {
        // Optional fields for insert
        if(isset($id))
            $this->id = $id;
        if(isset($creation_date))
            $this->creation_date = $creation_date;

        // Required fields
        $this->title = $title;
        $this->description = $description;
        $this->price = $price;

        // Optional fields
        if(isset($sale_price))
            $this->sale_price = $sale_price;
        $this->on_sale = $on_sale;

        if(isset($theme_img_file)) {
            $this->theme_img_file = $theme_img_file;

            if(isset($theme_img_ext))
                $this->theme_img_ext = $theme_img_ext;

            if(isset($theme_img_mime_type))
                $this->theme_img_mime_type = $theme_img_mime_type;
        }

        if(isset($features))
            $this->features = $features;
    }

    /**
     * @return int
     */
    public function getID(): int {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): string {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getDescription(): string {
        return $this->description;
    }

    /**
     * @return float
     */
    public function getPrice(): float {
        return $this->price;
    }

    /**
     * @return float|null
     */
    public function getSalePrice(): ?float {
        return $this->sale_price;
    }

    /**
     * @return bool
     */
    public function isOnSale(): bool {
        return $this->on_sale;
    }

    /**
     * @return string|null
     */
    public function getThemeImgFile(): ?string {
        return $this->theme_img_file ?? null;
    }

    /**
     * @return string|null
     */
    public function getThemeImgExt(): ?string {
        return $this->theme_img_ext ?? null;
    }

    /**
     * @return string|null
     */
    public function getThemeImgMimeType(): ?string {
        return $this->theme_img_mime_type ?? null;
    }

    /**
     * @return array
     */
    public function getFeatures(): array {
        return $this->features ?? [];
    }

    /**
     * @return DateTime
     */
    public function getCreationDate(): DateTime {
        return $this->creation_date;
    }

    /**
     * @return string
     */
    public function getThemeImgURL(): string {
        return Core::REQUEST_PATH . "product" . DIRECTORY_SEPARATOR . "theme_img.php?id=" . $this->id;
    }
}