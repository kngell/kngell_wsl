<?php

declare(strict_types=1);
class PostsManager extends Model
{
    protected string $_colID = 'postID';
    protected string $_table = 'posts';

    public function __construct()
    {
        parent::__construct($this->_table, $this->_colID);
        $this->_modelName = str_replace(' ', '', ucwords(str_replace('_', ' ', $this->_table))) . 'Manager';
    }

    public function get_fieldName(string $table = '')
    {
        return '';
    }

    public function getAll() : ?array
    {
        $tables = ['table_join' => ['posts' => ['*'], 'post_categorie' => ['postID', 'catID'], 'categories'=>['categorie', 'description', 'photo', 'status', 'brID']]];
        $data = ['join' => 'LEFT JOIN', 'rel' => [['postID', 'postID'], ['catID', 'catID']], 'return_mode' => 'object', 'group_by' => ['postID DESC' => ['tbl' => 'posts']]];
        $wh = $this->getAllItem($data, $tables);
        return $wh->count() > 0 ? $wh->get_results() : [];
    }

    public function get_successMessage(string $method = '', string $params = '')
    {
        switch ($method) {
            case 'update':
                if ($params == 'custom_message') {
                    return FH::showMessage('success text-center', 'Article mis à jour ave succès');
                }
                return 'Article mis a jour avec succès!';
                break;
            case 'delete':
                return 'Article supprimé !';
                break;
            default:
                return 'Article créee avec success!';
                break;
        }
    }
}