<?php

namespace Ihb\ModuleCreator\Model;

use \Braintree\Exception;
use \Ihb\ModuleCreator\Helper\Data;
use \Magento\Framework\App\Filesystem\DirectoryList;
use \Magento\Framework\Filesystem;

/**
 * Class Creator
 * @package Ihb\ModuleCreator\Model
 * @author Dmitriy Antonenko <indaheartbeat@gmail.com>
 */
class Creator
{
    const REGISTRATION_FILENAME = 'registration.php';
    const COMPOSER_FILENAME     = 'composer.json';
    const CODE_DIR              = 'code/';

    /** PHP 5.6 and higher format */
    /** const MODULE_FOLDERS      = array('Block', 'Controller', 'etc', 'Helper', 'Model', 'Observer', 'Test', 'Setup', 'view');  */

    /**
     * Vendor_Module format
     *
     * @var string
     */
    private $moduleName;

    /**
     * Vendor name
     *
     * @var string
     */
    private $vendor;

    /**
     * Module name
     *
     * @var string
     */
    private $module;

    /**
     * Full path to app/code dir
     *
     * @var string
     */
    private $codeDir;

    /**
     * Full path to app/code/vendor dir
     *
     * @var string
     */
    private $vendorDir;

    /**
     * Full path to app/code/vendor/module dir
     *
     * @var string
     */
    private $moduleDir;

    /**
     * @var \Ihb\ModuleCreator\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $directoryList;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $fileSystem;

    /**
     * @param Data $dataHelper
     * @param DirectoryList $directoryList
     * @param Filesystem $fileSystem
     */
    public function __construct(
        Data $dataHelper,
        DirectoryList $directoryList,
        FileSystem $fileSystem
    ) {
        $this->directoryList = $directoryList;
        $this->dataHelper    = $dataHelper;
        /** @var \Magento\Framework\Filesystem $filesystem */
        $this->fileSystem    = $fileSystem;
    }

    /**
     * init
     *
     * @param $moduleName
     * @return string
     */
    public function init($moduleName)
    {
        /** @var \Magento\Framework\Filesystem\Directory\ReadInterface|\Magento\Framework\Filesystem\Directory\Read $reader */
        $reader = $this->fileSystem->getDirectoryRead(DirectoryList::APP);
        $appAbsolutePath = $reader->getAbsolutePath();

        $modulePathSplit    = explode("_", $moduleName);
        $this->moduleName   = $moduleName;
        $this->vendor       = $modulePathSplit[0];
        $this->module       = $modulePathSplit[1];
        $this->codeDir      = $appAbsolutePath . self::CODE_DIR;
        $this->vendorDir    = $this->codeDir . $this->vendor;
        $this->moduleDir    = $this->codeDir . $this->vendor . '/' . $this->module . '/';

        return $this->create();
    }

