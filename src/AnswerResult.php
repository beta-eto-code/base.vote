<?php

declare(strict_types=1);

namespace Base\Vote;

use Base\Vote\Interfaces\AnswerResultInterface;
use Base\Vote\Interfaces\AnswerVariantInterface;
use Base\Vote\Interfaces\QuestionInterface;
use Base\Vote\Interfaces\VoteResultInterface;
use Base\Vote\Traits\PropertyAccessor;

class AnswerResult implements AnswerResultInterface
{
    use PropertyAccessor;

    /**
     * @var AnswerVariantInterface
     */
    private $answerVariant;
    /**
     * @var string
     */
    private $message;
    /**
     * @var VoteResultInterface|null
     */
    private $voteResult;

    public function __construct(AnswerVariantInterface $answerVariant, array $data = [])
    {
        $this->answerVariant = $answerVariant;
        $this->message = (string)$data['message'];
        $this->initProps((array)($data['props'] ?? []));
        $this->voteResult = null;
    }

    /**
     * @param AnswerVariantInterface $answerVariant
     * @param string|null $message
     * @return AnswerResultInterface
     */
    public static function createNewResultAnswer(
        AnswerVariantInterface $answerVariant,
        ?string $message = null
    ): AnswerResultInterface {
        return new AnswerResult($answerVariant, [
            'message' => $message
        ]);
    }

    /**
     * @return AnswerVariantInterface
     */
    public function getAnswerVariant(): AnswerVariantInterface
    {
        return $this->answerVariant;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return void
     */
    public function setMessage(string $message)
    {
        $this->message = $message;
    }

    /**
     * @param VoteResultInterface|null $voteResult
     * @return void
     */
    public function setVoteResult(?VoteResultInterface $voteResult)
    {
        $this->voteResult = $voteResult;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $answerVariant = $this->getAnswerVariant();
        $question = $answerVariant instanceof AnswerVariantInterface ? $answerVariant->getQuestion() : null;
        return [
            'question_title' => $question instanceof QuestionInterface ? $question->getTitle() : '',
            'answer_variant_title' => $answerVariant instanceof AnswerVariantInterface ?
                $answerVariant->getTitle() : '',
            'message' => $this->message,
            'props' => $this->getProps(),
        ];
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return boolean
     */
    public function assertValueByKey(string $key, $value): bool
    {
        return $this->hasValueKey($key) && $this->getValueByKey($key) == $value;
    }

    /**
     * @param string $key
     * @return boolean
     */
    public function hasValueKey(string $key): bool
    {
        if ($key === 'message') {
            return true;
        }

        return isset($this->props[$key]);
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getValueByKey(string $key)
    {
        if ($key === 'message') {
            $this->getMessage();
        }

        return $this->props[$key] ?? null;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
