
<?php
/**
 * Plugin Name: Spiky Geolocation & Redirect
 * Plugin URI: https://spikystudio.co.uk/
 * Description: Provides redirect prompt based on user location.
 * Version: 0.1
 * Author: Joe @ Spiky
 * Author URI: https://joelewispodmore.github.io/
 **/

if (!defined('ABSPATH'))
{
    die("Access denied.");
}

add_action('wp_enqueue_scripts', 'callback_for_setting_up_scripts');
function callback_for_setting_up_scripts() {
    wp_enqueue_style( 'geoStyle', plugins_url('css/geoStyle.css', __FILE__), false, '1.0.1', 'all');
}

add_action( 'wp_enqueue_scripts', 'load_geo_script' );
function load_geo_script(){
    if( !is_admin() ){
        // The "!" makes sure the user is _not_ logged in.
        ?>
            <div id="spiky-geo--wrapper">
                <div id="spiky-geo--inside">
                    <div id="spiky-geo--header">
                        <h2>
                            Location Notice
                        </h2>
                    </div>
                    <div id="spiky-geo--content">
                        <p>

                        </p>
                    </div>
                    <div id="spiky-geo--footer">
                        <div id="geo-buttons">
                            <button id="redirectButton">

                            </button>
                            <button id="stayButton">

                            </button>
                        </div>

                    </div>
                </div>
            </div>

        <?php
        $leftHand = array();
        $rightHand = array();
        $args = array(
            'post_type' => 'spiky-geo-key',
            'posts_per_page' => -1
        );
        $query = new WP_Query($args);
        if ($query->have_posts()):
            while ($query->have_posts()): $query->the_post();
                $leftHand[] = get_the_title();
                $rightHand[] = get_the_content();
            endwhile;

            wp_reset_postdata();
        endif;
        wp_enqueue_script( 'spiky_geo_script', plugins_url('js/geo.js', __FILE__), false, '1.0.1', 'all');
        wp_localize_script( 'spiky_geo_script', 'php_vars', array( 'leftHand' => $leftHand ,
                'rightHand' => $rightHand)
        );

    }
}



class spiky_geo_dash
{
    function activate()
    {
        $this->create_custom_post_type();
        flush_rewrite_rules();
    }
    function deactivate()
    {
        flush_rewrite_rules();
    }
    function uninstall()
    {

    }
    public function __construct()
    {
        add_action('init', array($this, 'create_custom_post_type'));
    }

    // Register Custom Post Type
    function create_custom_post_type() {

        $labels = array(
            'name'                  => 'Rules',
            'singular_name'         => 'Geolocation Rules',
            'menu_name'             => 'Rule Types',
            'name_admin_bar'        => 'Rule Type',
            'archives'              => 'Rule Archives',
            'attributes'            => 'Rule Attributes',
            'parent_item_colon'     => 'Parent Rule:',
            'all_items'             => 'All Rules',
            'add_new_item'          => 'Add New Rule',
            'add_new'               => 'Add New',
            'new_item'              => 'New Rule',
            'edit_item'             => 'Edit Rule',
            'update_item'           => 'Update Rule',
            'view_item'             => 'View Rule',
            'view_items'            => 'View Rules',
            'search_items'          => 'Search Rule',
            'not_found'             => 'Not found',
            'not_found_in_trash'    => 'Not found in Trash',
            'featured_image'        => 'Featured Image',
            'set_featured_image'    => 'Set featured image',
            'remove_featured_image' => 'Remove featured image',
            'use_featured_image'    => 'Use as featured image',
            'insert_into_item'      => 'Insert into rule',
            'uploaded_to_this_item' => 'Uploaded to this rule',
            'items_list'            => 'Rules list',
            'items_list_navigation' => 'Rules list navigation',
            'filter_items_list'     => 'Filter rules list',
        );
        $args = array(
            'label'                 => 'Geolocation Rules',
            'description'           => 'Spiky Geolocation Rules Plugin',
            'labels'                => $labels,
            'supports'              => array( 'title', 'editor', 'custom-fields', 'page-attributes' ),
            'taxonomies'            => array( 'category', 'post_tag' ),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 25,
            'menu_icon'             => 'dashicons-star-filled',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => false,
            'exclude_from_search'   => true,
            'publicly_queryable'    => true,
            'capability_type'       => 'page',
        );
        register_post_type( 'spiky-geo-key', $args );

    }
}

if (class_exists('spiky_geo_dash'))
{
    $spikyDash = new spiky_geo_dash;
}

register_activation_hook(__FILE__, array($spikyDash, 'activate'));
register_deactivation_hook(__FILE__, array($spikyDash, 'deactivate'));

