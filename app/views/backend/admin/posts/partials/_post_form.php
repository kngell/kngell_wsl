<div class="post_frm_wrapper">
    <?=$this->post_frm->globalAttr($params)->begin()?>
    <?=$this->post_frm->input('postTitle')->labelUp('Titre:')?>
    <div class="contenu">
        <?= $this->post_frm->textarea('postContent')->class('postContent')?>
    </div>
    <div class="category_management">
        <?=$this->post_frm->select('categorie')->labelUp('Categorie:')->class('categorie')?>
    </div>
    <div class="card">
        <div class="card-body">
            <h4 class="text-center">Media</h4>
            <?=$this->dragAndDrop?>
        </div>
        <!-- end card-body -->
    </div>
    <?=$this->post_frm->submit(2)?>
    <?=$this->post_frm->end()?>
</div>