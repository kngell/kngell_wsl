<?=$this->frm->globalAttr($params)->class('px-3')->begin()?>
<?=$this->frm->input('br_name')->placeholder('Brand Name...')->required()->attr(['aria-describedby'=>'brands-feedback']) ?>
<?=$this->frm->textarea('br_descr')->placeholder('Description...')->attr(['aria-describedby'=>'brands-feedback'])?>
<?=$this->frm->checkbox('status')->label('Active')?>
<?=$this->frm->submit(2)?>
<?=$this->frm->end()?>