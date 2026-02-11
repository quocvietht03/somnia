<?php

/**
 * Display single product reviews (comments)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product-reviews.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.7.0
 */
defined('ABSPATH') || exit;

global $product;

if (!comments_open()) {
    return;
}

$rating_counts = $product->get_rating_counts();
$total_ratings = $product->get_rating_count();

$distribution = array(
    5 => 0,
    4 => 0,
    3 => 0,
    2 => 0,
    1 => 0
);

if (!empty($rating_counts)) {
    foreach ($rating_counts as $rating => $count) {
        $distribution[$rating] = round(($count / $total_ratings) * 100);
    }
}
?>
<div id="reviews" class="woocommerce-Reviews">
    <div id="comments" class="<?php echo ($product->get_average_rating() > 0) ? 'bt-has-comments' : 'bt-no-comments'; ?>">
        <?php if ($product->get_average_rating() > 0) { ?>
            <div class="bt-summary-rating">
                <div class="bt-left-summary">
                    <div class="bt-product-rating">
                        <div class="bt-product-total"><?php echo esc_html(number_format($product->get_average_rating(), 1)); ?></div>
                        <?php echo wc_get_rating_html($product->get_average_rating()); ?>
                        <?php if ($product->get_rating_count()): ?>
                            <div class="bt-product-rating--count">
                                <span class="bt-count-text">
                                    (<?php
                                        echo esc_html($product->get_rating_count());
                                        echo esc_html($product->get_rating_count() == 1 ? ' Rating' : ' Ratings');
                                        ?>)
                                </span>
                                <span class="bt-count-text-version-two">
                                    (<?php echo esc_html($product->get_rating_count()); ?>)
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="bt-summary-rating--divider"></div>
                <div class="bt-center-summary">
                    <?php
                    if (!empty($distribution)) {
                        foreach ($distribution as $key => $rating) {
                    ?>
                            <div class="bt-bar">
                                <div class="bt-num"><?php echo esc_html($key); ?></div>
                                <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M19.2949 8.93578L15.7715 12.0108L16.827 16.5889C16.8828 16.8282 16.8669 17.0787 16.7812 17.309C16.6954 17.5394 16.5437 17.7393 16.3449 17.8839C16.1462 18.0284 15.9092 18.1112 15.6637 18.1218C15.4182 18.1324 15.175 18.0704 14.9645 17.9436L10.9715 15.5217L6.98713 17.9436C6.77664 18.0704 6.53342 18.1324 6.28789 18.1218C6.04236 18.1112 5.8054 18.0284 5.60665 17.8839C5.40791 17.7393 5.25618 17.5394 5.17045 17.309C5.08471 17.0787 5.06877 16.8282 5.12463 16.5889L6.17853 12.0155L2.65431 8.93578C2.46791 8.77502 2.33313 8.5628 2.26686 8.32574C2.20059 8.08868 2.20578 7.83733 2.28179 7.60321C2.3578 7.36909 2.50124 7.16262 2.69413 7.0097C2.88701 6.85678 3.12075 6.76421 3.36603 6.7436L8.01135 6.34125L9.82463 2.01625C9.91932 1.78931 10.079 1.59546 10.2837 1.45911C10.4883 1.32276 10.7287 1.25 10.9746 1.25C11.2205 1.25 11.4609 1.32276 11.6656 1.45911C11.8702 1.59546 12.0299 1.78931 12.1246 2.01625L13.9434 6.34125L18.5871 6.7436C18.8324 6.76421 19.0662 6.85678 19.259 7.0097C19.4519 7.16262 19.5954 7.36909 19.6714 7.60321C19.7474 7.83733 19.7526 8.08868 19.6863 8.32574C19.62 8.5628 19.4852 8.77502 19.2988 8.93578H19.2949Z"
                                        fill="#FFB600" />
                                </svg>
                                <div class="bt-bar-percent">
                                    <span style="width: <?php echo esc_attr($rating); ?>%"></span>
                                </div>
                                <div class="bt-num-percent"><?php echo esc_html($rating); ?>%</div>
                            </div>
                    <?php
                        }
                    }
                    ?>
                </div>
                <div class="bt-summary-rating--divider"></div>
                <div class="bt-right-summary">
                    <a href="#" class="bt-action-review"><?php echo esc_html__('Write a review', 'somnia') ?></a>
                </div>

            </div>
        <?php } ?>
        <h2 class="woocommerce-Reviews-title">
            <?php
            $count = $product->get_review_count();
            if ($count && wc_review_ratings_enabled()) {
                /* translators: 1: reviews count 2: product name */
                $reviews_title = sprintf(esc_html(_n('%1$s review', '%1$s reviews', $count, 'somnia')), esc_html($count));
                echo apply_filters('woocommerce_reviews_title', $reviews_title, $count, $product);  // WPCS: XSS ok.
            } else {
                esc_html_e('Reviews', 'somnia');
            }
            ?>
        </h2>

        <?php if (have_comments()): ?>
            <ol class="commentlist">
                <?php wp_list_comments(apply_filters('woocommerce_product_review_list_args', array('callback' => 'woocommerce_comments'))); ?>
            </ol>

            <?php
            if (get_comment_pages_count() > 1 && get_option('page_comments')):
                echo '<nav class="woocommerce-pagination">';
                paginate_comments_links(
                    apply_filters(
                        'woocommerce_comment_pagination_args',
                        array(
                            'prev_text' => is_rtl() ? '&rarr;' : '&larr;',
                            'next_text' => is_rtl() ? '&larr;' : '&rarr;',
                            'type' => 'list',
                        )
                    )
                );
                echo '</nav>';
            endif;
            ?>
        <?php else: ?>
            <p class="woocommerce-noreviews"><?php esc_html_e('There are no reviews yet.', 'somnia'); ?></p>
        <?php endif; ?>
    </div>

    <?php if (get_option('woocommerce_review_rating_verification_required') === 'no' || wc_customer_bought_product('', get_current_user_id(), $product->get_id())): ?>
        <div id="review_form_wrapper" <?php if ($product->get_average_rating() > 0) { ?> class="bt-form-review-popup" <?php } ?>>
            <div class="bt-review-overlay"></div>
            <div class="bt-review-close"></div>
            <div id="review_form">
                <?php
                $commenter = wp_get_current_commenter();
                $comment_form = array(
                    'title_reply' => '',
                    'title_reply_to' => esc_html__('Leave a Reply to %s', 'somnia'),
                    'title_reply_before' => '<span id="reply-title" class="comment-reply-title">',
                    'title_reply_after' => '</span>',
                    'comment_notes_after' => '',
                    'label_submit' => esc_html__('Submit Review', 'somnia'),
                    'logged_in_as' => '',
                    'comment_field' => '',
                );

                $name_email_required = (bool) get_option('require_name_email', 1);
                $fields = array(
                    'author' => array(
                        'label' => __('You Name (Public)', 'somnia'),
                        'type' => 'text',
                        'value' => $commenter['comment_author'],
                        'required' => $name_email_required,
                        'placeholder' => 'You Name (Public)'
                    ),
                    'email' => array(
                        'label' => __('Your email (private)', 'somnia'),
                        'type' => 'email',
                        'value' => $commenter['comment_author_email'],
                        'required' => $name_email_required,
                        'placeholder' => 'Your email (private)'
                    ),
                );

                $comment_form['fields'] = array();

                foreach ($fields as $key => $field) {
                    $field_html = '<p class="comment-form-' . esc_attr($key) . '">';
                    $field_html .= '<label for="' . esc_attr($key) . '">' . esc_html($field['label']);

                    if ($field['required']) {
                        $field_html .= '&nbsp;<span class="required">*</span>';
                    }

                    $field_html .= '</label><input id="' . esc_attr($key) . '" name="' . esc_attr($key) . '" placeholder="' . $field['placeholder'] . '" type="' . esc_attr($field['type']) . '" value="' . esc_attr($field['value']) . '" size="30" ' . ($field['required'] ? 'required' : '') . ' /></p>';

                    $comment_form['fields'][$key] = $field_html;
                }

                $account_page_url = wc_get_page_permalink('myaccount');

                if ($account_page_url) {
                    /* translators: %s opening and closing link tags respectively */
                    $comment_form['must_log_in'] = '<p class="must-log-in">' . sprintf(esc_html__('You must be %1$slogged in%2$s to post a review.', 'somnia'), '<a href="' . esc_url($account_page_url) . '">', '</a>') . '</p>';
                }

                if (wc_review_ratings_enabled()) {

                    $comment_form['comment_field'] = '<div class="comment-form-rating"><label for="rating">' . esc_html__('Write a review:', 'somnia') . (wc_review_ratings_required() ? '&nbsp;<span class="required">*</span>' : '') . "</label><select name=\"rating\" id=\"rating\" required>
\t\t\t\t\t\t<option value=\"\">" . esc_html__('Rate&hellip;', 'somnia') . "</option>
\t\t\t\t\t\t<option value=\"5\">" . esc_html__('Perfect', 'somnia') . "</option>
\t\t\t\t\t\t<option value=\"4\">" . esc_html__('Good', 'somnia') . "</option>
\t\t\t\t\t\t<option value=\"3\">" . esc_html__('Average', 'somnia') . "</option>
\t\t\t\t\t\t<option value=\"2\">" . esc_html__('Not that bad', 'somnia') . "</option>
\t\t\t\t\t\t<option value=\"1\">" . esc_html__('Very poor', 'somnia') . "</option>
\t\t\t\t\t</select></div>";
                }
                $comment_form['comment_field'] .= '<p class="comment-form-review-title"><label for="review_title">' . esc_html__('Review Title', 'somnia') . '</label><input id="review_title" name="review_title" type="text" placeholder="' . esc_attr__('Give your review a title', 'somnia') . '" required /></p>';

                $comment_form['comment_field'] .= '<p class="comment-form-comment"><label for="comment">' . esc_html__('Review', 'somnia') . '</label><textarea placeholder="' . esc_attr__('Write your comment here', 'somnia') . '" id="comment" name="comment" cols="45" rows="8" required></textarea></p>';

                comment_form(apply_filters('woocommerce_product_review_comment_form_args', $comment_form));
                ?>
            </div>
        </div>
    <?php else: ?>
        <p class="woocommerce-verification-required">
            <?php esc_html_e('Only logged in customers who have purchased this product may leave a review.', 'somnia'); ?>
        </p>
    <?php endif; ?>

    <div class="clear"></div>
</div>