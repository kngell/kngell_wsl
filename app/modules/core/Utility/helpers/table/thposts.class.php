<?php

declare(strict_types=1);
class THPosts
{
    /**
     * Construct.
     * ===============================================================================.
     * @param Token $token
     * @param Form $form
     */
    public function __construct(private Token $token, private Form $form, private PostsManager $pm)
    {
    }

    public function postsTable(array $data = []) : string
    {
        $output = '<div id="tbl-alertErr"></div>';
        $output .= '<table id="ecommerce-datatable" class="table table-middle table-hover table-responsive">
            <thead>
                <tr>
                    <th scope="col" style="width:2%" class="text-center">#</th>
                    <th scope="col">Image</th>
                    <th scope="col" style="width:20%">Post Title</th>
                    <th scope="col">Post Content</th>
                    <th scope="col">Author</th>
                    <th scope="col">Date</th>
                    <th scope="col" style="width:20%">Actions</th>
                </tr>
            </thead>
                    <tbody>';
        foreach ($data as $item) {
            $active = $item->postStatus == 'on' ? "style='color:green'" : '';
            $txtactive = $item->postStatus == 'on' ? 'Active Post' : 'Inactive Post';
            $media = !empty($item->postImg) ? ImageManager::asset_img(unserialize($item->postImg)[0]) : ImageManager::asset_img('product-80x80.jpg');
            $output .= ' <tr>
                <th scope="row" class="text-center">' . $item->{$this->pm->get_colID()} . '</th>
                <td>
                    <a href="#">
                        <img class="img-thumbnail" alt="Product" src="' . $media . '" width="48">
                    </a>
                </td>
                <td>' . $this->pm->htmlDecode($item->postTitle) . '</td>
                <td>' . $this->pm->getContentOverview($item->postContent) . '</td>
                <td>' . $this->pm->htmlDecode($item->postAuthor) . '</td>
                <td>' . $item->postDate . '</td>
                <td class="action">
                    ' . $this->get_form_action($item, $active, $txtactive) . '
                </td>   
            </tr>';
        }
        $output .= '</tbody></table>';

        return $output;
    }

    private function get_form_action(Object $item, string $active, string $txtactive) : string
    {
        $formString = '';
        foreach ($this->form_params($item) as $key => $params) {
            $formString .= $this->form->globalAttr($params)->begin();
            $formString .= $this->get_button($key, $active, $txtactive);
            $formString .= $this->form->end();
        }
        return $formString;
    }

    private function get_button(string $index, string $active, string $txtactive) : BaseField
    {
        $class = '';
        $text = '';
        $attr = [];
        $title = '';
        switch ($index) {
            case 'status':
                $class .= 'text-danger activateBtn';
                $text .= '<i class="fal fa-power-off fa-lg" ' . $active . '></i>';
                $title = $txtactive;
                break;
            case 'edit':
                $class .= 'text-primary editBtn mx-2';
                $text .= '<i class="fal fa-edit fa-lg"></i>';
                $attr = [
                    // 'data-bs-toggle'=>'modal',
                    // 'data-bs-target'=>'#modal-box',
                ];
                $title = 'Edit Post';
                break;
            case 'delete':
                $class .= 'text-danger deleteBtn';
                $text .= '<i class="fal fa-trash-alt fa-lg"></i>';
                $title = 'Delete Post';
                $type = 'submit';
                break;

            default:
                // code...
                break;
        }
        return $this->form->button(isset($type) ? $type : '')->noWrapper()->title($title)->class($class)->text($text)->attr($attr);
    }

    private function form_params(Object $obj) : array
    {
        return [
            'status'=> [
                'action'=>'#',
                'method' => 'post',
                'formClass' => 'posts-status',
                'formID' => 'posts-status' . $obj->postID,
                'token'=>$this->token,
                'alertErr'=>true,
                'inputHidden'=>[
                    'postID'=> $obj->postID,
                ],
            ],
            'edit'=> [
                'action'=>'#',
                'method' => 'post',
                'formClass' => 'edit_post-status mx-2',
                'formID' => 'edit_post-status' . $obj->postID,
                'token'=>$this->token,
                'alertErr'=>true,
                'inputHidden'=>[
                    'postID'=> $obj->postID,
                ],
            ],
            'delete'=>[
                'action'=>'#',
                'method' => 'post',
                'formClass' => 'delete-posts-status',
                'formID' => 'delete-posts-status' . $obj->postID,
                'token'=>$this->token,
                'alertErr'=>true,
                'inputHidden'=>[
                    'postID'=> $obj->postID,
                ],
            ],
        ];
    }
}