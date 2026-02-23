<?php
global $product;

if ( ! $product ) return;

// Get upsell IDs
$upsells = $product->get_upsell_ids();

if ( ! empty( $upsells ) ) : ?>
    <?php do_action('theme_section_open', [
        'classes' => 'section section--product-upsells',
    ]); ?>

        <?php do_action('theme_section_container_open'); ?>

            <h2><?php echo apply_filters( 'woocommerce_upsells_products_heading', __( 'You may also like&hellip;', 'woocommerce' ) ); ?></h2>
            <?php woocommerce_upsell_display( 4, 4 ); ?>

        <?php do_action('theme_section_container_close'); ?>
        
    <?php do_action('theme_section_close'); ?>
<?php endif; ?>
