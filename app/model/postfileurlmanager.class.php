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

    public function getMediaModel(string $url, Model $model) : ?Model
    {
        if ($model->count() > 0) {
            $m = current(array_filter($model->get_results(), function ($m) use ($url) {
                if (basename(unserialize($m->fileUrl)[0]) == $url) {
                    return $m;
                }
            }));
            return !is_object($m) ? null : $m;
        }
    }

    public function model_diff(array $clientAry, array $dbAry) : array
    {
        if (isset($clientAry) && isset($dbAry)) {
            foreach ($clientAry as $cm) {
                if (null != $cm) {
                    foreach ($dbAry as $key=>$bm) {
                        if ($cm == $bm) {
                            unset($dbAry[$key]);
                        }
                    }
                }
            }
            return array_values($dbAry);
        }
    }

    public function urlsToRemove(array $imgAry, Model $m) : array
    {
        if ($m->count() > 0 && isset($imgAry)) {
            $dbUslrsModel = [];
            foreach ($imgAry as $url) {
                $dbUslrsModel[] = $this->getMediaModel($url, $m);
            }
            return $this->model_diff($dbUslrsModel, $m->get_results());
        }
        return [];
    }

    public function cleanDbFilesUrls(array $clientAry = [], ?Model $m = null, string $folder = '') : bool
    {
        try {
            $urlToRemove = $this->urlsToRemove(!empty($clientAry) ? $clientAry : [], $m == null ? $this->getAllItem(['where'=>[$this->_colIndex=>'IS NULL'], 'return_mode'=>'class']) : $m);
            if (isset($urlToRemove) && is_array($urlToRemove) && !empty($urlToRemove)) {
                $filesToRemove = [];
                foreach ($urlToRemove as $m) {
                    if ($m->delete()) {
                        $filesToRemove[] = ['fileName'=>basename(unserialize($m->fileUrl)[0]), 'folder'=> $folder == '' ? dirname(unserialize($m->fileUrl)[0]) : $folder];
                    }
                }
                return $this->cleanFilesSystemUrls($filesToRemove);
            }
            return true;
        } catch (\Throwable $th) {
            throw new FileSystemManagementException('Impossible de supprimer les fichiers! ' . $th->getMessage(), $th->getCode());
        }

        return false;
    }

    public function fileAryFromModel(Model $media) : array
    {
        return array_map(function ($m) {
            return basename(unserialize($m->fileUrl)[0]);
        }, $media->get_results());
    }

    public function mediaAry(array $data, Request $request) : array
    {
        return array_map(function ($url) {
            return basename(trim($url));
        }, array_filter(json_decode($request->htmlDecode($data['imageUrlsAry']), true), function ($url) {
            return $url != null;
        }));
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
                    $f = '';
                    $fileToRemove = '';
                    if (is_array($file)) {
                        $f = $folder == '' ? $file['folder'] : $folder;
                        $fileToRemove = $f . DS . $file['fileName'];
                    } else {
                        if ($folder != '') {
                            $fileToRemove = $folder . DS . $file;
                        }
                    }
                    if ($fileToRemove != '') {
                        $urls = $this->getAllItem(['where'=>['fileUrl'=>serialize([$fileToRemove])], 'return_mode'=>'class']);
                        if ($urls->count() == 0) {
                            file_exists(IMAGE_ROOT . $fileToRemove) ? unlink(IMAGE_ROOT . $fileToRemove) : '';
                            file_exists(IMAGE_ROOT_SRC . $fileToRemove) ? unlink(IMAGE_ROOT_SRC . $fileToRemove) : '';
                        }
                    }
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