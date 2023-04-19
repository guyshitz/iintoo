<?php

class View
{
    /**
     * @param Product $product
     * @return void
     */
    public static function printProductItem(Product $product): void {
        //avoiding xss effect
        $esc_title = htmlspecialchars($product->getTitle(), ENT_QUOTES);
        $esc_description = htmlspecialchars($product->getDescription(), ENT_QUOTES);
        $theme_img = !empty($product->getThemeImgFile()) ? $product->getThemeImgURL() : "assets/images/product-placeholder.jpg";

        echo '
        <li class="product-card">
            <div class="product-card-body' . ($product->isOnSale() ? ' on-sale-badge' : '') . '">
                <img src="' . $theme_img . '" alt="' . $esc_title . '">
                <h3>' . $esc_title . '</h3>
                <div class="product-price">
                    <span class="product-price-symbol">$</span>
                    <span class="product-price-value">' . $product->getPrice() . '</span>';

        if($sale_price = $product->getSalePrice())
            echo '
                    <span class="product-sale-price">$' . $sale_price . '</span>';
        echo '
                </div>
                <p>' . $esc_description . '</p>';

        //prints features if available
        if($features = $product->getFeatures()) {
            echo '
                <dl>';

            foreach($features as $name => $val)
                echo '
                    <dt>' . htmlspecialchars($name, ENT_QUOTES) . '</dt>
                    <dd>' . htmlspecialchars($val, ENT_QUOTES) . '</dd>';

            echo '
                </dl>';
        }

        echo '
            </div>
        </li>';
    }
}