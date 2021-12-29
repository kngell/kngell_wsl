<?=$this->frm->globalAttr($params)->class('px-3')->begin()?>
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <?=$this->frm->input('p_title')->labelUp('Title :')->required() ?>
                        <?=$this->frm->textarea('p_descr')->labelUp('Description :')->attr(['rows'=>3])?>
                        <?=$this->frm->input('p_short_descr')->labelUp('Short Description :')->required()->class('ck-content') ?>
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
            </div>
            <!-- end card-body -->
        </div>
        <!-- end card -->
        <div class="card">
            <div class="card-body">
                <h4 class="text-center">Media</h4>
                <?=$this->dragAndDrop?>
            </div>
            <!-- end card-body -->
        </div>
        <!-- end card -->
        <div class="card">
            <div class="card-body">
                <h4>Pricing</h4>
                <div class="row border-bottom mb-3">
                    <div class="col-lg-6">
                        <?=$this->frm->input('p_regular_price')->labelUp('Regular Price :')->setType('number') ?>
                    </div>
                    <!-- end col -->
                    <div class="col-lg-6">
                        <?=$this->frm->input('p_compare_price')->labelUp('Compare at price :')->setType('number') ?>
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
                <div class="row mt-3">
                    <div class="col-lg-6">
                        <?=$this->frm->input('p_cost_per_item')->labelUp('Cost per item :')->setType('number')->helpBlock('Customers won’t see this') ?>
                    </div>
                    <!-- end col -->
                    <div class="col-lg-6 d-flex align-items-center">
                        <?=$this->frm->checkbox('p_charge_tax')->label('Charge tax on this product')->LabelClass('custom-checkbox')->removeWrapperClass('mb-3')?>
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
            </div>
            <!-- end card-body -->
        </div>
        <!-- end card -->
        <div class="card">
            <div class="card-body">
                <h4>Inventory</h4>
                <div class="row">
                    <div class="col-lg-6">
                        <?=$this->frm->input('p_sku')->labelUp('SKU (Stock Keeping Unit) :')->setType('number')?>
                    </div>
                    <!-- end col -->
                    <div class="col-lg-6">
                        <?=$this->frm->input('p_barre_code')->labelUp('Barcode (ISBN, UPC, GTIN, etc.) :')?>
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
                <div class="row border-bottom mb-3 pb-3">
                    <div class="col-12">
                        <label class="custom-checkbox">
                            <input type="checkbox" class="form-check-input" name="p_track_qty" id="p_track_qty" checked>
                            Track quantity
                            <span></span>
                        </label>
                    </div>
                    <div class="col-12">
                        <?=$this->frm->checkbox('p_continious_sell')->label('Continue selling when out of stock')->LabelClass('custom-checkbox')->removeWrapperClass('mb-3')->setFieldWrapperClass('ps-0')->checked()?>
                    </div>
                </div>
                <!-- end row -->
                <h5 class="mt-3">QUANTITY</h5>
                <div class="row">
                    <div class="col-lg-6">
                        <?=$this->frm->input('p_qty')->labelUp('Stock quantity :')->setType('number')?>
                    </div>
                    <!-- end col -->
                    <div class="col-lg-6">
                        <?=$this->frm->select('p_back_border')->setFieldWrapperClass('select-box')->labelUp('Allow backorder :')->options([['id'=>0, 'value'=>'No not allow'], ['id'=>1, 'value'=>'Allow, but notify customer'], ['id'=>2, 'value'=>'Allow']])?>
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
                <div class="row">
                    <div class="col-lg-6">
                        <?=$this->frm->input('p_stock_threshold')->labelUp('Low stock threshold :')->setType('number')?>
                    </div>
                    <div class="col-lg-6">
                        <?=$this->frm->select('p_unitID')->setFieldWrapperClass('select-box')->labelUp('Product Unit :')->attr(['style'=>'width:100%; height:100%;'])?>
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
            </div>
            <!-- end card-body -->
        </div>
        <!-- end card -->
        <div class="card">
            <div class="card-body">
                <h4 class="mb-3">Shipping</h4>
                <div class="row border-bottom mb-3 pb-3">
                    <div class="col-lg-6">
                        <?=$this->frm->input('p_weight')->labelUp('Weight (kg) :')?>
                    </div>
                </div>
                <div class="row border-bottom mb-3 pb-3">
                    <label for="product-length" class="col-lg-3 col-form-label fw-700">Dimensions
                        (cm)</label>
                    <div class="col-lg-9">
                        <div class="row">
                            <div class="col-3">
                                <?=$this->frm->input('p_lenght')->placeholder('Length')->removeWrapperClass('mb-3')?>
                            </div>
                            <div class="col-3">
                                <?=$this->frm->input('p_width')->placeholder('Width')->removeWrapperClass('mb-3')?>
                            </div>
                            <div class="col-3">
                                <?=$this->frm->input('p_height')->placeholder('Height')->removeWrapperClass('mb-3')?>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end row -->
                <div class="row">
                    <div class="col-lg-6">
                        <?=$this->frm->select('p_shipping_class')->setFieldWrapperClass('select-box')->labelUp('Shipping Class :')?>
                    </div>
                </div>
            </div>
            <!-- end card-body -->
        </div>
        <!-- end card -->
        <div class="card">
            <div class="card-body">
                <h4>Variants</h4>
                <!-- Loop true options -->
                <div class="row mb-3">
                    <div class="form-group">
                        <label for="p_variant_title">Option 1</label>
                    </div>
                    <!-- end form-group -->
                    <div class="col-3">
                        <?=$this->frm->input('p_variant_title')->removeWrapperClass('mb-3')->value('size')->attr(['form'=>'product-variant-frm'])?>
                    </div>
                    <!-- end col -->
                    <div class="col-9">
                        <?=$this->frm->input('p_variant_value')->removeWrapperClass('mb-3')->attr(['form'=>'product-variant-frm'])->placeholder('Separate options with a comma')?>
                    </div>
                    <!-- end col -->
                </div>

                <!-- end row -->
                <?=$this->frm->button()->class('btn btn-sm btn-outline-highlight')->icon('fal fa-plus-circle')->text('Add another option')?>
            </div>
            <!-- end card-body -->
        </div>
        <!-- end card -->
    </div>
    <!-- end col -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h4>Organization</h4>
                <div class="row">
                    <div class="col-lg-12">
                        <?=$this->frm->select('p_company')->setFieldWrapperClass('select-box')?>
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
                <div class="row">
                    <div class="col-lg-12">
                        <?=$this->frm->select('p_warehouse')->setFieldWrapperClass('select-box')->attr(['aria-label'=>'.form-select Default'])?>
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
            </div>
            <!-- end card-body -->
        </div>
        <!-- end card -->
        <div class="card">
            <div class="card-body">
                <h4>Product categories</h4>
                <div id="check-box-wrapper">
                    <?php array_map(function ($cat) {?>
                    <label class="custom-checkbox">
                        <input type="checkbox" value="<?=$cat->catID?>" name='categorie[]' class="categorie">
                        <?=$cat->categorie?>
                        <span></span>
                    </label>
                    <?php }, $this->view_data)?>
                </div>
                <button class="btn btn-sm btn-outline-highlight" type="button"><i class="fal fa-plus-circle"></i>
                    Add new
                    category</button>
            </div>
            <!-- end card-body -->
        </div>
        <!-- end card -->
        <div class="card">
            <div class="card-body">
                <h4>Product tags</h4>
                <form class="">
                    <div class="d-flex align-items-center">
                        <div class="form-group me-1 flex-grow-1">
                            <input type="text" class="form-control" />
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-highlight">
                            Add
                        </button>
                    </div>
                    <span class="fs-sm"><i>Separate tags with commas</i></span>
                    <div class="product-tags mt-2">
                        <span class="badge bg-highlight"><a href="javascript:void(0);"><i class="fal fa-times"></i></a>
                            T-shirt</span>
                        <span class="badge bg-highlight"><a href="javascript:void(0);"><i class="fal fa-times"></i></a>
                            Christmas</span>
                        <span class="badge bg-highlight"><a href="javascript:void(0);"><i class="fal fa-times"></i></a>
                            Lorem</span>
                        <span class="badge bg-highlight"><a href="javascript:void(0);"><i class="fal fa-times"></i></a>
                            Ipsum</span>
                    </div>
                </form>
            </div>
            <!-- end card-body -->
        </div>
        <!-- end card -->
        <div class="card">
            <div class="card-body d-flex justify-content-between">
                <button class="btn btn-outline-highlight" type="button">Save Draft</button>
                <button class="btn btn-highlight" id="save-all" type="submit"><i class="fal fa-save"></i>
                    Save</button>
            </div>
            <!-- end card-body -->
        </div>
        <!-- end card -->
    </div>
    <!-- end col -->
</div>
<?=$this->frm->submit(2)?>
<?=$this->frm->end()?>