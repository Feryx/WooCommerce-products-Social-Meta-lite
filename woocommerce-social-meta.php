<?php
/*
Plugin Name: WooCommerce products Social Meta lite
Description: Provide a unique social meta title, description, and image for WooCommerce products. Egyedi social meta cím, leírás és kép megadása WooCommerce termékekhez.
Version: 1.0
Author: Feryx
*/

// Create admin fields 
add_action('woocommerce_product_options_general_product_data', function() {
    echo '<div class="options_group">';

    woocommerce_wp_text_input([
        'id'          => '_social_title',
        'label'       => __('Social cím', 'woocommerce'),
        'description' => __('Megosztáskor megjelenő cím', 'woocommerce'),
        'desc_tip'    => true,
    ]);

    woocommerce_wp_textarea_input([
        'id'          => '_social_description',
        'label'       => __('Social leírás', 'woocommerce'),
        'description' => __('Megosztáskor megjelenő leírás (pl. Facebook, Messenger)', 'woocommerce'),
        'desc_tip'    => true,
    ]);

    woocommerce_wp_text_input([
        'id'          => '_social_image',
        'label'       => __('Social kép URL', 'woocommerce'),
        'placeholder' => 'https://...',
        'description' => __('Ha nem adod meg, az elsődleges termékkép lesz használva.', 'woocommerce'),
        'desc_tip'    => true,
    ]);

    echo '</div>';
});

// Save the fileds
add_action('woocommerce_process_product_meta', function($post_id) {
    foreach (['_social_title', '_social_description', '_social_image'] as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
        }
    }
});

// Social meta tags add to head
add_action('wp_head', function() {
    if (!is_product()) return;

    global $post;

    $title = get_post_meta($post->ID, '_social_title', true);
    $desc  = get_post_meta($post->ID, '_social_description', true);
    $image = get_post_meta($post->ID, '_social_image', true);

    if (!$title) $title = get_the_title($post);
    if (!$desc)  $desc  = strip_tags(get_the_excerpt($post));
    if (!$image) {
        $thumbnail_id = get_post_thumbnail_id($post);
        if ($thumbnail_id) {
            $image = wp_get_attachment_url($thumbnail_id);
        }
    }

    if ($title) echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
    if ($desc)  echo '<meta property="og:description" content="' . esc_attr($desc) . '">' . "\n";
    if ($image) echo '<meta property="og:image" content="' . esc_url($image) . '">' . "\n";

    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    if ($title) echo '<meta name="twitter:title" content="' . esc_attr($title) . '">' . "\n";
    if ($desc)  echo '<meta name="twitter:description" content="' . esc_attr($desc) . '">' . "\n";
    if ($image) echo '<meta name="twitter:image" content="' . esc_url($image) . '">' . "\n";
}, 5);
