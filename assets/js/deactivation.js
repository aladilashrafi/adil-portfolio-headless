jQuery(document).ready(function($) {
    const $modal = $('#hpcms-deactivation-modal');
    const $pluginRow = $(`tr[data-slug="${hpcms_deactivation.plugin_slug}"]`);
    const $deactivateLink = $pluginRow.find('.deactivate a');
    let deactivationUrl = '';

    if (!$modal.length || !$deactivateLink.length) return;

    $deactivateLink.on('click', function(e) {
        e.preventDefault();
        deactivationUrl = $(this).attr('href');
        $modal.css('display', 'flex').hide().fadeIn(200);
    });

    $modal.find('.hpcms-btn-cancel').on('click', function() {
        $modal.fadeOut(200);
    });

    $modal.find('.hpcms-btn-confirm').on('click', function() {
        const $btn = $(this);
        const deleteData = $modal.find('input[name="hpcms_uninstall_choice"]:checked').val();
        
        $btn.prop('disabled', true).text('Saving...');

        $.post(hpcms_deactivation.ajax_url, {
            action: 'hpcms_save_deactivation_choice',
            nonce: hpcms_deactivation.nonce,
            delete_data: deleteData
        }, function() {
            window.location.href = deactivationUrl;
        }).fail(function() {
            window.location.href = deactivationUrl; // Still proceed even if AJAX fails
        });
    });

    // Close on overlay click
    $modal.on('click', function(e) {
        if ($(e.target).is('.hpcms-modal-overlay')) {
            $modal.fadeOut(200);
        }
    });
});
