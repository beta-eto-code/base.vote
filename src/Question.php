<?php

declare(strict_types=1);

namespace Base\Vote;

use Base\Vote\Interfaces\AnswerVariantInterface;
use Base\Vote\Interfaces\QuestionInterface;
use Base\Vote\Interfaces\QuestionType;
use Base\Vote\Interfaces\VoteSchemaInterface;
use Base\Vote\Traits\PropertyAccessor;
use Bx\Model\Collection;
use Bx\Model\Interfaces\ReadableCollectionInterface;
use SplObjectStorage;

class Question implements QuestionInterface
{
    use PropertyAccessor;

    /**
     * @var string
     */
    private $title;
    /**
     * @var bool
     */
    private $isRequired;
    /**
     * @var int
     */
    private $type;
    /**
     * @var VoteSchemaInterface|null
     */
    private $vote;
    /**
     * @var SplObjectStorage
     */
    private $answers;
    /**
     * @var bool
     */
    private $isMultiple;

    public function __construct(array $data)
    {
        $this->title = (string)$data['title'];
        $this->isRequired = (bool)$data['is_required'];
        $this->isMultiple = (bool)$data['is_multiple'];
        $this->initProps((array)($data['props'] ?? []));
        $this->type = (int)($data['type'] ?? QuestionType::RADIO);
        $this->vote = null;
        $this->answers = new SplObjectStorage();

        $answersDataList = (array)($data['answers'] ?? []);
        foreach ($answersDataList as $answerData) {
            if (is_array($answerData)) {
                $answer = new AnswerVariant($answerData);
                $this->addAnswerVariant($answer);
            }
        }
    }

    /**
     * Текст вопроса
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return void
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * Является ли вопрос обязательным для ответа
     * @return boolean
     */
    public function isRequired(): bool
    {
        return $this->isRequired;
    }

    /**
     * @param boolean $state
     * @return void
     */
    public function setRequired(bool $state)
    {
        $this->isRequired = $state;
    }

    /**
     * Тип вопроса (radio, checkbox, dropdown, multiselect, режим совместимости)
     * @return integer
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param integer $questionType
     * @return void
     */
    public function setType(int $questionType)
    {
        $this->type = $questionType;
    }

    /**
     * @param string $title
     * @return AnswerVariantInterface|null
     */
    public function getAnswerVariantByTitle(string $title): ?AnswerVariantInterface
    {
        foreach ($this->answers as $answer) {
            /**
             * @var AnswerVariantInterface $answer
             */
            if ($answer->getTitle() === $title) {
                return $answer;
            }
        }

        return null;
    }

    /**
     * Добавляем новый вариант ответа
     * @param AnswerVariantInterface $answerVariant
     * @return void
     */
    public function addAnswerVariant(AnswerVariantInterface $answerVariant)
    {
        $action = 'new';
        $currentQuestion = $answerVariant->getQuestion();
        if ($currentQuestion instanceof Question && $currentQuestion !== $this) {
            $currentQuestion->removeAnswerVariant($answerVariant, true);
            $action = 'move';
        }

        $this->answers->attach($answerVariant, $action);
        $answerVariant->setQuestion($this);
        $this->normalizeTypes($answerVariant);
    }

    /**
     * Создаем новый вариант ответа
     * @param string $title
     * @param integer $type
     * @return AnswerVariantInterface
     */
    public function createAnswerVariant(string $title, int $type = null): AnswerVariantInterface
    {
        if ($type === null && $this->type !== QuestionType::MIXED_TYPE) {
            $type = $this->type;
        }

        $answerVariant = new AnswerVariant([
            'type' => (int)$type,
            'title' => $title,
        ]);
        $this->addAnswerVariant($answerVariant);

        return $answerVariant;
    }

    /**
     * @param AnswerVariantInterface $answerVariant
     * @return void
     */
    private function normalizeTypes(AnswerVariantInterface $answerVariant)
    {
        if ($answerVariant->getType() !== $this->type && $this->type !== QuestionType::MIXED_TYPE) {
            $this->type = QuestionType::MIXED_TYPE;
        }
    }

    /**
     * Удаляем указанный вариант ответа из списка вопроса
     * @param AnswerVariantInterface $answerVariant
     * @param boolean $isMoved
     * @return void
     */
    public function removeAnswerVariant(AnswerVariantInterface $answerVariant, bool $isMoved = false)
    {
        if ($this->answers->contains($answerVariant)) {
            if (!$isMoved) {
                $this->answers[$answerVariant] = 'remove';
                $answerVariant->setQuestion(null);
            } else {
                $this->answers->detach($answerVariant);
            }
        }
    }

    /**
     * @return VoteSchemaInterface|null
     */
    public function getVote(): ?VoteSchemaInterface
    {
        return $this->vote;
    }

    /**
     * @param VoteSchemaInterface $voteSchemaInterface
     * @return void
     */
    public function setVote(?VoteSchemaInterface $voteSchema)
    {
        $this->vote = $voteSchema;
    }

    public function toArray(): array
    {
        $answerVariants = [];
        foreach ($this->getAnswerVariants() as $answer) {
            /**
             * @var AnswerVariantInterface $answer
             */
            $answerVariants[] = $answer->toArray();
        }

        return [
            'title' => $this->title,
            'is_required' => $this->isRequired,
            'type' => $this->type,
            'props' => $this->getProps(),
            'answers' => $answerVariants,
        ];
    }

    /**
     * @param string|null $action
     * @return AnswerVariantInterface[]|ReadableCollectionInterface
     *
     * @psalm-suppress MismatchingDocblockReturnType
     */
    public function getAnswerVariants(string $action = null): ReadableCollectionInterface
    {
        $collection = new Collection();
        foreach ($this->answers as $answer) {
            /**
             * @var AnswerVariantInterface $answer
             */
            $currentAction = $this->answers[$answer];
            if ($action === null && $currentAction !== 'delete') {
                $collection->append($answer);
            } elseif ($action === $currentAction) {
                $collection->append($answer);
            }
        }

        return $collection;
    }

    public function __clone()
    {
        $this->props = [];
        $this->vote = null;
    }

    /**
     * @return integer
     *
     * @psalm-suppress PossiblyInvalidMethodCall
     */
    public function getAnswerVariantCount(): int
    {
        return $this->getAnswerVariants()->count();
    }

    /**
     * @return boolean
     */
    public function isMultiple(): bool
    {
        return $this->isMultiple;
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
        if (in_array($key, ['title', 'type', 'is_required', 'is_multiple'])) {
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
        switch ($key) {
            case 'title':
                return $this->getTitle();
            case 'type':
                return $this->getType();
            case 'is_required':
                return $this->isRequired();
            case 'is_multiple':
                return $this->isMultiple();
            default:
                return $this->props[$key] ?? null;
        }

        return $this->props[$key] ?? null;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
