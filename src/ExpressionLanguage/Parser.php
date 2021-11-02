<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\ExpressionLanguage;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\Node\NameNode;
use Symfony\Component\ExpressionLanguage\Node\Node;

class Parser
{
    public function getUsedFields(string $expression, array $fields): array
    {
        $rootNode = (new ExpressionLanguage())
            ->parse($expression, $fields)
            ->getNodes()
        ;

        return $this->getFields($rootNode);
    }

    /**
     * @return array<int, string>
     */
    private function getFields(Node $node): array
    {
        $identifiers = [];

        foreach ($node->nodes as $childNode) {
            if ($childNode instanceof NameNode) {
                $identifiers[] = $childNode->attributes['name'];
                continue;
            }

            $identifiers = [...$identifiers, ...$this->getFields($childNode)];
        }

        return $identifiers;
    }
}
