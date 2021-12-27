<?php

declare(strict_types=1);
class PostCategorieManager extends Model
{
    protected string $_colID = 'ptID';
    protected string $_table = 'post_categorie';
    protected string $_colIndex = 'postID';

    public function __construct()
    {
        parent::__construct($this->_table, $this->_colID);
        $this->_modelName = str_replace(' ', '', ucwords(str_replace('_', ' ', $this->_table))) . 'Manager';
    }

    public function save_categories(string $id, array $params) : ?Model
    {
        if ($this->_current_ctrl_method == 'update') {
            return $this->update_categories(strval($id), $params);
        }
        return $this->add_categories(strval($id), $params);
    }

    private function add_categories(string $postID, array $params) : ?Model
    {
        $categories = isset($params['categorie']) ? json_decode($this->htmlDecode($params['categorie']), true) : [];
        if (json_last_error() === JSON_ERROR_NONE && isset($postID) && !empty($categories)) {
            foreach ($categories as $cat) {
                if ($cat['id'] != '') {
                    $this->postID = $postID;
                    $this->catID = $cat['id'];
                    if (!$this->save()) {
                        break;
                        return null;
                    }
                }
            }
        }
        return $params['saveID'] ?? null;
    }

    /**
     * Update Categories
     * ======================================================================.
     * @param string $postID
     * @param array $params
     * @return Model|null
     */
    private function update_categories(string $postID, array $params) : ?Model
    {
        $postCat = $this->getAllbyIndex((string) $postID, ['return_mode' => 'class']);
        if ($postCat->count() > 0) {
            foreach ($postCat->get_results() as $pc) {
                if (!$pc->delete()) {
                    break;
                    return null;
                }
            }
        }
        return $this->add_categories($postID, $params);
    }
}