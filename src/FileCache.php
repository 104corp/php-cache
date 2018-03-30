<?php
namespace Corp104\Cache;

use Corp104\Cache\Exception\InvalidArgumentException;
use Corp104\Cache\Util\Helper;
use Psr\SimpleCache\CacheInterface;
use Traversable;

/**
 * File array cache
 */
class FileCache implements CacheInterface
{
    /**
     * @var string
     */
    private static $fileTemplate = <<< 'EOF'
<?php 
return %s;

EOF;

    /**
     * @var array
     */
    private $data;

    /**
     * @var string
     */
    private $file;

    /**
     * @param mixed $value
     * @throws InvalidArgumentException
     */
    public static function checkValueType($value)
    {
        if (is_array($value) || $value instanceof Traversable) {
            foreach ($value as $item) {
                static::checkValueType($item);
            }

            return;
        }

        if ((null !== $value) && !is_scalar($value) && !is_object($value)) {
            throw new InvalidArgumentException('Input value type is invalid');
        }
    }

    /**
     * @param string $file
     * @throws InvalidArgumentException
     */
    public static function checkFileStatus($file)
    {
        $dir = dirname($file);
        $filename = basename($file);

        if (!preg_match('/\.php$/', $filename)) {
            throw new InvalidArgumentException("'$filename' is not a valid PHP script file");
        }

        if (!is_writable($dir)) {
            throw new InvalidArgumentException("Path '$dir' is not writable");
        }

        if (is_file($file) && !is_writable($dir)) {
            throw new InvalidArgumentException("File '$file' is not writable");
        }
    }

    /**
     * @param string $file
     * @throws InvalidArgumentException
     */
    public static function initFile($file)
    {
        static::checkFileStatus($file);

        if (!is_file($file)) {
            touch($file);
            file_put_contents($file, sprintf(static::$fileTemplate, '[]'));
        }
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param null|int $ttl
     * @throws InvalidArgumentException
     */
    private function setData($key, $value, $ttl = null)
    {
        $this->data[$key] = [
            'expire' => Helper::normalizeExpireAt($ttl),
            'data' => $value,
        ];
    }

    /**
     * @param string $file Full file path
     * @throws InvalidArgumentException
     */
    public function __construct($file)
    {
        static::initFile($file);

        $this->file = $file;
        $this->reloadFile();
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        Helper::checkStringType($key);

        if (!isset($this->data[$key])) {
            return $default;
        }

        $item = $this->data[$key];

        if ($item['expire'] !== null && time() >= $item['expire']) {
            return $default;
        }

        return $this->data[$key]['data'];
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $ttl = null)
    {
        Helper::checkStringType($key);
        static::checkValueType($value);

        $this->setData($key, $value, $ttl);

        return $this->writeFile($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        Helper::checkStringType($key);

        unset($this->data[$key]);

        return $this->writeFile($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->data = [];

        return $this->deleteFile();
    }

    /**
     * {@inheritdoc}
     */
    public function getMultiple($keys, $default = null)
    {
        Helper::checkTraversableType($keys);

        $data = [];

        foreach ($keys as $key) {
            $data[$key] = $this->get($key, $default);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function setMultiple($values, $ttl = null)
    {
        Helper::checkTraversableType($values);

        foreach ($values as $key => $value) {
            $this->setData($key, $value, $ttl);
        }

        return $this->writeFile($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteMultiple($keys)
    {
        Helper::checkTraversableType($keys);

        foreach ($keys as $key) {
            unset($this->data[$key]);
        }

        return $this->writeFile($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        Helper::checkStringType($key);

        return isset($this->data[$key]);
    }

    /**
     * @return boolean
     */
    private function deleteFile()
    {
        return @unlink($this->file);
    }

    /**
     * Reload file
     */
    private function reloadFile()
    {
        $data = require $this->file;

        foreach ($data as $key => $item) {
            $data[$key]['data'] = unserialize($item['data']);
        }

        $this->data = $data;
    }

    /**
     * @param array $data
     * @param bool $failAndReload
     * @return bool
     * @throws InvalidArgumentException
     */
    private function writeFile($data, $failAndReload = true)
    {
        static::initFile($this->file);

        foreach ($data as $key => $item) {
            $data[$key]['data'] = serialize($item['data']);
        }

        $valueExport = var_export($data, true);

        $isSuccess = (boolean)file_put_contents($this->file, sprintf(static::$fileTemplate, $valueExport));

        if (!$isSuccess && $failAndReload) {
            $this->reloadFile();
        }

        return $isSuccess;
    }
}
