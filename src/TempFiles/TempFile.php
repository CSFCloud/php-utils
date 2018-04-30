<?php

namespace CSFCloud\TempFiles;

class TempFile {

    private $id;
    private $full_path;

    public function __construct(string $id, string $path) {
        $this->id = $id;
        $this->full_path = $path;
    }

    public function getId() : string {
        return $this->id;
    }

    public function getPath() : string {
        return $this->full_path;
    }

    public function __toString(): string {
        return $this->full_path;
    }

    public function getText() : string {
        return file_get_contents($this->full_path);
    }

    public function getLastLines(int $lines = 10, $buffer = 4096) : string {
        $f = fopen($this->full_path, "rb");
        fseek($f, -1, SEEK_END);
        if(fread($f, 1) != "\n") {
            $lines -= 1;
        }
        $output = '';
        $chunk = '';

        while(ftell($f) > 0 && $lines >= 0) {
            $seek = min(ftell($f), $buffer);
            fseek($f, -$seek, SEEK_CUR);
            $output = ($chunk = fread($f, $seek)).$output;
            fseek($f, -mb_strlen($chunk, '8bit'), SEEK_CUR);
            $lines -= substr_count($chunk, "\n");
        }

        while($lines++ < 0) {
            $output = substr($output, strpos($output, "\n") + 1);
        }
    
        fclose($f);
        return $output;
    }

    public function delete() {
        unlink($this->full_path);
    }

}