# CONTRIBUTING

## 基本規範

Git 流程採用 TBD ( Trunk Based Development ， [參考網頁](http://paulhammant.com/2013/04/05/what-is-trunk-based-development/) ) 。 Mainline 名稱固定為 `master` ，原始碼的所有測試會在 `master` 上。

Git Commit 的內容沒有特別規範，但請先參考[這份文件](https://blog.louie.lu/2017/03/21/%E5%A6%82%E4%BD%95%E5%AF%AB%E4%B8%80%E5%80%8B-git-commit-message/)。

開發建議使用 [Fork + Pull Request](https://git-scm.com/book/zh-tw/v2/GitHub-%E5%8F%83%E8%88%87%E4%B8%80%E5%80%8B%E5%B0%88%E6%A1%88) 並使用 Squash and Merge 合併。當發 PR 時， Travis 也會幫您測試。

### 目錄結構

```yaml
# 所有原始碼
- src

# 測試程式
- tests
```

## 環境建置

開發需要的工具如下：

* [Composer][]

安裝 Composer 套件：

    $ composer install

## Coding Style

開發請遵守 [PSR-2](http://www.php-fig.org/psr/psr-2/) 規範，檢查指令如下：

    $ php vendor/bin/phpcs

自動修正的指令：

    $ php vendor/bin/phpcbf

其他細節規範如下：

* 最低要支援 PHP 5.5 ，因此需注意 PHP 7 的新寫法是不被允許的
* Array 實字請使用 `[]` ，不要使用 `array()`
* 開頭 `namespace` 關鍵字與 `<?php` 間不空行
* variable 、 function 、 property 、 method 等，均使用 `camelCase` 風格 

PHP Document 建議參考 [104 Guideline][] 上的說明。

## Testing

測試使用 [PHPUnit][] 套件，執行測試指令如下

    $ php vendor/bin/phpunit

## 版號定義

使用最常見的 [`major.minor.build`](http://www.ithome.com.tw/voice/85505) 的定義。


[PHPUnit]: https://phpunit.de/
[Composer]: https://getcomposer.org/
[104 Guideline]: https://github.com/104corp/guideline/blob/master/language/php/phpdoc.md
