<?php
/**
 * Frontend integration for NextGen Image Optimizer.
 *
 * Wraps images with <picture> and adds AVIF/WebP sources where available.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class NGIO_Frontend {

    /**
     * Constructor.
     */
    public function __construct() {
        add_filter( 'wp_get_attachment_image', array( $this, 'filter_attachment_image' ), 10, 4 );
    }

    /**
     * Get plugin settings (merged with defaults).
     *
     * @return array
     */
    protected function get_settings() {
        $defaults = array(
            'enable_webp'     => 1,
            'enable_avif'     => 1,
            'quality'         => 82,
            'auto_on_upload'  => 1,
            'enable_picture'  => 1,
        );

        $saved = get_option( 'ngio_settings', array() );
        if ( ! is_array( $saved ) ) {
            $saved = array();
        }

        return wp_parse_args( $saved, $defaults );
    }

    /**
     * Filter attachment image HTML to wrap with <picture>.
     *
     * @param string       $html           The HTML for the image.
     * @param int          $attachment_id  Attachment ID.
     * @param string|array $size           Image size.
     * @param bool         $icon           Whether it is an icon.
     *
     * @return string
     */
    public function filter_attachment_image( $html, $attachment_id, $size, $icon ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
        // Admin, feed vs. üzerinde oynamayalım.
        if ( is_admin() || is_feed() ) {
            return $html;
        }

        // Eğer zaten <picture> ise, bir daha sarmayalım.
        if ( false !== strpos( $html, '<picture' ) ) {
            return $html;
        }

        $settings = $this->get_settings();

        // Frontend picture özelliği kapalıysa dokunma.
        if ( empty( $settings['enable_picture'] ) ) {
            return $html;
        }

        // Sadece JPEG/JPG/PNG.
        $mime = get_post_mime_type( $attachment_id );
        if ( ! in_array( $mime, array( 'image/jpeg', 'image/jpg', 'image/png' ), true ) ) {
            return $html;
        }

        $meta = wp_get_attachment_metadata( $attachment_id );
        if ( empty( $meta ) || ! is_array( $meta ) ) {
            return $html;
        }

        // Hangi size için HTML üretiliyor?
        $size_key = 'full';

        if ( is_string( $size ) && 'full' !== $size && isset( $meta['sizes'][ $size ] ) ) {
            $size_key = $size;
        }

        if ( empty( $meta['ngio_converted'][ $size_key ] ) || ! is_array( $meta['ngio_converted'][ $size_key ] ) ) {
            // Bu size için hiç dönüşüm yapılmamışsa dokunma.
            return $html;
        }

        $converted  = $meta['ngio_converted'][ $size_key ];
        $upload_dir = wp_upload_dir();

        if ( ! empty( $upload_dir['error'] ) ) {
            return $html;
        }

        $baseurl = trailingslashit( $upload_dir['baseurl'] );

        $avif_url = '';
        $webp_url = '';

        if ( ! empty( $settings['enable_avif'] ) && ! empty( $converted['avif'] ) ) {
            $avif_url = $baseurl . ltrim( $converted['avif'], '/\\' );
        }

        if ( ! empty( $settings['enable_webp'] ) && ! empty( $converted['webp'] ) ) {
            $webp_url = $baseurl . ltrim( $converted['webp'], '/\\' );
        }

        // Kullanabileceğimiz format yoksa hiç dokunma.
        if ( empty( $avif_url ) && empty( $webp_url ) ) {
            return $html;
        }

        // Orijinal <img> tag'ini HTML içinden çıkaralım.
        if ( ! preg_match( '/<img\s[^>]*>/i', $html, $img_match ) ) {
            return $html;
        }

        $img_tag = $img_match[0];

        // picture + source dizilimi: önce AVIF, sonra WebP, en altta orijinal <img>.
        $sources = '';

        if ( $avif_url ) {
            $sources .= '<source type="image/avif" srcset="' . esc_url( $avif_url ) . '" />';
        }

        if ( $webp_url ) {
            $sources .= '<source type="image/webp" srcset="' . esc_url( $webp_url ) . '" />';
        }

        $picture = '<picture class="ngio-picture">' . $sources . $img_tag . '</picture>';

        // Eğer orijinal HTML img'den başka sarmalayıcılar da içeriyorsa (örneğin <figure>),
        // img'yi picture ile değiştirelim.
        $result = str_replace( $img_tag, $picture, $html );

        // Güvenli olması için aşırı uç durumlarda fallback olarak sadece picture döndürelim.
        if ( $result === $html ) {
            $result = $picture;
        }

        return $result;
    }
}
