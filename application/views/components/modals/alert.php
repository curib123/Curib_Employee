<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$flash = isset($flash) && is_array($flash) ? $flash : array();
$validation_errors = isset($validation_errors) && is_array($validation_errors) ? $validation_errors : array();
$has_validation_errors = !empty($validation_errors);
$has_flash = !empty($flash);
$has_alert = $has_validation_errors || $has_flash;
$reopen_modal = isset($reopen_modal) ? $reopen_modal : '';
$alert_type = 'info';
$alert_title = 'Notice';
if ($has_validation_errors) {
    $alert_type = 'danger';
    $alert_title = 'Please check your information';
} elseif ($has_flash) {
    $allowed_types = array('success', 'danger', 'warning', 'info');
    $alert_type = isset($flash['type']) && in_array($flash['type'], $allowed_types, TRUE) ? $flash['type'] : 'info';
    $titles = array('success' => 'Success', 'danger' => 'Something went wrong', 'warning' => 'Attention', 'info' => 'Notice');
    $alert_title = $titles[$alert_type];
}
?>
<div class="modal alert-<?= html_escape($alert_type); ?>" id="alertModal" role="dialog" aria-modal="true" aria-hidden="true" aria-labelledby="alertModalLabel" data-auto-show="<?= $has_alert ? '1' : '0'; ?>" data-reopen-modal="<?= html_escape($reopen_modal); ?>">
    <div class="modal-dialog">
        <section class="modal-card">
            <header class="modal-header">
                <div><p class="eyebrow">System message</p><h2 id="alertModalLabel"><?= html_escape($alert_title); ?></h2></div>
                <button class="modal-close" type="button" data-modal-close aria-label="Close">×</button>
            </header>
            <div class="modal-body">
                <?php if ($has_validation_errors): ?>
                    <p>Correct the following before continuing:</p>
                    <ul class="alert-list"><?php foreach ($validation_errors as $error): ?><li><?= html_escape($error); ?></li><?php endforeach; ?></ul>
                <?php elseif ($has_flash): ?>
                    <p><?= html_escape(isset($flash['message']) ? $flash['message'] : ''); ?></p>
                <?php endif; ?>
            </div>
            <footer class="modal-footer"><button class="btn btn-secondary" type="button" data-modal-close>OK</button></footer>
        </section>
    </div>
</div>
