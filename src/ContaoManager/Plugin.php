<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\ManagerPlugin\Config\ConfigPluginInterface;
use Contao\ManagerPlugin\Routing\RoutingPluginInterface;
use Mvo\ContaoSurvey\DependencyInjection\Compiler\RegisterSessionStoragePass;
use Mvo\ContaoSurvey\MvoContaoSurveyBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\KernelInterface;

class Plugin implements BundlePluginInterface, RoutingPluginInterface, ConfigPluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(MvoContaoSurveyBundle::class)
                ->setLoadAfter([
                    TwigBundle::class,
                    ContaoCoreBundle::class,
                ]),
        ];
    }

    public function getRouteCollection(LoaderResolverInterface $resolver, KernelInterface $kernel)
    {
        $file = '@MvoContaoSurveyBundle/Resources/config/routing.yaml';

        $loader = $resolver->resolve($file);

        if (false === $loader) {
            throw new \RuntimeException('Could not load routing configuration.');
        }

        return $loader->load($file);
    }

    public function registerContainerConfiguration(LoaderInterface $loader, array $managerConfig): void
    {
        $loader->load('@MvoContaoSurveyBundle/Resources/config/config.yaml');

        $loader->load(
            static fn (ContainerBuilder $container) => $container->addCompilerPass(
                new RegisterSessionStoragePass()
            )
        );
    }
}
