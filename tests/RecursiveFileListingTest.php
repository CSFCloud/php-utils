<?php

namespace CSFCloud\Tests;

use PHPUnit\Framework\TestCase;
use CSFCloud\RecursiveFileListing;

final class RecursiveFileListingTest extends TestCase {

    /**
     * @expectedException Exception
     */
    public function testInvalidDirectory() {
        $finder = new RecursiveFileListing(__DIR__ . "/not_existing_dir");
    }

    public function testFindFiles() {
        $finder = new RecursiveFileListing(__DIR__ . "/RecFileListTestDir");
        $files = $finder->scan();

        $this->assertEquals(3, count($files));
    }

    public function testFilterFiles() {
        $finder = new RecursiveFileListing(__DIR__ . "/RecFileListTestDir");
        $finder->addFilter('/.*\.txt$/i');
        $files = $finder->scan();

        $this->assertEquals(2, count($files));
    }

}