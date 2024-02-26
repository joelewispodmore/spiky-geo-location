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
add_action( 'wp_footer', 'load_geo_script' );
function load_geo_script(){
    if( !is_admin() ){
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
                                Stay here
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
                $ID = get_the_ID();
                $insideArray = get_post_meta($ID, '_geo_data')[0];
                $length = 0;
                foreach ($insideArray as $val)
                {
                    $length++;
                }
                for ($x = 0; $x < $length; $x++)
                {
                        $leftHand[] = $insideArray[$x]['region_code'];
                        $rightHand[] = $insideArray[$x]['country_codes'];
                }
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
function add_custom_meta_boxes()
{
    add_meta_box(
        'spiky_geo_key_meta_box',
        __('Spiky Geo Key Meta Box'),
        'render_spiky_geo_key_meta_box',
        'spiky-geo-key',
        'normal',
        'high'
    );
}
function render_spiky_geo_key_meta_box($post) {
    $geo_data = get_post_meta($post->ID, '_geo_data', true);
    ?>
    <style>
        .spiky-geo-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .spiky-geo-table th,
        .spiky-geo-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .spiky-geo-table th {
            background-color: #f2f2f2;
        }

        .spiky-geo-table input[type="text"] {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            /* Allow text to wrap onto new lines */
            white-space: normal;
        }

        .delete-row {
            background-color: #ff6666;
            color: white;
            border: none;
            padding: 6px;
            cursor: pointer;
            display: block;
            margin: auto;
            width: 100%;
        }

        #add_geo_row {
            margin-top: 10px;
            padding: 8px;
            background-color: #4caf50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>

    <table class="spiky-geo-table">
        <thead>
        <tr>
            <th style="width: 10%;">Region Code</th>
            <th style="width: 85%;">Country Codes</th>
            <th style="width: 5%;"></th>
        </tr>
        </thead>
        <tbody>
        <?php
        if ($geo_data) {
            foreach ($geo_data as $row) {
                echo '<tr>';
                echo '<td><input type="text" name="region_code[]" value="' . esc_attr($row['region_code']) . '"></td>';
                echo '<td><input type="text" name="country_codes[]" value="' . esc_attr($row['country_codes']) . '"></td>';
                echo '<td><button type="button" class="delete-row" data-row-id="' . esc_attr($row['id']) . '">Delete</button></td>';
                echo '</tr>';
            }
        } else {
            // Display one empty row if no data is present
            echo '<tr>';
            echo '<td><input type="text" name="region_code[]" value=""></td>';
            echo '<td><input type="text" name="country_codes[]" value=""></td>';
            echo '<td></td>';
            echo '</tr>';
        }
        ?>
        </tbody>
    </table>
    <button type="button" class="button" id="add_geo_row">Add Row</button>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('add_geo_row').addEventListener('click', function () {
                var tbody = document.querySelector('.spiky-geo-table tbody');
                var newRow = document.createElement('tr');
                newRow.innerHTML = '<td><input type="text" name="region_code[]" value=""></td>' +
                    '<td><input type="text" name="country_codes[]" value=""></td>' +
                    '<td><button type="button" class="delete-row">Delete</button></td>';
                tbody.appendChild(newRow);
            });

            // Event delegation for delete buttons
            document.querySelector('.spiky-geo-table tbody').addEventListener('click', function (event) {
                if (event.target.classList.contains('delete-row')) {
                    var row = event.target.closest('tr');
                    row.parentNode.removeChild(row);
                }
            });
        });
    </script>
    <?php
}
function save_spiky_geo_key_meta_data($post_id) {
    if (isset($_POST['region_code']) && isset($_POST['country_codes'])) {
        $region_codes = array_map('sanitize_text_field', $_POST['region_code']);
        $country_codes = array_map('sanitize_text_field', $_POST['country_codes']);

        $geo_data = array();

        foreach ($region_codes as $key => $region_code) {
            // Check if both region_code and country_codes are not empty
            if (!empty($region_code) || !empty($country_codes[$key])) {
                $geo_data[] = array(
                    'region_code' => $region_code,
                    'country_codes' => isset($country_codes[$key]) ? $country_codes[$key] : '',
                );
            }
        }

        update_post_meta($post_id, '_geo_data', $geo_data);
    }
}
add_action('add_meta_boxes', 'add_custom_meta_boxes');
add_action('save_post', 'save_spiky_geo_key_meta_data');
if (class_exists('spiky_geo_dash'))
{
    $spikyDash = new spiky_geo_dash;
}
register_activation_hook(__FILE__, array($spikyDash, 'activate'));
register_deactivation_hook(__FILE__, array($spikyDash, 'deactivate'));

