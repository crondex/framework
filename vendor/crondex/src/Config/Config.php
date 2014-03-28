<?php namespace Crondex\Config;

use Exception;

class Config
{
    protected $_configFilePath;

    public function __construct($configFilePath)
    {
        $this->_configFilePath = $configFilePath;
        $this->load();
    }

    protected function load()
    {
        if (!file_exists("$this->_configFilePath")){
            throw new Exception('Configuration file not found');
        } else {
            //$this->_config = file_get_contents($this->_configFilePath);
            $this->_config = require $this->_configFilePath;
            //echo '<pre>';
            //var_dump($this->_config);
            //echo '</pre>';
            //foreach ($this->_config as $k => $v) {
            //   echo "\$a[$k] => $v.\n";
            //}
        }
    }

    public function get($configKey) {
        if ($this->_config !== NULL) {
            if (isset($this->_config[$configKey])) {             
                return $this->_config[$configKey];
            } else {
                throw new Exception('Config Variable ' . $configKey . ' does not exist');
            }
        } else {
            throw new Exception('Configuration file was not loaded.');
        } 
    }
}
