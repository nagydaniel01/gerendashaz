<?php get_header(); ?>

<?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>

    <?php 
        $current_user_id = get_current_user_id();
        $posts_per_page  = get_option('posts_per_page');

        $post_id         = get_the_ID();
        $post_type       = get_post_type();
        $categories_list = get_the_category_list(', ');
        $post_lead       = get_field('post_lead');

        $published       = get_the_date();
        $modified        = get_the_modified_date();
        
        $estimated_reading_time = get_estimated_reading_time( get_the_content() );

        // Related posts
        $related_posts = new WP_Query([
            'post_type'      => 'apartment',
            'post_status'    => 'publish',
            'posts_per_page' => $posts_per_page,
            'category__in'   => wp_get_post_categories(get_the_ID()),
            'post__not_in'   => [get_the_ID()],
        ]);

        // Define taxonomy dynamically based on post type
        switch ( $post_type ) {
            case 'post':
                $taxonomy = 'category';
                break;

            default:
                $taxonomy = '';
                break;
        }
    ?>

    <main class="page page--single page--single-<?php echo esc_attr( $post_type ); ?>">
        <?php if (!has_acf_section()) : ?>
            
            <section class="section section--single section--single-<?php echo esc_attr( $post_type ); ?>">
                <div class="container">
                    <header class="section__header">
                        <?php if ( function_exists('rank_math_the_breadcrumbs') ) rank_math_the_breadcrumbs(); ?>

                        <h1 class="section__title"><?php the_title(); ?></h1>

                        <?php if ( $post_lead ) : ?>
                            <div class="section__lead"><?php echo wp_kses_post( $post_lead ); ?></div>
                        <?php endif; ?>

                        <?php if ( has_post_thumbnail() ) : ?>
                            <div class="section__image-wrapper">
                                <?php
                                    $thumbnail_id = get_post_thumbnail_id( get_the_ID() );
                                    $alt_text = get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true );

                                    the_post_thumbnail('full', [
                                        'class'         => 'section__image',
                                        'alt'           => $alt_text ?: get_the_title(),
                                        'loading'       => 'eager',
                                        'fetchpriority' => 'high',
                                        'decoding'      => 'async'
                                    ]);
                                ?>
                            </div>
                        <?php endif; ?>
                    </header>
                    
                    <div class="section__content">
                        <?php echo do_shortcode( '[table_of_contents]' ); ?>
                        
                        <?php
                        // The main content
                        the_content();

                        // Optional: Pagination for multi-page posts
                        wp_link_pages(array(
                            'before' => '<div class="page-links">' . __('Pages:', 'gerendashaz'),
                            'after'  => '</div>',
                        ));
                        ?>
                    </div>
                </div>
            </section>

            <footer class="section__footer">
                <div class="container">
                    <?php if ( $taxonomy ) : ?>
                        <?php 
                            $taxonomy_obj   = get_taxonomy( $taxonomy ); 
                            $taxonomy_label = $taxonomy_obj ? $taxonomy_obj->labels->name : __('Categories', 'gerendashaz');
                        ?>
                        <span class="section__categories category">
                            <div class="category__container">
                                <strong class="visually-hidden"><?php echo esc_html( $taxonomy_label ) . ':'; ?></strong>
                                <div class="category__wrapper">
                                    <?php 
                                        // Categories
                                        wp_list_categories( array(
                                            'current_category'     => 0,
                                            'depth'                => 0,
                                            'echo'                 => true,
                                            'exclude'              => '',
                                            'exclude_tree'         => '',
                                            'feed'                 => '',
                                            'feed_image'           => '',
                                            'feed_type'            => '',
                                            'hide_title_if_empty'  => false,
                                            'separator'            => '',
                                            'show_count'           => 0,
                                            'show_option_all'      => '',
                                            'show_option_none'     => '',
                                            'style'                => '',
                                            'taxonomy'             => $taxonomy,
                                            'title_li'             => '',
                                            'use_desc_for_title'   => 0,
                                            'walker'               => '',
                                        ) ); 
                                    ?>
                                </div>
                            </div>
                        </span>
                    <?php endif; ?>

                    <?php if ( $related_posts->have_posts() ) : ?>
                        <div class="section__related-posts">
                            <h2 class="section__title"><?php echo esc_html__('You may also like…', 'gerendashaz'); ?></h2>

                            <div class="slider slider--related" id="related-posts-slider">
                                <div class="slider__list">
                                    <?php while ( $related_posts->have_posts() ) : $related_posts->the_post(); ?>
                                        <div class="slider__item">
                                            <?php
                                                $template_args = [
                                                    'post_type' => esc_attr($post_type)
                                                ];
                                                
                                                $template_slug = 'template-parts/cards/card-related.php';

                                                if ( locate_template( $template_slug ) ) {
                                                    // File exists, include it
                                                    get_template_part( 'template-parts/cards/card', 'related', $template_args );
                                                } else {
                                                    // File does not exist, handle accordingly
                                                    get_template_part( 'template-parts/cards/card', 'default', $template_args );
                                                }
                                            ?>
                                        </div>
                                    <?php endwhile; wp_reset_postdata(); ?>
                                </div>
                                <div class="slider__controls"></div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </footer>

            <?php
            // Load comments template
            if ( comments_open() || get_comments_number() ) :
                comments_template();
            endif;
            ?>
        
        <?php else : ?>
    
            <?php get_template_part('template-parts/flexibile-elements'); ?>
    
        <?php endif; ?>
    
    </main>

    <?php endwhile; ?>
<?php endif; ?>

<?php get_footer(); ?>
