<?php
namespace Doku\MerchantHosted\Setup;

use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class Uninstall implements UninstallInterface{

	public function uninstall(
		SchemaSetupInterface $setup,
		ModuleContextInterface $context
	){

		$installer = $setup;
		$installer->startSetup();
		$installer->getConnection()->dropTable($installer->getTable('doku_tokenization'));
		$installer->endSetup();

	}

}