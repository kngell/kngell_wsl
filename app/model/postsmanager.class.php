<?php

declare(strict_types=1);
class PostsManager extends Model
{
    protected string $_colID = 'postID';
    protected string $_table = 'posts';
    protected string $_status = 'postStatus';
    protected string $_media_img = 'postImg';
    protected string $_img_folder = 'blog-post';

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

    public function filesToRemove()
    {
        if (isset($this->_media_img) && is_array($this->{$this->_media_img})) {
            $fTr = $this->{$this->_media_img};
            $f = [];
            foreach ($fTr as $url) {
                $f[] = str_replace(IMG, '', $url);
            }
            return $f;
        }
    }

    public function deletePosts(string $id, array $data) : bool
    {
        try {
            if ($this->delete($id, $data)) {
                return  $this->container->make(Files::class)->cleanDbFilesUrls([], $this->container->make(PostFileUrlManager::class)->getAllItem(['where'=>['itemID'=>$id], 'return_mode'=>'class']));
            }
        } catch (\Throwable $th) {
            throw new FileSystemManagementException('Impossible de supprimer les fichiers! ' . $th->getMessage(), $th->getCode());
        }
    }

    public function clientAry(array $cR): array
    {
        $r = [];
        foreach ($cR as $url) {
            $r[] = basename($url);
        }
        return $r;
    }

    /**
     * AfterSaving Operations
     * ==========================================================================.
     * @param array $params
     * @return Model|null
     */
    public function afterSave(array $params = []) : ?Model
    {
        try {
            $id = !isset($this->postID) || $this->postID == '' ? $params['saveID']->get_lastID() : $this->postID;
            $this->container->make(PostCategorieManager::class)->save_categories(strval($id), $params);
            $urls = $this->container->make(PostFileUrlManager::class)->getAllbyIndex((string) $id . $this->_img_folder, ['return_mode' => 'class']);
            if ($urls->count() > 0) {
                $clientAry = $this->{$this->get_media()} != false ? $this->clientAry(unserialize($this->{$this->get_media()})) : [];
                $this->container->make(Files::class)->cleanDbFilesUrls($clientAry, $urls);
                $urls->add_urls_dependencies($this, array_diff(unserialize($this->{$this->get_media()}), array_map(function ($url) {
                    return unserialize($url)[0];
                }, array_column($urls->get_results(), 'fileUrl'))));
            } else {
                $urls->add_urls_dependencies($this, unserialize($this->{$this->get_media()}));
            }
            return isset($params['saveID']) ? $params['saveID'] : null;
        } catch (\Throwable $th) {
            throw new FileSystemManagementException('Impossible de sauvegarder les dependances! ' . $th->getMessage(), $th->getCode());
        }
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