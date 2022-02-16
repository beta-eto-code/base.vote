<?php

declare(strict_types=1);

namespace Base\Vote;

use Base\Vote\Interfaces\AnswerResultInterface;
use Base\Vote\Interfaces\AnswerVariantInterface;
use Base\Vote\Interfaces\QuestionInterface;
use Base\Vote\Interfaces\VoteResultInterface;
use Base\Vote\Interfaces\VoteSchemaInterface;
use Base\Vote\Traits\PropertyAccessor;
use Bx\Model\Collection;
use Bx\Model\Interfaces\ReadableCollectionInterface;
use Exception;
use SplObjectStorage;

class VoteResult implements VoteResultInterface
{
    use PropertyAccessor;

    /**
     * @var VoteSchemaInterface
     */
    private $voteSchema;
    /**
     * @var SplObjectStorage
     */
    private $answerResults;

    /**
     * @var array
     */
    protected $props;

    public function __construct(
        VoteSchemaInterface $voteSchema,
        array $data = []
    ) {
        $this->voteSchema = $voteSchema;
        $this->answerResults = new SplObjectStorage();
        $this->props = [];
        $this->initProps((array)($data['props'] ?? []));

        $answerResultDataList = (array)($data['answer'] ?? []);
        $this->loadAnswerResults($answerResultDataList);
    }

    /**
     * @param array $answerResultDataList
     * @return void
     */
    private function loadAnswerResults(array $answerResultDataList)
    {
        foreach ($answerResultDataList as $answerResultData) {
            $questionTitle = $answerResultData['question_title'] ?? '';
            $answerVariantTitle = $answerResultData['answer_variant_title'] ?? '';
            $answerVariant = $this->getAnswerVariantByTitle($questionTitle, $answerVariantTitle);

            if ($answerVariant instanceof AnswerVariantInterface) {
                $this->answerResults->attach(new AnswerResult($answerVariant, $answerResultData), 'new');
            }
        }
    }

    /**
     * @param string $questionTitle
     * @param string $answerVariantTitle
     * @return AnswerVariantInterface|null
     */
    private function getAnswerVariantByTitle(string $questionTitle, string $answerVariantTitle): ?AnswerVariantInterface
    {
        if (empty($questionTitle) || empty($answerVariantTitle)) {
            return null;
        }

        $question = $this->voteSchema->getQuestionByTitle($questionTitle);
        if (!($question instanceof QuestionInterface)) {
            return null;
        }

        $answerVariant = $question->getAnswerVariantByTitle($answerVariantTitle);

        return $answerVariant instanceof AnswerVariantInterface ? $answerVariant : null;
    }

    /**
     * @param VoteSchemaInterface $voteSchema
     * @return VoteResultInterface
     */
    public static function createNewResult(VoteSchemaInterface $voteSchema): VoteResultInterface
    {
        return new VoteResult($voteSchema);
    }

    /**
     * @param string $questionTitle
     * @param string $answerVariantTitle
     * @param string|null $message
     * @return AnswerResultInterface|null
     * @throws Exception
     */
    public function createAnswerResultByTitle(
        string $questionTitle,
        string $answerVariantTitle,
        string $message = null
    ): ?AnswerResultInterface {
        $answerVariant = $this->getAnswerVariantByTitle($questionTitle, $answerVariantTitle);
        if (!($answerVariant instanceof AnswerVariant)) {
            return null;
        }

        return $this->createAnswerResult($answerVariant, $message);
    }

    /**
     * @return VoteSchemaInterface
     */
    public function getVoteSchema(): VoteSchemaInterface
    {
        return $this->voteSchema;
    }

    /**
     * @param AnswerVariantInterface $answerVariant
     * @param string|null $message
     * @return AnswerResultInterface
     * @throws Exception
     */
    public function createAnswerResult(
        AnswerVariantInterface $answerVariant,
        string $message = null
    ): AnswerResultInterface {
        $question = $answerVariant->getQuestion();
        $vote = $question instanceof QuestionInterface ? $question->getVote() : null;
        if (empty($vote) || $vote !== $this->voteSchema) {
            throw new Exception('Invalid answer variant');
        }

        $answerResult = AnswerResult::createNewResultAnswer($answerVariant, $message);
        $this->addAnswerResult($answerResult);

        return $answerResult;
    }

    /**
     * @param AnswerResultInterface $answerResult
     * @return QuestionInterface|null
     */
    private function getQuestionByAnswerResult(AnswerResultInterface $answerResult): ?QuestionInterface
    {
        $answerVariant = $answerResult->getAnswerVariant();
        return $answerVariant instanceof AnswerVariantInterface ? $answerVariant->getQuestion() : null;
    }

