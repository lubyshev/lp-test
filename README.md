# lp-test

Тестовое задание для компании Платформа LP

## Установка.

Установите yii2-advanced по [инструкции](https://www.yiiframework.com/extension/yiisoft/yii2-app-advanced/doc/guide/2.0/en/start-installation).

Скрипт для создания БД:

```mysql
CREATE SCHEMA `lubyshev_test` DEFAULT CHARACTER SET utf8;
```

Клонируйте репозитарий в локальное хранилище:

```bash
$ cd /my/local/repos
$ git clone git@github.com:lubyshev/lp-test.git
```

Измените/добавьте строки в `/yii2-advanced-folder/composer.json`:
```json
{
    "require": {
        "php": ">=7.4.0"
    },
    "repositories": [
        {
            "type": "path",
            "url": "/my/local/repos/lp-test"
        }
    ]
}
```

Добавьте компонент к проекту:

```bash
$ cd /yii2-advanced-folder
$ composer update
$ composer require lubyshev/lp-test
```
