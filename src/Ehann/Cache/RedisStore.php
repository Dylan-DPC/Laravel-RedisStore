<?php

namespace Ehann\Cache;

use Illuminate\Cache\RedisStore as IlluminateRedisStore;

class RedisStore extends IlluminateRedisStore
{
    /**
     * Default value of first byte of zlib compressed string.
     *
     * @var string
     */
    const COMPRESSION_ENABLED_FILE_HEADER = '78';

    /**
     * @var bool
     */
    protected $useCompression = true;

    /**
     * @var int
     */
    protected $compressionLevel = -1;

    /**
     * Get whether or not compression is enabled.
     *
     * @return bool
     */
    public function getUseCompression()
    {
        return $this->useCompression;
    }

    /**
     * Enable or disable compression.
     *
     * @param bool $useCompression
     */
    public function setUseCompression($useCompression)
    {
        $this->useCompression = $useCompression;
    }

    /**
     * @return int
     */
    public function getCompressionLevel()
    {
        return $this->compressionLevel;
    }

    /**
     * @param int $compressionLevel
     */
    public function setCompressionLevel($compressionLevel)
    {
        $this->compressionLevel = $compressionLevel;
    }

    /**
     * Serialize the value.
     *
     * @param  mixed  $value
     * @return mixed
     */
    protected function serialize($value)
    {
        if (is_numeric($value) || is_null($value)) {
            return $value;
        }
        return $this->useCompression ? gzcompress(serialize($value), $this->compressionLevel) : serialize($value);
    }

    /**
     * Unserialize the value.
     *
     * @param  mixed  $value
     * @return mixed
     */
    protected function unserialize($value)
    {
        if (is_string($value)) {
            $isValueCompressed = bin2hex(mb_strcut($value, 0, 1)) === self::COMPRESSION_ENABLED_FILE_HEADER;
            return unserialize($isValueCompressed ? gzuncompress($value) : $value);
        }
        return $value;
    }
}