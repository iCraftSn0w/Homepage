<?php
/*
 * Blocks WP add-ons
 *
 * Created by: Pinegrow.com
 *
 */

if ( ! function_exists( 'blocks_pg_init' ) ) :

    function blocks_pg_init() {

        register_post_type('blocks_content', array(
            'labels' =>
                array(
                    'name' => __( 'Blocks content' )
                ),
            'public' => true,
            'hierarchical' => true,
            'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'custom-fields', 'comments', 'revisions', 'page-attributes', 'post-formats' ),
            'has_archive' => true,
            'show_in_menu' => true
        ));
    }
endif;
add_action( 'init', 'blocks_pg_init' );

/* Add link field to attachments */
if ( ! function_exists( 'blocks_add_image_attachment_fields_to_edit' ) ) :

    function blocks_add_image_attachment_fields_to_edit($form_fields, $post) {

        $form_fields["blocks_link"] = array(
            "label" => __("Link"),
            "input" => "text", // this is default if "input" is omitted
            "value" => get_post_meta($post->ID, "_blocks_link", true),
            "helps" => __("URL of the image link."),
        );
        return $form_fields;
    }

    add_filter("attachment_fields_to_edit", "blocks_add_image_attachment_fields_to_edit", null, 2);
endif;

if ( ! function_exists( 'blocks_add_image_attachment_fields_to_save' ) ) :

    function blocks_add_image_attachment_fields_to_save($post, $attachment) {

        if( isset($attachment['blocks_link']) ){
            update_post_meta($post['ID'], '_blocks_link', $attachment['blocks_link']);
        }
        return $post;
    }

    add_filter("attachment_fields_to_save", "blocks_add_image_attachment_fields_to_save", null , 2);
endif;

/* Get gallery info with tags */
function blocks_get_gallery($post_id) {
    $ret = array(
        'tags' => array(),
        'images' => array()
    );

    $gallery = get_post_gallery( $post_id, false );
    $gallery_ids = $gallery ? explode( ',', $gallery['ids']) : array();

    foreach($gallery_ids as $id) {
        $thumb = wp_get_attachment_image_src( $id, 'medium');
        $image = wp_get_attachment_image_src( $id, 'large');

        if($thumb && $image) {
            $image_data = array( 'tags' => array(), 'tags_string' => '');
            $image_data['thumbnail'] = $thumb;
            $image_data['image'] = $image;
            $image_data['tags'] = wp_get_post_tags( $id );
            foreach($image_data['tags'] as $tag) {
                $ret['tags'][$tag->slug] = $tag->name;
                $image_data['tags_string'] .= ' '.$tag->slug;
            }
            $data = wp_prepare_attachment_for_js( $id );
            $image_data['title'] = $data['title'];
            $image_data['caption'] = $data['caption'];
            $image_data['alt'] = $data['alt'];
            $image_data['description'] = $data['description'];
            $image_data['link'] = get_post_meta( $id, "_blocks_link", true);

            $ret['images'][] = $image_data;
        }
    }
    asort($ret['tags']);

    return $ret;
}

function blocks_the_category() {
    $categories = get_the_category();
    $output = array();
    if($categories){
        foreach($categories as $category) {
            $output[] = '<a href="'.get_category_link( $category->term_id ).'" title="' . esc_attr( sprintf( __( "View all posts in %s" ), $category->name ) ) . '">'.$category->cat_name.'</a>';
        }
    }
    echo implode(', ', $output);
}

?>