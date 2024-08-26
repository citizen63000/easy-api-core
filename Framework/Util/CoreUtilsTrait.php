<?php

namespace EasyApiCore\Util;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;

trait CoreUtilsTrait
{
    abstract protected function getDoctrine(): ?ManagerRegistry;
    abstract protected function getContainer(): ContainerInterface;

    /**
     * @throws \Exception
     */
    protected function getEntityManager(): ?ObjectManager
    {
        return $this->getDoctrine()->getManager();
    }

    protected function getRepository(string $repository): ObjectRepository
    {
        return $this->getDoctrine()->getRepository($repository);
    }

    /**
     * @throws \Exception
     */
    protected function persistAndFlush($entity): mixed
    {
        $em = $this->getEntityManager();
        $em->persist($entity);
        $em->flush();

        return $entity;
    }

    /**
     * @param $entity
     *
     * @throws \Exception
     */
    protected function removeAndFlush($entity): void
    {
        $em = $this->getEntityManager();
        $em->remove($entity);
        $em->flush();
    }

    protected function getUserClassname(): string
    {
        return $this->getParameter('easy_api.user_class');
    }

    /**
     * @throws \Exception
     */
    protected function getCache(): AdapterInterface
    {
        return $this->getContainer()->get('cache.app');
    }
}
