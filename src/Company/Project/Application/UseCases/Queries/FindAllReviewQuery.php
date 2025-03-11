<?php

namespace Src\Company\Project\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Project\Domain\Repositories\ReviewRepositoryInterface;

class FindAllReviewQuery implements QueryInterface
{
    private ReviewRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = app()->make(ReviewRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getReviews();
    }
}