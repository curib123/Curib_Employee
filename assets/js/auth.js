/**
 * assets/js/auth.js | 2026-09-21
 * Registration password copy-and-go-to-login behavior.
 */

document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    const generatedModalElement = document.getElementById('generatedPasswordModal');

    if (!generatedModalElement) {
        return;
    }

    const modal = bootstrap.Modal.getOrCreateInstance(generatedModalElement);
    const copyButton = document.getElementById('copyPasswordButton');
    const passwordValue = document.getElementById('generatedPasswordValue');

    modal.show();

    function fallbackCopy(text) {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.setAttribute('readonly', '');
        textarea.className = 'visually-hidden';
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
    }

    copyButton.addEventListener('click', async function () {
        const password = passwordValue.textContent.trim();

        try {
            if (navigator.clipboard && window.isSecureContext) {
                await navigator.clipboard.writeText(password);
            } else {
                fallbackCopy(password);
            }

            copyButton.textContent = 'Copied! Opening Login...';
            copyButton.disabled = true;

            window.setTimeout(function () {
                window.location.href = copyButton.dataset.loginUrl;
            }, 650);
        } catch (error) {
            fallbackCopy(password);
            window.location.href = copyButton.dataset.loginUrl;
        }
    });
});
