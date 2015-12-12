<?php
namespace Ihb\ModuleCreator\Command;

use \Symfony\Component\Console\Input\InputArgument;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Output\OutputInterface;
use \Ihb\ModuleCreator\Model\ModuleStructureCreator;
use \Magento\Framework\ObjectManagerInterface;

/**
 * Class CreateCommand
 * @package Ihb\ModuleCreator\Command
 * @author Dmitriy Antonenko <indaheartbeat@gmail.com>
 */
class CreateCommand extends AbstractCommand
{
    /**
     * @var \Ihb\ModuleCreator\Model\ModuleStructureCreator
     */
    protected $moduleStructureCreator;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param ModuleStructureCreator $moduleStructureCreator
     */
    public function __construct(ObjectManagerInterface $objectManager, ModuleStructureCreator $moduleStructureCreator)
    {
        $this->moduleStructureCreator = $moduleStructureCreator;
        parent::__construct($objectManager);
    }

    /**
     * configure
     */
    protected function configure()
    {
        $this->addArgument(
            'moduleName',
            InputArgument::REQUIRED,
            'Name of your module in \'Vendor_Module\' format.'
        );
        $this->setName('ihb:module-create');
        $this->setDescription('Creates simple Magento 2 module structure.');

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return mixed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $moduleStructureCreator = $this->moduleStructureCreator;
        $response  = $moduleStructureCreator->init($input->getArgument('moduleName'));

        $output->writeln(
            $response
        );
    }
}