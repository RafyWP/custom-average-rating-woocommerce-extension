<?php

/**
 * Custom Average Rating WooCommerce Extension
 *
 * This class handles the integration of custom product ratings in WooCommerce,
 * including custom fields, frontend display, and REST API endpoints.
 *
 * @link              https://litcode.store/product/custom-average-rating-woocommerce-extension/
 * @since             1.0.0
 * @package           rafy
 * @author            Lit ✴ Code
 * @license           GPL-2.0-or-later
 */

class CustomAverageRating
{
    /**
     * Constructor
     *
     * Hooks into WooCommerce to add and manage custom ratings.
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        add_action(
            'woocommerce_product_options_general_product_data',
            [$this, 'addStarRatingCustomField']
        );
        add_action('woocommerce_process_product_meta', [$this, 'saveStarRatingCustomField']);
        add_action('rest_api_init', [$this, 'registerLitcodeRatingRoutes']);
    }

    /**
     * Adds a custom star rating field to the WooCommerce product edit page.
     *
     * @since 1.0.0
     * @return void
     */
    public function addStarRatingCustomField()
    {
        global $post;

        $rating = get_post_meta($post->ID, '_star_rating', true);

        if (empty($rating)) {
            $rating = get_post_meta($post->ID, '_wc_average_rating', true);
        }

        ?>
        <div class="options_group">
            <p class="form-field">
                <label for="_star_rating"><?php esc_html_e('Star Rating', 'woocommerce'); ?></label>
                <input type="number" id="_star_rating" name="_star_rating" value="<?php echo esc_attr($rating); ?>" min="1" max="5" step="0.5">
            </p>
        </div>
        <?php
    }

    /**
     * Saves the custom star rating when the product is updated.
     *
     * @since 1.0.0
     * @param int $post_id The product ID.
     * @return void
     */
    public function saveStarRatingCustomField($post_id)
    {
        if (isset($_POST['_star_rating']) && $_POST['_star_rating'] !== '') {
            update_post_meta($post_id, '_star_rating', sanitize_text_field($_POST['_star_rating']));
        } else {
            $average_rating = get_post_meta($post_id, '_wc_average_rating', true);
            update_post_meta($post_id, '_star_rating', $average_rating);
        }
    }

    /**
     * Registers the REST API routes for retrieving product ratings.
     *
     * @since 1.0.0
     * @return void
     */
    public function registerLitcodeRatingRoutes()
    {
        register_rest_route('custom-average-rating/v1', '/product_rating/(?P<id>\d+)', [
            'methods'             => 'GET',
            'callback'            => [$this, 'getProductStarRating'],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route('custom-average-rating/v1', '/average_rating', [
            'methods'             => 'GET',
            'callback'            => [$this, 'getAverageRatingAllProducts'],
            'permission_callback' => '__return_true',
        ]);
    }

    /**
     * Retrieves the star rating of a specific product via REST API.
     *
     * @since 1.0.0
     * @param WP_REST_Request $request The request data.
     * @return array|WP_Error The product rating or an error message.
     */
    public function getProductStarRating($request)
    {
        $product_id = $request->get_param('id');

        if (empty($product_id)) {
            return new WP_Error('no_product_id', 'Invalid product ID.', ['status' => 400]);
        }        

        $star_rating = get_post_meta($product_id, '_star_rating', true);

        if (empty($star_rating)) {
            $star_rating = get_post_meta($product_id, '_wc_average_rating', true);
        }

        $comments = get_comments([
            'post_id' => $product_id,
            'status'  => 'approve',
            'orderby' => 'comment_date',
            'order'   => 'DESC',
        ]);

        $comments_data = [];

        foreach ($comments as $comment) {
            $rating = get_comment_meta($comment->comment_ID, 'rating', true);
            $avatar_url = get_avatar_url($comment->user_id, ['size' => 48]);

            $display_name = $comment->user_id ? get_the_author_meta('display_name', $comment->user_id) : $comment->comment_author;

            $comments_data[] = [
                'comment_id'   => $comment->comment_ID,
                'author'       => $display_name,
                'avatar'       => $avatar_url,
                'content'      => $comment->comment_content,
                'date'         => $comment->comment_date,
                'rating'       => !empty($rating) ? intval($rating) : null,
            ];
        }

        return [
            'product_id'  => $product_id,
            'star_rating' => $star_rating,
            'comments'    => $comments_data,
        ];
    }

    /**
     * Calculates and returns the average rating of all WooCommerce products.
     *
     * @since 1.0.0
     * @return array The average rating and total number of products.
     */
    public function getAverageRatingAllProducts()
    {
        $args = [
            'post_type'      => 'product',
            'posts_per_page' => -1,
            'fields'         => 'ids',
        ];

        $products = get_posts($args);
        $total_rating = 0;
        $count = 0;

        if (empty($products)) {
            return [
                'average_rating' => 0,
                'total_products' => 0,
            ];
        }

        foreach ($products as $product_id) {
            $rating = get_post_meta($product_id, '_star_rating', true);

            if (empty($rating)) {
                $rating = get_post_meta($product_id, '_wc_average_rating', true);
            }

            if ($rating) {
                $total_rating += floatval($rating);
                $count++;
            }
        }

        $average = $count > 0 ? $total_rating / $count : 0;

        return [
            'average_rating' => $average,
            'total_products' => count($products),
        ];
    }
}
