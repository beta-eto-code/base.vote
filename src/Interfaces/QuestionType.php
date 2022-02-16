<?php

declare(strict_types=1);

namespace Base\Vote\Interfaces;

interface QuestionType
{
    public const RADIO = 0;
    public const CHECKBOX = 1;
    public const DROPDOWN = 2;
    public const MULTISELECT = 3;
    public const MIXED_TYPE = 99999;
}
