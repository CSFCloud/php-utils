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
        if (!is_writable(self::TEMP_DIR)) {
            throw new Exception("The temp directory must be writeable (" . self::TEMP_DIR . ")");
        }
        $this->dir = self::TEMP_DIR . "/" . $context;
        if (!file_exists($this->dir)) {
            mkdir($this->dir);
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
        return $this->createFile($id, $this->IdToPath($id));
    }

    public function getFile(string $id) : TempFile {
        return new TempFile($id, $this->IdToPath($id));
    }

}