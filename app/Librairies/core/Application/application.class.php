<?php

declare(strict_types=1);

class Application extends Container implements ApplicationInterface
{
    public static string $appRoot;
    public Controller $controller;
    protected RooterInterface $rooter;

    /**
     * Main constructor
     * ===============================================================================================================.
     * @param string $appRoot
     */
    public function __construct(?string $appRoot = null)
    {
        if ($appRoot) {
            $this->setAppRoot($appRoot);
        }
    }

    public function registerDatabaseClass()
    {
        $dataMapperEnvConfig = $this->singleton(DataMapperEnvironmentConfig::class, fn () => new DataMapperEnvironmentConfig(YamlFile::get('database')))->make(DataMapperEnvironmentConfig::class);
        $credentials = $dataMapperEnvConfig->getDatabaseCredentials('mysql');
        $this->singleton(DatabaseConnexionInterface::class, fn () => new DatabaseConnexion($credentials));
        $this->singleton(DataMapper::class, fn () =>new DataMapper($this->make(DatabaseConnexionInterface::class)));
        $this->singleton(QueryBuilder::class, fn () =>new QueryBuilder());
        $this->bind(DataMapperInterface::class, fn () => $this->make(DataMapper::class));
        $this->bind(QueryBuilderInterface::class, fn () => $this->make(QueryBuilder::class));
        $this->bind(EntityManagerFactory::class);
        $this->singleton(DataMapperFactory::class, fn () => new DataMapperFactory());
        // $this->bind(DataRepository::class, fn () =>$this->make(DataRepositoryFactory::class));
        $this->bind(DataRepositoryInterface::class, fn () =>$this->make(DataRepositoryFactory::class));
    }

    /**
     * Set the base path for the application.
     *
     * @param  string  $basePath
     * @return $this
     */
    public function setAppRoot($appRoot)
    {
        self::$appRoot = rtrim($appRoot, '\/');

        $this->bindPathsInContainer();

        return $this;
    }

    /**
     * Get the base path of the Laravel installation.
     *
     * @param  string  $path Optionally, a path to append to the base path
     * @return string
     */
    public function appRoot($path = '')
    {
        return self::$appRoot . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Determine if the given abstract type has been bound.
     *
     * @param  string  $abstract
     * @return bool
     */
    public function bound($abstract)
    {
        return $this->isDeferredService($abstract) || parent::bound($abstract);
    }

    /**
     * Determine if the given service is a deferred service.
     *
     * @param  string  $service
     * @return bool
     */
    public function isDeferredService($service)
    {
        return isset($this->deferredServices[$service]);
    }

    /**
     * Run
     * ==============================================================================================================.
     * @return self
     */
    public function run() :self
    {
        $this->constants();
        if (version_compare($phpVersion = PHP_VERSION, $coreVersion = Config::APP_MIN_VERSION, '<')) {
            die(sprintf('You are running php %s, but, the core framwork required at least PHP %s', $phpVersion, $coreVersion));
        }
        $this->registerDatabaseClass();
        $this->registerBaseBindings();
        $this->registerBaseAppSingleton();
        $this->environment();
        $this->errorHandler();
        // dump(YamlFile::get('database'));
        // die();
        return $this;
    }

    public function setSession() :self
    {
        SystemTrait::sessionInit(true);

        return $this;
    }

    public function setrouteHandler() :self
    {
        try {
            $this->rooter = $this->make(RooterFactory::class)->create(YamlFile::get('routes'));
            $this->rooter->resolve();
        } catch (\Exception $e) {
            $this->rooter->getResponse()->setStatusCode($e->getCode());
            $this->make(ErrorsController::class)->set_path('Client' . DS)
                ->set_token()
                ->iniParams(ErrorsController::class, '_errors')
                ->index(['exception' => $e]);
        }

        return $this;
    }

    public function handleCors()
    {
        $this->make(Cors::class)->handle();

        return $this;
    }

    public function getController()
    {
        return $this->controler;
    }

    public function setController($controller)
    {
        $this->controler = $controller;
    }

    public static function isGuest()
    {
        return true;
    }

    public function registerMiddleware(BaseMiddleWare $middleware) : void
    {
        $this->middlewares[] = $middleware;
    }

    /**
     * Register the basic bindings into the container.
     *
     * @return void
     */
    protected function registerBaseBindings()
    {
        static::setInstance($this);
        $this->instance('app', $this);
        $this->instance(Container::class, $this);
    }

    /**
     * Register the basic bindings into the container.
     *
     * @return void
     */
    protected function registerBaseAppSingleton()
    {
        $objs = AppHelper::singleton(self::$instance);
        if (is_array($objs)) {
            foreach ($objs as $obj => $value) {
                $this->singleton($obj, $value);
            }
        }
    }

    /**
     * Bind all of the application paths in the container.
     *
     * @return void
     */
    protected function bindPathsInContainer()
    {
        $this->instance('path.appRoot', $this->appRoot());
    }

    /**
     * Constant
     * ====================================================================================.
     * @return void
     */
    private function constants() :void
    {
        defined('DS') or define('DS', DIRECTORY_SEPARATOR);
        defined('APP_ROOT') or define('APP_ROOT', self::$appRoot);
        defined('CONFIG_PATH') or define('CONFIG_PATH', APP_ROOT . DS . 'config' . DS . 'yaml');
        defined('TEMPLATE_PATH') or define('TEMPLATE_PATH', APP_ROOT . DS . 'App' . DS);
        defined('LOG_DIR') or define('LOG_DIR', APP_ROOT . DS . 'temp' . DS . 'log');
    }

    /**
     * Environnement
     * ====================================================================================.
     * @return void
     */
    private function environment()
    {
        ini_set('default_charset', 'UTF-8');
    }

    /**
     * ErrorHandler
     * ====================================================================================.
     * @return void
     */
    private function errorHandler() : void
    {
        error_reporting(E_ALL | E_STRICT);
        set_error_handler('ErroHandling::errorHandler');
        set_exception_handler('ErroHandling::exceptionHandler');
    }
}