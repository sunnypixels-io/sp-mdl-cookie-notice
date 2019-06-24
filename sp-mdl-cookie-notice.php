<?php
/**
 * Plugin Name:       MDL Cookie Notice
 * Plugin URI:        https://sunnypixels.io/extension/mdl-cookie-notice/
 * Description:       Add a Cookie notice on your website to inform users that you are using cookies to comply with the EU cookie law GDPR regulations.
 * Version:           1.0.0
 * Author:            SunnyPixels
 * Author URI:        https://sunnypixels.io/
 * Requires at least: 4.5.0
 * Tested up to:      5.2.2
 *
 * Text Domain: sp-mdl-cookie-notice
 * Domain Path: /languages/
 *
 * @package SP_MDL_Cookie_Notice
 * @category Core
 * @author SunnyPixels
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Returns the main instance of SP_MDL_Cookie_Notice to prevent the need to use globals.
 *
 * @return object SP_MDL_Cookie_Notice
 * @since 1.0.0
 */
function SP_MDL_Cookie_Notice()
{
    return SP_MDL_Cookie_Notice::instance();
}

SP_MDL_Cookie_Notice();

/**
 * @class   SP_MDL_Cookie_Notice
 * @version 1.0.0
 * @since   1.0.0
 * @package SP_MDL_Cookie_Notice
 */
final class SP_MDL_Cookie_Notice
{
    /**
     * SP_MDL_Cookie_Notice The single instance of SP_MDL_Cookie_Notice.
     * @var    object
     * @access private
     * @since  1.0.0
     */
    private static $_instance = null;

    /**
     * The token.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $token;

    /**
     * The version number.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $version;

    // Admin - Start
    /**
     * The admin object.
     * @var     object
     * @access  public
     * @since   1.0.0
     */
    public $admin;

    /**
     * Constructor function.
     * @access  public
     * @return  void
     * @since   1.0.0
     */
    public function __construct()
    {
        $this->token = 'sp-mdl-cookie-notice';
        $this->plugin_url = plugin_dir_url(__FILE__);
        $this->plugin_path = plugin_dir_path(__FILE__);
        $this->version = '1.0.0';

        register_activation_hook(__FILE__, array($this, 'install'));

        add_action('init', array($this, 'load_plugin_textdomain'));

        add_action('init', array($this, 'setup'));

        // TODO create plugin updater API
        // add_action('init', array($this, 'updater'), 1);

    }

    /**
     * Initialize License Updater.
     * Load Updater initialize.
     * @return void
     */
    public function updater()
    {
        // Plugin Updater Code
        if (class_exists('SunnyPixels_Plugin_Updater')) {
            $license = new SunnyPixels_Plugin_Updater(__FILE__, 'Cookie Notice', $this->version, 'SunnyPixels');
        }
    }

    /**
     * Main SP_MDL_Cookie_Notice Instance
     *
     * Ensures only one instance of SP_MDL_Cookie_Notice is loaded or can be loaded.
     *
     * @return Main SP_MDL_Cookie_Notice instance
     * @see SP_MDL_Cookie_Notice()
     * @since 1.0.0
     * @static
     */
    public static function instance()
    {
        if (is_null(self::$_instance))
            self::$_instance = new self();
        return self::$_instance;
    }

