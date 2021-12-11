<?php

declare(strict_types=1);

interface EntityManagerInterface
{
    /**
     * --------------------------------------------------------------------------------------------------
     * Insert query.
     * @return object
     */
    public function getCrud():Object;
}