<?php
namespace Reactor\json2yaml;

use Reactor\CliArguments\ArgumentsParser;

include __dir__.'/../vendor/autoload.php';

$cli = new CliController();
$cli->handle(new ArgumentsParser($GLOBALS['argv']));
