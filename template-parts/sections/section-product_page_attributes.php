<?php
global $product;

if ( ! $product ) return;

// Get the product attributes
$attributes = $product->get_attributes();

if ( ! empty( $attributes ) ) : ?>
    <?php do_action('theme_section_open', [
        'classes' => 'section section--product-attributes',
    ]); ?>

        <?php do_action('theme_section_container_open'); ?>

            <h2><?php echo apply_filters( 'woocommerce_product_additional_information_heading', __( 'Additional information', 'woocommerce' ) ); ?></h2>
            <?php wc_display_product_attributes( $product ); ?>

        <?php do_action('theme_section_container_close'); ?>
        
    <?php do_action('theme_section_close'); ?>
<?php endif; ?>
