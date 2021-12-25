<?php

declare(strict_types=1);
class Sanitizer
{
    /**
     * Clean Data
     * ======================================================================================.
     * @param mixed $dirtydata
     * @return mixed
     */
    public static function clean($dirtydata)
    {
        if (is_array($dirtydata) && count($dirtydata) > 0) {
            $input = [];
            foreach ($dirtydata as $key => $value) {
                if (!isset($key)) {
                    throw new BaseInvalidArgumentException('Invalid Key');
                }
                if (!is_array($value)) {
                    $value = htmlspecialchars(trim(stripslashes($value)), ENT_QUOTES, 'UTF-8');
                }
                $input[$key] = self::validate($value);
            }
            if (isset($input) && count($input) > 0) {
                return $input;
            }
        } else {
            $input = htmlspecialchars(trim(stripslashes($dirtydata)), ENT_QUOTES, 'UTF-8');

            return self::validate($input);
        }
    }

    public static function cleanOutputModel(Object $m) : Object
    {
        foreach ($m as $key => $value) {
            if (is_string($value)) {
                $m->$key = $m->htmlDecode($m->$key);
            }
        }

        return $m;
    }

    /**
     * Support clean Data
     * ======================================================================================.
     * @param [type] $value
     * @return void
     */
    private static function validate($value)
    {
        switch (true) {
            case is_int($value):
                return  isset($value) ? filter_var($value, FILTER_SANITIZE_NUMBER_INT) : '';
                break;
            case is_bool($value):
                return  isset($value) ? filter_var($value, FILTER_VALIDATE_BOOLEAN) : '';
                break;

            case is_numeric($value):
                   return isset($value) ? filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : '';
                break;
            case is_array($value) && !empty($value):
                    $arr = [];
                    foreach ($value as $k => $v) {
                        if (is_int($v)) {
                            $arr[$k] = isset($v) ? filter_var($value, FILTER_SANITIZE_NUMBER_INT) : '';
                        } else {
                            $arr[$k] = isset($v) ? filter_var($value, FILTER_SANITIZE_STRING) : '';
                        }
                    }

                    return $arr;
                 break;
            default:
             return isset($value) ? filter_var($value, FILTER_SANITIZE_STRING) : '';
                break;
        }
    }
}