<?php

declare(strict_types=1);

abstract class AbstractModel implements ModelInterface
{
    public function get_defaultShippingClass()
    {
        if (!isset($this->p_shipping_class) || $this->p_shipping_class === null || $this->p_shipping_class == '[]') {
            $defaultShippingClass = $this->container->make(ShippingClassManager::class)->getAllItem(['where' => ['default_shipping_class' => ['value' => 0, 'operator' => '!=']]]);
            if ($defaultShippingClass->count() === 1) {
                return current($defaultShippingClass->get_results());
            }

            return null;
        }
    }

    /**
     * Get Results
     * =========================================================================================================.
     * @return mixed
     */
    public function get_results() : mixed
    {
        return isset($this->_results) ? $this->_results : [];
    }

    // Find select2 data
    public function getSelect2Data($params)
    {
        $search = strtolower($params['searchTerm']);
        $where = isset($params['parentID']) && $params['parentID'] != '' ? [$params['parentElt'] => $params['parentID']] : [];
        $data = $where ? $this->getAllItem(['where' => $where, 'return_mode' => 'class'])->get_results() : $this->getAllItem(['return_mode' => 'class'])->get_results();
        $colTitle = $this->get_colTitle();
        $output = array_filter($data, function ($item) use ($search, $colTitle) {
            return str_starts_with(strtolower($item->$colTitle), $search);
        });

        return array_map(
            function ($group) use ($colTitle) {
                $colID = $group->get_colID();

                return ['id' => (int) $group->$colID, 'text' => $this->htmlDecode($group->$colTitle)];
            },
            $output
        );
    }

    /**
     * Set Select2 Data
     * =========================================================================================================.
     * @param array $params
     * @return self
     */
    public function setselect2Data(array $params = []) : self
    {
        if (isset($this->select2_field)) {
            $field = in_array($this->get_tableName(), ['products', 'warehouse']) ? 'id' : 'text';
            foreach ($this->select2_field as $select2) {
                $select2_data = isset($params[$select2]) ? json_decode($this->htmlDecode($params[$select2]), true) : [];
                if ($select2_data && $select2_data[0]) {
                    $this->$select2 = $select2_data[0][$field];
                    $select2_data = null;
                }
            }
        }

        return $this;
    }

    /**
     * Global Before Save
     * =========================================================================================================.
     * @return void
     */
    public function beforeSave(array $params = []) : mixed
    {
        if (isset(AuthManager::$currentLoggedInUser->userID) && property_exists($this, 'userID')) {
            if (!isset($this->userID) || empty($this->userID) || $this->userID == null) {
                $this->userID = AuthManager::$currentLoggedInUser->userID;
            }
        }
        if (isset($this->msg)) {
            unset($this->msg);
        }
        if (isset($this->fileErr)) {
            unset($this->fileErr);
        }

        return true;
    }

    /**
     * Count num rows
     *  =========================================================================================================.
     */
    public function count()
    {
        return $this->_count;
    }

    //Find first corresponding record
    public function findFirst($params = []) : self
    {
        if (isset($params['return_mode']) && $params['return_mode'] == 'class' && !isset($params['class'])) {
            $params = array_merge($params, ['class' => get_class($this)]);
        }
        $params = $this->set_deleted_Params($params);
        $cond = $params['where'];
        unset($params['where']);
        $resultQuery = $this->repository->findOneBy($cond, $params);
        if ($resultQuery->count() <= 0) {
            $this->_count = 0;

            return $this;
        }
        $this->_count = $resultQuery->count();
        $this->_results = $this->afterFind($resultQuery)->get_results();

        return $this;
    }

    /**
     * After Find
     * ==========================================================================================================.
     * @param object $m
     * @return void
     */
    public function afterFind(?DataMapper $m = null) : DataMapper
    {
        if ($m->count() === 1) {
            $model = current($m->get_results());
            $media_key = UploadHelper::get_mediaKey($model);
            $model->$media_key = $model->$media_key !== null ? unserialize($model->$media_key) : ['products' . US . 'product-80x80.jpg'];
            if (is_array($model->$media_key)) {
                foreach ($model->$media_key as $key => $url) {
                    $model->$media_key[$key] = IMG . $url; // ImageManager::asset_img($url);//
                }
            }
            $m->get_results()[0] = $model;
        }

        return $m;
    }

    /**
     * Get Col ID or TablschemaID.
     *
     * @return string
     */
    public function get_colID() : string
    {
        return isset($this->_colID) ? $this->_colID : '';
    }

