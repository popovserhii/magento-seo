<?php
/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$tableRule = $installer->getTable('agere_seo/rule');
$date = new DateTime();

$installer->startSetup();

$rule = $installer->getConnection()
    ->newTable($tableRule)
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Autoincrement')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
    ), 'Store id')
    ->addColumn('date_created', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'nullable'  => false,
    ), 'Дата створення правила')
    ->addColumn('type', Varien_Db_Ddl_Table::TYPE_CHAR, 16, array(
        'nullable'  => false,
    ), 'Тип правила (для: каталогу, продукту, ін.)')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
    ), 'Title meta tag')
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
    ), 'Meta description tag')
    ->addColumn('keywords', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
    ), 'Meta keywords tag')
    ->addColumn('seo_attributes', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
    ), 'Magento attributes for SEO');
$installer->getConnection()->createTable($rule);

/*$sql = "INSERT INTO `{$tableRule}` (`date_created`, `type`, `title`, `description`, `keywords`, `seo_attributes`) VALUES
    ('{$date->format('Y-m-d H:i:s')}',
    'product',
    '%brand% %model% %engine_size%, год %year%, %body%, %fuel%, %transmission% с пробегом',
    '%brand% %model% %engine_size% %year% - автомобиль с пробегом - купить авто с пробегом %brand% в АИС-Маркет Киев',
    'купить %brand% %model%, %brand:translit% %model:translit% с пробегом, продажа %brand% %model%, продать %brand% %model%, %brand% %model% с пробегом, %brand% %model% бу',
    'brand,model_*,body,fuel,transmission,engine_size,year'
    ),
    ('{$date->format('Y-m-d H:i:s')}',
    'category',
    '%brand% %model% %body% %fuel% %transmission% с пробегом',
    'АИС Маркет предлагает надежные автомобили с пробегом %brand% %model% с гарантией происхождения. Купите авто %brand% %model% %body% %fuel% %transmission% с пробегом по отличной цене. Звоните: +38 044 585 22 02',
    'купить %brand% %model%, %brand:translit% %model:translit% с пробегом, продажа %brand% %model%, продать %brand% %model%, %brand% %model% с пробегом, %brand% %model% бу',
    'brand,model_*,body,fuel,transmission'
    );";
$installer->run($sql);*/

$installer->endSetup();