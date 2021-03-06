<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class MvoContaoSurveyExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        // load services
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );

        $loader->load('services.yaml');

        // set parameters
        $container->setParameter('mvo_survey.session_max_idle_time', $config['session_max_idle_time']);
        $container->setParameter('mvo_survey.slug_generator_options', $config['slug_generator']);
        $container->setParameter('mvo_survey.protect_editing', $config['protect_editing']);

        // register configured survey types
        foreach ($config['types'] as $key => $item) {
            $container
                ->getDefinition('mvo.survey.registry')
                ->addMethodCall('add', [
                    $key,
                    $item['question'],
                    $item['answer'],
                    $item['form'],
                    $item['form_template'],
                ])
            ;
        }
    }
}
