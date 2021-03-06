<?php

declare(strict_types=1);

namespace Base\Vote\Interfaces;

use Bitrix\Main\Result;
use Bx\Model\Interfaces\CollectionInterface;

interface VoteResultServiceInterface
{
    /**
     * @param VoteResultInterface $voteResultInterface
     * @return Result
     *
     * @psalm-suppress UndefinedClass
     */
    public function saveVoteResult(VoteResultInterface $voteResult): Result;

    /**
     * @param VoteSchemaInterface $voteSchema
     * @param array $params
     * @return VoteResultInterface[]|CollectionInterface
     *
     * @psalm-suppress MismatchingDocblockReturnType
     */
    public function getVoteResultList(VoteSchemaInterface $voteSchema, array $params = []): CollectionInterface;
    /**
     * @param VoteSchemaInterface $voteSchemaInterface
     * @param integer $userId
     * @return VoteResultInterface|null
     */
    public function getVoteResultByUser(VoteSchemaInterface $voteSchema, int $userId): ?VoteResultInterface;
}
