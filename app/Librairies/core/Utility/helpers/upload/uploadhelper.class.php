<?php

declare(strict_types=1);

class UploadHelper
{
    public function __construct(private ImageManager $im)
    {
    }

    public static function upload(array $path, array $file)
    {
        $targetDir = $path[1];
        //filename
        $filename = (pathinfo(basename($file['name']))['filename'] == 'image') ? self::rename_image($file) : basename($file['name']);
        $targetFilePath = $targetDir . DS . $filename;
        //upload file in the server
        if (!file_exists($path[0] . $targetFilePath)) {
            if (move_uploaded_file($file['tmp_name'], $path[0] . $targetFilePath)) {
                if (!file_exists(IMAGE_ROOT_SRC . $targetFilePath) && !in_array($path[1], ['posts'])) {
                    copy($path[0] . $targetFilePath, IMAGE_ROOT_SRC . $targetFilePath);
                }
                return $targetFilePath;
            }
            return 'error';
        }
        return  $targetFilePath;
    }

    //rename image
    public static function rename_image($file)
    {
        $ext = pathinfo(basename($file['name']), PATHINFO_EXTENSION);

        // return md5(rand('image')) . '.' . $ext;
    }

    public function upload_files($files, $model)
    {
        $paths = [];
        $status = [];
        if ($files) {
            foreach ($files as $file) {
                $result = self::validate_and_upload_file($file, $model, $this->im);
                if ($result['status'] == true) {
                    if ($result['url'] != '') {
                        $paths[] = $result['url'];
                        $result['url'] = ImageManager::asset_img($result['url']);
                    } else {
                        return ['success' => true, 'msg' => ''];
                    }
                } else {
                    return ['success' => false, 'msg' => FH::showMessage('danger', $result['msg']), 'upload_result' => $result];
                }
                $status[] = $result;
            }
            if ($paths) {
                self::cleanFiles($paths, $model);
                return ['success' => true, 'msg' => self::updateModel(serialize($paths), $model), 'upload_result' => $status];
            }
            return ['success' => false, 'msg' => self::updateModel('', $model), 'upload_result' => $status];
        } else {
            self::cleanFiles([], $model);
        }
        return ['success' => true, 'msg' => self::updateModel('', $model), 'upload_result' => $status];
    }

    // Clean files
    public static function cleanFiles($paths, $m)
    {
        $actual_files = self::modelImageField($m);
        if ($actual_files) {
            $real_paths = array_filter($actual_files, function ($path) use ($paths, $m) {
                $u = explode(DS, $path);
                $file = array_pop($u);
                $targetpth = array_pop($u) . DS . $file;
                if (!in_array($targetpth, array_merge($paths, ['users' . DS . 'avatar.png', '\users/avatar.png']))) {
                    $del = self::deleteImage($targetpth, $m);
                } else {
                    return $path;
                }
            });

            return $real_paths;
        }

        return false;
    }

    //Validate file
    public static function validate_and_upload_file($file, $model, $imgMgr)
    {
        $path_dir = self::get_path($model)[1];

        $arr_file = [
            'allowType' => ['JPG', 'PNG', 'JPEG', 'GIF', 'PDF', 'DOC', 'DOCX'],
            'filename' => basename($file['name']),
            'targetDir' => $path_dir,
            'targetFilePath' => $path_dir . DS . basename($file['name']),
            'fileType' => strtoupper(pathinfo($path_dir . DS . basename($file['name']), PATHINFO_EXTENSION)),
            'size' => 10 * 1024 * 1024,
        ];
        if (empty($file['tmp_name'])) {
            $status['msg'] = 'Success';
            $status['url'] = '';
            $status['status'] = true;

            return $status;
        }
        $imgMgr->init($file['tmp_name'], '', IMAGE_ROOT . $path_dir);
        $status = [
            'name' => $file['name'],
            'type' => $file['type'],
            'url' => '',
            'msg' => 'Invalid Type',
            'status' => false,
        ];
        // Validate file Name
        if (empty($arr_file['filename'])) {
            $status['status'] = true;
            $media = self::get_mediaKey($model);
            $status['url'] = $model->$media != '' ? $model->$media : '';
            $arr_file = [];
            $path_dir = '';

            return $status;
        }
        //Validate type
        if (!in_array($arr_file['fileType'], $arr_file['allowType'])) {
            $arr_file = [];
            $path_dir = '';

            return $status;
        }
        // Validate length width
        $img_infos = $imgMgr->get_infos(); //get_imagesInfos($file['tmp_name']);
        if ($img_infos[0] > '1840' && $img_infos[1] > '860') {
            $status['msg'] = 'Invalid file size! Please change your file.';
            $arr_file = [];
            $path_dir = '';

            return $status;
        }
        //Validate size
        if ($file['size'] == 0) {
            $mediaKey = self::get_mediaKey($model);
            if ($mediaKey == 'profileImage' && $model->$mediaKey == '') {
                $status['url'] = ImageManager::asset_img('users' . US . 'avatar.png');
                $arr_file = [];
                $path_dir = '';

                return $status;
            }
        }
        if ($file['size'] > $arr_file['size']) {
            $status['msg'] = 'Invalid file size! Please change your file.';
            $arr_file = [];
            $path_dir = '';

            return $status;
        }
        //Validate existing file
        $actual_Path = self::modelImageField($model);
        if ($actual_Path !== false && file_exists(IMAGE_ROOT . $arr_file['targetFilePath'])) {
            if (in_array(IMG . $arr_file['targetFilePath'], $actual_Path)) {
                $status['msg'] = 'Success';
                $status['url'] = $arr_file['targetFilePath'];
                $status['status'] = true;
                $arr_file = [];
                $path_dir = '';
                $actual_Path = '';

                return $status;
            }
        }
        $path = self::upload(self::get_path($model), $file, $imgMgr);
        if ($path == 'error') {
            $arr_file = [];
            $path_dir = '';
            $actual_Path = '';
            $status['msg'] = 'Error moving file to Server';
        } else {
            $status['msg'] = 'Success';
            $status['url'] = $path;
            $status['status'] = true;
        }
        $arr_file = [];
        $path_dir = '';
        $actual_Path = '';
        $path = '';

        return $status;
    }

