<div class="post_frm_wrapper">
    <?=$this->post_frm->globalAttr($params)->begin()?>
    <?=$this->post_frm->input('postTitle')->labelUp('Titre:')?>
    <div class="contenu">
        <?= $this->post_frm->textarea('postContent')->class('postContent')?>
    </div>
    <?=$this->post_frm->submit(2)?>
    <?=$this->post_frm->end()?>
</div>