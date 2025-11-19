=== NextGen Image Optimizer ===
Contributors: your-wporg-username
Donate link: https://hedefhosting.com.tr/
Tags: image optimization, webp, avif, performance, images
Requires at least: 6.5
Tested up to: 6.7
Requires PHP: 8.1
Stable tag: 0.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Convert JPEG and PNG images to modern WebP and AVIF formats on upload or in bulk, and optionally serve them using <picture> tags.

== Description ==

NextGen Image Optimizer is a lightweight yet powerful image optimization plugin that converts your JPEG and PNG uploads into modern WebP and AVIF formats.

It focuses on three main goals:

* **Better performance** – Serve lighter images without visibly losing quality.
* **Modern formats** – Use WebP and AVIF when supported by the server and browser.
* **Simple control** – Configure how images are converted and served from a single settings page.

= Features =

* Automatically convert JPEG/JPG and PNG images to WebP and AVIF on upload.
* Bulk optimization tool for existing Media Library images.
* Server capabilities checker for GD / Imagick WebP and AVIF support.
* Optional frontend integration using `<picture>` tags with WebP/AVIF `<source>` elements.
* Adjustable compression quality (0–100).
* Works with the built-in Media Library and `wp_get_attachment_image()` / featured images.

= How it works =

1. When you upload a JPEG or PNG image, the plugin can automatically generate `.webp` and `.avif` versions (if your server supports them).
2. For existing images, you can use the **Media → Bulk Image Optimization** screen to process the Media Library in batches.
3. On the frontend, you can enable the `<picture>` integration so that supported browsers will load AVIF / WebP, while older browsers still get the original image.

= Requirements =

* PHP 8.1 or higher (for AVIF support through GD or Imagick).
* WordPress 6.5 or higher.

== Installation ==

1. Upload the `nextgen-image-optimizer` folder to the `/wp-content/plugins/` directory, or install it via the WordPress.org plugin repository.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. Go to **Settings → Image Optimizer** to configure conversion options.
4. (Optional) Go to **Media → Bulk Image Optimization** to convert existing images.

== Frequently Asked Questions ==

= Does this plugin modify my original image files? =

No. The plugin keeps your original JPEG/PNG files and creates additional `.webp` and `.avif` versions in the same upload folder.

= Will it work if my server does not support WebP or AVIF? =

If your server cannot generate WebP and/or AVIF, the plugin will show this in the "Server Support" section on the settings page. In that case, only the supported formats will be generated, or none at all.

= How does the `<picture>` option affect my theme? =

When enabled, the plugin wraps images output by `wp_get_attachment_image()` and featured images in a `<picture>` tag, adding `<source>` elements for WebP and AVIF. The original `<img>` tag remains inside, so themes usually continue to work as expected.

= Can I remove the generated files if I uninstall the plugin? =

By default, uninstalling the plugin removes only its settings. The generated image files remain in the uploads directory. This is intentional to avoid breaking existing content. You can remove them manually if necessary.

== Screenshots ==

1. Settings page showing general options and server support.
2. Bulk optimization screen in the Media menu.

== Changelog ==

= 0.1.0 =
* Initial release: automatic WebP/AVIF conversion on upload, bulk optimization tool, server support checker, and optional `<picture>` frontend integration.

== Upgrade Notice ==

= 0.1.0 =
Initial release.
