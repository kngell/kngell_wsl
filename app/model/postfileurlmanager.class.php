<?php

declare(strict_types=1);
class PostFileUrlManager extends Model
{
    protected $_table = 'post_file_url';
    protected $_colID = 'pfuID';
    protected $_colIndex = 'imgID';

    public function __construct()
    {
        parent::__construct($this->_table, $this->_colID);
        $this->_modelName = str_replace(' ', '', ucwords(str_replace('_', ' ', $this->_table))) . 'Manager';
    }

    public function storeFile()
    {
        if (Cookies::exists(VISITOR_COOKIE_NAME)) {
            $this->user_cookie = Cookies::get(VISITOR_COOKIE_NAME);
            return $this->save();
        }
        return false;
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

    /**
     * Clean Unsued Urls
     * ========================================================================.
     * @param string $table
     * @return bool
     */
    public function cleanUnusedUrls(string $table = '') : bool
    {
        try {
            $urls = $this->getAllItem(['where'=>[$this->_colIndex=>'IS NULL'], 'return_mode'=>'class']);
            $urlToRemove = [];
            if ($urls->count() > 0) {
                foreach ($urls->get_results() as $key => $m) {
                    if ($m->delete()) {
                        $urlToRemove[] = basename(unserialize($m->fileUrl)[0]);
                    }
                }
                return $this->cleanFilesSystemUrls($urlToRemove, $table);
            }
            return true;
        } catch (\Throwable $th) {
            throw new FileSystemManagementException('Impossible de supprimer les fichiers! ' . $th->getMessage(), $th->getCode());
        }
    }

    /**
     * cleanFilesSystemUrls
     * ===========================================================================.
     * @param array $urlsAry
     * @param string $table
     * @return mixed
     */
    public function cleanFilesSystemUrls(array $urlsAry = [], string $folder = '') : mixed
    {
        try {
            if (!empty($urlsAry)) {
                foreach ($urlsAry as $file) {
                    file_exists(IMAGE_ROOT . $folder . DS . $file) ? unlink(IMAGE_ROOT . $folder . DS . $file) : '';
                    file_exists(IMAGE_ROOT_SRC . $folder . DS . $file) ? unlink(IMAGE_ROOT_SRC . $folder . DS . $file) : '';
                }
            }
            return true;
        } catch (\Throwable $th) {
            throw new FileSystemManagementException('Impossible de supprimer les fichiers! ' . $th->getMessage(), $th->getCode());
        }
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