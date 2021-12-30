<?=$this->frm->globalAttr($params)->class('px-3')->begin()?>
<?=$this->frm->input('name')->required()->labelUp('Group Name :') ?>
<?=$this->frm->textarea('description')->class('ck-content')->labelUp('Group Description :')?>
<?=$this->frm->checkbox('status')->label('Active')?>
<?=$this->frm->select('parentID')->setFieldWrapperClass('select-box')->labelUp('Parent Group :')?>
<?=$this->frm->submit(2, 'Enregistrer')?>
<?=$this->frm->end()?>