<?php

declare(strict_types=1);

class SessionManager
{
    public function __construct()
    {
    }

    public static function initialize()
    {
        $container = Container::getInstance();

        return $container->make(SessionFactory::class)->create('generic_session_name', SessionStorageInterface::class, YamlFile::get('session'));
    }
}
