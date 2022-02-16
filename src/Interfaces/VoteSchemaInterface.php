<?php

declare(strict_types=1);

namespace Base\Vote\Interfaces;

use Bx\Model\Models\File;
use Bx\Model\Interfaces\ReadableCollectionInterface;

interface VoteSchemaInterface extends BaseElementInterface
{
    /**
     * @param string $title
     * @param string|null $description
     * @return VoteSchemaInterface
     */
    public static function createNewVote(string $title, ?string $description = null): VoteSchemaInterface;
    /**
     * Название опроса
     * @return string
     */
    public function getTitle(): string;
    /**
     * @param string $title
     * @return void
     */
    public function setTitle(string $title);
    /**
     * Изображение для опроса
     * @return File|null
     */
    public function getImage(): ?File;
    /**
     * Описание опроса
     * @return string
     */
    public function getDescription(): string;
    /**
     * @param string $description
     * @return void
     */
    public function setDescription(string $description);
    /**
     * @param string $title
     * @return QuestionInterface|null
     */
    public function getQuestionByTitle(string $title): ?QuestionInterface;
    /**
     * @param QuestionInterface $questionInterface
     * @return void
     */
    public function addQuestion(QuestionInterface $questionInterface);
    /**
     * @param string $title
     * @param integer $type
     * @return QuestionInterface
     */
    public function createQuestion(string $title, int $type = null): QuestionInterface;
    /**
     * Undocumented function
     * @param QuestionInterface $question
     * @param boolean $isMoved
     * @return void
     */
    public function removeQuestion(QuestionInterface $question, bool $isMoved = false);
    /**
     * @param string|null $action
     * @return QuestionInterface[]|ReadableCollectionInterface
     *
     * @psalm-suppress MismatchingDocblockReturnType
     */
    public function getQuestions(string $action = null): ReadableCollectionInterface;
    /**
     * @return integer
     */
    public function getQuestionCount(): int;
}
