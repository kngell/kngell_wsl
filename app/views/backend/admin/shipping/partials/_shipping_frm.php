<?=$this->frm->globalAttr($params)->class('px-3')->begin()?>
<?=$this->frm->select('sh_name')->setFieldWrapperClass('select-box')?>
<?=$this->frm->textarea('sh_descr')->placeholder('Description...')?>
<?=$this->frm->input('price')->placeholder('Prix...') ?>
<?=$this->frm->checkbox('status')->label('Active')?>
<?=$this->frm->submit(2)?>
<?=$this->frm->end()?>