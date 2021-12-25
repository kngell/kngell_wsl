<?php

declare(strict_types=1);

class Files implements FileInterface
{
    /**
     * Get Files
     * ==============================================================================.
     * @param string $folder
     * @param string $file
     * @return mixed
     */
    public function get(string $folder, string $file = '') : mixed
    {
        $file = file_exists($folder . $file) ? [$folder . $file] : $this->search_file($folder, $file);
        if (isset($file) && count($file) === 1) {
            $file = current($file);
            $infos = pathinfo($file);

            return match ($infos['extension']) {
                'json' => json_decode(file_get_contents($file), true),
                'php' => '',
            };
        }

        return false;
    }

    public function createDir(string $folder) : bool
    {
        $path = realpath($folder);
        if ($path !== false and is_dir($path)) {
            return true;
        } else {
            try {
                mkdir($folder);
                return true;
            } catch (\Throwable $th) {
                throw new FileSystemManagementException('Impossible de créer le fichier! ' . $th->getMessage(), $th->getCode());
            }
        }
        return false;
    }

    private function search_file(string $folder, ?string $file_to_search = null, array &$results = []) : array
    {
        $files = ($folder !== false and is_dir($folder)) ? scandir($folder) : false;
        if ($files) {
            foreach ($files as $key => $value) {
                $path = realpath($folder . DS . $value);
                if (!is_dir($path)) {
                    if ($file_to_search == $value) {
                        $results[] = $path;
                    }
                } elseif ($value != '.' && $value != '..') {
                    $this->search_file($path, $file_to_search, $results);
                    if ($file_to_search == $value) {
                        $results[] = $path;
                    }
                }
            }

            return $results;
        }

        return false;
    }
}