<?php

declare(strict_types=1);

namespace Base\Vote\Interfaces;

use Bx\Model\Interfaces\ReadableCollectionInterface;

interface VoteResultInterface extends BaseElementInterface
{
    /**
     * @param VoteSchemaInterface $voteSchema
     * @return VoteResultInterface
     */
    public static function createNewResult(VoteSchemaInterface $voteSchema): VoteResultInterface;
    /**
     * @return VoteSchemaInterface
     */
    public function getVoteSchema(): VoteSchemaInterface;

    /**
     * @param AnswerVariantInterface $answerVariant
     * @param string|null $message
     * @return AnswerResultInterface
     */
    public function createAnswerResult(
        AnswerVariantInterface $answerVariant,
        ?string $message = null
    ): AnswerResultInterface;

    /**
     * @param string $questionTitle
     * @param string $answerVariantTitle
     * @param string|null $message
     * @return AnswerResultInterface|null
     */
    public function createAnswerResultByTitle(
        string $questionTitle,
        string $answerVariantTitle,
        ?string $message = null
    ): ?AnswerResultInterface;
    /**
     * @param AnswerResultInterface $answerResult
     * @return void
     */
    public function addAnswerResult(AnswerResultInterface $answerResult);
    /**
     * @param QuestionInterface $question
     * @return AnswerResultInterface[]|ReadableCollectionInterface
     *
     * @psalm-suppress MismatchingDocblockReturnType
     */
    public function getAnswerResultsByQuestion(QuestionInterface $question): ReadableCollectionInterface;
    /**
     * @param string $questionTitle
     * @return AnswerResultInterface[]|ReadableCollectionInterface
     *
     * @psalm-suppress MismatchingDocblockReturnType
     */
    public function getAnswerResultByQuestionTitle(string $questionTitle): ReadableCollectionInterface;
    /**
     * @param QuestionInterface $question
     * @return boolean
     */
    public function hasAnswerResultByQuestion(QuestionInterface $question): bool;
    /**
     * @param string $questionTitle
     * @return boolean
     */
    public function hasAnswerResultByQuestionTitle(string $questionTitle): bool;
    /**
     * @param AnswerResultInterface $answerResult
     * @param bool $isMoved
     * @return void
     */
    public function removeAnswerResult(AnswerResultInterface $answerResult, bool $isMoved = false);
    /**
     * @return AnswerResultInterface[]|ReadableCollectionInterface
     *
     * @psalm-suppress MismatchingDocblockReturnType
     */
    public function getAnswerResults(string $action = null): ReadableCollectionInterface;
    /**
     * @return integer
     */
    public function getAnswerResultsCount(): int;
    /**
     * @param QuestionInterface $question
     * @return AnswerResultInterface[]|ReadableCollectionInterface
     *
     * @psalm-suppress MismatchingDocblockReturnType
     */
    public function getAnswerResultByQuestion(QuestionInterface $question): ReadableCollectionInterface;
}