    //get indexed colID
    public function get_colIndex()
    {
        return isset($this->_colIndex) ? $this->_colIndex : '';
    }

    public function update(array $cond, array $fields) :self
    {
        if (isset($fields['id'])) {
            unset($fields['id']);
        }
        $this->_count = $this->repository->update($fields, $cond);

        return $this;
    }

    public function insert(array $fields) : self
    {
        if (empty($fields)) {
            return false;
        }
        $insert = $this->repository->create($fields);
        $this->_count = $insert != null ? 1 : 0;
        $this->_lastID = $insert;

        return $this;
    }

    public function assign($params) : self
    {
        $columns = $this->get_tableColumn();
        $params = is_object($params) ? (array) $params : $params;
        if (isset($columns) && !empty($columns) && isset($params) && !empty($params)) {
            foreach ($columns as $field) {
                if (in_array($field, array_keys($params))) {
                    $this->$field = $params[$field];
                }
            }

            return $this;
        }

        return false;
    }

    public function populate($params) : self
    {
        if (isset($params) && is_array($params) && !empty($params)) {
            foreach ($params as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }

            return $this;
        }

        return false;
    }

    public function beforeSaveUpadate(array $fields = [])
    {
        $f = $fields;
        $current = new DateTime();
        $key = current(array_filter(array_keys($fields), function ($field) {
            return str_starts_with($field, 'update');
        }));
        if ($key && !is_array($key)) {
            $f[$key] = $current->format('Y-m-d H:i:s');
        }
        if (isset($f[$this->get_colID()])) {
            unset($f[$this->get_colID()]);
        }

        return $f;
    }

    public function beforeSaveInsert($fields = [])
    {
        $f = $fields;
        if (array_key_exists('token', $f)) {
            unset($f['token']);
        }
        if (isset($f['fmt'])) {
            unset($f['fmt']);
        }
        if (isset($f['_modelName'])) {
            unset($f['_modelName']);
        }
        if (isset($f['_lastID'])) {
            unset($f['_lastID']);
        }
        foreach ($f as $key => $val) {
            if (empty($val) || $val == '[]') {
                unset($f[$key]);
            }
        }
        if (array_key_exists($this->get_colID(), $f)) {
            unset($f[$this->get_colID()]);
        }

        return $f;
    }

    public function afterSave(array $params = [])
    {
        return $params['saveID'] ?? null;
    }

    //Before delete
    public function beforeDelete($params = [])
    {
        return empty($params) ? true : $params;
    }

    //After delete
    public function afterDelete($params = [])
    {
        $params = null;

        return true;
    }

    //get table Name
    public function get_tableName()
    {
        return isset($this->_table) ? $this->_table : '';
    }

    public function get_tableColumn() : array
    {
        $columns = $this->repository->get_tableColumn(['return_mode' => 'object']);
        if ($columns->count() > 0) {
            $columnsName = [];
            foreach ($columns->get_results() as $column) {
                $columnsName[] = $column->Field;
            }
        }

        return $columnsName;
    }

    public function validationPasses()
    {
        return $this->validates;
    }

    public function get_unique(string $colid_name = '', string $prefix = '', string $suffix = '', int $token_length = 24)
    {
        $token = $this->get_container()->make(Token::class);
        $output = $prefix . $token->generate($token_length) . $suffix;
        while ($this->getDetails($output, $colid_name)->count() > 0) :
            $output = $prefix . $token->generate($token_length) . $suffix;
        endwhile;
        $token = null;

        return $output;
    }

    //set soft delete to true (=update)
    public function set_SoftDelete($value)
    {
        $this->_softDelete = $value;

        return $this;
    }

    //Check is new object
    public function isNew()
    {
        return (property_exists($this, 'id') && !empty($this->id)) ? false : true;
    }

    public function runValidation($validator)
    {
        $validator->run();
        $key = $validator->field;
        //dd($validator);
        if (!$validator->success) {
            $this->validates = false;
            $this->validationErr[$key] = $validator->msg;
        }
        //dd($validator);
    }

    //get errors when validating
    public function getErrorMessages()
    {
        return $this->validationErr;
    }

    //get title
    public function get_colTitle()
    {
        return isset($this->_colTitle) ? $this->_colTitle : '';
    }

    public function get_fieldName(string $table = '')
    {
        return $this->get_colTitle();
    }

    public function get_lastID() : ?int
    {
        return isset($this->_lastID) ? $this->_lastID : null;
    }

    public function set_container()
    {
        $this->container = Container::getInstance();

        return $this;
    }

