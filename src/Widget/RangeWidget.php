<?php

declare(strict_types=1);

/*
 * @author  Moritz Vondano
 * @license MIT
 */

namespace Mvo\ContaoSurvey\Widget;

use Contao\TextField;

class RangeWidget extends TextField
{
    public function generate(): string
    {
        $min = $this->arrConfiguration['minval'] ?? 0;
        $max = $this->arrConfiguration['maxval'] ?? 100;

        $outputId = "ctrl_{$this->strId}_output";
        $value = (int) ($this->varValue ?: ($min + $max) / 2);

        $rangeAttributes = sprintf(
            'type="range" min="%d" max="%d" step="1" oninput="%s.value=value"',
            $min,
            $max,
            $outputId,
        );

        $inputField = sprintf(
            '<input name="%s" id="%s" value="%d"%s onfocus="Backend.getScrollOffset()" %s>',
            $this->strName,
            "ctrl_{$this->strId}",
            $value,
            $this->getAttributes(),
            $rangeAttributes
        );

        $outputField = sprintf(
            '<output id="%s">%d</output>',
            $outputId,
            $value
        );

        return $inputField.$outputField;
    }
}
