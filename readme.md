# 51Degrees PHP Pipeline Core

![51Degrees](https://51degrees.com/DesktopModules/FiftyOne/Distributor/Logo.ashx?utm_source=github&utm_medium=repository&utm_content=readme_main&utm_campaign=php-open-source "Data rewards the curious") **PHP Pipeline API**

[Developer Documentation](https://51degrees.com/documentation/4.2/index.html?utm_source=github&utm_medium=repository&utm_content=documentation&utm_campaign=php-open-source"developer documentation")

## Introduction
This project contains the core source code for the PHP implementation of the 51Degrees Pipeline API.

The Pipeline is a generic micro-services aggregation solution with the ability to add a range of 51Degrees and/or custom plug ins (Engines) 

## Examples

To run the examples, you first need to install dependencies. Navigate to the repository root and execute:

```
composer install
```

This will create the vendor directory containing autoload.php. Now navigate to the examples directory and start a PHP server with the relevant file. For example:

```
PHP -S localhost:3000 customFlowElement.php
```

This will start a local web server listening on port 3000. Open your web browser and browse to http://localhost:3000/ to see the example in action.

## Tests
To run the tests in this repository, make sure PHPUnit is installed then, in the root of this repo, call:
```
phpunit --log-junit test-results.xml
```
