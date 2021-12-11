<?php

declare(strict_types=1);

class GlobalsManager implements GlobalsManagerInterface
{
    /**
     * @inheritdoc
     *  =====================================================================
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function set(string $key, $value) : void
    {
        $GLOBALS[$key] = $value;
    }

    /**
     * Get Globals
     * =====================================================================.
     * @param string $key
     * @return SessionInterface
     * @throws GlobalManagerInvalidException
     */
    public static function get(string $key) :  SessionInterface
    {
        self::isglobalsValid($key);
        try {
            return $GLOBALS[$key];
        } catch (\Throwable $th) {
            throw new GlobalsManagerExceptions('An exception occured trying to retrieve Globals! ' . $key);
        }
    }

    /**
     * Check for Valid globals key and not empty
     * =====================================================================.
     * @param string $key
     * @return void
     * @throws GlobalManagerInvalidException
     */
    private static function isglobalsValid(string $key) :void
    {
        if (!isset($GLOBALS[$key])) {
            throw new GlobalManagerInvalidException('Invalid Globals Key! ' . $key);
        }
        if (empty($key)) {
            throw new GlobalManagerInvalidException('Empty key is not allowed! ' . $key);
        }
    }
}