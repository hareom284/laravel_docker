<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Project\Domain\Model\Entities\Review;
use Src\Company\Project\Domain\Repositories\ReviewRepositoryInterface;

class StoreReviewCommand implements CommandInterface
{
    private ReviewRepositoryInterface $repository;

    public function __construct(
        private readonly Review $review
    )
    {
        $this->repository = app()->make(ReviewRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->store($this->review);
    }
}