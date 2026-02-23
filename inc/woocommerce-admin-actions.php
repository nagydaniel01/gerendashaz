<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }

    if ( ! class_exists( 'WooCommerce' ) ) {
        return;
    }

    use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;

    if ( ! function_exists( 'add_customer_geo_meta_box' ) ) {

        /**
         * Add a meta box to WooCommerce order admin for Customer Geo & Browser Info.
         */
        function add_customer_geo_meta_box() {
            // Determine if HPOS custom orders table is active
            $screen = 'shop_order';
            if ( class_exists( CustomOrdersTableController::class ) ) {
                $controller = wc_get_container()->get( CustomOrdersTableController::class );
                if ( method_exists( $controller, 'custom_orders_table_usage_is_enabled' ) && $controller->custom_orders_table_usage_is_enabled() ) {
                    $screen = wc_get_page_screen_id( 'shop-order' );
                }
            }

            add_meta_box(
                'woocommerce-customer-geo-info',
                __( 'Customer Geo & Browser Info', 'gerendashaz' ),
                'show_customer_geo_meta_box',
                $screen,
                'side',
                'high'
            );
        }
        add_action( 'add_meta_boxes', 'add_customer_geo_meta_box' );
    }

    if ( ! function_exists( 'show_customer_geo_meta_box' ) ) {
        /**
         * Display the Customer Geo & Browser Info meta box in order admin.
         *
         * @param WP_Post $post Order post object
         */
        function show_customer_geo_meta_box( $post ) {

            $order = wc_get_order( $post->ID );

            echo '<div class="customer-geo-info order-attribution-metabox">';

            if ( ! $order ) {
                echo '<h4>' . esc_html__( 'Order not found.', 'gerendashaz' ) . '</h4>';
                echo '</div>';
                return;
            }

            // Customer IP and Browser
            $ip         = $order->get_customer_ip_address();
            $user_agent = $order->get_customer_user_agent();

            echo '<h4>' . esc_html__( 'Customer IP', 'gerendashaz' ) . '</h4><span>' . esc_html( $ip ?: __( 'Not available', 'gerendashaz' ) ) . '</span>';
            echo '<h4>' . esc_html__( 'Browser Info', 'gerendashaz' ) . '</h4><span>' . esc_html( $user_agent ?: __( 'Not available', 'gerendashaz' ) ) . '</span>';

            // Try cached geo
            $geo_data = $order->get_meta( '_customer_geo_data', true );

            if ( empty( $geo_data ) && $ip ) {
                $response = wp_safe_remote_get( "http://ip-api.com/json/{$ip}" );

                if ( ! is_wp_error( $response ) ) {
                    $body = wp_remote_retrieve_body( $response );
                    $geo_data = json_decode( $body, true );

                    if ( ! empty( $geo_data ) && isset( $geo_data['status'] ) && $geo_data['status'] === 'success' ) {
                        $order->update_meta_data( '_customer_geo_data', $geo_data );
                        $order->save();
                    } else {
                        $geo_data = null;
                    }
                } else {
                    $geo_data = null;
                }
            }

            // Show geo
            if ( ! empty( $geo_data ) && is_array( $geo_data ) ) {

                echo '<h4>' . esc_html__( 'Country', 'gerendashaz' ) . '</h4><span>' . esc_html( $geo_data['country'] ?? __( 'Not available', 'gerendashaz' ) ) . '</span>';
                echo '<h4>' . esc_html__( 'Region', 'gerendashaz' ) . '</h4><span>' . esc_html( $geo_data['regionName'] ?? __( 'Not available', 'gerendashaz' ) ) . '</span>';
                echo '<h4>' . esc_html__( 'City', 'gerendashaz' ) . '</h4><span>' . esc_html( $geo_data['city'] ?? __( 'Not available', 'gerendashaz' ) ) . '</span>';
                echo '<h4>' . esc_html__( 'ZIP', 'gerendashaz' ) . '</h4><span>' . esc_html( $geo_data['zip'] ?? __( 'Not available', 'gerendashaz' ) ) . '</span>';
                echo '<h4>' . esc_html__( 'ISP', 'gerendashaz' ) . '</h4><span>' . esc_html( $geo_data['isp'] ?? __( 'Not available', 'gerendashaz' ) ) . '</span>';

            } else {
                echo '<span>' . esc_html__( 'Geo data not available.', 'gerendashaz' ) . '</span>';
            }

            echo '</div>';
        }
    }
