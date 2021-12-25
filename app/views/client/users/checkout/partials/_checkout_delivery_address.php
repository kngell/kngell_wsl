<?php declare(strict_types=1); ?>
<div class="form-title">
    <h4 class="mt-2 mb-3 card-sub-title">Adresse de Livraison</h4>
</div>
<div class="row">
    <div class="col-md-12">
        <?= $form->select('pays')->Label('Pays')->id('pays')->class('select_country')->req()?>
    </div>
</div> <!-- end row -->
<div class="row">
    <div class="col-12">
        <?= $form->input('address1')->Label('Adresse ligne 1')->id('address1')->req()?>
        <?= $form->input('address2')->Label('Adresse ligne 2')->id('address2')?>
    </div>
</div>
<!-- end row -->
<div class="row">
    <div class="col-md-4">
        <?= $form->input('ville')->Label('Ville')->id('ville')->req()?>
    </div>
    <div class="col-md-4">
        <?= $form->input('region')->Label('Région/Etat')->id('region')?>
    </div>
    <div class="col-md-4">
        <?= $form->input('zip_code')->Label('Code Postal')->id('zip_code')->req()?>
    </div>
</div>
<!-- end row -->

<div class="row">
    <div class="col-12 mb-4">
        <?= $form->textarea('u_comment')->Label('Commentaires, notes ...')->id('u_comment')->attr(['form' => 'user-ckeckout-frm'])->row(3)->class('input-box__textarea')?>
    </div>
</div>
<!-- end row -->
<?= $form->checkbox('checkout-remember-me')->Label('Sauvegarder ces informations pour la prochaine fois')->id('checkout-remember-me')->class('checkbox__input')->checkboxType()->spanClass('checkbox__box')->LabelClass('checkbox')->setFieldWrapperClass('mt-2');