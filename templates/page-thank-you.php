<?php
/**
 * Template Name: Thank You Template
 */
?>

<?php get_header(); ?>

<?php
    // Get the message_id from query string (?message_id=...)
    $message_id = isset($_GET['message_id']) ? sanitize_text_field($_GET['message_id']) : '';
    $data = $message_id ? get_transient($message_id) : false;

    $back_url = wp_get_referer() ?: home_url();
?>

<main class="page page--default">
    <section class="section section--default">
        <div class="container">
            <header class="page__header">
                <h1 class="page__title"><?php echo esc_html__('Thank You!', 'gerendashaz'); ?></h1>
            </header>
            <div class="page__content">

                <?php if ($data) : ?>

                    <?php echo wpautop( esc_html__('We’ve received your message. Here’s a summary of what you submitted:', 'gerendashaz') ); ?>

                    <ul class="thank-you-details">
                        <li>
                            <strong><?php echo esc_html__('Name:', 'gerendashaz'); ?></strong>
                            <?php echo esc_html($data['name']); ?>
                        </li>
                        <li>
                            <strong><?php echo esc_html__('E-mail:', 'gerendashaz'); ?></strong>
                            <?php echo esc_html($data['email']); ?>
                        </li>
                        <?php if (!empty($data['phone'])) : ?>
                            <li>
                                <strong><?php echo esc_html__('Phone:', 'gerendashaz'); ?></strong>
                                <?php echo esc_html($data['phone']); ?>
                            </li>
                        <?php endif; ?>
                        <li>
                            <strong><?php echo esc_html__('Subject:', 'gerendashaz'); ?></strong>
                            <?php echo esc_html($data['subject']); ?>
                        </li>
                        <li>
                            <strong><?php echo esc_html__('Message:', 'gerendashaz'); ?></strong>
                            <?php echo nl2br(esc_html($data['message'])); ?>
                        </li>
                    </ul>

                <?php else : ?>
                    <?php echo wpautop( esc_html__('Sorry, we couldn’t find your message details or the session has expired.', 'gerendashaz') ); ?>
                <?php endif; ?>

                <div class="text-center mt-3">
                    <a href="<?php echo esc_url( trailingslashit( $back_url ) ); ?>" class="btn btn-outline-primary btn-lg page__button">
                        <svg class="icon icon-arrow-left" role="img" aria-hidden="true"><use xlink:href="#icon-arrow-left"></use></svg>
                        <span><?php echo esc_html__('Back to previous page', 'gerendashaz'); ?></span>
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>
