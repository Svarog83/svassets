<?php

namespace SVApp\Classes;

class RedisCache extends \Doctrine\Common\Cache\RedisCache {
	public function get($key, \Closure $fallback = null, $expiration = null) {
		$result = $this->fetch($key);

		if ($result === false && $fallback instanceof \Closure) {
			$result = $fallback();
			if ($this->save($key, $result, $expiration) === false) {
				return false;
			}
		}

		return $result;
	}

	/**
	 * Clears Redis cache by pattern (or everything if empty)
	 *
	 * @param string $pattern
	 * @return int
	 */
	public function flushCurrent($pattern = '') {
		$fullPattern = "*" . $pattern;

		$c=0;
		$cr = $this->getRedis()->keys($fullPattern);
		foreach ($cr as $oneKey) {
			list(, $key) = explode (':', $oneKey);

			$this->doDelete($key);
			$c++;
		}
		return $c;
	}
}
