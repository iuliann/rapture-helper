# Rapture Helper component

[![PhpVersion](https://img.shields.io/badge/php-7.0-orange.svg?style=flat-square)](#)
[![License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](#)

Rapture Helper has multiple classes for helping with common tasks like:
- Arrays manipulation
- Strings manipulation
- Country codes
- Frontend assets collection
- Time/Date
- Render HTML elements
- Locale setup for Gettext

## Requirements

- PHP v7.0

## Install

```
composer require iuliann/rapture-helper
```

## Quick start

```php
# Assets
$assets = new Assets();
$assets->add(['styles' => 'css/main.css', 'script' => ['js/jquery.js', 'js/main.js']]);
$assets->renderCss();
// <link rel="stylesheet" type="text/css" href="/assets/css/main.css" />
$assets->renderJs();
// <script type="text/javascript" src="/assets/js/jquery.js"></script>
// <script type="text/javascript" src="/assets/js/main.js"></script>

# Time
$t = Time::now();
// getters
$t->getYear(); 			// .. $t->getSecond()
$t->getDayOfWeek(); 	// 1=Monday...7=Sunday
$t->getDayOfYear(); 	// 0 through 365
$t->getWeekOfYear();	// ISO-8601 week number of year, weeks starting on Monday
$t->getDaysInMonth();	// 28..31
$t->getQuarter();		// 1..4
// checks
$t->isWeekend();
$t->isWeekday();
$t->isToday();
$t->isYesterday();
$t->isTomorrow();
$t->isNextWeek();
$t->isLastMonth(); 	
$t->isCurrentYear();	//etc..
// modifiers start with 'go'
$t = Time::go()->goBack('1 day')->goToNext(Time::MONDAY)->goToStartOf(Time::DAY);
// format
$t->toDate(); // Y-m-d
$t->toTime(); // H:i:s
$t->toDateTime(): // Y-m-d H:i:s

# Strings
$enc  = Strings::encrypt('secret', 'secret-key');	// not for production
$dec  = Strings::decrypt($enc, 'secret-key');
$slug = Strings::sluggify('Hello world!'); // hello-world
```

## About

### Author

Iulian N. `rapture@iuliann.ro`

### Testing

```
cd ./test && phpunit
```

### License

Rapture Helper is licensed under the MIT License - see the `LICENSE` file for details.
