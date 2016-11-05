<?php
namespace BasicInvoices\Option;

class ConfigProvider
{
    /**
     * Retrieve zend-db default configuration.
     *
     * @return array
     */
    public function __invoke()
    {
        return [
            'dependencies' => $this->getDependencyConfig(),
        ];
    }

    /**
     * Retrieve zend-db default dependency configuration.
     *
     * @return array
     */
    public function getDependencyConfig()
    {
        return [
            'factories' => [
                Option::class => OptionServiceFactory::class,
            ],
        ];
    }
}
