<div class="post_frm_wrapper">
    <?=$this->frm->globalAttr($params)->begin()?>
    <div class="row">
        <div class="col-md-6 h-100">
            <h5 class="font-weight-lighter text-center text-muted">Infos juridiques</h5>
            <?=$this->frm->input('sigle')->labelUp('Sigle :')->class('sigle') ?>
            <?=$this->frm->input('denomination')->labelUp('Dénomination :')->class('denomination') ?>
            <?=$this->frm->input('suret')->labelUp('Siret :')->class('siret') ?>
            <?=$this->frm->input('tva')->labelUp('N° de TVA :')->class('tva') ?>
            <?= $this->frm->textarea('activite')->LabelUp('Activité :')->class('activite')?>
        </div>
        <div class="col-md-6 h-100">
            <h5 class="text-centertext-muted text-center font-weight-lighter">Coordonnées</h5>
            <div class="row">
                <?=$this->frm->input('phone')->labelUp('N° Téléphone fixe :')->class('phone')?>
                <?=$this->frm->input('mobile')->labelUp('N° Téléphone portable :')->class('mobile')?>
                <?=$this->frm->input('email')->labelUp('Courriel :')->class('email')?>
                <?=$this->frm->input('site_web')->labelUp('Site Web : ')->class('site_web')?>
                <?=$this->frm->textarea('address1')->LabelUp('Adresse :')->class('address1')?>
                <div class="row">
                    <?=$this->frm->input('ville')->labelUp('Ville : ')->class('ville')->setFieldWrapperClass('col-md-7')?>
                    <?=$this->frm->input('zip_code')->labelUp('Code Postal : ')->class('zip_code')->setFieldWrapperClass('col-md-5')?>
                    <?=$this->frm->input('pays')->labelUp('Pays :')->class('pays'); ?>
                </div>
            </div>
        </div>
    </div>
    <?=$this->frm->submit(2)?>
    <?=$this->frm->end()?>
</div>