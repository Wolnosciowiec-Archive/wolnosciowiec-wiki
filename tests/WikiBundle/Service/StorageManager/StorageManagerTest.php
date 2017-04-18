<?php declare(strict_types=1);

namespace Tests\WikiBundle\Service\StorageManager;

use Tests\TestCase;
use WikiBundle\Service\StorageManager\StorageManager;

/**
 * @see StorageManager
 */
class StorageManagerTest extends TestCase
{
    public function provideProjects()
    {
        return [
            [
                'projectName' => 'anarchifaq_local',
                'meta' => [
                    'containsPath' => '/Storage/anarchifaq_local',
                    'path' => '/var/www/websites/Storage/anarchifaq_local',
                ],
            ],
        ];
    }

    /**
     * @see StorageManager::findPathFor()
     * @dataProvider provideProjects()
     *
     * @param string $projectName
     * @param array $meta
     */
    public function testFindPathFor(string $projectName, array $meta)
    {
        $this->assertContains($meta['containsPath'], $this->getManager()->findPathFor($projectName, true));
    }

    /**
     * @see StorageManager::getRepositoryName()
     * @dataProvider provideProjects()
     *
     * @param string $projectName
     * @param array $meta
     */
    public function testGetRepositoryName(string $projectName, array $meta)
    {
        $this->getManager()->setKnownRepositories([
            'anarchifaq_local' => '/var/www/websites/Storage/anarchifaq_local@master',
        ]);

        $this->assertSame($projectName, $this->getManager()->getRepositoryName($meta['path'], 'master'));
    }

    /**
     * @see StorageManager::findCompiledPathFor()
     * @see StorageManager::normalizePath()
     */
    public function testFindCompiledPathFor()
    {
        $path = $this->getManager()->findCompiledPathFor('anarchifaq_local', '/./../non-existing-directory/test.md');

        // check if not escaped from project root directory
        $this->assertNotContains('./../', $path);
        $this->assertNotContains('/app/non-existing-directory', $path);

        $this->assertContains('/Compiled/non-existing-directory/test.md', $path);
        $this->assertDirectoryExists(dirname($path));

        // clean up
        rmdir(dirname($path));
    }

    private function getManager(): StorageManager
    {
        return $this->container->get('wolnosciowiec.wiki.manager.storage');
    }
}
