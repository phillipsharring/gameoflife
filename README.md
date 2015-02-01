# GameOfLife

An implementation of Conway's Game of Life. With help from Laracast's [Code Katas](http://laracasts.com/series/code-katas-in-php) and [Gulp File](https://laracasts.com/lessons/how-to-trigger-tests-on-save).

## Requirements

1 PHP 5.4+
2 [Node & NPM](http://nodejs.org)
3 [Composer](https://getcomposer.org)

## Installation

```bash
$ git clone https://github.com/philsown/GameOfLife.git
$ cd GameOfLife
$ npm install
$ composer install
```

## Tests

```bash
$ vendor/bin/phpspec run
```

### Automating Tests

```bash
$ gulp
```

I like to run it silently so I only see the test output.

```bash
$ gulp --silent
```

Feedback invited. Send any to philsown at gmail. Thanks.