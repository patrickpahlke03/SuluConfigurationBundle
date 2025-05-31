<?php

declare(strict_types=1);

/*
 * This file is part of the SuluConfigurationBundle.
 *
 * (c) Patrick Pahlke
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PatLabs\SuluConfigurationBundle\Tests\Functional\Controller;

use Doctrine\ORM\EntityManagerInterface;
use PatLabs\SuluConfigurationBundle\Entity\Config;
use Sulu\Bundle\TestBundle\Testing\SuluTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;

class ConfigControllerTest extends SuluTestCase
{
    private EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = $this->createAuthenticatedClient();

        $this->purgeDatabase();
        $this->entityManager = $this->getEntityManager();
    }

    public function testGetAction(): void
    {
        // Create test configurations in the database
        $this->createConfig('app_settings.title', 'My Website');
        $this->createConfig('app_settings.description', 'This is a test website');
        $this->createConfig('app_settings.email', 'info@example.com');
        $this->createConfig('other_settings.value', 'should not be returned');

        $this->client->request('GET', '/admin/api/config?id=app_settings');

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());

        $result = \json_decode($response->getContent(), true);

        // Check that the response contains the expected data
        $this->assertIsArray($result);
        $this->assertCount(6, $result); // 6 because there are 6 fields defined in config/configs
        $this->assertSame('My Website', $result['title']);
        $this->assertSame('This is a test website', $result['description']);
        $this->assertSame('info@example.com', $result['email']);
        $this->assertArrayNotHasKey('value', $result);
    }

    public function testGetActionWithNonExistingPrefix(): void
    {
        // Create test configuration in the database
        $this->createConfig('app_settings.title', 'My Website');

        $this->client->request('GET', '/admin/api/config?id=non_existing_prefix');

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testPutAction(): void
    {
        // Create test configuration in the database
        $this->createConfig('app_settings.title', 'Original Title');

        $this->client->request(
            'PUT',
            '/admin/api/config?id=app_settings',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            \json_encode([
                'title' => 'Updated Title',
                'description' => 'New Description',
            ]),
        );

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());

        $result = \json_decode($response->getContent(), true);

        // Check that the response contains the updated data
        $this->assertIsArray($result);
        $this->assertSame('Updated Title', $result['title']);
        $this->assertSame('New Description', $result['description']);

        // Verify the data was actually saved in the database
        $this->client->request('GET', '/admin/api/config?id=app_settings');
        $getResponse = $this->client->getResponse();
        $getResult = \json_decode($getResponse->getContent(), true);

        $this->assertSame('Updated Title', $getResult['title']);
        $this->assertSame('New Description', $getResult['description']);
    }

    public function testPutActionWithInvalidId(): void
    {
        $this->client->request(
            'PUT',
            '/admin/api/config?id=',  // Missing ID
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            \json_encode(['title' => 'Test Title']),
        );

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /**
     * Creates a test configuration in the database.
     */
    private function createConfig(string $key, mixed $value): void
    {
        $config = new Config();
        $config->setConfigKey($key);
        $config->setValue($value);

        $this->entityManager->persist($config);
        $this->entityManager->flush();
    }
}
