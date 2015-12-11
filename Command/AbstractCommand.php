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
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @param ObjectManagerInterface $manager
     */
    public function __construct(ObjectManagerInterface $manager)
    {
        $this->objectManager = $manager;
        parent::__construct();
    }

    /**
     * @return ObjectManagerInterface
     */
    protected function getObjectManager()
    {
        return $this->objectManager;
    }
    
}