<?php

declare(strict_types=1);
$form = $this->form?>
<div class="row">
    <div class="col-md-6">
        <?= $form->input('lastName')->Label('Nom')->id('chk-lastName')->req()?>
    </div>
    <div class="col-md-6">
        <?= $form->input('firstName')->Label('Prénom')->id('chk-firstName')->req()?>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <?= $form->input('phone')->Label('Téléphone')->id('chk-phone')?>
    </div>
    <div class="col-md-6">
        <?= $form->input('email')->Label('Email')->id('chk-email')->req()->emailType()?>
    </div>
</div> <!-- end row -->