# CSFCloud Utils

## Contents
* Temporary file manager
* Command executer
* Recursive file lister

## Temporary file manager
Create a new temp file
```php
use CSFCloud\TempFiles\TempManager;

$tmp = new TempManager("my_context"); // Create a context
$file = $tmp->createFile(); // Create a new file
$file_path = $file->getPath(); // Get the full path to the file
$id = $file->getId(); // Get the file id, to access this file in an other session
```

Load existing temp file
```php
use CSFCloud\TempFiles\TempManager;

$tmp = new TempManager("my_context"); // Create a context
$file = $tmp->getFile("my_file_id"); // Load the file with the file id
```

## Command executer

Run a command
```php
use CSFCloud\Shell\CommandRunner;

$runner = new CommandRunner();
$output_file = $runner->run(CommandRunner::COMMAND_SYNC, __DIR__, "ls");
echo $output_file->getText();
$output_file->delete();
```

## Recursive file finder

Find files recursively  in a directory
```php
use CSFCloud\RecursiveFileListing;

$finder = new RecursiveFileListing(__DIR__ . "/my_directory");
$files = $finder->scan();

var_dump($files);
```

Find txt files recursively in a directory
```php
use CSFCloud\RecursiveFileListing;

$finder = new RecursiveFileListing(__DIR__ . "/my_directory");
$finder->addFilter('/.*\.txt$/i');
$files = $finder->scan();

var_dump($files);
```