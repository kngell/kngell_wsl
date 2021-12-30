<?=$this->frm->globalAttr($params)->class('px-3')->begin()?>
<?=$this->frm->input('categorie')->placeholder('Catégorie...')->required()->attr(['aria-describedby'=>'categorie-feedback']) ?>
<?=$this->frm->textarea('description')->placeholder('Description...')->class('ck-content')->attr(['aria-describedby'=>'description-feedback'])?>
<?=$this->frm->checkbox('status')->label('Active')?>
<?=$this->frm->select('parentID')->setFieldWrapperClass('select-box')?>
<?=$this->frm->select('brID')->setFieldWrapperClass('select-box')?>
<?=$this->frm->submit(2)?>
<?=$this->frm->end()?>