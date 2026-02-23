<?php
global $product;

if ( ! $product ) return;

// Get the product content
$content = apply_filters( 'the_content', get_the_content() );

if ( ! empty( trim( strip_tags( $content ) ) ) ) : ?>
    <?php do_action('theme_section_open', [
        'classes' => 'section section--product-description',
    ]); ?>

        <?php do_action('theme_section_container_open'); ?>

            <h2><?php echo apply_filters( 'woocommerce_product_description_heading', __( 'Description', 'woocommerce' ) ); ?></h2>
            <?php echo $content; ?>

        <?php do_action('theme_section_container_close'); ?>
        
    <?php do_action('theme_section_close'); ?>
<?php endif; ?>