    //re-arrange files
    public static function reArrayFiles($files, $m)
    {
        $fileAry = [];
        $file_count = count($files['name']);
        $file_keys = array_keys($files);

        for ($i = 0; $i < $file_count; $i++) {
            foreach ($file_keys as $key) {
                $fileAry[$i][$key] = $files[$key][$i];
            }
        }

        return $fileAry;
    }

    //Get image key
    public static function get_mediaKey($model)
    {
        $table = $model->get_tableName();
        switch (true) {
            case $table == 'realisations':
                return 'brand';
                break;
            case in_array($table, ['users', 'user_sessions']):
                return 'profileImage';
                break;
            case $table == 'posts':
                return 'postImg';
                break;
            case $table == 'candidatures':
                return 'cv';
                break;
            case $table == 'formations_inscriptions':
                return 'cv';
                break;
            case $table == 'post_file_url':
                return 'fileUrl';
                break;
            case in_array($table, ['products', 'sliders']):
                return 'p_media';
                break;
            default:
                // code...
                break;
        }
    }

    //upload post Url base 64
    public static function uploadPostUrl($postContent)
    {
        $dom = new \DomDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        $dom->loadHtml($postContent);
        $images = $dom->getElementsByTagName('img');
        $bs64 = 'base64'; //variable to check the image is base64 or not
        foreach ($images as $k => $img) {
            $data = $img->getAttribute('src');
            if (strpos($data, $bs64) == true) { //if the Image is base 64
                $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data));
                $image_name = 'post_url' . time() . $k . '.png';
                $path = UPLOAD_ROOT . 'postsImg' . DS . $image_name;
                file_put_contents($path, $data);
                $img->removeAttribute('src');
                $img->setAttribute('src', $image_name);
            } else { //put '/' to prevent lossing image  actual path
                $image_name = '/' . $data;
                $img->setAttribute('src', $image_name);
            }
        }
        $editor_content_save = $dom->saveHTML();

        return $editor_content_save;
    }

    public function getMadiaAry(array $data, Request $request) : array
    {
        return array_filter(json_decode($request->htmlDecode($data['imageUrlsAry'])), function ($url) {
            if (null != $url) {
                return trim($url);
            }
        });
    }

    public function getFileAryFromModel(Model $media) : array
    {
        return array_map(function ($m) {
            return basename(unserialize($m->fileUrl)[0]);
        }, $media->get_results());
    }

    public function getMediaModel(string $url, Model $model) : ?Model
    {
        if ($model->count() > 0) {
            return current(array_filter($model->get_results(), function ($m) use ($url) {
                if (basename(unserialize($m->fileUrl)[0]) == basename($url)) {
                    return $m;
                }
            }));
        }
    }

    public function manage_uploadImage($lastID, $data, Request $request, container $container)
    {
        if (isset($data['folder']) && isset($data['imageUrlsAry'])) {
            $imgUrlsAry = $this->getMadiaAry($data, $request);
            $tempUrls = $container->make(PostFileUrlManager::class)->getDbUrls($lastID, $data['folder']);
            if ($tempUrls->count() > 0) {
                switch (true) {
                    case empty($imgUrlsAry):
                        $tempUrls->cleanUnusedUrls($data['folder']);
                        break;
                    default:
                        $bdUrlsAry = $this->getFileAryFromModel($tempUrls);
                        foreach ($imgUrlsAry as $key => $url) {
                            if (in_array(basename($url), $bdUrlsAry)) {
                                if (($m = $this->getMediaModel($url, $tempUrls)) != null) {
                                    if (!isset($m->imgID) || ($m->imgID == null)) {
                                        $m->id = $m->pfuID;
                                        $m->imgID = $lastID . $data['folder'];
                                        if ($m->save()) {
                                            !file_exists(IMAGE_ROOT_SRC . $data['folder'] . DS . basename($url)) ? copy(IMAGE_ROOT . $data['folder'] . DS . basename($url), IMAGE_ROOT_SRC . $data['folder'] . DS . basename($url)) : '';
                                        }
                                    }
                                }
                            }
                        }
                        $tempUrls->cleanUnusedUrls($data['folder']);
                        break;
                }
            } else {
                $tempUrls->saveFilesUrls($imgUrlsAry, $data['folder'], $lastID);
            }
            return $tempUrls->cleanDiskFiles($data['folder']);
        }
        return false;
    }

    // //remove unused urls
    // public function removeUnusedUrls(PostFileUrlManager $tempUrls)
    // {
    //     $postsFiles = array_diff(scandir(IMAGE_ROOT . 'posts'), ['.', '..']);
    //     if ($tempUrls->count() > 0) {
    //         $urlsAry = array_column($tempUrls->get_results(), 'fileUrl');
    //         foreach ($postsFiles as $key => $file) {
    //             if (!in_array(ImageManager::asset_img('posts' . DS . $file), $urlsAry)) {
    //                 unlink(IMAGE_ROOT . 'posts' . DS . $file);
    //                 $tempUrls->delete('', ['fileUrl' => ImageManager::asset_img('postsImg' . DS . $file)]);
    //             }
    //         }
    //     }
    // }

    //Delete Image
    private static function deleteImage($path, $m)
    {
        if ($m->getAllItem(['where' => [self::get_mediaKey($m) => serialize([$path])]])->count() <= 1) {
            if (file_exists(IMAGE_ROOT . $path)) {
                unlink(IMAGE_ROOT . $path);
                unlink(str_replace('public', 'src', IMAGE_ROOT) . $path);

                return true;
            }
        }

        return false;
    }

    //update model
    private static function updateModel($path, $model)
    {
        $table = $model->get_tableName();
        $md = $model;
        switch (true) {
            case $table == 'realisations':
                $md->brand = $path;
                break;
            case $table == 'users':
                $md->profileImage = $path;
                break;
            case $table == 'posts':
                $md->postImg = $path;
                break;
            case $table == 'candidatures':
                $md->cv = $path;
                break;
            case $table == 'formations_inscriptions':
                $md->cv = $path;
                break;
            case $table == 'post_file_url':
                $md->fileUrl = $path;
                break;
            case in_array($table, ['products', 'sliders']):
                $md->p_media = $path;
                break;

            default:
                // code...
                break;
        }

        return $md;
    }

    //get path
    private static function get_path($model)
    {
        $table = $model->get_tableName();
        switch ($table) {
            case 'realisations':
                $path = [IMAGE_ROOT, 'brand'];
                break;
            case 'users':
                $path = [IMAGE_ROOT, 'users'];
                break;
            case 'posts':
                $path = [IMAGE_ROOT, 'blog-post'];
                break;
            case 'candidatures':
                $path = [IMAGE_ROOT, 'candidats'];
                break;
            case 'post_file_url':
                $path = [IMAGE_ROOT, 'posts'];
                break;
            case 'products':
                $path = [IMAGE_ROOT, 'products'];
                break;
            case 'sliders':
                $path = [IMAGE_ROOT, 'sliders'];
                break;
            default:
                // code...
                break;
        }

        return $path;
    }

    // Image field
    private static function modelImageField($model)
    {
        $table = $model->get_tableName();
        switch (true) {
            case $table == 'realisations':
                return !empty($model->brand) ? $model->brand : false;
                break;
            case $table == 'users':
                return !empty($model->profileImage) ? [$model->profileImage] : false;
                break;
            case $table == 'candidatures':
                return !empty($model->cv) ? $model->cv : false;
                break;
            case $table == 'posts':
                return !empty($model->postImg) ? $model->postImg : false;
                break;
            case $table == 'post_file_url':
                return isset($model->fileUrl) ? $model->fileUrl : false;
                break;
            case in_array($table, ['products', 'sliders']):
                return !empty($model->p_media) ? $model->p_media : false;
                break;

            default:

                break;
        }
    }
}