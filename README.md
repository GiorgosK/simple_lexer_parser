# Simple Lexer Parser

A simple lexer and parser that can compute simple mathematical operations using the most basic operators `+, -, *, /` and can correctly handle parentheses `()` and floating point operations (decimal numbers).  It can NOT currently handle negative numbers or unary operations.

It is implemented as a Drupal 8 service module and provides a text field formatter.

The formatter currently displays errors inline.

## Requirements

* Drupal 8

## Installation

Download, install and enable as you normally install a Drupal 8 module hosted on github.

```bash
cd modules/contrib
git clone git@github.com:GiorgosK/simple_lexer_parser.git
drush en simple_lexer_parser
```

## Usage

After enabling the module create a `text field` in your content type and choose `Simple lexer parser formatter` as a formatter at `admin/structure/types/manage/CONTENT_TYPE/display`

The formatter will display both the expression and the result (or error message)

```
(1 + 2) * 4  = 12
```

The result will be revealed on hover with a delayed css animation

## Unit testing

Run test cases for this module using `phpunit` (tested with phpunit 6.5)

```
cd web
../vendor/bin/phpunit -c core/phpunit.xml.dist modules/custom/simple_lexer_parser/tests/src/Calculator/CalculatorTest.php
```

You should get 10 tests and 10 assertions
