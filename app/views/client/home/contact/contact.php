<?php declare(strict_types=1);
$this->start('head'); ?>
<!-------Costum-------->
<link href="<?= $this->asset('css/custom/client/home/contact/contact', 'css') ?? ''?>" rel="stylesheet" type="text/css">
<?php $this->end(); ?>
<?php $this->start('body'); ?>
<main id="main-site">
    <!-- Content -->
    <div class="container">
        <div class="page-content py-5">
            <?php require_once VIEW . 'client/home/contact/partials/_contact_form.php'?>
        </div>
    </div>

    <!-- Fin Content -->
    <input type="hidden" id="ip_address" style="display:none" value="<?=H_visitors::getIP()?>">
</main>
<?php $this->end(); ?>
<?php $this->start('footer') ?>
<!----------custom--------->
<script type="text/javascript" src="<?= $this->asset('js/custom/client/home/contact/contact', 'js') ?? ''?>">
</script>
<?php $this->end();