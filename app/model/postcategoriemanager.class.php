<?php

declare(strict_types=1);
class PostCategorieManager extends Model
{
    protected string $_colID = 'ptID';
    protected string $_table = 'post_categorie';

    public function __construct()
    {
        parent::__construct($this->_table, $this->_colID);
        $this->_modelName = str_replace(' ', '', ucwords(str_replace('_', ' ', $this->_table))) . 'Manager';
    }
}
