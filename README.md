# VulcanPhp Captcha
All-In-One Simple Captcha Validation for PHP Application

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