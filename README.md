# VulcanPhp Captcha
All-In-One Simple Captcha Validation for PHP Application

![Screen Shot 2024-03-06 at 2 54 33 PM](https://github.com/vulcanphp/captcha/assets/128284645/f715c4cf-892d-425c-87a3-1b3a831b7681)
![Screen Shot 2024-03-06 at 2 54 05 PM](https://github.com/vulcanphp/captcha/assets/128284645/111bab28-197a-4ebc-9011-f04209b11b2d)


## Installation

It's recommended that you use [Composer](https://getcomposer.org/) to install VulcanPhp Captcha.

```bash
$ composer require vulcanphp/captcha
```

## Basic Usage
```php
<?php

use VulcanPhp\Captcha;

require __DIR__ . '/vendor/autoload.php';


// Render Captcha for Contact Form
echo Captcha::render('contact');

// Validate Captcha for contact form
if(Captcha::validate('contact')){
    // passed captcha validation
}else{
    // failed captcha validation
}

```

### Advanced Usage
```php
<?php

use VulcanPhp\Captcha;

require __DIR__ . '/vendor/autoload.php';


// Render Captcha Using Alphanumeric
echo Captcha::render(
    'contact',
    Captcha::TYPE_ALPHANUMERIC,
    Captcha::DIFFICULTY_HARD, // list of all difficulties:
        // Captcha::DIFFICULTY_EASY (Only Small Letters)
        // Captcha::DIFFICULTY_MEDIUM (Small Letters + Numbers)
        // Captcha::DIFFICULTY_HARD (Capital Letters + Small Letters + Numbers)
);

// Render a Captcha Using Mathematical Calculations
echo Captcha::render('contact', Captcha::TYPE_MATHEMATICAL);

// Validate Captcha for contact form
if(Captcha::validate('contact', Captcha::TYPE_MATHEMATICAL)){
    // passed captcha validation
}else{
    // failed captcha validation
}

...
```
