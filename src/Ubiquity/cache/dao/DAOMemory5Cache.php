<?php

namespace Ubiquity\cache\dao;

/**
 * Ubiquity\cache\dao$DAOMemoryCache
 * This class is part of Ubiquity
 *
 * @author jc
 * @version 1.0.0
 *
 */
class DAOMemory5Cache extends AbstractDAOCache {
	/**
	 *
	 * @var array
	 */
	protected $arrayCache;

	public function store($class, $key, $object) {
		$this->arrayCache [\md5 ( $class )] [$key] = $object;
	}

	public function fetch($class, $key) {
		return $this->arrayCache [\md5 ( $class )] [$key] ?? false;
	}

	public function delete($class, $key) {
		$k = [ \md5 ( $class ) ];
		if (isset ( $this->arrayCache [$k] [$key] )) {
			unset ( $this->arrayCache [$k] [$key] );
		}
	}
}
