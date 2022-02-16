<?php

declare(strict_types=1);

namespace Base\Vote\Interfaces;

interface AnswerResultInterface extends BaseElementInterface
{
    /**
     * @param AnswerVariantInterface $answerVariant
     * @param string|null $message
     * @return AnswerResultInterface
     */
    public static function createNewResultAnswer(
        AnswerVariantInterface $answerVariant,
        ?string $message = null
    ): AnswerResultInterface;
    /**
     * @return AnswerVariantInterface
     */
    public function getAnswerVariant(): AnswerVariantInterface;
    /**
     * @return string
     */
    public function getMessage(): string;
    /**
     * @param string $message
     * @return void
     */
    public function setMessage(string $message);
    /**
     * @param VoteResultInterface|null $voteResult
     * @return void
     */
    public function setVoteResult(?VoteResultInterface $voteResult);
}
