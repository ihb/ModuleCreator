<?php

namespace Ihb\ModuleCreator\Model;

use \Braintree\Exception;
use \Ihb\ModuleCreator\Helper\RestrictedKeywordValidator;
use \Magento\Framework\App\Filesystem\DirectoryList;
use \Magento\Framework\Filesystem;

/**
 * Class ModuleStructureCreator
 * @package Ihb\ModuleCreator\Model
 * @author Dmitriy Antonenko <indaheartbeat@gmail.com>
 */
class ModuleStructureCreator
{
    const REGISTRATION_FILENAME = 'registration.php';
    const COMPOSER_FILENAME     = 'composer.json';
    const CODE_DIR              = 'code/';

    /** PHP 5.6 and higher format */
    /** const MODULE_FOLDERS      = array('Block', 'Controller', 'etc', 'Helper', 'Model', 'Observer', 'Test', 'Setup', 'view');  */

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
     * @var \Ihb\ModuleCreator\Helper\RestrictedKeywordValidator
     */
    protected $keywordValidator;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $directoryList;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $fileSystem;

    /**
     * @var FileTemplateCreator
     */
    protected $fileTemplateCreator;

    /**
     * @param RestrictedKeywordValidator $keywordValidator
     * @param DirectoryList $directoryList
     * @param Filesystem $fileSystem
     * @param FileTemplateCreator $fileTemplateCreator
     */
    public function __construct(
        RestrictedKeywordValidator $keywordValidator,
        DirectoryList $directoryList,
        FileSystem $fileSystem,
        FileTemplateCreator $fileTemplateCreator
    ) {
        $this->directoryList       = $directoryList;
        $this->keywordValidator    = $keywordValidator;
        $this->fileSystem          = $fileSystem;
        $this->fileTemplateCreator = $fileTemplateCreator;
    }

    /**
     * init
     *
     * @param $vendorModuleName
     * @return string
     */
    public function init($vendorModuleName)
    {
        try {
            /** @var \Magento\Framework\Filesystem\Directory\ReadInterface|\Magento\Framework\Filesystem\Directory\Read $reader */
            $reader = $this->fileSystem->getDirectoryRead(DirectoryList::APP);
            $appAbsolutePath = $reader->getAbsolutePath();

            $modulePathSplit = explode("_", $vendorModuleName);
            $this->vendor    = $modulePathSplit[0];
            $this->module    = $modulePathSplit[1];
            $this->codeDir   = $appAbsolutePath . self::CODE_DIR;
            $this->vendorDir = $this->codeDir . $this->vendor . '/';
            $this->moduleDir = $this->vendorDir . $this->module . '/';

            $this->fileTemplateCreator->setVendorModuleName($vendorModuleName);
            $this->fileTemplateCreator->setVendor($this->vendor);
            $this->fileTemplateCreator->setModule($this->module);

            return $this->create();
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

    /**
     * create
     *
     * @return string
     * @throws \Exception
     */
    private function create()
    {
        if (empty ($this->vendor) || empty ($this->module)) {
            throw new \Exception('Please, enter name of your module in \'Vendor_moduleName\' format.');
        } else if ($this->keywordValidator->checkForReservedWords($this->vendor) || $this->keywordValidator->checkForReservedWords($this->module)) {
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

        $this->createDirFileStructure();

        return 'Module was successfully created, check it out in ' . "'" . $this->moduleDir . "'" . ' folder.
               Please, run \'setup:upgrade\' command to enable module.';

    }

    /**
     * Declare dirs array and iterate it.
     */
    private function createDirFileStructure()
    {
        $dirsArray = array('Block', 'Controller', 'etc', 'Helper', 'Model', 'Observer', 'Test', 'Setup', 'view');

        $this->createRootFiles();

        foreach ($dirsArray as $dir) {
            mkdir(sprintf("%s%s", $this->moduleDir, $dir));

            $fullDirName = sprintf("%s%s/", $this->moduleDir, $dir);
            $method      = sprintf("fill%sDir", ucfirst($dir));

            if (method_exists($this, $method)) {
                call_user_func([$this, $method], $fullDirName);
            }
        }
    }

    /**
     * create root files - composer.json and registration.php
     */
    public function createRootFiles() {
        file_put_contents($this->moduleDir . self::REGISTRATION_FILENAME, $this->fileTemplateCreator->getRegistrationPhpContent());
        file_put_contents($this->moduleDir . self::COMPOSER_FILENAME, $this->fileTemplateCreator->getComposerJsonContent());

    }

    /**
     * Fill Block dir
     *
     * @param $fullDirName
     */
    private function fillBlockDir($fullDirName)
    {

    }

    /**
     * @param $fullDirName
     */
    private function fillControllerDir($fullDirName)
    {
        mkdir($fullDirName . 'Index');
        file_put_contents($fullDirName . 'Index/Index.php', $this->fileTemplateCreator->getControllerPhpContent());
    }

    /**
     * Fill etc dir
     *
     * @param $fullDirName
     */
    private function fillEtcDir($fullDirName)
    {
        mkdir($fullDirName . 'frontend');
        mkdir($fullDirName . 'adminhtml');
        file_put_contents($fullDirName . 'module.xml', $this->fileTemplateCreator->getModuleXmlContent());
        file_put_contents($fullDirName . 'frontend/routes.xml', $this->fileTemplateCreator->getRoutesXmlContent());
    }

    /**
     *  Fill Helper dir
     *
     * @param $fullDirName
     */
    private function fillHelperDir($fullDirName)
    {

    }

    /**
     * Fill Model dir
     *
     * @param $fullDirName
     */
    private function fillModelDir($fullDirName)
    {

    }

    /**
     * Fill Observer dir
     *
     * @param $fullDirName
     */
    private function fillObserverDir($fullDirName)
    {

    }

    /**
     * Fill Test dir
     *
     * @param $fullDirName
     */
    private function fillTestDir($fullDirName)
    {
        mkdir($fullDirName . 'Unit');
    }

    /**
     * Fill Setup dir
     *
     * @param $fullDirName
     */
    private function fillSetupDir($fullDirName)
    {

    }

    /**
     * Fill View dir
     *
     * @param $fullDirName
     */
    private function fillViewDir($fullDirName)
    {
        mkdir($fullDirName . 'frontend');
        mkdir($fullDirName . 'adminhtml');
        mkdir($fullDirName . 'frontend/layout');
        mkdir($fullDirName . 'frontend/templates');
        mkdir($fullDirName . 'frontend/web');
    }


}

