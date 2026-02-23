<?php
global $product;

if ( ! $product ) return;

// Get related products
$related_products = wc_get_related_products( $product->get_id(), 4 ); // 4 is the number of products to display

if ( ! empty( $related_products ) ) : ?>
    <?php do_action('theme_section_open', [
        'classes' => 'section section--product-related',
    ]); ?>

        <?php do_action('theme_section_container_open'); ?>

            <h2><?php echo apply_filters( 'woocommerce_product_related_products_heading', __( 'Related products', 'woocommerce' ) ); ?></h2>
            <?php woocommerce_output_related_products(); ?>

        <?php do_action('theme_section_container_close'); ?>
        
    <?php do_action('theme_section_close'); ?>
<?php endif; ?>
