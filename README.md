# yii2-vk

Helper class to work with Vkontakte services and API.

Installation
------------

Install package by composer
```composer
{
    "require": {
       "strong2much/yii2-vk": "dev-master"
    }
}

Or

$ composer require strong2much/yii2-vk "dev-master"
```

Use the following code in your configuration file. You can use different services
```php
'vk' => [
    'class' => 'strong2much\vk\Api'
]
```

Use the following code to run widget in view:
```php
echo strong2much\vk\widgets\ShareButtonWidget::widget([
    'url' => '',
]);
```
