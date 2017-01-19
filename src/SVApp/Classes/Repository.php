<?php

namespace SVApp\Classes;
use Silex\Application;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

/**
 * Class Repository
 * @package SVApp\Classes
 */
abstract class Repository {
	protected $app;
	/**
	 * @var int record ID to operate with
	 */
	protected $ID = NULL;

	/**
	 * @var Entity record to operate with
	 */
	protected $Entity = NULL;

	/**
	 * @var EntityRepository Entity repository
	 */
	protected $EntityRepository = NULL;

	/**
	 * @var ClassMetadataInfo $EntityMetadata
	 */
	protected $EntityMetadata = NULL;

	protected static $cache = NULL;
	protected static $em = NULL;

	public function __construct(Application $app, $entityName = NULL) {
		$this->app = $app;
		$this::$cache = $app['cache'];

		if ($entityName) {
			$this->entName = $entityName;
			$this->setEntityRepository($entityName);
		}
	}

	/**
	 * @return EntityManager|null
	 */
	protected function getEM() {
		if (NULL === $this::$em) {
			$this::$em = $this->app['orm.em'];
		}
		return $this::$em;
	}

	/**
	 * @return RedisCache|null
	 */
	protected function getCache() {
		if (NULL === $this::$cache) {
			$this::$cache = $this->app['cache'];
		}
		return $this::$cache;
	}


	/**
	 * @param string $fName Name of ENUMed property from entity
	 * @return array
	 * @throws \Doctrine\ORM\Mapping\MappingException
	 * @throws \Exception
	 */
	public function getEnumValues($fName) {
		$retArr = [];

		/** @var $q \Doctrine\ORM\Mapping\ClassMetadataInfo */
		$q = $this->EntityMetadata;
		$cDef = $q->getFieldMapping($fName)['columnDefinition'];

		if (strpos($cDef,'ENUM')===FALSE)
			throw new \Exception('Not a ENUM field');

		preg_match('|ENUM\((.+)\)|msiU',$cDef,$matches);

		if ($matches[1]) {
			foreach (explode(',',$matches[1]) as $oneOption) {
				$retArr[] = trim($oneOption,"\n'\"\t ");
			}
		}
		return $retArr;
	}

	/**
	 * @throws \Exception no record found
	 * @param integer $ID ID of entity to load
	 * @return Repository instance of self
	 */
	public function setEntityByID($ID) {
		$ent = $this->findEntityByID($ID);
		if (NULL !== $ent) {
			$this->Entity = $ent;
			$this->setID($ID);
		} else {
			throw new \Exception('Record with ID = ' . $ID . ' not found');
		}

		return $this;
	}

	/**
	 * @param int $ID
	 * @return Repository instance of self
	 */
	protected function setID($ID) {
		$this->ID = $ID;
		return $this;
	}

	/**
	 * Get entity instance by ID
	 * @param $id
	 * @param bool $cacheAllowed if FALSE doctrine will initiate new DB query on every call
	 * @return Entity of corresponding class or NULL if no record found for given ID
	 */
	public function findEntityByID($id, $cacheAllowed = TRUE) {
		if (!intval($id)) return NULL;

		$entity = $this->EntityRepository->find($id);
		if ($entity && !$cacheAllowed) {
			$this->getEM()->refresh($entity);
		}

		return $entity;
	}

	/**
	 * Get entity instance by ID
	 * @param $id
	 * @return mixed Entity of corresponding class or NULL if no record found for given ID
	 */
	public function findEntityRefByID($id) {
		if (!intval($id)) return NULL;

		return $this->getEM()->getReference($this->entName,$id);
	}



	/**
	 * @return Repository instance of self
	 */
	public function setEntityNew() {
		$name = $this->EntityMetadata->getName();
		$this->Entity = new $name();
		return $this;
	}

	/**
	 * @return \SVApp\Classes\Entity
	 */
	public function getEntity() {
		return $this->Entity;
	}

	/**
	 * Set Entity to NULL
	 * @return $this
	 */
	public function unsetEntity()
	{
		$this->Entity = NULL;
		return $this;
	}

	/**
	 * @throws \Exception type mismatch error
	 * @param Entity $Entity
	 * @return Repository instance of self
	 */
	public function setEntity(Entity $Entity) {
		$class = $this->EntityMetadata->name;

		if ($Entity instanceof $class) {
			$this->Entity = $Entity;
		} else {
			throw new \Exception('Wrong Class in setEntity');
		}
		return $this;
	}

	public function remove(Entity $instance) {
		$this->getEM()->remove($instance);
		$this->getEM()->flush();
		return $this;
	}

	public function removeByID($id) {
		$instance = $this->findEntityByID($id);
		$this->remove($instance);
		return $this;
	}

	public function saveToDB(Entity $instanceToSave) {
		$this->persist($instanceToSave);
		return $this;
	}

	public function commit(Entity $entity = NULL) {
		$this->getEM()->flush($entity);
		return $this;
	}

	public function clear() {
		$this->getEM()->clear();
	}

	public function flush() {
		$this->getEM()->flush();
		return $this;
	}
	/**
	 * @param Entity $instanceToSave
	 */
	protected function persist(Entity $instanceToSave) {
		$this->getEM()->persist($instanceToSave);
	}

	/**
	 * Get all entities
	 * @throws \Exception if no entity inited
	 * @return \SVApp\Classes\Entity[]
	 */
	public function getList() {
		return $this->EntityRepository->findAll();
	}

	/**
	 * Get entities list by criteria
	 *
	 * @param      $criteria
	 * @param null $order
	 * @param null $limit
	 * @param null $offset
	 * @return Entity[]
	 */
	public function getListBy($criteria, $order = NULL, $limit = NULL, $offset = NULL) {
		return $this->EntityRepository->findBy($criteria, $order, $limit, $offset);
	}

	/**
	 * Get entities list by criteria
	 *
	 * @param $criteria
	 * @return Entity
	 */
	public function getOneBy($criteria) {
		return $this->EntityRepository->findOneBy($criteria);
	}

	/**
	 * @return self Entity
	 */
	protected function getEntityRepository() {
		return $this->EntityRepository;
	}

	/**
	 * @throws \Exception type mismatch error
	 * @param $entityName
	 * @return Repository instance of self
	 */
	protected function setEntityRepository($entityName) {
		$this->EntityRepository = $this->getEM()->getRepository($entityName);
		$this->EntityMetadata = $this->getEM()->getClassMetadata($entityName);
		return $this;
	}


	/**
	 * @return null
	 */
	public function getEntityMetadata() {
		return $this->EntityMetadata;
	}
}