<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\DependencyInjection;

use Mvo\ContaoSurvey\Entity\Answer;
use Mvo\ContaoSurvey\Entity\Question;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Form\FormTypeInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('mvo_contao_survey');

        $treeBuilder->getRootNode()
            ->children()
                ->integerNode('session_max_idle_time')
                    ->min(0)
                    ->info('Set the number of seconds without interaction after which the survey session will be destroyed. Set to 0 for no limit.')
                ->end()
                ->arrayNode('types')
                    ->info('Define survey types to be registered.')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('question')
                                ->info('The type\'s question entity')
                                ->cannotBeEmpty()
                                ->validate()
                                    ->always(fn (string $value) => $this->validateClass($value, Question::class))
                                ->end()
                            ->end()
                            ->scalarNode('answer')
                                ->info('The type\'s answer entity')
                                ->cannotBeEmpty()
                                ->validate()
                                    ->always(fn (string $value) => $this->validateClass($value, Answer::class))
                                ->end()
                            ->end()
                            ->scalarNode('form')
                                ->info('The type\'s form type')
                                ->cannotBeEmpty()
                                ->validate()
                                    ->always(fn (string $value) => $this->validateClass($value, FormTypeInterface::class))
                                ->end()
                            ->end()
                            ->scalarNode('form_template')
                                ->info('A custom template that renders the form.')
                                ->defaultNull()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }

    private function validateClass(string $className, string $baseClassOrInterface): string
    {
        $class = new \ReflectionClass($className);

        if (interface_exists($baseClassOrInterface)) {
            if (!$class->implementsInterface($baseClassOrInterface)) {
                throw new \InvalidArgumentException("Class '$className' must implement '$baseClassOrInterface'.");
            }

            return $className;
        }

        if (!$class->isSubclassOf($baseClassOrInterface)) {
            throw new \InvalidArgumentException("Class '$className' must inherit from '$baseClassOrInterface'.");
        }

        return $className;
    }
}
