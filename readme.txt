=== NextGen Image Optimizer ===
Contributors: hedefhosting
Donate link: https://hedefhosting.com.tr/
Tags: image optimization, images, webp, avif, performance, nextgen, media library, picture
Requires at least: 6.5
Tested up to: 6.7
Requires PHP: 8.1
Stable tag: 0.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Convert JPEG and PNG images to modern WebP and AVIF formats on upload or in bulk, and optionally serve them using <picture> tags. 100% local – no external API.

== Description ==

NextGen Image Optimizer is a lightweight yet powerful image optimization plugin that converts your JPEG and PNG uploads into modern WebP and AVIF formats – **entirely on your own server**.

It focuses on three main goals:

* **Better performance** – Serve lighter images without visibly losing quality.
* **Modern formats** – Use WebP and AVIF when supported by the server and browser.
* **Simple control** – Configure how images are converted and served from a single, modern settings page.

The plugin detects your server capabilities (GD / Imagick, WebP / AVIF support), lets you pick which formats to generate, controls quality and resizing, and offers both automatic on-upload optimization and a dedicated bulk optimizer for existing media.

It also integrates with the Media Library so you can see **per-image savings**, re-optimize single images, and monitor how much space you’ve saved overall.

= Key highlights =

* 100% local conversion – your images never leave your server.
* WebP and AVIF generation (GD and/or Imagick, when available).
* Automatic optimization on upload (optional).
* Bulk optimization screen with progress bar, activity log and savings donut.
* Media Library column and per-image tools (stats + “Re-optimize now”).
* Frontend `<picture>` / `srcset` integration (optional).
* Clean, modern admin UI with server status and media overview cards.
* Ready for translation and ships with a full Turkish (tr_TR) translation.

= Local-only by design =

NextGen Image Optimizer does **not** send your images to any external service or API.

All conversions are performed using PHP GD and/or Imagick on your own server. This is ideal if you:

* Need to keep media on-premise or on your own infrastructure.
* Want predictable performance and no external API billing.
* Prefer a simple, transparent stack that you fully control.

= Features =

**Conversion & automation**

* Automatically convert JPEG/JPG and PNG images to WebP and/or AVIF on upload.
* Bulk optimization tool for existing Media Library images (runs in small batches).
* Per-image optimization tools on the attachment screen (optimize / re-optimize).
* Toggle for “optimize on upload” so you can control when auto-conversion runs.
* Optional backup of original files to safely change quality settings later.
* Exclusion rules by filename/path (e.g. skip logos, icons, or specific folders).

**Frontend delivery**

* Optional frontend integration using `<picture>` tags with WebP/AVIF `<source>` elements.
* Keeps the original `<img>` tag as a fallback for older browsers.
* Plays nicely with `wp_get_attachment_image()`, featured images, and most themes.
* Designed to cooperate with responsive `srcset` attributes where possible.

**Quality & resizing**

* Adjustable compression quality (0–100) for WebP and AVIF.
* Recommended ranges explained in the UI (e.g. 80–85 for most sites).
* Optional max width/height for downscaling very large images before conversion.
* Option to strip EXIF/IPTC metadata from next-gen copies (originals remain untouched).

**Media & reporting**

* Modern settings page with hero header, capability badges, and media overview stats.
* “Server support” card showing GD/Imagick WebP/AVIF availability.
* “Media overview” showing total items, optimized items and estimated savings.
* Bulk optimizer overview with donut chart showing your global space saved (%).
* Bulk activity log listing which images were processed in each batch.

**Media Library integration**

* “NextGen” column in the Media Library list view:
  * shows whether the image is optimized,
  * displays original size, next-gen size and space saved,
  * offers a “Re-optimize now” action.
* Per-attachment meta box for single image control and status.

**Internationalization**

* Text-domain: `nextgen-image-optimizer`
* Fully translatable.
* Includes a complete **Turkish (tr_TR)** translation out of the box.

= How it works =

