<?php
/**
 * application/views/components/modals/alert.php | 2026-09-21
 * Shared Bootstrap modal for flash messages and validation errors.
 */
defined('BASEPATH') OR exit('No direct script access allowed');

$flash = isset($flash) && is_array($flash) ? $flash : array();
$validation_errors = isset($validation_errors) && is_array($validation_errors)
    ? $validation_errors
    : array();
$has_validation_errors = !empty($validation_errors);
$has_flash = !empty($flash);
$has_alert = $has_validation_errors || $has_flash;
$reopen_modal = isset($reopen_modal) ? $reopen_modal : '';

$alert_type = 'info';
$alert_title = 'Notice';

if ($has_validation_errors)
{
    $alert_type = 'danger';
    $alert_title = 'Please check your information';
}
elseif ($has_flash)
{
    $allowed_types = array('success', 'danger', 'warning', 'info');
    $alert_type = isset($flash['type']) && in_array($flash['type'], $allowed_types, TRUE)
        ? $flash['type']
        : 'info';
    $alert_title = $alert_type === 'success' ? 'Success' : ($alert_type === 'danger' ? 'Something went wrong' : 'Notice');
}
?>
<div
    class="modal fade"
    id="alertModal"
    tabindex="-1"
    aria-labelledby="alertModalLabel"
    aria-hidden="true"
    data-auto-show="<?= $has_alert ? '1' : '0'; ?>"
    data-reopen-modal="<?= html_escape($reopen_modal); ?>"
>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 text-bg-<?= html_escape($alert_type); ?> px-4 py-3">
                <h2 class="modal-title fs-5" id="alertModalLabel"><?= html_escape($alert_title); ?></h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <?php if ($has_validation_errors): ?>
                    <p class="mb-2">Correct the following before continuing:</p>
                    <ul class="mb-0">
                        <?php foreach ($validation_errors as $error): ?>
                            <li><?= html_escape($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php elseif ($has_flash): ?>
                    <p class="mb-0"><?= html_escape(isset($flash['message']) ? $flash['message'] : ''); ?></p>
                <?php endif; ?>
            </div>
            <div class="modal-footer border-0 bg-body-tertiary px-4 py-3">
                <button type="button" class="btn btn-primary px-4" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>
