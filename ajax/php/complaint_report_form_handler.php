<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }

    if ( ! function_exists('complaint_report_form_handler') ) {
        /**
         * Handles AJAX submissions for the complaint report form.
         *
         * This function processes POST requests submitted via AJAX for the complaint report form.
         * It performs the following steps:
         *   1. Ensures the request method is POST.
         *   2. Sanitizes and validates form inputs.
         *   3. Verifies reCAPTCHA token with Google's API to prevent bot submissions.
         *   4. Validates the security nonce.
         *   5. Handles optional file uploads, storing them in a custom folder and adding them to the Media Library.
         *   6. Sends an HTML email to the site admin with the form details and attachment links.
         *   7. Stores the message temporarily in a WordPress transient for 15 minutes.
         *   8. Returns a JSON response indicating success or failure.
         *
         * @return void Outputs a JSON response and terminates execution.
         */
        function complaint_report_form_handler() {
            try {
                // Ensure the request method is POST
                if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
                    wp_send_json_error([
                        'message' => __('Invalid request method.', 'gerendashaz')
                    ], 405);
                }

                /*
                // Check if form data is present
                if ( empty($_POST['form_data']) ) {
                    wp_send_json_error([
                        'message' => __('No form data received.', 'gerendashaz')
                    ], 400);
                }

                // Parse serialized form data into an associative array
                $form = [];
                if ( isset($_POST['form_data']) ) {
                    parse_str($_POST['form_data'], $form);
                }
                */

                // Use $_POST directly (since JS sends FormData)
                $form = $_POST;

                // Get reCAPTCHA token
                $recaptcha_token = isset($form['recaptcha_token']) ? sanitize_text_field($form['recaptcha_token']) : '';

                if (empty($recaptcha_token)) {
                    wp_send_json_error(['message' => __('reCAPTCHA verification failed (missing token).', 'gerendashaz')], 400);
                }

                // Send verification request to Google
                $response = wp_remote_post('https://www.google.com/recaptcha/api/siteverify', [
                    'body' => [
                        'secret'   => RECAPTCHA_SECRET_KEY,
                        'response' => $recaptcha_token,
                    ],
                    'timeout' => 10
                ]);

                if (is_wp_error($response)) {
                    wp_send_json_error(['message' => __('Unable to verify reCAPTCHA (request failed).', 'gerendashaz')], 400);
                }

                // Decode Google API response
                $recaptcha = json_decode(wp_remote_retrieve_body($response), true);

                // Log score for debugging
                //error_log('reCAPTCHA score: ' . ($recaptcha['score'] ?? 'null'));

                // If reCAPTCHA fails OR score too low → possible bot
                if ( empty($recaptcha['success']) || ($recaptcha['score'] ?? 0) < 0.3 ) {
                    wp_send_json_error([
                        'message' => __('Suspicious activity detected. reCAPTCHA failed.', 'gerendashaz')
                    ], 403);
                }

                // Nonce verification for security
                if ( ! isset($form['complaint_report_form_nonce']) || ! wp_verify_nonce($form['complaint_report_form_nonce'], 'complaint_report_form_action') ) {
                    wp_send_json_error([
                        'message' => __('Invalid security token.', 'gerendashaz')
                    ], 403);
                }

                // Extract and sanitize form fields
                $name    = isset($form['crf_name']) ? sanitize_text_field($form['crf_name']) : '';
                $email   = isset($form['crf_email']) ? sanitize_email($form['crf_email']) : '';
                $phone   = isset($form['crf_phone']) ? sanitize_text_field($form['crf_phone']) : '';
                $subject = isset($form['crf_subject']) ? sanitize_text_field($form['crf_subject']) : '';
                $message = isset($form['crf_message']) ? sanitize_textarea_field($form['crf_message']) : '';
                $privacy = isset($form['crf_privacy_policy']) ? sanitize_text_field($form['crf_privacy_policy']) : '';
                $referer = isset($form['_wp_http_referer']) ? esc_url_raw($form['_wp_http_referer']) : '';

                if ( $referer && strpos($referer, 'http') !== 0 ) {
                    $referer = home_url($referer);
                }

                // Validate required fields
                if ( empty($name) || empty($email) || empty($subject) || empty($message) ) {
                    wp_send_json_error([
                        'message' => __('All required fields must be filled out.', 'gerendashaz')
                    ], 422);
                }

                // Validate email format
                if ( ! is_email($email) ) {
                    wp_send_json_error([
                        'message' => __('Invalid email format.', 'gerendashaz')
                    ], 422);
                }

                // Validate privacy policy consent
                if ( empty($privacy) || $privacy !== 'on' ) {
                    wp_send_json_error([
                        'message' => __('You must agree to the privacy policy.', 'gerendashaz')
                    ], 422);
                }

                // Handle uploaded files (Dropzone) + Media Library
                
                if ( ! empty($_FILES['crf_files']) && ! empty($_FILES['crf_files']['tmp_name']) ) {

                    require_once ABSPATH . 'wp-admin/includes/file.php';
                    require_once ABSPATH . 'wp-admin/includes/media.php';
                    require_once ABSPATH . 'wp-admin/includes/image.php';

                    $upload_dir = wp_upload_dir();

                    // Sanitize email for folder name
                    $safe_email = sanitize_email($email);
                    $safe_email = str_replace(['@', '.'], '_', $safe_email);

                    // Custom folder path
                    $custom_dir = trailingslashit($upload_dir['basedir']) . 'complaint_report_form/' . $safe_email . '/';
                    $custom_url = trailingslashit($upload_dir['baseurl']) . 'complaint_report_form/' . $safe_email . '/';

                    // Create directory
                    if ( ! wp_mkdir_p($custom_dir) ) {
                        wp_send_json_error([
                            'message' => __('Failed to create upload directory.', 'gerendashaz')
                        ], 500);
                    }

                    $uploaded_files = [];

                    foreach ($_FILES['crf_files']['tmp_name'] as $key => $tmp_name) {

                        if ( ! is_uploaded_file($tmp_name) ) {
                            continue;
                        }

                        $original_name = sanitize_file_name($_FILES['crf_files']['name'][$key]);
                        $ext           = pathinfo($original_name, PATHINFO_EXTENSION);
                        $filename          = pathinfo($original_name, PATHINFO_FILENAME);

                        // Generate unique filename
                        $filename = sprintf('%s-%s-%s.%s', date('Ymd-His'), wp_generate_password(6, false, false), sanitize_title($filename), $ext);
                        
                        $filetype    = wp_check_filetype($filename);
                        $destination = $custom_dir . $filename;
                        $file_url    = $custom_url . $filename;

                        if ( move_uploaded_file($tmp_name, $destination) ) {

                            // Prepare attachment post
                            $attachment = [
                                'guid'           => $file_url,
                                'post_mime_type' => $filetype['type'],
                                'post_title'     => preg_replace('/\.[^.]+$/', '', $filename),
                                'post_content'   => '',
                                'post_status'    => 'inherit',
                            ];

                            // Insert into Media Library
                            $attachment_id = wp_insert_attachment($attachment, $destination);

                            if ( ! is_wp_error($attachment_id) ) {

                                // Generate metadata (thumbnails, sizes, etc.)
                                $attach_data = wp_generate_attachment_metadata($attachment_id, $destination);
                                wp_update_attachment_metadata($attachment_id, $attach_data);

                                $uploaded_files[] = esc_url($file_url);
                            }
                        }
                    }
                }

                // Get admin email and validate
                $admin_email = get_option('admin_email');
                if ( ! $admin_email || ! is_email($admin_email) ) {
                    wp_send_json_error([
                        'message' => __('Admin email is not configured properly.', 'gerendashaz')
                    ], 500);
                }

                // Email headers
                $headers = [
                    'From: ' . get_bloginfo('name') . ' <' . $admin_email . '>',
                    'Reply-To: ' . $name . ' <' . $email . '>',
                    'Content-Type: text/html; charset=UTF-8'
                ];

                // Email subject formatted with site name
                $mail_subject = sprintf(
                    __('[%1$s] New message: %2$s', 'gerendashaz'),
                    get_bloginfo('name'),
                    $subject
                );

                // Convert message lines into HTML paragraphs
                $message_lines = array_filter(preg_split("/\r\n|\n|\r/", $message), function($line) {
                    return trim($line) !== '';
                });

                $formatted_message = implode('', array_map(function($line) {
                    return wpautop( esc_html($line) );
                }, $message_lines));

                // Build final email body
                $phone_html = !empty($phone) ? '<strong>Phone:</strong> ' . esc_html($phone) . '<br/>' : '';
                
                $attachments_html = '';
                if ( ! empty($uploaded_files) ) {
                    $attachments_html .= '<strong>Attachments:</strong><ul>';
                    foreach ( $uploaded_files as $file_url ) {
                        $filename = esc_html( basename( $file_url ) );
                        $attachments_html .= sprintf(
                            '<li><a href="%1$s" target="_blank" rel="noopener">%2$s</a></li>',
                            esc_url( $file_url ),
                            $filename
                        );
                    }
                    $attachments_html .= '</ul>';
                }

                $referer_html = !empty($referer) ? '<strong>Form submitted from:</strong> <a href="' . esc_url($referer) . '" target="_blank" rel="noopener">' . esc_html($referer) . '</a><br/>' : '';

                $mail_message = sprintf(
                    '<strong>Name:</strong> %s<br/>
                    <strong>Email:</strong> %s<br/>
                    %s
                    <strong>Subject:</strong> %s<br/>
                    %s
                    %s
                    %s',
                    esc_html($name),
                    esc_html($email),
                    $phone_html,
                    esc_html($subject),
                    $formatted_message,
                    $attachments_html,
                    $referer_html
                );

                // Send the email
                $sent = wp_mail($admin_email, $mail_subject, $mail_message, $headers);

                if ( ! $sent ) {
                    wp_send_json_error([
                        'message' => __('Message could not be sent. Please try again later.', 'gerendashaz')
                    ], 500);
                }

                // Generate a unique message ID and store message in a transient for 15 minutes
                // Useful for debugging, logging, or displaying confirmation later: get_transient($message_id).
                $message_id = time() . wp_generate_password(8, false, false);
                set_transient( $message_id, [
                    'name'        => $name,
                    'email'       => $email,
                    'phone'       => $phone,
                    'subject'     => $subject,
                    'message'     => $message,
                    'attachments' => $uploaded_files,
                    'referer'     => $referer
                ], 15 * MINUTE_IN_SECONDS ); // expires after 15 mins
                
                // Success response
                wp_send_json_success([
                    'message'      => __('Your message has been sent successfully!', 'gerendashaz'),
                    'redirect_url' => esc_url( trailingslashit( get_the_permalink( THANK_YOU_PAGE_ID ) ) ),
                    'message_id'   => $message_id
                ], 200);

            } catch ( Exception $e ) {
                // Catch any unexpected errors
                wp_send_json_error([
                    'message' => sprintf(__('Unexpected error: %s', 'gerendashaz'), $e->getMessage())
                ], 500);
            }
        }

        // Register AJAX handlers for logged-in and non-logged-in users
        add_action('wp_ajax_complaint_report_form_handler', 'complaint_report_form_handler');
        add_action('wp_ajax_nopriv_complaint_report_form_handler', 'complaint_report_form_handler');
    }
