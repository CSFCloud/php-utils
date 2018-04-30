<?php

namespace CSFCloud\Tests;

use PHPUnit\Framework\TestCase;
use CSFCloud\Shell\CommandRunner;

final class CommandRunnerTest extends TestCase {

    public function testRunningCommand() {
        $runner = new CommandRunner();
        $file = $runner->run(CommandRunner::COMMAND_SYNC, __DIR__ . "/RecFileListTestDir", "ls");
        $file_lists = $file->getText();
        $file->delete();
        $lines = explode("\n", $file_lists);

        $this->assertEquals(true, count($lines) > 2);
    }

}