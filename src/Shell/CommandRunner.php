<?php

namespace CSFCloud\Shell;

use CSFCloud\TempFiles\TempManager;
use CSFCloud\TempFiles\TempFile;
use Exception;

class CommandRunner {

    const COMMAND_SYNC = 1;
    const COMMAND_ASYNC = 2;

    private $tempmrg;

    public function __construct() {
        $this->tempmrg = new TempManager("csf_command_runner");
    }

    public function run(int $type, string $dir, string $command, bool $sudo = false) : TempFile {
        $tempfile = $this->tempmrg->createFile();
        $temp_path = $tempfile->getPath();

        $cmd = "cd " . escapeshellarg($dir) . " && ";
        if ($sudo === true) {
            $cmd .= "sudo ";
        }
        $cmd .= $command . " ";
        $cmd .= ">> " . escapeshellarg($temp_path) . " 2>> " . escapeshellarg($temp_path) . " ";

        if ($type === self::COMMAND_SYNC) {

        } else if ($type === self::COMMAND_ASYNC) {
            $cmd = "(" . $cmd . ") &";
        } else {
            throw new Exception("Invalid command type");
        }

        shell_exec($cmd);

        return $tempfile;
    }

}