<?php if (!empty($recommended_products)) : ?>
    <div class="wc-dynamic-recommendations">
        <h3>You may also like:</h3>
        <ul>
            <?php foreach ($recommended_products as $product) : ?>
                <?php
                $product_obj = new WC_Product($product);
                $product_id = $product_obj->ID;
                $product_title = $product_obj->post_title;
                $product_price = $product_obj->get_price_html();
                $product_link = get_permalink($product_id);
                $product_image = $product_obj->get_image('woocommerce_thumbnail'); // Get the product image.
                ?>
                <li>
                    <a href="<?php echo esc_url($product_link); ?>" class="product-link">
                        <?php echo $product_image; ?>
                        <span class="product-title"><?php echo esc_html($product_title); ?></span>
                    </a>
                    <span class="price"><?php echo $product_price; ?></span>
                    <form class="cart" method="post" enctype="multipart/form-data">
                        <button
                            type="submit"
                            class="button ajax_add_to_cart add_to_cart_button"
                            data-product_id="<?php echo $product; ?>"
                            data-product_sku="<?php echo $product_obj->get_sku(); ?>"
                            aria-label="<?php echo esc_attr(sprintf(__('Add “%s” to your cart', 'woocommerce'), $product_title)); ?>"
                            rel="nofollow">
                            <?php esc_html_e('Add to Cart', 'woocommerce'); ?>
                        </button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
