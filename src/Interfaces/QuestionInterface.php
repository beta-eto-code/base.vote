<?php

declare(strict_types=1);

namespace Base\Vote\Interfaces;

use Bx\Model\Interfaces\ReadableCollectionInterface;

interface QuestionInterface extends BaseElementInterface
{
    /**
     * Текст вопроса
     * @return string
     */
    public function getTitle(): string;
    /**
     * @param string $title
     * @return void
     */
    public function setTitle(string $title);
    /**
     * Является ли вопрос обязательным для ответа
     * @return boolean
     */
    public function isRequired(): bool;
    /**
     * @param boolean $state
     * @return void
     */
    public function setRequired(bool $state);
    /**
     * Тип вопроса (radio, checkbox, dropdown, multiselect, режим совместимости)
     * @return integer
     */
    public function getType(): int;
    /**
     * @param integer $questionType
     * @return void
     */
    public function setType(int $questionType);
    /**
     * @param string $title
     * @return AnswerVariantInterface|null
     */
    public function getAnswerVariantByTitle(string $title): ?AnswerVariantInterface;
    /**
     * Добавляем новый вариант ответа
     * @param AnswerVariantInterface $answerVariant
     * @return void
     */
    public function addAnswerVariant(AnswerVariantInterface $answerVariant);
    /**
     * Создаем новый вариант ответа
     * @param string $title
     * @param integer|null $type
     * @return AnswerVariantInterface
     */
    public function createAnswerVariant(string $title, int $type = null): AnswerVariantInterface;
    /**
     * Удаляем указанный вариант ответа из списка вопроса
     * @param AnswerVariantInterface $answerVariant
     * @param boolean $isMoved
     * @return void
     */
    public function removeAnswerVariant(AnswerVariantInterface $answerVariant, bool $isMoved = false);
    /**
     * @return VoteSchemaInterface|null
     */
    public function getVote(): ?VoteSchemaInterface;
    /**
     * @param VoteSchemaInterface $voteSchemaInterface
     * @return void
     */
    public function setVote(?VoteSchemaInterface $voteSchema);
    /**
     * @param string|null $action
     * @return AnswerVariantInterface[]|ReadableCollectionInterface
     */
    public function getAnswerVariants(string $action = null): ReadableCollectionInterface;
    /**
     * @return boolean
     */
    public function isMultiple(): bool;
    /**
     * @return integer
     */
    public function getAnswerVariantCount(): int;
}
