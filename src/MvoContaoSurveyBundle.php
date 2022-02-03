<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MvoContaoSurveyBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
    }
}