    /**
     * create
     *
     * @return string
     */
    private function create()
    {
        try {
            if (empty ($this->vendor) || empty ($this->module)) {
                throw new \Exception('Please, enter name of your module in \'Vendor_moduleName\' format.');
            } else if ($this->dataHelper->checkForReservedWords($this->vendor) || $this->dataHelper->checkForReservedWords($this->module)) {
                throw new \Exception('Your VendorName/ModuleName contains PHP reserved words/constants, please, change the name of your module.');
            }

            if (!file_exists($this->vendorDir)) {
                mkdir($this->vendorDir);
            }

            if (!file_exists($this->moduleDir)) {
                mkdir($this->moduleDir);
            } else {
                throw new \Exception('Module folder with this name already exist! Please, change the name of your module folder.');
            }

            $this->createDirStructure();

            return 'Module was successfully created, check it out in ' . "'" . $this->moduleDir . "'" . ' folder.
                   Please, run \'setup:upgrade\' command to enable module.';

        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

    /**
     * Declare dirs array and iterate it. Creates registration.php and composer.json files.
     */
    private function createDirStructure()
    {
        $dirsArray = array('Block', 'Controller', 'etc', 'Helper', 'Model', 'Observer', 'Test', 'Setup', 'view');

        foreach($dirsArray as $dir) {
            mkdir($this->moduleDir . $dir);
            $this->createDirsFiles($dir);
        }
        file_put_contents($this->moduleDir . self::REGISTRATION_FILENAME, $this->getRegistrationPhpContent());
        file_put_contents($this->moduleDir . self::COMPOSER_FILENAME, $this->getComposerJsonContent());
    }

    /**
     * Creates simple Magento 2 structure depends on passed param.
     *
     * @param $dir
     */
    private function createDirsFiles($dir)
    {
        $fullDir = $this->moduleDir . $dir . '/';
        switch ($dir) {
            case 'Block' :
                break;
            case 'Controller' :
                mkdir($fullDir . 'Index');
                file_put_contents($fullDir . 'Index/Index.php', $this->getControllerPhpContent());
                break;
            case 'etc' :
                mkdir($fullDir . 'frontend');
                mkdir($fullDir . 'adminhtml');
                file_put_contents($fullDir . 'module.xml', $this->getModuleXmlContent());
                file_put_contents($fullDir . 'frontend/routes.xml', $this->getRoutesXmlContent());
                break;
            case 'Helper' :
                break;
            case 'Model' :
                break;
            case 'Observer' :
                break;
            case 'Test' :
                mkdir($fullDir . 'Unit');
                break;
            case 'Setup' :
                break;
            case 'view' :
                mkdir($fullDir . 'frontend');
                mkdir($fullDir . 'adminhtml');
                mkdir($fullDir . 'frontend/layout');
                mkdir($fullDir . 'frontend/templates');
                mkdir($fullDir . 'frontend/web');
                break;
        }
    }

    /**
     * registration.php file content
     *
     * @return string
     */
    private function getRegistrationPhpContent()
    {
        $template = '<?php
    \Magento\Framework\Component\ComponentRegistrar::register(
        \Magento\Framework\Component\ComponentRegistrar::MODULE,
        ' . "'" . $this->moduleName . "'" .',
        __DIR__
    );
        ';

        return $template;
    }

    /**
     * composer.json file content
     *
     * @return string
     */
    private function getComposerJsonContent()
    {
        $template = '{
  "name": "' . strtolower($this->vendor) . '/' . strtolower($this->module) .'",
  "description": "Magento 2 ' . $this->vendor . ' ' . $this->module . '",
  "require": {
    "php": "~5.5.0|~5.6.0|~7.0.0",
    "magento/module-config": "1.0.0-beta",
    "magento/module-backend": "1.0.0-beta",
    "magento/magento-composer-installer": "*"
  },
  "type": "magento2-module",
  "version": "100.0.0",
  "license": [
    "proprietary"
  ]
}
        ';

        return $template;
    }

    /**
     * Controller/Index/Index.php file content
     *
     * @return string
     */
    private function getControllerPhpContent()
    {
        $template ='<?php

namespace ' . $this->vendor . "\\" . $this->module . '\Controller\Index;

use Magento\Framework\App\Action\Action;

class Index extends Action
{
    public function execute()
    {
        echo "Test controller";
        var_dump(__METHOD__);
    }
}
        ';

        return $template;
    }

    /**
     * etc/module.xml file content
     *
     * @return string
     */
    private function getModuleXmlContent()
    {
        $template = '<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="../../../../../lib/internal/Magento/Framework/Module/etc/module.xsd">
    <module name="' . $this->moduleName . '" setup_version="2.0.0"/>
</config>
';

        return $template;
    }

    /**
     * etc/frontend/routes.xml file content
     *
     * @return string
     */
    private function getRoutesXmlContent()
    {
        $routeName = strtolower($this->vendor) . strtolower($this->module);
        $template = '<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="../../../../../../lib/internal/Magento/Framework/App/etc/routes.xsd">
    <router id="standard">
        <route id="' . $routeName . '" frontName="' . $routeName . '">
            <module name="' . $this->moduleName . '"/>
        </route>
    </router>
</config>';

        return $template;
    }
}

