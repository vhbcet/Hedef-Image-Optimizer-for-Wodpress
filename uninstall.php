<?php
/**
 * Uninstall handler for NextGen Image Optimizer.
 *
 * Bu dosya eklenti WordPress üzerinden tamamen kaldırıldığında çalışır.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Ayarları temizle.
delete_option( 'ngio_settings' );
