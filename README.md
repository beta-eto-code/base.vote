# Опросы/голосования

Данный пакет предоставляет функционал для описания функционала опросов/голосований, разрабатывался как обёртка модуля vote CMS Bitrix. Не является полной реализацией, отсуствует имплементация сервисов:

* VoteServiceInterface - предназначен для работы со схемой опроса (сохранение схемы, запрос схем(ы) по заданным критериям)
* VoteResultServiceInterface - предназначен для работы с результатами опроса (сохранение результата, запрос результатов по заданным критериям)

**Для работы с опросами задействованы следующие интерфейсы:**

1. VoteSchemaInterface - интерфейс схемы опроса, содержит: название и описание. Так же предоставляет возможность оперировать с произвольными свойствами через методы getProp(string $key) и setProp(string $key, $value). Предоставляет доступ с вопросам.
2. QuestionInterface - интерфейс вопроса. содержит: текст вопроса, признак необходимости обязательного ответа, тип вопроса (произвольное целое число, может варьироваться в зависимости от реализации). Предоставляет доступ к вариантам ответа, варианты ответа обязательны для любых вопросов, даже для тех которые требуют ответ в произвольной форме. Предоставляет возможность оперировать с произвольными свойствами через методы getProp(string $key) и setProp(string $key, $value).
3. AnswerVariantInterface - интерфейс варианта ответа, содержит: текст ответа и тип варианта ответа (произвольное целое число, может варьироваться в зависимости от реалзации). Предоставляет возможность оперировать с произвольными свойствами через методы getProp(string $key) и setProp(string $key, $value).

**Интерфейсы для работы с результатом опроса:**

1. VoteResultInterface - интерфейс результата опроса, предоставляет доступ к ответам на вопросы. Так же предоставляет возможность оперировать с произвольными свойствами через методы getProp(string $key) и setProp(string $key, $value). 
2. AnswerResultInterface - интерфейс ответа на вопрос, содержит ссылку на вариант ответа - AnswerVariantInterface и произвольный текст ответа. Предоставляет возможность оперировать с произвольными свойствами через методы getProp(string $key) и setProp(string $key, $value).

Схема опроса построена в виде дерева, каждый дочений элемент имеет доступ к родителю:

    VoteSchemaInterface <-> QuestionInterface <-> AnswerVariantInterface

Результат опроса:

    VoteResultInterface <-> AnswerResultInterface -> AnswerVariantInterface

## Пример создания опроса:

```php
use Base\Vote\Interfaces\VoteServiceInterface;
use Base\Vote\QuestionType;
use Base\Vote\AnswerVariantType;
use Base\Vote\VoteSchema;

/**
 * @var VoteServiceInterface $voteService
 **/
$voteService = new SomeImplementationVoteService();
$newVoteSchema = VoteSchema::createNewVote('Новый опрос', 'Некоторое описание для опроса');

$question1 = $newVoteSchema->createQuestion('Что вы думаете по поводу нового API для опросов?', QuestionType::RADIO);
$question1->createAnswerVariant('Ну такое...');
$question1->createAnswerVariant('Это что-то невероятное');
$question1->createAnswerVariant('Как это теперь развидеть?');
$question1->createAnswerVariant('Некоторое описание', AnswerVariantType::TEXT);

$question2 = $newVoteSchema->createQuestion('Ещё один неоднозначный вопрос...', QuestionType::CHECKBOX);
$question2->createAnswerVariant('Вариант 1');
$question2->createAnswerVariant('Вариант 2');
$question2->createAnswerVariant('Вариант 3');
$question2->createAnswerVariant('Вариант 4');

$result = $voteService->saveVote($newVoteSchema);
```

## Пример добавления результата опроса:

```php
use Base\Vote\Interfaces\VoteResultServiceInterface;
use Base\Vote\Interfaces\QuestionInterface;
use Base\Vote\VoteResult;

/**
 * @var VoteServiceInterface $voteService
 **/
$voteService = new SomeImplementationVoteService();
/**
 * @var VoteServiceInterface $voteService
 **/
$voteResultService = new SomeImplementationVoteResultService();

$voteSchema = $voteService->getVoteSchemaById(1);
$newVoteResult = new VoteResult($voteSchema);

// Добавление ответа
$newVoteResult->createAnswerResultByTitle(
    'Планируете ли вы дополнительно обучаться профильной специальности?', 
    'да'
);

// Добавление того же ответа другим методом
$question = $voteSchema->getQuestionByTitle('Планируете ли вы дополнительно обучаться профильной специальности?');
$answerVariant = $question instanseof QuestionInterface ? $question->getAnswerVariantByTitle('да') : null;
$newVoteResult->createAnswerResult($answerVariant);

$result = $voteResultService->saveVoteResult($newVoteResult);
```