    //Get selected options
    public function get_selectedOptions(?Object $m = null)
    {
        $response = [];
        $colTitle = $m->get_colTitle();
        $selected_option = null;
        if (isset($this->parentID) && $m->get_modeName() == $this->get_modeName()) {
            $selected_option = $m->getDetails($this->parentID);
        } else {
            $selected_option = $m->getDetails($this->{$m->colOptions});
        }
        if ($selected_option->count() === 1) {
            $selected_option = current($selected_option->get_results());
            $response[$selected_option->{$m->get_colID()}] = !empty($colTile) ? $this->htmlDecode($selected_option->$colTitle) : $this->get_customTitle($this);
        }

        return $response;
    }

    public function get_customTitle(?Object $m = null)
    {
        switch (true) {
            case $this->get_tableName() == 'orders' || $this->colOptions == 'ord_userID':
                return $m->lastName . ' ' . $m->firstName;
                break;
            default:
                // code...
                break;
        }
    }

    //Update status
    public function updateStatus($item = [])
    {
        $colID = $this->get_colID();
        $elts = $this->getDetails($item[$colID]);
        $output = '';
        if ($elts->count() === 1) {
            $elts = current($elts->get_results());
            if (!property_exists($elts, 'status')) {
                return '';
            } else {
                $elts->status = $elts->status == 'on' ? null : 'on';
            }
            $elts->id = $item[$colID];
            $output = '';
            if ($elts->save()) {
                if ($elts->status == 'on') {
                    $output = 'green';
                } else {
                    $output = '#dc3545';
                }
            }
        }

        return $output;
    }

    public function manageCheckboxes(array $params = []) : ?self
    {
        //Manage Checkboxes
        if (isset($this->checkboxes)) {
            foreach ($this->checkboxes as $checkbox) {
                if (!isset($params[$checkbox]) && isset($this->$checkbox)) {
                    $this->$checkbox = null;
                }
            }
        }

        return $this;
    }

    public function get_modeName()
    {
        return $this->_modelName;
    }

    //Get countrie
    public function get_countrie(string $ctr = '')
    {
        $data = file_get_contents(FILES . 'json' . DS . 'data' . DS . 'countries.json');
        $country = array_filter(array_column(json_decode($data, true), 'name'), function ($countrie) use ($ctr) {
            return $countrie == $ctr;
        }, ARRAY_FILTER_USE_KEY);

        return $country;
    }

    public function get_idFromString(array $params, string $name)
    {
        $arr = array_filter($params, function ($shClass) use ($name) {
            return str_starts_with($shClass, $name);
        }, ARRAY_FILTER_USE_KEY);

        return is_array($arr) && count($arr) === 1 ? $arr[$name] : null;
    }

    public function getDate($date, $format = 'd-m-y'): string
    {
        return $date !== null ? (new DateTime($date))->format($format) : '';
    }

    public function getLabel($attribute)
    {
        return $this->labels()[$attribute] ?? $attribute;
    }

    public function labels() : array
    {
        return [];
    }

    public function getFirstError($attribute)
    {
        return $this->validationErr[$attribute][0] ?? false;
    }

    public function hasError($attribute)
    {
        return $this->validationErr[$attribute] ?? false;
    }

    /**
     * Set deteleted attribute / or not
     * =========================================================================================================.
     * @param array $params
     * @return void
     */
    protected function set_deleted_Params(array $params)
    {
        if (property_exists($this, 'deleted')) {
            if (!$this->_deleted_item) {
                if (array_key_exists('where', $params) && is_array($params['where'])) {
                    $params['where'] = array_merge($params['where'], ['deleted' => !1]);
                } else {
                    $params['where'] = ['deleted' => !1];
                }
            } else {
                if (array_key_exists('where', $params) && is_array($params['where'])) {
                    $params['where'] = array_merge($params['where'], ['deleted' => 1]);
                } else {
                    $params['where'] = ['deleted' => 1];
                }
            }
        }

        return $params;
    }

    protected function setQueryParams(array $params)
    {
        if (!$limit = ModelHelper::array_search_recursive('limit', $params)) {
            $limit = [];
        }

        return $limit;
    }

    protected function run_delete(array $conditions, $params)
    {
        if ($params = $this->beforeDelete($params)) {
            if ($this->_softDelete) {
                $delete = $this->repository->update(isset($params['restore']) ? $params['restore'] : ['deleted' => 1], $conditions);
            } else {
                $delete = $this->repository->delete($conditions);
            }
            if ($delete > 0) {
                $del_actions = $this->afterDelete($params);
            }
        }

        return isset($del_actions) ? $del_actions : $delete;
    }
}