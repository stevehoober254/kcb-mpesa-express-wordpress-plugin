jQuery(document).ready(function ($) {
    const $input = $('#kcb_mpesa_license_key');
    const $status = $('#kcb-license-status');

    $input.on('blur', function () {
        const key = $input.val().trim();
        if (!key) return;

        $status.text('⏳ Checking...');

        $.post(KCBLicenseChecker.ajax_url, {
            action: 'kcb_validate_license',
            key,
            nonce: KCBLicenseChecker.nonce
        }).done(function (res) {
            if (res.success) {
                $status.html('<span style="color:green;">' + res.data + '</span>');
            } else {
                $status.html('<span style="color:red;">' + res.data + '</span>');
            }
        }).fail(function () {
            $status.html('<span style="color:red;">❌ Failed to check license</span>');
        });
    });
});