    /**
     * Load the localisation file.
     * @access  public
     * @return  void
     * @since   1.0.0
     */
    public function load_plugin_textdomain()
    {
        load_plugin_textdomain('sp-mdl-cookie-notice', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }

    /**
     * Cloning is forbidden.
     *
     * @since 1.0.0
     */
    public function __clone()
    {
        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), $version);
    }

    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.0.0
     */
    public function __wakeup()
    {
        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), $version);
    }

    /**
     * Installation.
     * Runs on activation. Logs the version number and assigns a notice message to a WordPress option.
     * @access  public
     * @return  void
     * @since   1.0.0
     */
    public function install()
    {
        $this->_log_version_number();
    }

    /**
     * Log the plugin version number.
     * @access  private
     * @return  void
     * @since   1.0.0
     */
    private function _log_version_number()
    {
        // Log the version number.
        update_option($this->token . '-version', $this->version);
    }

    /**
     * Setup all the things.
     * Only executes if Material Design Lite or a child theme using Material Design Lite as a parent is active and the extension specific filter returns true.
     * @return void
     */
    public function setup()
    {
        $theme = wp_get_theme();

        if ('Material Design Lite' == $theme->name || 'material-design-lite' == $theme->template) {
            add_filter('sp_localize_array', array($this, 'localize_array'));
            add_action('wp_enqueue_scripts', array($this, 'public_scripts'), 999);

            if (is_admin()) {
                add_action('admin_enqueue_scripts', array($this, 'admin_scripts'), 999);
            }

            $this->_kirki_customizer();
        }
    }

    /**
     * Check is current user from Europe
     * @return bool|null
     * @since  1.0.0
     */
    private function _is_europe()
    {
        if (get_theme_mod('cookie_notice_only_europe', false)) {
            require_once plugin_dir_path(__FILE__) . '/includes/geoplugin.class.php';

            $geoplugin = new geoPlugin();
            $geoplugin->locate();

            return $geoplugin->inEU;
        }

        // show for all visitors
        return true;
    }

    /**
     * Customizer Controls and settings
     *
     * @since 1.0.0
     */
    private function _kirki_customizer()
    {
        if (class_exists('Kirki')) {

            Kirki::add_section('sp_mdl_cookie_notice', array(
                'title' => esc_html__('Cookie Notice', 'sp-mdl-cookie-notice'),
                'priority' => 199,
            ));

            Kirki::add_field('material_design_lite', [
                'type' => 'toggle',
                'settings' => 'cookie_notice_enabled',
                'label' => esc_html__('Enable Cookies notice', 'sp-mdl-cookie-notice'),
                'section' => 'sp_mdl_cookie_notice',
                'default' => '1',
                'priority' => 120
            ]);

            Kirki::add_field('material_design_lite', [
                'type' => 'toggle',
                'settings' => 'cookie_notice_only_europe',
                'label' => esc_html__('Show only for Europe', 'sp-mdl-cookie-notice'),
                'section' => 'sp_mdl_cookie_notice',
                'default' => '0',
                'priority' => 121,
                'active_callback' => [
                    [
                        'setting' => 'cookie_notice_enabled',
                        'operator' => '==',
                        'value' => true,
                    ]
                ],
            ]);

            Kirki::add_field('material_design_lite', [
                'type' => 'custom',
                'settings' => 'cookie_notice_title',
                'section' => 'sp_mdl_cookie_notice',
                'default' => kirki_custom_title('Cookie Notice'),
                'priority' => 130,
                'active_callback' => [
                    [
                        'setting' => 'cookie_notice_enabled',
                        'operator' => '==',
                        'value' => true,
                    ]
                ],
            ]);

            Kirki::add_field('material_design_lite', [
                'type' => 'text',
                'settings' => 'cookie_notice_popup_title',
                'label' => esc_html__('Title', 'sp-mdl-cookie-notice'),
                'section' => 'sp_mdl_cookie_notice',
                'default' => __('Cookies', 'sp-mdl-cookie-notice'),
                'priority' => 131,
                'active_callback' => [
                    [
                        'setting' => 'cookie_notice_enabled',
                        'operator' => '==',
                        'value' => true,
                    ]
                ],
            ]);

            Kirki::add_field('material_design_lite', [
                'type' => 'editor',
                'settings' => 'cookie_notice_popup_content',
                'label' => esc_html__('Content', 'sp-mdl-cookie-notice'),
                'section' => 'sp_mdl_cookie_notice',
                'default' => __('By continuing to use this website, you consent to the use of cookies in accordance with our Cookie Policy.', 'sp-mdl-cookie-notice'),
                'priority' => 132,
                'active_callback' => [
                    [
                        'setting' => 'cookie_notice_enabled',
                        'operator' => '==',
                        'value' => true,
                    ]
                ],
            ]);

            Kirki::add_field('material_design_lite', [
                'type' => 'custom',
                'settings' => 'cookie_notice_accept_cookies_title',
                'section' => 'sp_mdl_cookie_notice',
                'default' => kirki_custom_title('Accept cookies'),
                'priority' => 140,
                'active_callback' => [
                    [
                        'setting' => 'cookie_notice_enabled',
                        'operator' => '==',
                        'value' => true,
                    ]
                ],
            ]);

            Kirki::add_field('material_design_lite', [
                'type' => 'select',
                'settings' => 'cookie_notice_expiry',
                'label' => esc_html__('Cookie Expiry', 'sp-mdl-cookie-notice'),
                'section' => 'sp_mdl_cookie_notice',
                'default' => '2592000',
                'priority' => 141,
                'multiple' => 1,
                'choices' => [
                    '3600' => esc_html__('An Hour', 'sp-mdl-cookie-notice'),
                    '86400' => esc_html__('1 Day', 'sp-mdl-cookie-notice'),
                    '604800' => esc_html__('1 Week', 'sp-mdl-cookie-notice'),
                    '2592000' => esc_html__('1 Month', 'sp-mdl-cookie-notice'),
                    '7862400' => esc_html__('3 Month', 'sp-mdl-cookie-notice'),
                    '15811200' => esc_html__('6 Month', 'sp-mdl-cookie-notice'),
                    '31536000' => esc_html__('1 Year', 'sp-mdl-cookie-notice'),
                    '2147483647' => esc_html__('Infinity', 'sp-mdl-cookie-notice'),
                ],
                'active_callback' => [
                    [
                        'setting' => 'cookie_notice_enabled',
                        'operator' => '==',
                        'value' => true,
                    ]
                ],
            ]);

            Kirki::add_field('material_design_lite', [
                'type' => 'text',
                'settings' => 'cookie_notice_positive',
                'label' => esc_html__('Accept button', 'sp-mdl-cookie-notice'),
                'section' => 'sp_mdl_cookie_notice',
                'default' => __('Accept', 'sp-mdl-cookie-notice'),
                'priority' => 142,
                'active_callback' => [
                    [
                        'setting' => 'cookie_notice_enabled',
                        'operator' => '==',
                        'value' => true,
                    ]
                ],
            ]);

            Kirki::add_field('material_design_lite', [
                'type' => 'custom',
                'settings' => 'cookie_notice_learn_more_title',
                'section' => 'sp_mdl_cookie_notice',
                'default' => kirki_custom_title('Learn more option'),
                'priority' => 150,
                'active_callback' => [
                    [
                        'setting' => 'cookie_notice_enabled',
                        'operator' => '==',
                        'value' => true,
                    ]
                ],
            ]);

            Kirki::add_field('material_design_lite', [
                'type' => 'toggle',
                'settings' => 'cookie_notice_negative',
                'label' => esc_html__('Enable Learn more link', 'sp-mdl-cookie-notice'),
                'section' => 'sp_mdl_cookie_notice',
                'default' => '0',
                'priority' => 151,
                'active_callback' => [
                    [
                        'setting' => 'cookie_notice_enabled',
                        'operator' => '==',
                        'value' => true,
                    ]
                ],
            ]);

            Kirki::add_field('material_design_lite', [
                'type' => 'text',
                'settings' => 'cookie_notice_negative_title',
                'label' => esc_html__('Learn more button', 'sp-mdl-cookie-notice'),
                'section' => 'sp_mdl_cookie_notice',
                'default' => __('Learn more', 'sp-mdl-cookie-notice'),
                'priority' => 152,
                'active_callback' => [
                    [
                        'setting' => 'cookie_notice_enabled',
                        'operator' => '==',
                        'value' => true,
                    ], [
                        'setting' => 'cookie_notice_negative',
                        'operator' => '==',
                        'value' => true,
                    ]
                ],
            ]);

            Kirki::add_field('material_design_lite', [
                'type' => 'link',
                'settings' => 'cookie_notice_negative_url',
                'label' => __('Url', 'sp-mdl-cookie-notice'),
                'section' => 'sp_mdl_cookie_notice',
                'default' => home_url('/cookies'),
                'priority' => 153,
                'active_callback' => [
                    [
                        'setting' => 'cookie_notice_enabled',
                        'operator' => '==',
                        'value' => true,
                    ], [
                        'setting' => 'cookie_notice_negative',
                        'operator' => '==',
                        'value' => true,
                    ]
                ],
            ]);

            Kirki::add_field('material_design_lite', [
                'type' => 'custom',
                'settings' => 'cookie_notice_reset_title',
                'section' => 'sp_mdl_cookie_notice',
                'default' => kirki_custom_title('Reset cookies'),
                'priority' => 160,
                'active_callback' => [
                    [
                        'setting' => 'cookie_notice_enabled',
                        'operator' => '==',
                        'value' => true,
                    ]
                ],
            ]);

            Kirki::add_field('material_design_lite', [
                'type' => 'custom',
                'settings' => 'cookie_notice_reset_button',
                'section' => 'sp_mdl_cookie_notice',
                'description' => esc_html__('You can reset cookies for your browser to show up cookies notice again.', 'sp-mdl-cookie-notice'),
                'default' => kirki_custom_button('Delete cookies', 'button-link-delete js-mdl-customizer-reset-cookies'),
                'priority' => 161,
                'active_callback' => [
                    [
                        'setting' => 'cookie_notice_enabled',
                        'operator' => '==',
                        'value' => true,
                    ]
                ],
            ]);

        }
    }

    /**
     * Localize array
     *
     * @since 1.0.0
     */
    public function localize_array($array)
    {
        if (get_theme_mod('cookie_notice_enabled', true)) :

            $array['isEU'] = $this->_is_europe();

            $array['cookieNotice'] = array(
                'show' => get_theme_mod('cookie_notice_enabled', true),
                // TODO: add more options to cookie style
                'style' => get_theme_mod('cookie_notice_style', 'popup'),
                'title' => get_theme_mod('cookie_notice_popup_title', __('Cookies', 'material-design-lite')),
                'content' => esc_html__(get_theme_mod('cookie_notice_popup_content', __('By continuing to use this website, you consent to the use of cookies in accordance with our Cookie Policy.', 'material-design-lite'))),
                'expires' => get_theme_mod('cookie_notice_expiry', 2592000),
                'positive' => get_theme_mod('cookie_notice_positive', __('Accept', 'material-design-lite')),
            );

            if (get_theme_mod('cookie_notice_negative', false)) :
                $array['cookieNotice']['negative'] = array(
                    'url' => get_theme_mod('cookie_notice_negative_url', '#'),
                    'title' => get_theme_mod('cookie_notice_negative_title', __('Learn more', 'material-design-lite'))
                );
            endif;

        endif;

        return $array;
    }

    /**
     * Enqueue public styles and scripts.
     *
     * @since 1.0.0
     */
    public function public_scripts()
    {
        // load wpCookies helper
        wp_enqueue_script('utils');

        // Load main stylesheet
        wp_enqueue_style('sp-mdl-cookie-notice-style', plugins_url('/assets/css/public.css', __FILE__), array('sp-mdl-style'));

        // Load custom js methods.
        wp_enqueue_script('sp-mdl-cookie-notice-scripts', plugins_url('/assets/js/public.js', __FILE__), array('jquery', 'sp-mdl-scripts'), null, true);
    }

    /**
     * Enqueue admin styles and scripts.
     *
     * @since 1.0.0
     */
    public function admin_scripts()
    {
        // Load main stylesheet
        // wp_enqueue_style('sp-mdl-style', plugins_url('/assets/css/public.css', __FILE__));

        // Load custom js methods.
        wp_enqueue_script('sp-mdl-cookie-notice-js-admin', plugins_url('/assets/js/admin.js', __FILE__), array('jquery'), null, true);
    }

}