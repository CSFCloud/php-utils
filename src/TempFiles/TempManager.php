<?php

namespace CSFCloud\TempFiles;

use Exception;
use CSFCloud\TempFiles\TempFile;

class TempManager {

    const TEMP_DIR = __DIR__ . "/temp";

    private $dir;

    public function __construct(string $context) {
        if (!preg_match('/^[a-z0-9\_\-]+$/i', $context)) {
            throw new Exception("The temp context must be an alphanumeric string");
        }
        if (!file_exists(self::TEMP_DIR)) {
            mkdir(self::TEMP_DIR, 0777, true);
        }
        if (is_writable(self::TEMP_DIR)) {
            $this->dir = self::TEMP_DIR . "/" . $context;
        } else {
            $this->dir = sys_get_temp_dir() . "/csf/" . $context;
        }
        $this->dir = self::TEMP_DIR . "/" . $context;
        if (!file_exists($this->dir)) {
            if (!mkdir($this->dir, 0777, true)) {
                throw new Exception("Creating new directory failed (" . $this->dir . ")");
            }
        }
        if (!is_writable($this->dir)) {
            throw new Exception("The temp directory must be writeable (" . $this->dir . ")");
        }
    }

    private function IdToPath(string $id) : string {
        $path = $this->dir . "/" . $id;
        if (!file_exists($path)) {
            touch($path);
        }
        return $path;
    }

    public function createFile() : TempFile {
        $id = uniqid("", true);
        return new TempFile($id, $this->IdToPath($id));
    }

    public function getFile(string $id) : TempFile {
        return new TempFile($id, $this->IdToPath($id));
    }

}