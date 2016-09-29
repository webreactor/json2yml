<?php

namespace Reactor\json2yaml;

use Symfony\Component\Yaml\Yaml;
use Reactor\CliArguments\ArgumentDefinition;

class CliController {
    private $version = '0.0.1';
    private $pwd;

    public function __construct() {
        $this->pwd = getcwd().'/';
    }

    public function handle($arguments_container) {
        try {
            $this->handleLogic($arguments_container);
        } catch (\Exception $e) {
            echo "Error: ".$e->getMessage()."\n\n";
            exit(1);
        }
    }

    public function handleLogic($arguments_container) {
        $arguments = $this->parseArguments($arguments_container);
        switch ($arguments['command']) {
            case 'help':
                $this->printHelp($arguments_container);
            break;
            case 'file':
                $file = $this->pwd.$arguments['file'];
                if (!is_file($file)) {
                    throw new \Exception("File not found $file", 1);
                }
                echo Yaml::dump(json_decode(file_get_contents($this->pwd.$arguments['file']), true), 4);
            break;
            case 'stream':
                $string = '';
                while ($t = fgets(STDIN)) {
                    $string .= $t;
                }
                echo Yaml::dump(json_decode($string, true), 4);
            break;

        };
    }

    public function parseArguments($arguments_container) {
        $this->defineArguments($arguments_container);
        $arguments = array();

        $_cli_words = $arguments_container->get('_words_');
        $is_help = $arguments_container->get('help');
        if (!isset($_cli_words[1])) {
            $arguments['command'] = 'stream';
        } else {
            $arguments['file'] = $_cli_words[1];
            $arguments['command'] = 'file';
        }
        if ($is_help) {
            $arguments['command'] = 'help';
        }
        return $arguments;
    }

    public function defineArguments($arguments) {
        $arguments->addDefinition(new ArgumentDefinition('_words_', '', '', false, true, 'command'));
        $arguments->addDefinition(new ArgumentDefinition('help', 'h', true, true, false, 'show help'));
        $arguments->parse();
        return $arguments;
    }

    public function printHelp($arguments) {
        echo "Converts josn file to YAML {$this->version}\n";
        echo "\nUsage:\n";
        echo "  json2yml [<file.json>] > file.yml\n";
        echo "  cat file.json | json2yml > file.yml\n";
        echo "\nArguments:\n";
        echo "  Full name    | Short | Default            | Note\n";
        echo "-------------------------------------------------------\n";

        foreach ($arguments->definitions as $key => $definition) {
            if ($key != '_words_') {
                echo sprintf("  --%-12s -%-6s %-20s %s\n",
                    $definition->name,
                    $definition->short,
                    $definition->default,
                    $definition->description
                );
            }
        }
        echo "\n";
    }

}
