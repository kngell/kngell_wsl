<?=$this->frm->globalAttr($params)->class('px-3')->begin()?>
<?=$this->frm->input('wh_name')->placeholder('Warehouse Name...')->class('wh_name') ?>
<?= $this->frm->textarea('wh_descr')->placeholder('Description...')->class('wh_descr')?>
<?=$this->frm->checkbox('status')->label('Active')?>
<?=$this->frm->select('company')->setFieldWrapperClass('select-box')?>
<?=$this->frm->select('country_code')->setFieldWrapperClass('select-box')?>
<?=$this->frm->submit(2)?>
<?=$this->frm->end()?>