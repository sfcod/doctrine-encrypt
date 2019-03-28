<?php

namespace SfCod\DoctrineEncryptBundle\Command;

use Doctrine\Common\Annotations\Reader;
use SfCod\DoctrineEncryptBundle\Subscribers\DoctrineEncryptSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Base command containing usefull base methods.
 *
 * @author Michael Feinbier <michael@feinbier.net>
 **/
abstract class AbstractCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var DoctrineEncryptSubscriber
     */
    protected $subscriber;

    /**
     * @var AnnotationReader
     */
    protected $annotationReader;

    /**
     * AbstractCommand constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param Reader $annotationReader
     * @param DoctrineEncryptSubscriber $subscriber
     */
    public function __construct(EntityManagerInterface $entityManager, Reader $annotationReader, DoctrineEncryptSubscriber $subscriber)
    {
        $this->entityManager = $entityManager;
        $this->annotationReader = $annotationReader;
        $this->subscriber = $subscriber;

        parent::__construct();
    }

    /**
     * Get an result iterator over the whole table of an entity.
     *
     * @param string $entityName
     *
     * @return \Doctrine\ORM\Internal\Hydration\IterableResult
     */
    protected function getEntityIterator($entityName)
    {
        $query = $this->entityManager->createQuery(sprintf('SELECT o FROM %s o', $entityName));
        return $query->iterate();
    }

    /**
     * Get the number of rows in an entity-table
     *
     * @param string $entityName
     *
     * @return int
     */
    protected function getTableCount($entityName)
    {
        $query = $this->entityManager->createQuery(sprintf('SELECT COUNT(o) FROM %s o', $entityName));
        return (int) $query->getSingleScalarResult();
    }

    /**
     * Return an array of entity-metadata for all entities
     * that have at least one encrypted property.
     *
     * @return array
     */
    protected function getEncryptionableEntityMetaData()
    {
        $validMetaData = [];
        $metaDataArray = $this->entityManager->getMetadataFactory()->getAllMetadata();

        foreach ($metaDataArray as $entityMetaData)
        {
            if ($entityMetaData->isMappedSuperclass) {
                continue;
            }

            $properties = $this->getEncryptionableProperties($entityMetaData);
            if (count($properties) == 0) {
                continue;
            }

            $validMetaData[] = $entityMetaData;
        }

        return $validMetaData;
    }

    /**
     * @param $entityMetaData
     *
     * @return array
     */
    protected function getEncryptionableProperties($entityMetaData)
    {
        //Create reflectionClass for each meta data object
        $reflectionClass = New \ReflectionClass($entityMetaData->name);
        $propertyArray = $reflectionClass->getProperties();
        $properties    = [];

        foreach ($propertyArray as $property) {
            if ($this->annotationReader->getPropertyAnnotation($property, 'SfCod\DoctrineEncryptBundle\Configuration\Encrypted')) {
                $properties[] = $property;
            }
        }

        return $properties;
    }
}
