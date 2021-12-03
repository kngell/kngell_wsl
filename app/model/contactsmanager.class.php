<?php

declare(strict_types=1);
class ContactsManager extends Model
{
    protected string $_colID = 'ctID';
    protected string $_table = 'contacts';

    public function __construct()
    {
        parent::__construct($this->_table, $this->_colID);
        $this->_modelName = str_replace(' ', '', ucwords(str_replace('_', ' ', $this->_table))) . 'Manager';
    }

    public function get_successMsg(Model $model, string $action = '', $method = '')
    {
        switch ($method) {
            case 'Add':
                return FH::showMessage('success', 'Vous requête a bien été enregistrée. Vous recevrez une réponse dans 48h maximum!');
                break;
            case 'delete':
                return 'slider supprimé!';
                break;
            default:
                return 'Slider créee avec success!';
                break;
        }
    }
}
