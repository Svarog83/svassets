<?php

namespace App\Classes;

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

	public function flushCurrent() {
		$pattern = "*";
		$c=0;
		$cr = $this->getRedis()->keys($pattern);
		foreach ($cr as $oneKey) {
			list(, $key) = explode (':', $oneKey);

			$this->doDelete($key);
			$c++;
		}
		return $c;
	}
}
