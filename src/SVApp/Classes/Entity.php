<?php
namespace SVApp\Classes;

abstract class Entity {
	/* @var $application \Silex\Application*/
	static public $application = NULL;

	/**
	 * Returns item ID
	 * @return integer item ID
	 */
	abstract function getID();

	/**
	 * Returns item's properties as array.
	 * Does not include item's relations, those have to be handled manually in the child classes.
	 * @return array item's properties
	 */
	public function getArray() {
		/* Get and traverse object vars */
		$arr_props = get_object_vars($this);

		$arr = array();
		foreach ($arr_props as $k => $v) {
			if ($k == 'skipORMEvents' || $k == '__initializer__' || $k == '__cloner__' || $k == '__isInitialized__') continue;

			if (is_scalar($v) || $v === NULL) {
				$arr[$k] = $v;
			}
		}

		return $arr;
	}

	public function getEntityChangeSet() {
		/** @var \Doctrine\ORM\EntityManager $em */
		$em = self::$application['orm.em'];
		$uow = $em->getUnitOfWork();
		$uow->computeChangeSets();
		return $uow->getEntityChangeSet($this);
	}
}
