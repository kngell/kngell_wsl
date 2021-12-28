<?=$this->frm->globalAttr($params)->class('px-3')->begin()?>
<?=$this->frm->input('t_name')->placeholder('Taxe...') ?>
<?=$this->frm->input('t_rate')->placeholder('Taux...')->setType('number') ?>
<?=$this->frm->input('t_class')->placeholder('Class...') ?>
<?=$this->frm->textarea('t_descr')->placeholder('Taxe description...')->class('wh_descr')?>
<?=$this->frm->checkbox('status')->label('Active')?>
<?=$this->frm->select('categorieID')->setFieldWrapperClass('select-box')->attr(['multiple'=>'multiple'])?>
<?=$this->frm->submit(2)?>
<?=$this->frm->end()?>