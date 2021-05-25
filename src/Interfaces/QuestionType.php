<?php

declare(strict_types=1);

namespace Base\Vote\Interfaces;

interface QuestionType
{
    const RADIO = 0;
    const CHECKBOX = 1;
    const DROPDOWN = 2;
    const MULTISELECT = 3;
    const MIXED_TYPE = 99999;
}
