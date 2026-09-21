<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal" id="logoutModal" role="dialog" aria-modal="true" aria-hidden="true" aria-labelledby="logoutModalLabel">
    <div class="modal-dialog">
        <section class="modal-card">
            <?= form_open('logout', array('class' => 'form')); ?>
                <header class="modal-header"><h2 id="logoutModalLabel">Sign out?</h2><button class="modal-close" type="button" data-modal-close aria-label="Close">×</button></header>
                <div class="modal-body"><p>Your authenticated session will end and you will return to the login page.</p></div>
                <footer class="modal-footer"><button class="btn btn-secondary" type="button" data-modal-close>Stay signed in</button><button class="btn btn-danger" type="submit" data-submitting-text="Signing out…">Logout</button></footer>
            <?= form_close(); ?>
        </section>
    </div>
</div>
