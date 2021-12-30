<?php

declare(strict_types=1);
class Model extends AbstractModel
{
    protected Object $repository;
    protected ContainerInterface $container;
    protected MoneyManager $money;
    protected $validates = true;
    protected $_results;
    protected $_count;
    protected $_modelName;
    protected $_softDelete = false;
    protected $_deleted_item = false;
    protected $_current_ctrl_method = 'update';
    protected $validationErr = [];
    protected $_lasID;

    /**
     * Main Constructor
     * =======================================================================================================.
     * @param string $tableSchema
     * @param string $tableSchemaID
     */
    public function __construct(string $tableSchema, string $tableSchemaID)
    {
        $this->set_container();
        $this->set_money();
        $this->throwException($tableSchema, $tableSchemaID);
        $this->createRepository($tableSchema, $tableSchemaID);
    }

    /**
     * Soft Delete
     * =======================================================================.
     * @param [type] $value
     * @return self
     */
    public function softDelete($value) : self
    {
        $this->_softDelete = $value;

        return $this;
    }

    /**
     * Current Controller Method
     * =======================================================================.
     * @param string $value
     * @return self
     */
    public function current_ctrl_method(string $value) : self
    {
        $this->_current_ctrl_method = $value;
        return $this;
    }

    /**
     * Get Data Repository method
     * =======================================================================================================.
     * @return DataRepositoryInterface
     */
    public function getRepository() : DataRepository
    {
        return $this->repository;
    }

    /**
     * Create the model repositories
     * =========================================================================================================.
     * @param string $tableSchema
     * @param string $tableSchemaID
     * @return void
     */
    public function createRepository(string $tableSchema, string $tableSchemaID): void
    {
        $this->repository = $this->container->make(DataRepositoryFactory::class)->initParams('crudIdentifier', $tableSchema, $tableSchemaID)->create(DataRepository::class);
    }

    public function set_container()
    {
        if (!isset($this->container)) {
            $this->container = Container::getInstance();
        }

        return $this;
    }

    public function set_money()
    {
        if (!isset($this->money)) {
            $this->money = MoneyManager::getInstance();
        }

        return $this;
    }

    public function get_money() : MoneyManager
    {
        return $this->money;
    }

    /**
     * Get All items
     * =========================================================================================================.
     * @param array $data
     * @param array $params
     * @param array $tables
     * @return self
     */
    public function getAllItem(array $data = [], array $tables = [], array $params = []) : ?self
    {
        $params = array_merge($this->setQueryParams($data), $params);
        $data = $this->set_deleted_Params($data);
        if (isset($data['return_mode']) && $data['return_mode'] == 'class' && !isset($data['class'])) {
            $data = array_merge($data, ['class' => get_class($this)]);
        }
        $results = $this->repository->findBy([], [], $params, array_merge($data, $tables));
        $this->_results = $results->count() > 0 ? $results->get_results() : null;
        $this->_count = $results->count() > 0 ? $results->count() : 0;
        $results = null;

        return $this;
    }

    /**
     * Get Html Decode texte
     * =========================================================================================================.
     * @param string $str
     * @return string
     */
    public function htmlDecode(?string $str) : ?string
    {
        return !empty($str) ? htmlspecialchars_decode(html_entity_decode($str), ENT_QUOTES) : '';
    }

    public function getContentOverview($content):string
    {
        // $headercontent = preg_match_all('|<h[^>]+>(.*)</h[^>]+>|iU', htmlspecialchars_decode($content, ENT_NOQUOTES), $headings);
        return substr(strip_tags($this->htmlDecode($content)), 0, 200) . '...';
    }

    /**
     * Get Detail
     * =========================================================================================================.
     * @param mixed $id
     * @param string $colID
     * @return self|null
     */
    public function getDetails(mixed $id, string $colID = '') : ?self
    {
        $data_query = ['where' => [$colID != '' ? $colID : $this->get_colID() => $id], 'return_mode' => 'class'];

        return $this->findFirst($data_query);
    }

    /**
     * Get By Index
     * =========================================================================================================.
     * @param string $index_value
     * @param array $params
     * @param array $tables
     * @return self|null
     */
    public function getAllbyIndex(string $index_value, array $params = [], array $tables = []) :?self
    {
        $data = array_merge(['where' => [$this->get_colIndex() => $index_value]], ['return_mode' => 'class'], ['class' => get_class($this)], $tables);
        $results = $this->repository->findBy([], [], [], $data);
        $this->_results = $results->count() > 0 ? $results->get_results() : null;
        $this->_count = $results->count() > 0 ? $results->count() : 0;
        $results = null;

        return $this;
    }

