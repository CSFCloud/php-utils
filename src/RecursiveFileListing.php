<?php

namespace CSFCloud;

use Exception;

class RecursiveFileListing {

    private $root_dir;
    private $files;
    private $filters = [];

    public function __construct(string $dir) {
        if (!is_dir($dir)) {
            throw new Exception("Directory required");
        }

        $this->root_dir = $dir;
    }

    public function addFilter(string $regex) {
        $this->filters[] = $regex;
    }

    public function clearFilters() {
        $this->filters = [];
    }

    public function scan() : array {
        $this->files = [];
        $this->recursiveScan($this->root_dir);
        sort($this->files);
        return $this->files;
    }

    private function recursiveScan(string $dir) {
        $files = scandir($dir);
        foreach ($files as $filename) {
            if ($filename !== "." && $filename !== "..") {
                $file_path = $dir . "/" . $filename;
                if (is_file($file_path) && $this->isFileOk($file_path)) {
                    $this->files[] = $file_path;
                } else if (is_dir($file_path)) {
                    $this->recursiveScan($file_path);
                }
            }
        }
    }

    private function isFileOk(string $path) : bool {
        if (count($this->filters) == 0) {
            return true;
        }

        foreach ($this->filters as $filter) {
            if (preg_match($filter, $path)) {
                return true;
            }
        }

        return false;
    }

}
