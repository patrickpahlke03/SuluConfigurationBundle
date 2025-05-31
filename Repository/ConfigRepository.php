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

namespace PatLabs\SuluConfigurationBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ObjectRepository;
use PatLabs\SuluConfigurationBundle\Entity\Config;

class ConfigRepository implements ObjectRepository
{
    protected EntityRepository $entityRepository;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
        $this->entityRepository = $this->entityManager->getRepository(Config::class);
    }

    public function findByKey($key)
    {
        return $this->findOneBy(['configKey' => $key]);
    }

    public function findByPrefix(string $prefix): array
    {
        return $this->entityRepository->createQueryBuilder('c', 'c.configKey')
            ->select(['c.configKey', 'c.value'])
            ->where('c.configKey LIKE :prefix')
            ->setParameter('prefix', $prefix . '%')
            ->getQuery()
            ->execute();
    }

    public function findByKeys(array $keys): array
    {
        return $this->entityRepository->createQueryBuilder('c', 'c.configKey')
            ->select(['c.configKey', 'c.id'])
            ->where('c.configKey IN (:keys)')
            ->setParameter('keys', $keys)
            ->getQuery()
            ->execute();
    }

    public function updateConfig(int $id, mixed $value): void
    {
        $this->entityRepository->createQueryBuilder('c')
            ->update(Config::class, 'c')
            ->set('c.value', ':value')
            ->where('c.id = :id')
            ->setParameter('value', \json_encode(['_value' => $value]))
            ->setParameter('id', $id)
            ->getQuery()
            ->execute();
    }

    public function create(string $key, mixed $value): Config
    {
        $entity = new Config();
        $entity->setConfigKey($key)
            ->setValue($value);
        $this->entityManager->persist($entity);

        return $entity;
    }

    public function save(): void
    {
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function find($id)
    {
        return $this->entityRepository->find($id);
    }

    public function findAll()
    {
        return $this->entityRepository->findAll();
    }

    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null)
    {
        return $this->entityRepository->findBy($criteria, $orderBy, $limit, $offset);
    }

    public function findOneBy(array $criteria)
    {
        return $this->entityRepository->findOneBy($criteria);
    }

    public function getClassName(): string
    {
        return Config::class;
    }
}