    /**
     * @param AnswerResultInterface $answerResult
     * @return void
     */
    public function addAnswerResult(AnswerResultInterface $answerResult)
    {
        $question = $this->getQuestionByAnswerResult($answerResult);
        if (empty($question)) {
            return;
        }

        if (!$question->isMultiple() && $this->hasAnswerResultByQuestion($question)) {
            foreach ($this->getAnswerResultByQuestion($question) as $currentAnswerResult) {
                $this->removeAnswerResult($currentAnswerResult);
            }
        }

        $this->answerResults->attach($answerResult, 'new');
    }

     /**
     * @param QuestionInterface $question
     * @return AnswerResultInterface[]|ReadableCollectionInterface
      *
      * @psalm-suppress MismatchingDocblockReturnType
     */
    public function getAnswerResultsByQuestion(QuestionInterface $question): ReadableCollectionInterface
    {
        $collection = new Collection();
        foreach ($this->getAnswerResults() as $answerResult) {
            /**
             * @var AnswerResultInterface $answerResult
             */
            $currentQuestion = $this->getQuestionByAnswerResult($answerResult);
            if ($currentQuestion === $question) {
                $collection->append($answerResult);
            }
        }

        return $collection;
    }

    /**
     * @param string $questionTitle
     * @return AnswerResultInterface[]|ReadableCollectionInterface
     *
     * @psalm-suppress MismatchingDocblockReturnType
     */
    public function getAnswerResultByQuestionTitle(string $questionTitle): ReadableCollectionInterface
    {
        $question = $this->voteSchema->getQuestionByTitle($questionTitle);
        if ($question instanceof QuestionInterface) {
            return $this->getAnswerResultByQuestion($question);
        }

        return new Collection();
    }

    /**
     * @param QuestionInterface $question
     * @return boolean
     *
     * @psalm-suppress PossiblyInvalidMethodCall
     */
    public function hasAnswerResultByQuestion(QuestionInterface $question): bool
    {
        return $this->getAnswerResultByQuestion($question)->count() > 0;
    }

    /**
     * @param string $questionTitle
     * @return boolean
     *
     * @psalm-suppress PossiblyInvalidMethodCall
     */
    public function hasAnswerResultByQuestionTitle(string $questionTitle): bool
    {
        return $this->getAnswerResultByQuestionTitle($questionTitle)->count() > 0;
    }

    /**
     * @param AnswerResultInterface $answerResult
     * @param bool $isMoved
     * @return void
     */
    public function removeAnswerResult(AnswerResultInterface $answerResult, bool $isMoved = false)
    {
        if ($this->answerResults->contains($answerResult)) {
            if (!$isMoved) {
                $this->answerResults[$answerResult] = 'remove';
                $answerResult->setVoteResult(null);
            } else {
                $this->answerResults->detach($answerResult);
            }
        }
    }

    /**
     * @param string|null $action
     * @return AnswerResultInterface[]|ReadableCollectionInterface
     *
     * @psalm-suppress MismatchingDocblockReturnType
     */
    public function getAnswerResults(string $action = null): ReadableCollectionInterface
    {
        $collection = new Collection();
        foreach ($this->answerResults as $answerResult) {
            /**
             * @var AnswerResultInterface $answerResult
             */
            $currentAction = $this->answerResults[$answerResult];
            if ($action === null && $currentAction !== 'delete') {
                $collection->append($answerResult);
            } elseif ($action === $currentAction) {
                $collection->append($answerResult);
            }
        }

        return $collection;
    }

    /**
     * @return integer
     *
     * @psalm-suppress PossiblyInvalidMethodCall
     */
    public function getAnswerResultsCount(): int
    {
        return $this->getAnswerResults()->count();
    }

    /**
     * @param QuestionInterface $question
     * @return ReadableCollectionInterface|AnswerResultInterface[]
     *
     * @psalm-suppress MismatchingDocblockReturnType
     */
    public function getAnswerResultByQuestion(QuestionInterface $question): ReadableCollectionInterface
    {
        $collection = new Collection();
        foreach ($this->getAnswerResults() as $answerResult) {
            /**
             * @var AnswerResultInterface $answerResult
             */
            $answerVariant = $answerResult->getAnswerVariant();
            $currentQuestion = $answerVariant instanceof AnswerVariantInterface ? $answerVariant->getQuestion() : null;

            if ($currentQuestion === $question) {
                $collection->append($question);
            }
        }

        return $collection;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        /**
         * @psalm-suppress PossiblyInvalidMethodCall
         */
        $result = [
            'props' => $this->getProps(),
            'answer' => $this->getAnswerResults()->jsonSerialize()
        ];

        return $result;
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
        return isset($this->props[$key]);
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getValueByKey(string $key)
    {
        return $this->props[$key] ?? null;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
