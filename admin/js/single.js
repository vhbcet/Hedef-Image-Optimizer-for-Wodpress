jQuery(function ($) {

    var $btn     = $('#ngio-optimize-single');
    var $spinner = $('#ngio-single-spinner');
    var $status  = $('#ngio-single-status');

    if (!$btn.length || typeof ngioSingle === 'undefined') {
        return;
    }

    $btn.on('click', function (e) {
        e.preventDefault();

        var attachmentId = $btn.data('attachment-id');
        if (!attachmentId) {
            return;
        }

        $btn.prop('disabled', true);
        $spinner.addClass('is-active');

        if ($status.length && ngioSingle.textWorking) {
            $status.text(ngioSingle.textWorking);
        }

        $.post(
            ngioSingle.ajaxUrl,
            {
                action: 'ngio_optimize_single',
                nonce: ngioSingle.nonce,
                attachment_id: attachmentId
            }
        )
            .done(function (response) {
                if (!response || !response.success) {
                    var msg = (response && response.data && response.data.message)
                        ? response.data.message
                        : ngioSingle.textError;

                    if ($status.length) {
                        $status.text(msg);
                    }
                    return;
                }

                var data = response.data || {};
                var msg  = data.message || ngioSingle.textDone;

                if ($status.length) {
                    $status.text(msg);
                }
            })
            .fail(function () {
                if ($status.length) {
                    $status.text(ngioSingle.textError);
                }
            })
            .always(function () {
                $spinner.removeClass('is-active');
                $btn.prop('disabled', false);
            });
    });
});