    /**
     * Save Data insert or update
     * =========================================================================================================.
     * @param array $params
     * @return void
     */
    public function save(array $params = []) : ?Object
    {
        if ($this->beforeSave($params)) {
            $fields = H::getObjectProperties($this);
            if (property_exists($this, 'id') && $this->id != '') {
                $fields = $this->beforeSaveUpadate($fields);
                $save = $this->update([(!isset($params['colID'])) ? $this->get_colID() : $params['colID'] => $this->id], $fields);
            } else {
                $fields = $this->beforeSaveInsert($fields);
                $save = $this->insert($fields);
            }
            if ($save->count() > 0) {
                $params['saveID'] = $save ?? '';

                return $this->afterSave($params);
            }
        }

        return null;
    }

    /**
     * Delete Data
     * =========================================================================================================.
     * @param mixed $params
     * @return void
     */
    public function delete(mixed $cond = null, array $params = [])
    {
        $conditions = [];
        switch (true) {
            case is_array($cond) && count($cond):
                $conditions = $cond;
                break;
            case $cond == 'all' && empty($params) && !isset($this->{$this->get_colID()}):
                $conditions = [$cond];
                break;
            default:
            if ($cond == '' || !isset($cond)) {
                if ($this->{$this->get_colID()} != null) {
                    $conditions = [$this->get_colID() => $this->{$this->get_colID()}];
                }
            } else {
                $conditions = [$this->get_colID() => $cond];
            }
                break;
        }

        return !empty($conditions) ? $this->run_delete($conditions, $params) : null;
    }

    //Partial save
    public function partial_save($data = [], $params = [], $table = '', array $init_insert = [])
    {
        if (!empty($table)) {
            $m = str_replace(' ', '', ucwords(str_replace('_', ' ', $table)));
            $p_data = $this->container->make($m . 'Manager')->getAllItem($params);
            if ($p_data->count() > 0) {
                $p_data = current($p_data->get_results());
                $p_data->id = $p_data->{$p_data->get_colID()};
            } else {
                if (is_array($init_insert) && count($init_insert) > 0) {
                    foreach ($init_insert as $key => $value) {
                        $p_data->$key = $value;
                    }
                }
            }
            $p_data->assign($data);
            if ($r = $p_data->save()) {
                $p_data = null;

                return $r;
            }
            $p_data = null;
        }

        return false;
    }

    public function validator(array $source = [], array $items = [])
    {
        FH::validate_forms($source, $items, $this);
    }

    //Get selected options
    public function get_Options($selected_optons = [], $m = null)
    {
        if (!$selected_optons) {
            return [];
        }
        $all_options = $m->getAllItem(['return_mode' => 'class'])->get_results();
        return  [array_map(
            function ($option) use ($m) {
                $colID = $option->get_colID();
                $title = $option->get_colTitle();

                return ['id' => (int) $option->$colID, 'text' => !empty($title) ? $this->htmlDecode($option->$title) : $m->get_customTitle($option)];
            },
            $all_options
        ), array_map(
            function ($id) {
                return $id;
            },
            array_keys($selected_optons)
        )];
    }

    public function notify($userID, $type, $message)
    {
        $fields = ['type' => $type, 'message' => $message, 'userID' => $userID];
        $this->insert($fields);
    }

    //check empty parent items, categories, brands, groups etc...
    public function check_forEmptyParent($parentID = '')
    {
        $colIndex = $this->get_colIndex();
        if ($colIndex == $this->get_colID()) {
            return '';
        }
        $childItems = property_exists($this, $colIndex) ? $this->getAllbyIndex($parentID) : null;
        // $otherlink = $this->search_relatedLinks($parentID, $this->get_tableName(), $this->get_colID());
        $output = '';
        if (isset($childItems) && $childItems->count() > 0) {
            $output .= '<span class="lead text-black-50"> There are releted items : </span>';
            $output .= '<div class="py-2 text-gray ps-3">';
            foreach ($childItems->get_results() as $childItem) {
                $ponctuation = $childItem === end($childItems) ? '.' : ',';
                $coltitle = $childItem->get_colTitle();
                $output .= '<p class="my-0 italic">' . $childItem->$coltitle . $ponctuation . '</p>';
            }

            $output .= ($childItems) ? '</div>' : '';
            $output .= ($childItems) ? '<span class="text-center pt-3" style="font-size:.9rem">Do you really want to delete it ?</span>' : '';
        }

        return $output;
    }

    /**
     * Get Container.
     *
     * @return ContainerInterface
     */
    public function get_container() : ContainerInterface
    {
        return $this->container;
    }

    /**
     * Throw an exception
     * ========================================================================================================.
     * @return void
     */
    private function throwException(string $tableSchema, string $tableSchemaID): void
    {
        if (empty($tableSchema) || empty($tableSchemaID)) {
            throw new BaseInvalidArgumentException('Your repository is missing the required constants. Please add the TABLESCHEMA and TABLESCHEMAID constants to your repository.');
        }
    }
}