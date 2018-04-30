<?php

namespace WrapIt\Tests;

use PHPUnit\Framework\TestCase;
use CSFCloud\RecursiveFileListing;

final class RecursiveFileListingTest extends TestCase {

    public function InvalidDirectory() {
        $this->expectException(Exception::class);

        $finder = new RecursiveFileListing(__DIR__ . "/not_existing_dir");
    }

    public function FindFiles() {
        $finder = new RecursiveFileListing(__DIR__ . "/RecFileListTestDir");
        $files = $finder->scan();

        $this->assertEquals(
            3,
            count($files)
        );
    }

    public function FilterFiles() {
        $finder = new RecursiveFileListing(__DIR__ . "/RecFileListTestDir");
        $files->addFilter('/.*\.txt$/i');
        $files = $finder->scan();

        $this->assertEquals(
            2,
            count($files)
        );
    }

}