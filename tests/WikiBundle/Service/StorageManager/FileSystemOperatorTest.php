<?php declare(strict_types=1);

namespace Tests\WikiBundle\Service\StorageManager;

use Tests\TestCase;
use WikiBundle\Service\StorageManager\FileSystemOperator;

/**
 * @see FilesystemOperator
 */
class FileSystemOperatorTest extends TestCase
{
    /**
     * @see FilesystemOperator::isDir()
     */
    public function testIsDir()
    {
        $this->assertFalse($this->getOperator()->isDir(__FILE__));
        $this->assertTrue($this->getOperator()->isDir(__DIR__));
    }

    /**
     * @see FilesystemOperator::isFile()
     */
    public function testIsFile()
    {
        $this->assertTrue($this->getOperator()->isFile(__FILE__));
        $this->assertFalse($this->getOperator()->isFile(__DIR__));
    }

    /**
     * @see FilesystemOperator::mkdir()
     * @see FilesystemOperator::remove()
     */
    public function testMkdir()
    {
        $this->assertTrue($this->getOperator()->mkdir('/tmp/fs-operator-test', 0754));
        $this->assertEquals('0754', substr(sprintf('%o', fileperms('/tmp/fs-operator-test')), -4));

        $this->assertTrue($this->getOperator()->remove('/tmp/fs-operator-test'));
    }

    /**
     * @see FilesystemOperator::read()
     * @see FilesystemOperator::readAsStream()
     */
    public function testWriteAndRead()
    {
        $testString = date('Y-m-d H:i:s') . '.' . microtime(true);

        $this->getOperator()->write('/tmp/testWriteAndRead', $testString);

        // readFile()
        $this->assertSame($testString, $this->getOperator()->readFile('/tmp/testWriteAndRead'));

        // readFileAsStream()
        $this->assertSame($testString, fgets($this->getOperator()->readFileAsStream('/tmp/testWriteAndRead'), 1024));
    }

    /**
     * @see FilesystemOperator::guessExtension()
     */
    public function testGuessExtension()
    {
        $this->assertSame('twig', $this->getOperator()->guessExtension('test.html.twig'));
        $this->assertSame('php', $this->getOperator()->guessExtension(__FILE__));
        $this->assertSame('', $this->getOperator()->guessExtension(__DIR__));
    }

    /**
     * @see FilesystemOperator::guessMimeType()
     */
    public function testGuessMimeType()
    {
        $this->assertSame('text/x-php', $this->getOperator()->guessMimeType(__FILE__));
        $this->assertSame('text/plain', $this->getOperator()->guessMimeType(__DIR__ . '/../../../../README.md'));
    }

    /**
     * @see FilesystemOperator::guessMimeType()
     */
    public function testFailureGuessMimeType()
    {
        $this->expectExceptionMessage('The file "/tmp/non-existing-directory-guess-mime-type" does not exist');
        $this->getOperator()->guessMimeType('/tmp/non-existing-directory-guess-mime-type');
    }

    /**
     * @see FilesystemOperator::findFiles()
     */
    public function testFindFiles()
    {
        $files = $this->getOperator()->findFiles(__DIR__ . '/../../../../app/config');

        foreach ($files as $file) {
            $this->assertFileExists($file);
        }

        $basenames = array_map(function ($path) { return basename($path); }, $files);

        $this->assertContains('security.yml', $basenames);
        $this->assertContains('parameters.yml', $basenames);
        $this->assertContains('wiki.yml', $basenames);
    }

    /**
     * @see FilesystemOperator::findFiles()
     */
    public function testFailureFindFiles()
    {
        $this->expectExceptionMessage('The "/tmp/non-existing-directory-guess-mime-type" directory does not exist.');
        $this->getOperator()->findFiles('/tmp/non-existing-directory-guess-mime-type');
    }

    /**
     * @see FilesystemOperator::getFileSize()
     */
    public function testGetFileSize()
    {
        $this->assertInternalType('integer', $this->getOperator()->getFileSize(__FILE__));
        $this->assertGreaterThan(0, $this->getOperator()->getFileSize(__FILE__));
    }

    /**
     * @see FilesystemOperator::isHidden()
     */
    public function testIsHidden()
    {
        $this->assertTrue($this->getOperator()->isHidden('/tmp/.hidden'));
        $this->assertTrue($this->getOperator()->isHidden('/tmp/.hidden/file'));
        $this->assertTrue($this->getOperator()->isHidden('/tmp/.hidden/deeeply/file'));
        $this->assertTrue($this->getOperator()->isHidden('/tmp/deeply/.hidden/that/file'));
        $this->assertTrue($this->getOperator()->isHidden('/tmp/not-hidden/.hidden-file'));
        $this->assertFalse($this->getOperator()->isHidden('/tmp/not-hidden'));
        $this->assertFalse($this->getOperator()->isHidden('/tmp/not-hidden/not/not/not/h.uh.'));
    }

    /**
     * @see FilesystemOperator::getDirName()
     */
    public function testGetDirName()
    {
        $this->assertSame('/tmp/test', $this->getOperator()->getDirName('/tmp/test/somefile.md'));
        $this->assertSame('/tmp', $this->getOperator()->getDirName('/tmp/test/'));
    }

    /**
     * @see FilesystemOperator::getFileName()
     */
    public function testGetFileName()
    {
        $this->assertSame('somefile', $this->getOperator()->getFileName('/tmp/test/somefile.md'));
    }

    /**
     * @see FilesystemOperator::getFileBasename()
     */
    public function testGetFileBasename()
    {
        $this->assertSame('somefile.md', $this->getOperator()->getFileBasename('/tmp/test/somefile.md'));
    }

    /**
     * @see FilesystemOperator::getLastModTime()
     */
    public function testGetLastModTime()
    {
        $this->assertSame(date('Y-m-d', filemtime(__FILE__)), $this->getOperator()->getLastModTime(__FILE__)->format('Y-m-d'));
    }

    private function getOperator(): FileSystemOperator
    {
        return $this->container->get('wolnosciowiec.wiki.manager.filesystem');
    }
}