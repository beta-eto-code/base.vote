<?php

declare(strict_types=1);

namespace Base\Vote\Interfaces;

class AnswerVariantType
{
    const RADIO = 0;
    const CHECKBOX = 1;
    const DROPDOWN = 2;
    const MULTISELECT = 3;
    const TEXT = 4;
    const TEXTAREA = 5;
}
