<?php

namespace Ihb\ModuleCreator\Model;

/**
 * Class FileTemplateCreator
 * @package Ihb\ModuleCreator\Model
 * @author Dmitriy Antonenko <indaheartbeat@gmail.com>
 */
class FileTemplateCreator
{
    /**
     * Vendor_Module format
     *
     * @var string
     */
    protected $vendorModuleName;

    /**
     * Vendor name
     *
     * @var string
     */
    protected $vendor;

    /**
     * Module name
     *
     * @var string
     */
    protected $module;

    /**
     * @param $vendorModuleName
     */
    public function setVendorModuleName($vendorModuleName)
    {
        $this->vendorModuleName = $vendorModuleName;
    }

    /**
     * @param $vendor
     */
    public function setVendor($vendor)
    {
        $this->vendor = $vendor;
    }

    /**
     * @param $module
     */
    public function setModule($module)
    {
        $this->module = $module;
    }

    /**
     * registration.php file content
     *
     * @return string
     */
    public function getRegistrationPhpContent()
    {
        $template = sprintf(<<<TEMPLATE
<?php
\Magento\Framework\Component\ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::MODULE,
    '%s',
    __DIR__
);

TEMPLATE
        , $this->vendorModuleName);

        return $template;
    }

    /**
     * composer.json file content
     *
     * @return string
     */
    public function getComposerJsonContent()
    {
        $vendorModuleLower = sprintf("%s/%s", strtolower($this->vendor), strtolower($this->module));
        $vendorModuleSpace = sprintf("%s %s", $this->vendor, $this->module);
        $vendorModuleSlash = sprintf("%s/%s", $this->vendor, $this->module);

        $template =  sprintf(<<<TEMPLATE
{
  "name": "%s",
  "description": "Magento 2 %s",
  "require": {
    "php": "~5.5.0|~5.6.0|~7.0.0",
    "magento/magento-composer-installer": "*"
  },
  "type": "magento2-module",
  "version": "100.0.0",
  "license": [
    "proprietary"
  ],
  "extra": {
    "map": [
      [
        "*",
        "%s"
      ]
    ]
  }
}

TEMPLATE
         , $vendorModuleLower, $vendorModuleSpace, $vendorModuleSlash);
        return $template;
    }

    /**
     * Controller/Index/Index.php file content
     *
     * @return string
     */
    public function getControllerPhpContent()
    {
        $template = sprintf(<<<TEMPLATE
<?php

namespace %s\%s\Controller\Index;

use Magento\Framework\App\Action\Action;

class Index extends Action
{
    public function execute()
    {
        echo "Test controller";
        var_dump(__METHOD__);
    }
}

TEMPLATE
        , $this->vendor, $this->module);

        return $template;
    }

    /**
     * etc/module.xml file content
     *
     * @return string
     */
    public function getModuleXmlContent()
    {
        $template = sprintf(<<<TEMPLATE
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="../../../../../lib/internal/Magento/Framework/Module/etc/module.xsd">
    <module name="%s" setup_version="2.0.0"/>
</config>

TEMPLATE
        , $this->vendorModuleName);

        return $template;
    }

    /**
     * etc/frontend/routes.xml file content
     *
     * @return string
     */
    public function getRoutesXmlContent()
    {
        $routeName = sprintf('%s%s', strtolower($this->vendor), strtolower($this->module));

        $template = sprintf(<<<TEMPLATE
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="../../../../../../lib/internal/Magento/Framework/App/etc/routes.xsd">
    <router id="standard">
        <route id="%s" frontName="%s">
            <module name="%s"/>
        </route>
    </router>
</config>

TEMPLATE
        , $routeName, $routeName, $this->vendorModuleName);

        return $template;
    }
}
