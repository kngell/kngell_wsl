<?php

declare(strict_types=1);
class Response extends HttpGlobals
{
    public function setStatusCode(int $code)
    {
        http_response_code($code);
    }

    public function redirect(string $url)
    {
        header('Location: ' . $url);
    }

    public function is_image(string $file)
    {
        if (@is_array(getimagesize($file))) {
            $image = true;
        } else {
            $image = false;
        }
    }

    public function cacheRefresh()
    {
        $session = GlobalsManager::get('global_session');
        if ($session->exists(BRAND_NUM)) {
            switch ($session->get(BRAND_NUM)) {
                case 2:
                    Cache::getcache()->init()->delete('phone_products_and_cart.txt');
                    break;

                default:
                    Cache::getcache()->init()->delete('clothes_products_and_cart.txt');
                    break;
            }
        }
    }

    public function posts_frm_params()
    {
        return [
            'action'=>'#',
            'method' => 'post',
            'formClass' => 'posts-frm needs-validation',
            'formCustomAttr' => 'novalidate',
            'formID' => 'posts-frm',
            'fieldWrapperClass'=>'input-box',
            'token'=>Container::getInstance()->make(Token::class),
            'enctype'=>'multipart/form-data',
            'autocomplete'=>'nope',
            'alertErr'=>true,
            'inputHidden'=>[
                'postID'=>['id'=>'postID'],
                'postCommentCount'=> ['id'=>'postCommentCount'],
                'userID'=>['id'=>'userID'],
                'updateAt'=>['id'=>'updateAt'],
                'deleted'=>['id'=>'deleted'],
                'operation'=>['id'=>'operation'],
            ],
            'fieldCommonclass'=>[
                'fieldclass' => 'input-box__input',
                'labelClass' => 'input-box__label',
            ],
        ];
    }

    public function frm_params(string $frm_name, array $inpuHidden)
    {
        return [
            'action'=>'#',
            'method' => 'post',
            'formClass' => $frm_name . ' needs-validation',
            'formCustomAttr' => 'novalidate',
            'formID' => $frm_name,
            'fieldWrapperClass'=>'input-box',
            'token'=>Container::getInstance()->make(Token::class),
            'enctype'=>'multipart/form-data',
            'autocomplete'=>'nope',
            'alertErr'=>true,
            'inputHidden'=>$inpuHidden,
            'fieldCommonclass'=>[
                'fieldclass' => 'input-box__input',
                'labelClass' => 'input-box__label',
            ],
        ];
    }
}