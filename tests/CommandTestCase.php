<?php
namespace Pantheon\Terminus\Tests;

use League\Container\Container;
use Pantheon\Terminus\Config;
use Pantheon\Terminus\Runner;
use Pantheon\Terminus\Terminus;
use Robo\Robo;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

abstract class CommandTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Terminus
     */
    protected $app;
    /**
     * @var string
     */
    protected $status_code;
    /**
     * @var Config
     */
    protected $config;
    /**
     * @var Container
     */
    protected $container;
    /**
     * @var OutputInterface
     */
    protected $output;
    /**
     * @var Runner
     */
    protected $runner;
    /**
     * @var ArrayInput
     */
    protected $input;

    /**
     * @return Terminus
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * @param Terminus $app
     * @return $this
     */
    public function setApp($app)
    {
        $this->app = $app;
        return $this;
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param Config $config
     * @return CommandTestCase
     */
    public function setConfig($config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @return Runner
     */
    public function getRunner()
    {
        return $this->runner;
    }

    /**
     * @param Runner $runner
     * @return CommandTestCase
     */
    public function setRunner($runner)
    {
        $this->runner = $runner;
        return $this;
    }

    /**
     * @return OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Convert the output of a command to an easily digested string.
     * @return string|OutputInterface
     */
    public function fetchTrimmedOutput()
    {
        if (get_class($this->output) == BufferedOutput::class) {
            return trim($this->getOutput()->fetch());
        }
        return $this->getOutput();
    }

    /**
     * @param OutputInterface $output
     * @return CommandTestCase
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param mixed $container
     * @return CommandTestCase
     */
    public function setContainer($container)
    {
        $this->container = $container;
        return $this;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->status_code;
    }

    /**
     * @param mixed $status_code
     * @return CommandTestCase
     */
    public function setStatusCode($status_code)
    {
        $this->status_code = $status_code;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @param mixed $input
     * @return CommandTestCase
     */
    public function setInput($input)
    {
        $this->input = new ArrayInput($input);
        return $this;
    }

    /**
     * Run the command and capture the exit code.
     *
     * @return $this
     */
    public function runCommand()
    {
        $this->status_code = $this->runner->run($this->input, $this->output);
        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        if (!$this->config) {
            $this->config = new Config();
        }

        if (!$this->app) {
            $this->app = new Terminus('Terminus', $this->config->get('version'), $this->config);
        }

        if (!$this->container) {
            $this->container = new Container();
        }

        if (!$this->output) {
            $this->output = new BufferedOutput();
        }

        if (!$this->input) {
            $this->input = new ArrayInput([]);
        }
        // Configuring the dependency-injection container
        Robo::configureContainer(
            $this->container,
            $this->config,
            $this->input,
            $this->output,
            $this->app
        );

        if (!$this->runner) {
            $this->runner = new Runner($this->container);
        }
    }
}