<?php

declare(strict_types=1);
use Symfony\Component\Yaml\Yaml;

class YamlFile
{
    public static function get(string $File) : array
    {
        foreach (glob(CONFIG_PATH . DS . '*.yaml') as $file) {
            if (!file_exists($file)) {
                throw new BaseException($file . ' does not exist');
            }
            $parts = parse_url($file);
            $path = $parts['path'];
            if (strpos($path, $File) !== false) {
                return Yaml::parseFile(filename: $file);
            }
        }
    }

    public static function parsef(string $file, string $keytoReturn) : array
    {
        $content = Yaml::parseFile(filename: $file);
        if (!isset($content[$keytoReturn])) {
            throw new InvalidArgumentException(message : "Invalid key [$keytoReturn] in YAML File {$file}");
        }

        return $content[$keytoReturn];
    }
}
