<?php
/**
 * Core class for NextGen Image Optimizer.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class NGIO_Core {

    /**
     * Singleton instance.
     *
     * @var NGIO_Core|null
     */
    private static $instance = null;

    /**
     * Converter instance.
     *
     * @var NGIO_Converter|null
     */
    private $converter = null;

    /**
     * Private constructor.
     */
    private function __construct() {
        // Bilinçli olarak boş. run() içinde hook'lar eklenecek.
    }

    /**
     * Get singleton instance.
     *
     * @return NGIO_Core
     */
    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Plugin activation callback.
     */
    public static function activate() {
        // Varsayılan ayarları ekle.
        if ( ! get_option( 'ngio_settings' ) ) {
            add_option(
                'ngio_settings',
                array(
                    'enable_webp'     => 1,
                    'enable_avif'     => 1,
                    'quality'         => 82,
                    'auto_on_upload'  => 1,
                    'enable_picture'  => 1,
                )
            );
        }
    }

    /**
     * Plugin deactivation callback.
     */
    public static function deactivate() {
        // Şimdilik özel bir işlem yok.
    }

    /**
     * Get converter instance.
     *
     * @return NGIO_Converter
     */
    public function get_converter() {
        if ( null === $this->converter ) {
            require_once NGIO_PLUGIN_DIR . 'includes/class-ngio-converter.php';
            $this->converter = new NGIO_Converter();
        }

        return $this->converter;
    }

    /**
     * Run the plugin – add hooks.
     */
    public function run() {
        add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

        // Converter her ortamda yüklensin (sadece admin değil).
        $converter = $this->get_converter();

        // Upload sonrası metadata oluşurken optimize et.
        add_filter(
            'wp_generate_attachment_metadata',
            array( $converter, 'handle_attachment_metadata' ),
            10,
            2
        );

        // Frontend picture / source entegrasyonu.
        require_once NGIO_PLUGIN_DIR . 'includes/class-ngio-frontend.php';
        new NGIO_Frontend();

        // Admin tarafı.
        if ( is_admin() ) {
            require_once NGIO_PLUGIN_DIR . 'includes/class-ngio-admin.php';
            new NGIO_Admin();

            require_once NGIO_PLUGIN_DIR . 'includes/class-ngio-bulk.php';
            new NGIO_Bulk();
        }
    }

    /**
     * Load plugin textdomain.
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'nextgen-image-optimizer',
            false,
            dirname( plugin_basename( NGIO_PLUGIN_FILE ) ) . '/languages'
        );
    }
}
