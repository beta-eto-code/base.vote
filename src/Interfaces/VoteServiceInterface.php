<?php

declare(strict_types=1);

namespace Base\Vote\Interfaces;

use Bitrix\Main\Result;
use Bx\Model\Interfaces\CollectionInterface;

interface VoteServiceInterface
{
    /**
     * @param integer $id
     * @return VoteSchemaInterface|null
     */
    public function getVoteSchemaById(int $id): ?VoteSchemaInterface;
    /**
     * @param array $criteria
     * @param integer|null $limit
     * @param integer|null $offset
     * @return VoteSchemaInterface[]|CollectionInterface
     */
    public function getVoteSchemasByCriteria(array $criteria, int $limit = null, int $offset = null): CollectionInterface;
    /**
     * @param VoteSchemaInterface $voteSchema
     * @return Result
     */
    public function saveVote(VoteSchemaInterface $voteSchema): Result;
    /**
     * @param array $criteria
     * @return integer
     */
    public function getVoteSchemaCount(array $criteria): int;
}
