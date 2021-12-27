<?php

declare(strict_types=1);
class PostFileUrlManager extends Model
{
    protected string $_table = 'post_file_url';
    protected string $_colID = 'pfuID';
    protected string $_colIndex = 'imgID';
    protected string $_media_img = 'fileUrl';
    protected string $_img_folder = 'posts';

    public function __construct()
    {
        parent::__construct($this->_table, $this->_colID);
        $this->_modelName = str_replace(' ', '', ucwords(str_replace('_', ' ', $this->_table))) . 'Manager';
    }

    public function storeFile()
    {
        $this->folder = $this->_img_folder;
        return $this->save();
    }

    public function update_urls_dependencies(?Model $m, array $urlAry)
    {
        if ($this->count() > 0) {
            foreach ($this->get_results() as $dep) {
                if (!$dep->delete()) {
                    break;
                    return null;
                }
            }
        }
        return $this->add_urls_dependencies($m, $urlAry);
    }

    public function add_urls_dependencies(?Model $m, array $urlAry) : bool
    {
        foreach ($urlAry as $url) {
            if ($url != '') {
                $this->itemID = $m->{$m->get_colID()};
                $this->imgID = $this->itemID . $m->get_media_folder();
                $this->{$this->_media_img} = serialize([$url]);
                $this->folder = $m->get_media_folder();
                if (!$this->save()) {
                    break;
                    return true;
                }
            }
        }
        return true;
    }

    public function cleanBdFiles()
    {
        if (Cookies::exists(VISITOR_COOKIE_NAME)) {
            $this->delete(['user_cookie'=>Cookies::get(VISITOR_COOKIE_NAME)]);
            return true;
        }
        return false;
    }

    public function getDbUrls(string $lastID, string $table)
    {
        $sql = ['sql' => "SELECT * FROM $this->_table WHERE $this->_colIndex" . ' = ' . "'" . $lastID . $table . "'" . ' OR ' . "$this->_colIndex IS NULL", 'return_mode'=>'class'];
        return $this->getAllItem($sql);
    }

    public function cleanDiskFiles(string $folder) : bool
    {
        try {
            $diskFiles = array_diff(scandir(IMAGE_ROOT . $folder), ['.', '..']);
            $bdFilesAry = array_map(function ($url) {
                return basename(unserialize($url)[0]);
            }, array_column($this->getAllItem()->get_results(), 'fileUrl'));
            foreach ($diskFiles as $key => $file) {
                if (!in_array($file, $bdFilesAry)) {
                    file_exists(IMAGE_ROOT . $folder . DS . $file) ? unlink(IMAGE_ROOT . $folder . DS . $file) : '';
                    file_exists(IMAGE_ROOT_SRC . $folder . DS . $file) ? unlink(IMAGE_ROOT_SRC . $folder . DS . $file) : '';
                }
            }
            return true;
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function saveFilesUrls(array $mediaAry, string $folder, string $lastID)
    {
        if (is_array($mediaAry) && !empty($mediaAry)) {
            foreach ($mediaAry as $key => $url) {
                $this->fileUrl = serialize([$folder . DS . basename($url)]);
                $this->_colIndex = $lastID . $folder;
                if ($this->save()) {
                    !file_exists(IMAGE_ROOT_SRC . $folder . DS . basename($url)) ? copy(IMAGE_ROOT . $folder . DS . basename($url), IMAGE_ROOT_SRC . $folder . DS . basename($url)) : '';
                }
            }
        }
    }

    public function get_successMessage(string $method = '', ?array $params = []) : ?string
    {
        switch ($method) {
            case 'update':
                return 'Posts a jour avec succès!';
                break;
            case 'delete':
                return 'Posts supprimé!';
                break;
            default:
                return 'Posts créee avec success!';
                break;
        }
    }
}