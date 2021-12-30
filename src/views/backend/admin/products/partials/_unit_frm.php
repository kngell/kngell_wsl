<?=$this->frm->globalAttr($params)->class('px-3')->begin()?>
<?=$this->frm->select('unit')->setFieldWrapperClass('select-box')->required()?>
<?=$this->frm->textarea('descr')->placeholder('Description...')?>
<?=$this->frm->checkbox('status')->label('Active')?>
<?=$this->frm->submit(2)?>
<?=$this->frm->end()?>