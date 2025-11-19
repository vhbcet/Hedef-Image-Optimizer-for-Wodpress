<?php
/**
 * Frontend integration for NextGen Image Optimizer.
 *
 * - Wraps images in <picture> with AVIF / WebP <source> when available
 * - Hooks into wp_get_attachment_image, post_thumbnail_html and the_content
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class NGIO_Frontend {

    /**
     * @var array
     */
    protected $settings = array();

    /**
     * Constructor.
     */
    public function __construct() {
        $this->settings = $this->get_settings();

        if ( empty( $this->settings['enable_picture'] ) ) {
            // Frontend picture entegrasyonu kapalı ise hiçbir hook eklemeyelim.
            return;
        }

        // Çekirdek WP image çıktıları.
        add_filter( 'wp_get_attachment_image', array( $this, 'filter_attachment_image_html' ), 20, 5 );
        add_filter( 'post_thumbnail_html', array( $this, 'filter_post_thumbnail_html' ), 20, 5 );

        // İçerikteki wp-image-123 sınıflı <img> etiketleri.
        add_filter( 'the_content', array( $this, 'filter_content_images' ), 20 );
    }

    /**
     * Settings'i basit haliyle çek.
     *
     * @return array
     */
    protected function get_settings() {
        $defaults = array(
            'enable_picture' => 1,
            'enable_webp'    => 1,
            'enable_avif'    => 1,
        );

        $saved = get_option( 'ngio_settings', array() );
        if ( ! is_array( $saved ) ) {
            $saved = array();
        }

        $settings = wp_parse_args( $saved, $defaults );

        $settings['enable_picture'] = (int) ! empty( $settings['enable_picture'] );
        $settings['enable_webp']    = (int) ! empty( $settings['enable_webp'] );
        $settings['enable_avif']    = (int) ! empty( $settings['enable_avif'] );

        return $settings;
    }

    /**
     * wp_get_attachment_image filtresi.
     */
    public function filter_attachment_image_html( $html, $attachment_id, $size, $icon, $attr ) {
        return $this->build_picture_markup( $html, $attachment_id );
    }

    /**
     * post_thumbnail_html filtresi.
     */
    public function filter_post_thumbnail_html( $html, $post_id, $post_thumbnail_id, $size, $attr ) {
        if ( ! $post_thumbnail_id ) {
            return $html;
        }

        return $this->build_picture_markup( $html, $post_thumbnail_id );
    }

    /**
     * the_content filtresi: wp-image-123 class'lı <img> etiketlerini yakalar.
     */
    public function filter_content_images( $content ) {
        if ( false === strpos( $content, '<img' ) ) {
            return $content;
        }

        $self = $this;

        $content = preg_replace_callback(
            '/<img[^>]+class=["\'][^"\']*wp-image-(\d+)[^"\']*["\'][^>]*>/i',
            function ( $matches ) use ( $self ) {
                $img_html      = $matches[0];
                $attachment_id = (int) $matches[1];

                return $self->build_picture_markup( $img_html, $attachment_id );
            },
            $content
        );

        return $content;
    }

    /**
     * Verilen <img> HTML'ini, mevcutsa AVIF/WEBP source'ları olan <picture> ile sarar.
     *
     * @param string $html
     * @param int    $attachment_id
     *
     * @return string
     */
    protected function build_picture_markup( $html, $attachment_id ) {
        if ( empty( $this->settings['enable_picture'] ) ) {
            return $html;
        }

        // Zaten <picture> ile sarılıysa bir daha dokunma.
        if ( false !== stripos( $html, '<picture' ) ) {
            return $html;
        }

        $attachment_id = (int) $attachment_id;
        if ( $attachment_id <= 0 ) {
            return $html;
        }

        $mime = get_post_mime_type( $attachment_id );
        if ( ! in_array( $mime, array( 'image/jpeg', 'image/jpg', 'image/png' ), true ) ) {
            return $html;
        }

        $meta = wp_get_attachment_metadata( $attachment_id );
        if ( empty( $meta ) || ! is_array( $meta ) || empty( $meta['ngio'] ) || empty( $meta['ngio']['formats'] ) ) {
            // Bu görsel için henüz next-gen üretilmemiş.
            return $html;
        }

                $formats = (array) $meta['ngio']['formats'];

        // Eski meta içinde hem webp hem avif olabilir; ayarlarla uyumlu son listeyi çıkaralım.
        $has_webp = in_array( 'webp', $formats, true ) && ! empty( $this->settings['enable_webp'] );
        // AVIF sadece WebP kapalıysa kullanılacak:
        $has_avif = in_array( 'avif', $formats, true ) && ! empty( $this->settings['enable_avif'] ) && empty( $this->settings['enable_webp'] );

        $final_formats = array();
        if ( $has_avif ) {
            $final_formats[] = 'avif';
        } elseif ( $has_webp ) {
            $final_formats[] = 'webp';
        }

        if ( empty( $final_formats ) ) {
            return $html;
        }

        // <img src="..."> içindeki src'yi çekelim.
        if ( ! preg_match( '/<img[^>]+src=["\']([^"\']+)["\']/i', $html, $m ) ) {
            return $html;
        }

        $src = $m[1];

        $sources = '';

        if ( in_array( 'avif', $final_formats, true ) ) {
            $sources .= '<source type="image/avif" srcset="' . esc_url( $src . '.avif' ) . '">';
        }

        if ( in_array( 'webp', $final_formats, true ) ) {
            $sources .= '<source type="image/webp" srcset="' . esc_url( $src . '.webp' ) . '">';
        }


        if ( ! $sources ) {
            return $html;
        }

        return '<picture class="ngio-picture">' . $sources . $html . '</picture>';
    }
}
