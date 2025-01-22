<?php

namespace CSSoft\CheckoutDetails\Setup\Patch\Data;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use Magento\Store\Model\ScopeInterface;
use CSSoft\CheckoutDetails\Helper\Data as HelperData;
use CSSoft\CheckoutDetails\Model\ResourceModel\Field\CollectionFactory as FieldCollectionFactory;
use CSSoft\CheckoutDetails\Model\ResourceModel\Field\Value\CollectionFactory as FieldValueCollectionFactory;

class ConvertDatesToIsoFormat implements DataPatchInterface
{
    private ModuleDataSetupInterface $moduleDataSetup;

    private FieldCollectionFactory $fieldCollectionFactory;

    private FieldValueCollectionFactory $fieldValueCollectionFactory;

    private HelperData $helper;

    private ScopeConfigInterface $scopeConfig;

    private array $localeCodes = [];

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        FieldCollectionFactory $fieldCollectionFactory,
        FieldValueCollectionFactory $fieldValueCollectionFactory,
        HelperData $helper,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->fieldCollectionFactory = $fieldCollectionFactory;
        $this->fieldValueCollectionFactory = $fieldValueCollectionFactory;
        $this->helper = $helper;
        $this->scopeConfig = $scopeConfig;
    }

    public function apply()
    {
        $fieldIds = $this->fieldCollectionFactory->create()
            ->addFieldToFilter('frontend_input', 'date')
            ->getColumnValues('field_id');

        if (!$fieldIds) {
            return $this;
        }

        $this->moduleDataSetup->startSetup();

        $fieldValuesCollection = $this->fieldValueCollectionFactory->create()
            ->addFieldToFilter('order_id', ['notnull' => true])
            ->addFieldToFilter('field_id', ['in' => $fieldIds]);

        foreach ($fieldValuesCollection as $fieldValue) {
            $date = (string) $fieldValue->getValue();
            if (strpos($date, '00:00:00') !== false) {
                continue;
            }

            $localeCode = $this->getLocaleCode($fieldValue->getStoreId());

            try {
                $date = $this->helper->parseDateString($date, $localeCode);
                $fieldValue
                    ->setValue($date->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT))
                    ->save();
            } catch (\Exception $e) {
                //
            }
        }

        $this->moduleDataSetup->endSetup();

        return $this;
    }

    private function getLocaleCode($storeId)
    {
        if (!isset($this->localeCodes[$storeId])) {
            $this->localeCodes[$storeId] = $this->scopeConfig->getValue(
                'general/locale/code',
                ScopeInterface::SCOPE_STORE,
                $storeId
            );
        }
        return $this->localeCodes[$storeId];
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }
}
