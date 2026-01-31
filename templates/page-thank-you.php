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

<main class="page page--default page--thank-you-template">
    <section class="section section--default">
        <div class="container">
            <header class="page__header">
                <h1 class="page__title"><?php echo esc_html__('Thank You!', 'gerendashaz'); ?></h1>
            </header>
            <div class="page__content">
                <?php if ($data) : ?>
                    <?php echo wpautop( esc_html__('We’ve received your message. Here’s a summary of what you submitted:', 'gerendashaz') ); ?>

                    <ul class="thank-you-details">
                        <?php if (!empty($data['name'])) : ?>
                        <li>
                            <strong><?php echo esc_html__('Name:', 'gerendashaz'); ?></strong>
                            <?php echo esc_html($data['name']); ?>
                        </li>
                        <?php endif; ?>

                        <?php if (!empty($data['email'])) : ?>
                        <li>
                            <strong><?php echo esc_html__('E-mail:', 'gerendashaz'); ?></strong>
                            <?php echo esc_html($data['email']); ?>
                        </li>
                        <?php endif; ?>

                        <?php if (!empty($data['phone'])) : ?>
                            <li>
                                <strong><?php echo esc_html__('Phone:', 'gerendashaz'); ?></strong>
                                <?php echo esc_html($data['phone']); ?>
                            </li>
                        <?php endif; ?>

                        <?php if (!empty($data['subject'])) : ?>
                        <li>
                            <strong><?php echo esc_html__('Subject:', 'gerendashaz'); ?></strong>
                            <?php echo esc_html($data['subject']); ?>
                        </li>
                        <?php endif; ?>

                        <?php if (!empty($data['message'])) : ?>
                        <li>
                            <strong><?php echo esc_html__('Message:', 'gerendashaz'); ?></strong>
                            <?php echo nl2br(esc_html($data['message'])); ?>
                        </li>
                        <?php endif; ?>

                        <?php if (!empty($data['attachments'])) : ?>
                            <li>
                                <strong><?php echo esc_html__('Attachments:', 'gerendashaz'); ?></strong>
                                <ul>
                                    <?php foreach ($data['attachments'] as $file_url) : ?>
                                        <li>
                                            <a href="<?php echo esc_url($file_url); ?>" target="_blank" rel="noopener">
                                                <?php echo esc_html(basename($file_url)); ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                        <?php endif; ?>
                    </ul>

                <?php else : ?>
                    <?php echo wpautop( esc_html__('Sorry, we couldn’t find your message details or the session has expired.', 'gerendashaz') ); ?>
                <?php endif; ?>

                <a href="<?php echo esc_url( trailingslashit( $back_url ) ); ?>" class="btn btn-outline-primary btn-lg page__button">
                    <?php echo esc_html__( 'Back to previous page', 'gerendashaz' ); ?>
                </a>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>