1. When you upload a JPEG or PNG image, the plugin can automatically generate `.webp` and `.avif` versions (if your server supports them and the formats are enabled in settings).
2. For existing images, you can use the **Media → Bulk Optimization (NGIO)** screen to process the Media Library in batches. A progress bar and activity log show what’s happening.
3. On the frontend, you can enable the `<picture>` integration so that supported browsers will load AVIF / WebP, while older browsers still get the original image.
4. The plugin stores conversion statistics in attachment metadata so it can show you per-image and global savings (original size, next-gen size and space saved).

= Requirements =

* PHP 8.1 or higher.
* WordPress 6.5 or higher.
* PHP GD and/or Imagick extensions, ideally compiled with:
  * WebP support (for WebP output),
  * AVIF support (for AVIF output).
* Sufficient memory and CPU resources on your server for image processing.

If a particular format (e.g. AVIF) is not supported by your stack, the settings page will show this under **Server support**, and the plugin will only generate the formats that are actually available.

== Installation ==

1. Upload the `nextgen-image-optimizer` folder to the `/wp-content/plugins/` directory, or install it via the WordPress.org plugin repository.
2. Activate the plugin through the “Plugins” menu in WordPress.
3. Go to **Settings → Image Optimizer** to configure:
   * which formats to generate (WebP/AVIF),
   * quality and resize options,
   * on-upload optimization and `<picture>` delivery.
4. (Optional) Go to **Media → Bulk Optimization (NGIO)** to convert existing images in your Media Library.

== Frequently Asked Questions ==

= Does this plugin modify my original image files? =

No. The plugin keeps your original JPEG/PNG files and creates additional `.webp` and `.avif` versions in the same upload folder.

If you enable the “backup originals” option, a separate backup of the original file is kept so you can safely change quality/resizing and re-run optimization.

= Will it work if my server does not support WebP or AVIF? =

If your server cannot generate WebP and/or AVIF, the plugin will show this in the **Server support** section on the settings page. In that case:

* only the supported formats will be generated, or  
* if none are available, the plugin will not attempt conversion.

Your site will continue to use the original images as usual.

= Does the plugin send my images to any external API or service? =

No. All processing is done locally using PHP GD and/or Imagick on your own server. Your images are never sent to third-party servers.

= How does the `<picture>` option affect my theme? =

When enabled, the plugin wraps images output by `wp_get_attachment_image()` and featured images in a `<picture>` tag, adding `<source>` elements for WebP and AVIF. The original `<img>` tag remains inside, so themes usually continue to work as expected.

If you experience layout issues with a very custom theme, you can disable the `<picture>` integration and still keep the WebP/AVIF generation for use via custom code or CDNs.

= Can I remove the generated files if I uninstall the plugin? =

By default, uninstalling the plugin removes only its settings. The generated image files remain in the uploads directory. This is intentional to avoid breaking existing content.

If you want to clean up the generated files, you can remove them manually (for example via SSH, SFTP or a custom script) based on the `.webp` / `.avif` extensions.

= Does it work with page builders and custom image output? =

The plugin is designed to work with core WordPress APIs (`wp_get_attachment_image()`, featured images, Media Library). Many page builders rely on these APIs internally, so they benefit automatically.

If a builder outputs completely custom HTML without using WordPress image functions, the plugin may not be able to wrap those images in `<picture>` tags automatically, but you can still use the generated WebP/AVIF files manually.

== Screenshots ==

1. Settings page with modern layout, configuration options and server support overview.
2. Bulk optimization dashboard with donut chart, progress bar and activity log.
3. Media Library list view with “NextGen” column and per-image optimization stats.
4. Single attachment screen showing NextGen optimization meta box and actions.

== Changelog ==

= 0.1.0 =
* Initial release: automatic WebP/AVIF conversion on upload, bulk optimization tool, Media Library integration, server support checker, advanced quality/resize options and optional `<picture>` frontend integration.

== Upgrade Notice ==

= 0.1.0 =
Initial release with local WebP/AVIF conversion, bulk optimizer, server support detection and modern settings UI.
