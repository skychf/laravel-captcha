Captcha
=======

Installation
============

With composer :

``` json
{
    ...
    "require": {
        "skychf/captcha": "1.*"
    }
}
```

Usage
=====

修改 config/app.php :

```php
'providers' => array(
        'Skychf\Captcha\CaptchaServiceProvider'
)


'aliases' => array(
        'Captcha' => 'Skychf\Captcha\Facades\Captcha'
)
```


```php
return response(Captcha::output())->header('Content-type','image/jpeg');
```
OR

```php
session(['captcha' => Captcha::getChars()]);
return Captcha::inline();
```

License
=======

This library is under MIT license, have a look to the `LICENSE` file