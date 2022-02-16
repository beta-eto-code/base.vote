<?php

declare(strict_types=1);

namespace Base\Vote\Interfaces;

class AnswerVariantType
{
    public const RADIO = 0;
    public const CHECKBOX = 1;
    public const DROPDOWN = 2;
    public const MULTISELECT = 3;
    public const TEXT = 4;
    public const TEXTAREA = 5;
}
