<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://yiisoft.github.io/docs/images/yii_logo.svg" height="100px">
    </a>
    <h1 align="center">Yii Mutex Library</h1>
    <br>
</p>

This library provides mutex implementation.
It is used in [Yii Framework] but can be used separately.

[Yii Framework]: https://www.yiiframework.com/

[![Latest Stable Version](https://poser.pugx.org/yiisoft/mutex/v/stable.png)](https://packagist.org/packages/yiisoft/mutex)
[![Total Downloads](https://poser.pugx.org/yiisoft/mutex/downloads.png)](https://packagist.org/packages/yiisoft/mutex)
[![Build Status](https://github.com/yiisoft/mutex/workflows/build/badge.svg)](https://github.com/yiisoft/mutex/actions)
[![Code Coverage](https://scrutinizer-ci.com/g/yiisoft/mutex/badges/coverage.png)](https://scrutinizer-ci.com/g/yiisoft/mutex/)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/yiisoft/mutex/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/yiisoft/mutex/?branch=master)
[![static analysis](https://github.com/yiisoft/mutex/workflows/static%20analysis/badge.svg)](https://github.com/yiisoft/mutex/actions?query=workflow%3A%22static+analysis%22)

## Mutex drivers

Mutex drivers are implemented as separate packages:

- [DB - MySQL](https://github.com/yiisoft/mutex-db-mysql)
- [DB - Oracle](https://github.com/yiisoft/mutex-db-oracle)
- [DB - Redis](https://github.com/yiisoft/mutex-db-redis)
- [DB - Postgres](https://github.com/yiisoft/mutex-db-pgsql)
- [File](https://github.com/yiisoft/mutex-file)

## Unit testing

The package is tested with [PHPUnit](https://phpunit.de/). To run tests:

```shell
./vendor/bin/phpunit
```

## Static analysis

The code is statically analyzed with [Psalm](https://psalm.dev/). To run static analysis:

```shell
./vendor/bin/psalm
```
