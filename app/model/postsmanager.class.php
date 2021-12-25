<?php

declare(strict_types=1);
class PostsManager extends Model
{
    protected string $_colID = 'postID';
    protected string $_table = 'posts';
    protected string $_status = 'postStatus';

    public function __construct()
    {
        parent::__construct($this->_table, $this->_colID);
        $this->_modelName = str_replace(' ', '', ucwords(str_replace('_', ' ', $this->_table))) . 'Manager';
    }

    public function get_fieldName(string $table = '')
    {
        switch ($table) {
            case 'categories':
                return 'categorie';
            break;
            default:
            return '';
            break;
    }
    }

    public function getAll() : ?array
    {
        $tables = ['table_join' => ['posts' => ['*'], 'post_categorie' => ['catID'], 'categories'=>['categorie', 'description', 'photo', 'status', 'brID']]];
        $data = ['join' => 'LEFT JOIN', 'rel' => [['postID', 'postID'], ['catID', 'catID']], 'return_mode' => 'object', 'group_by' => ['postID DESC' => ['tbl' => 'posts']]];
        $wh = $this->getAllItem($data, $tables);
        return $wh->count() > 0 ? $wh->get_results() : [];
    }

    public function deletePosts(string $id, array $data) : bool
    {
        $imgID = isset($data['folder']) ? $id . $data['folder'] : '';
        if ($imgID != '') {
            if ($this->delete($id, $data)) {
                $urlModel = $this->container->make(PostFileUrlManager::class);
                return $urlModel->cleanDbFilesUrls([], $urlModel->getAllItem(['where'=>['imgID'=>$imgID], 'return_mode'=>'class']), $data['folder']);
            }
        }
        return false;
    }

    /**
     * AfterSaving Operations
     * ==========================================================================.
     * @param array $params
     * @return Model|null
     */
    public function afterSave(array $params = []) : ?Model
    {
        $id = !isset($this->postID) || $this->postID == '' ? $params['saveID']->get_lastID() : $this->postID;
        if ($this->_current_ctrl_method == 'update') {
            return $this->container->make(PostCategorieManager::class)->update_categories(strval($id), $params);
        }
        return $this->container->make(PostCategorieManager::class)->add_categories(strval($id), $params);
    }

    /**
     * Get Selected Options
     * =========================================================================.
     * @param string $table
     * @return mixed
     */
    public function get_selectedOptions(?Object $m = null)
    {
        $options = $this->get_options_data();
        $response = [];
        if ($options) {
            $colTitle = array_pop($options);
            $colID = array_pop($options);
            if (count($options) > 0) {
                foreach ($options as $item) {
                    $response[$item->$colID] = $this->htmlDecode($item->$colTitle);
                }
            }
        }
        $options = null;

        return $response ? $response : [];
    }

    /**
     * Get Options Data
     * ==========================================================================================================.
     * @return array
     */
    public function get_options_data() : array
    {
        $r = [];
        $tables = ['table_join' => ['categories' => ['*'], 'post_categorie' => ['postID', 'catID']]];
        $data = ['join' => 'INNER JOIN',
                    'rel' => [['catID', 'catID']],
                    'where' => ['postID' => ['value' => $this->postID, 'tbl' => 'post_categorie']],
                    'group_by' => 'categorie',
                    'return_mode' => 'class', ];
        $r = $this->container->make(PostCategorieManager::class)->getAllItem($data, $tables)->get_results();
        $r['colID'] = 'catID';
        $r['colTitle'] = 'categorie';
        return $r;
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