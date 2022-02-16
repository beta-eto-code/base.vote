<?php

declare(strict_types=1);

namespace Base\Vote;

use Base\Vote\Interfaces\QuestionInterface;
use Base\Vote\Interfaces\QuestionType;
use Base\Vote\Interfaces\VoteSchemaInterface;
use Bx\Model\Collection;
use Bx\Model\Interfaces\ReadableCollectionInterface;
use Bx\Model\Models\File;
use SplObjectStorage;

class VoteSchema implements VoteSchemaInterface
{
    /**
     * @var string
     */
    private $title;
    /**
     * @var string
     */
    private $description;
    /**
     * @var SplObjectStorage
     */
    private $questions;
    /**
     * @var array
     */
    protected $props;

    public function __construct(array $data)
    {
        $this->title = (string)$data['title'];
        $this->description = (string)$data['description'];
        $this->props = (array)($data['props'] ?? []);
        $this->questions = new SplObjectStorage();

        $questionDataList = (array)($data['questions'] ?? []);
        foreach ($questionDataList as $questionData) {
            if (is_array($questionData)) {
                $question = new Question($questionData);
                $this->addQuestion($question);
            }
        }
    }

    /**
     * @param string $title
     * @param string|null $description
     * @return VoteSchemaInterface
     */
    public static function createNewVote(string $title, ?string $description = null): VoteSchemaInterface
    {
        return new VoteSchema([
            'title' => $title,
            'description' => $description ?? '',
        ]);
    }

    /**
     * Название опроса
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
     * Изображение для опроса
     * @return File|null
     */
    public function getImage(): ?File
    {
        return $this->props['image_file'] instanceof File ? $this->props['image_file'] : null;
    }

    /**
     * Описание опроса
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return void
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getProp(string $key)
    {
        return $this->props[$key] ?? null;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setProp(string $key, $value)
    {
        $this->props[$key] = $value;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        /**
         * @psalm-suppress PossiblyInvalidMethodCall
         */
        return [
            'title' => $this->title,
            'description' => $this->description,
            'props' => $this->props,
            'questions' => $this->getQuestions()->jsonSerialize(),
        ];
    }

    /**
     * @param string $title
     * @return QuestionInterface|null
     */
    public function getQuestionByTitle(string $title): ?QuestionInterface
    {
        foreach ($this->questions as $question) {
            /**
             * @var QuestionInterface $question
             */
            if ($question->getTitle() === $title) {
                return $question;
            }
        }

        return null;
    }

    /**
     * @param QuestionInterface $questionInterface
     * @return void
     */
    public function addQuestion(QuestionInterface $questionInterface)
    {
        $action = 'new';
        $currentVote = $questionInterface->getVote();
        if ($currentVote instanceof VoteSchema && $currentVote !== $this) {
            $currentVote->removeQuestion($questionInterface, true);
            $action = 'move';
        }

        $questionInterface->setVote($this);
        $this->questions->attach($questionInterface, $action);
    }

    /**
     * @param string $title
     * @param integer $type
     * @return QuestionInterface
     */
    public function createQuestion(string $title, int $type = null): QuestionInterface
    {
        $question = new Question([
            'title' => $title,
            'type' => $type ?? QuestionType::RADIO,
        ]);
        $this->addQuestion($question);

        return $question;
    }

    /**
     * @param QuestionInterface $question
     * @param boolean $isMoved
     * @return void
     */
    public function removeQuestion(QuestionInterface $question, bool $isMoved = false)
    {
        if ($this->questions->contains($question)) {
            if (!$isMoved) {
                $this->questions[$question] = 'delete';
                $question->setVote(null);
            } else {
                $this->questions->detach($question);
            }
        }
    }

    /**
     * @param string $action
     * @return ReadableCollectionInterface|QuestionInterface[]
     * @psalm-suppress MismatchingDocblockReturnType
     */
    public function getQuestions(string $action = null): ReadableCollectionInterface
    {
        $collection = new Collection();
        foreach ($this->questions as $question) {
            /**
             * @var QuestionInterface $question
             */
            $currentAction = $this->questions[$question];
            if ($action === null && $currentAction !== 'delete') {
                $collection->append($question);
            } elseif ($action === $currentAction) {
                $collection->append($question);
            }
        }

        return $collection;
    }
    /**
     * @return integer
     */
    public function getQuestionCount(): int
    {
        $count = 0;
        foreach ($this->questions as $question) {
            $count++;
        }

        return $count;
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
            case 'description':
                return $this->getDescription();
            default:
                return $this->props[$key] ?? null;
        }
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
