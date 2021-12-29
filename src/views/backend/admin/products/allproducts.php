<?php declare(strict_types=1);
$this->start('head'); ?>
<!-------Costum-------->
<meta name="csrftoken" content="<?=$this->token->generate_token(8, 'all_product_page')?>" />
<link href="<?= $this->asset('css/custom/backend/admin/products/allproducts', 'css') ?? ''?>" rel="stylesheet"
    type="text/css">
<?php $this->end(); ?>
<?php $this->start('body'); ?>
<main id=main-site>
    <!-- Content -->
    <div class="page-content">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb ps-0 fs-base">
                <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                <li class="breadcrumb-item"><span>Products</span></li>
                <li class="breadcrumb-item active" aria-current="page">All Products</li>
            </ol>
        </nav>
        <div class="row header justify-content-between mb-4">
            <div class="col-12">
                <h1 class="header-title h3">
                    <i class="fad fa-star-half-alt text-highlight"></i>
                    Products
                </h1>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-12">
                <div class="card card-solid" id="allproducts-wrapper">
                    <div id="alertErr"></div>
                    <h5 class="card-header main-header">
                        <span class="lead">
                            <div class="input-group">
                                <button class="btn btn-highlight" type="button"><i class="fal fa-plus-circle"></i> <span
                                        class="d-none d-md-inline"
                                        onclick="window.location.href='<?='new_product'?>';">Add
                                        Product</span></button>
                                <button class="btn btn-highlight" type="button"><i class="fal fa-file-import"></i> <span
                                        class="d-none d-md-inline">Import</span></button>
                                <button class="btn btn-highlight" type="button"><i class="fal fa-arrow-to-bottom"></i>
                                    <span class="d-none d-md-inline">Export</span></button>
                                <button class="btn btn-highlight" type="button"><i class="fal fa-list-ul"></i> <span
                                        class="d-none d-md-inline">Customize Columns</span></button>
                                <button type="button" class="btn btn-highlight dropdown-toggle dropdown-toggle-split"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fal fa-pen"></i> <span class="d-none d-md-inline">Bulk Actions</span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">Delete</a></li>
                                    <li><a class="dropdown-item" href="#">Update Statuses</a></li>
                                    <li><a class="dropdown-item" href="#">Create Notes</a></li>
                                </ul>
                            </div>
                        </span>
                        <span class="ms-auto"> <a href="javascript:history.go(-1)" class="btn btn-light btn-secondary"
                                id="back"><i class="far fa-arrow-alt-circle-left fa-lg"></i></i>&nbsp;Back
                            </a>&nbsp;&nbsp;
                            <a href="" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#modal-box"
                                id="addNew"><i class="fas fa-plus-circle fa-lg"></i>&nbsp;Add new</a>
                        </span>
                    </h5>
                    <div id="globalErr"></div>
                    <div class="card-body pb-0">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="table-responsive" id="showAll">

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!----------Add new  Modal-------->
        <div class="modal fade" role="dialog" id="modal-box">
            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"> Add new</h5>
                        <button type="button" class="btn-close text-light" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <?php require_once 'partials/_product_frm.php'; ?>
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
<!----------custom--------->
<script type="text/javascript" src="<?= $this->asset('js/custom/backend/admin/products/allproducts', 'js') ?? ''?>">
</script>
<?php $this->end();