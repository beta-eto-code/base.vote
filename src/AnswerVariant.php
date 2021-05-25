<?php

declare(strict_types=1);

namespace Base\Vote;

use Base\Vote\Interfaces\AnswerVariantInterface;
use Base\Vote\Interfaces\AnswerVariantType;
use Base\Vote\Interfaces\QuestionInterface;
use Base\Vote\Traits\PropertyAccessor;

class AnswerVariant implements AnswerVariantInterface
{
    use PropertyAccessor;

    /**
     * @var string
     */
    private $title;
    /**
     * @var int
     */
    private $type;
    /**
     * @var QuestionInterface|null
     */
    private $question;

    public function __construct(array $data)
    {
        $this->title = (string)$data['title'];
        $this->type = (int)($data['type'] ?? AnswerVariantType::RADIO);
        $this->initProps((array)($data['props'] ?? []));
        $this->question = null;
    }

    /**
     * Текст варианта ответа
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
     * Тип поля (radio, checkbox, dropdown, multiselect, text, textarea)
     * @return integer
     */
    public function getType(): int
    {
        return $this->type;
    }
    
    /**
     * @param integer $answerVariantType
     * @return void
     */
    public function setType(int $answerVariantType)
    {
        $this->type = $answerVariantType;
    }

    /**
     * @return QuestionInterface|null
     */
    public function getQuestion(): ?QuestionInterface
    {
        return $this->question;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'type' => $this->type,
            'props' => $this->getProps(),
        ];
    }

    /**
     * @param QuestionInterface|null $question
     * @return void
     */
    public function setQuestion(?QuestionInterface $question)
    {
        $this->question = $question;
    }

    public function __clone()
    {
        $this->props = [];
        $this->question = null;
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
        if (in_array($key, ['title', 'type'])) {
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
        switch($key) {
            case 'title': 
                return $this->getTitle();
            case 'type': 
                return $this->getType();
            default:
                return $this->props[$key] ?? null;
        }
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
