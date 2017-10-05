# Cache 

[![Build Status](https://travis-ci.org/104corp/php-cache.svg?branch=master)](https://travis-ci.org/104corp/php-cache)
[![Coverage Status](https://coveralls.io/repos/github/104corp/php-cache/badge.svg?branch=master)](https://coveralls.io/github/104corp/php-cache?branch=master)

PSR-16 simple cache implements written by PHP.

## 系統需求

* PHP >= 5.5

## 安裝

使用 [Composer][] 安裝

```
$ composer require 104corp/cache
```

## 說明

Cache 實作遵守 [PSR-16 Simple Cache][]，並提供 `CacheAwareTrait` ，可以方便地掛載在需要 Cache 的元件上；提供幾種常見的實作，可以直接套用。

因實作遵守 PSR-16 規定，如果覺得不夠用的話，也可以直接無痛改用第三方套件。

### 基本

如果需要做 Cache 的話，首先先掛上 `CacheAwareTrait` ，並實作必要的方法 `getDefaultTtl()` ，接著就能開始使用了。下面是一個簡單的範例：

```php
use Corp104\Cache\Util\CacheAwareTrait;

class Resource
{
    use CacheAwareTrait;
        
    public function getDefaultTtl()
    {
        // 60 second
        return 60;
    }
    
    public function getData()
    {
        $data = null;
        
        if (null !== $this->cache) {
            $data = $this->cache->get('some-resource-key', null);
        }
        
        if (null === $data) {
            $data = $this->getRealData();
            
            if (null !== $this->cache) {
                $this->cache->set('some-resource-key', $data, $this->getTtl());
            }
        }
        
        return $data;
    }
    
    private function getRealData()
    {
        return 'RealData';
    }
}
```

實際要使用 `Resource` 物件時，只要傳入適當的 cache driver 即可運作，如 [Symfony Cache](https://github.com/symfony/cache) 。

```php
$cacheInstance = new \Symfony\Component\Cache\Simple\PhpFilesCache();

$resource = new Resource();
$resource->setCache($cacheInstance);

$data = $resource->getData();
```

### 實作

* `Corp104\Cache\FileCache` - 使用 PHP 檔案當作 cache 。

### 測試替身

Cache 元件實作 `Psr\SimpleCache\CacheInterface` ，因此可以直接使用此介面來產生 Stub / Spy / Mock 。

測試階段可使用 `Corp104\Cache\ArrayCache` 來測元件與 Cache 的互動和結果是否正常。

## Contributing

開發相關資訊可以參考 [CONTRIBUTING](/CONTRIBUTING.md) ，有任何問題或建議，歡迎發 issue ；如果覺得程式碼可以修更好的話，也歡迎發 PR 修正。

PR 如何使用可以參考 [Git 官方文件](https://git-scm.com/book/zh-tw/v2/GitHub-%E5%8F%83%E8%88%87%E4%B8%80%E5%80%8B%E5%B0%88%E6%A1%88)。


[Composer]: https://getcomposer.org/
[PSR-16 Simple Cache]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-16-simple-cache.md
