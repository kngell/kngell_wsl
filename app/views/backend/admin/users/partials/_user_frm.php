<?=$this->frm->globalAttr($params)->class('px-3')->begin()?>
<div class="row g-3">
    <?=$this->frm->input('firstName')->placeholder('Prénom...')->setFieldWrapperClass('col-sm-6')->required() ?>
    <?=$this->frm->input('lastName')->placeholder('Nom...')->setFieldWrapperClass('col-sm-6')->required() ?>
    <?=$this->frm->input('userName')->placeholder('Identifiant...')->setFieldWrapperClass('col-sm-6')->required() ?>
    <?=$this->frm->input('email')->placeholder('Email...')->setFieldWrapperClass('col-sm-6')->required()->emailType() ?>
</div>
<?=$this->frm->input('phone')->placeholder('Téléphone...')?>
<?=$this->frm->select('group')->setFieldWrapperClass('select-box')->multiple()?>
<?=$this->frm->submit(2)?>
<?=$this->frm->end()?>