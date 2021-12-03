<?php declare(strict_types=1);
$this->start('head'); ?>
<!-------Accueil-------->
<meta name="csrftoken" content="<?=$this->token->generate_token(8, 'all_product_page')?>" />
<link href="<?= $this->asset('css/custom/client/home/cart/cart', 'css') ?? ''?>" rel="stylesheet" type="text/css">
<?php $this->end(); ?>
<?php $this->start('body'); ?>
<!-- Start Main -->
<main id="main-site">
    <!-- Shoping cart  -->
    <?php require_once VIEW . 'client/home/cart/partials/_shopping_cart.php'?>
    <!-- !Shpoping cart -->
    <!-- Wishlist  -->
    <?php require_once VIEW . 'client/home/cart/partials/_wishlist.php'?>
    <!-- !Wishlist -->
    <!-- New Phones -->
    <?php require_once VIEW . 'client/home/partials/_new_products.php'?>
    <!-- End New Phones -->
    <input type="hidden" id="ip_address" style="display:none" value="<?=H_visitors::getIP()?>">
</main>
<!-- End  Main -->

<?php $this->end(); ?>
<?php $this->start('footer')?>
<!-- Html visitors -->
<script type="text/javascript" src="<?= $this->asset('js/custom/client/home/cart/cart', 'js') ?? ''?>">
</script>
<?php $this->end();
