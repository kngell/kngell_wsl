<?php declare(strict_types=1);
$this->start('head'); ?>
<!-------Costum-------->
<link href="<?= $this->asset('css/librairies/frontlib', 'css') ?? '' ?>" rel="stylesheet" type="text/css">
<link href="<?= $this->asset('css/plugins/homeplugins', 'css') ?? '' ?>" rel="stylesheet" type="text/css">
<link href="<?= $this->asset('css/main/frontend/main', 'css') ?? '' ?>" rel="stylesheet" type="text/css">
<?php $this->end(); ?>
<?php $this->start('body'); ?>
<main id="main-site">
    <!-- Content -->
    <div class="modal-test pt-5 mt-5">
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary mt-5" data-bs-toggle="modal" data-bs-target="#exampleModal">
            Launch demo modal
        </button>

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        ...
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fin Content -->
    <input type="hidden" id="ip_address" style="display:none" value="<?=H_visitors::getIP()?>">
</main>
<?php $this->end(); ?>
<?php $this->start('footer') ?>
<script type="text/javascript" src="<?= $this->asset('js/librairies/frontlib', 'js') ?? '' ?>">
</script>
<!-- Common vendor -->
<script type="text/javascript" src="<?= $this->asset('commons/frontend/commonVendor', 'js') ?? '' ?>">
</script>
<!-- Custom Common Modules  -->
<script type="text/javascript" src="<?= $this->asset('commons/frontend/commonCustomModules', 'js') ?? '' ?>">
</script>
<!-- Plugins -->
<script type="text/javascript" src="<?= $this->asset('js/plugins/homeplugins', 'js') ?? '' ?>">
</script>
<!-- Mainjs -->
<script type="text/javascript" src="<?= $this->asset('js/main/frontend/main', 'js') ?? '' ?>">
</script>
<!----------custom--------->
<link href="<?= $this->asset('js/custom/client/test/test', 'js') ?? ''?>" rel="stylesheet" type="text/css">
<?php $this->end();