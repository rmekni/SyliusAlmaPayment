<?php

declare(strict_types=1);

namespace Alma\SyliusPaymentPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class AlmaSyliusPaymentExtension extends Extension implements PrependExtensionInterface
{
    private const GATEWAY_CONFIGURATION_TEMPLATE = '@AlmaSyliusPaymentPlugin/admin/payment_method/gateway_configuration.html.twig';

    public function load(array $config, ContainerBuilder $container): void
    {
        $this->processConfiguration($this->getConfiguration([], $container), $config);
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }

    public function getConfiguration(array $config, ContainerBuilder $container): ConfigurationInterface
    {
        return new Configuration();
    }

    /**
     * Sylius 2 admin: shows the Alma settings in the payment method form ("alma_payments" gateway),
     * on the create and update pages.
     */
    public function prepend(ContainerBuilder $container): void
    {
        if (!$container->hasExtension('sylius_twig_hooks')) {
            return;
        }

        $hooks = [];
        foreach (['create', 'update'] as $page) {
            $hooks[sprintf('sylius_admin.payment_method.%s.content.form.sections.gateway_configuration.alma_payments', $page)] = [
                'alma_configuration' => [
                    'template' => self::GATEWAY_CONFIGURATION_TEMPLATE,
                    'priority' => 3,
                ],
            ];
        }

        $container->prependExtensionConfig('sylius_twig_hooks', ['hooks' => $hooks]);
    }
}
