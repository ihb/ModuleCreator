<?php
namespace Ihb\ModuleCreator\Command;

use \Symfony\Component\Console\Input\InputArgument;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Output\OutputInterface;
use \Ihb\ModuleCreator\Model\Creator;
use \Magento\Framework\ObjectManagerInterface;

/**
 * Class CreateCommand
 * @package Ihb\ModuleCreator\Command
 * @author Dmitriy Antonenko <indaheartbeat@gmail.com>
 */
class CreateCommand extends AbstractCommand
{
    protected $creator;

    public function __construct(ObjectManagerInterface $objectManager, Creator $creator)
    {
        $this->creator = $creator;
        parent::__construct($objectManager);
    }

    protected function configure()
    {
        $this->addArgument(
            'moduleName',
            InputArgument::REQUIRED,
            'Name of your module in \'Vendor_Module\' format.'
        );
        $this->setName('ihb:module-create');
        $this->setDescription('Creates simple Magento 2 module architecture.');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $creator = $this->creator;
        $response  = $creator->init($input->getArgument('moduleName'));

        $output->writeln(
            $response
        );
    }
}