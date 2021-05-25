<?php

declare(strict_types=1);

namespace Base\Vote\Interfaces;

interface AnswerVariantInterface extends BaseElementInterface
{
    /**
     * Текст варианта ответа
     * @return string
     */
    public function getTitle(): string;
    /**
     * @param string $title
     * @return void
     */
    public function setTitle(string $title);
    /**
     * Тип поля (radio, checkbox, dropdown, multiselect, text, textarea)
     * @return integer
     */
    public function getType(): int;
    /**
     * @param integer $answerVariantType
     * @return void
     */
    public function setType(int $answerVariantType);
    /**
     * @return QuestionInterface|null
     */
    public function getQuestion(): ?QuestionInterface;
    /**
     * @param QuestionInterface|null $question
     * @return void
     */
    public function setQuestion(?QuestionInterface $question);
}
