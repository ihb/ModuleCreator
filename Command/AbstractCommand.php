<?php
namespace Ihb\ModuleCreator\Command;

use \Symfony\Component\Console\Command\Command;
use \Magento\Framework\ObjectManagerInterface;

/**
 * Class AbstractCommand
 * @package Ihb\ModuleCreator\Command
 * @author Dmitriy Antonenko <indaheartbeat@gmail.com>
 */
class AbstractCommand extends Command
{
    protected $objectManager;
    public function __construct(ObjectManagerInterface $manager)
    {
        $this->objectManager = $manager;
        parent::__construct();
    }
    
    protected function getObjectManager()
    {
        return $this->objectManager;
    }
    
}