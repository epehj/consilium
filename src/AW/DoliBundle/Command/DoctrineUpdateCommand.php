<?php

namespace AW\DoliBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\Tools\SchemaTool;

# Ignore to update All Dolibarr table
# Solution found in http://stackoverflow.com/questions/12563005/ignore-a-doctrine2-entity-when-running-schema-manager-update?answertab=votes#tab-top
# New solution https://symfony.com/doc/current/console/commands_as_services.html
class DoctrineUpdateCommand extends \Doctrine\Bundle\DoctrineBundle\Command\Proxy\UpdateSchemaDoctrineCommand
{
  protected function configure()
  {
    parent::configure();

    $this
      ->setName('aw:doctrine:schema:update')
      ->addOption('em', null, InputOption::VALUE_OPTIONAL, 'The entity manager to use for this command');
  }

	protected function executeSchemaCommand(InputInterface $input, OutputInterface $output, SchemaTool $schemaTool, array $metadatas)
	{
    $newMetadatas = array();
    foreach($metadatas as $metadata){
      if($metadata->getReflectionClass()->getNamespaceName() != 'AW\DoliBundle\Entity'){
        array_push($newMetadatas, $metadata);
      }
    }

    parent::executeSchemaCommand($input, $output, $schemaTool, $newMetadatas);
  }
}
