<?php

namespace Src\Company\Project\Domain\Repositories;

use Src\Company\Project\Application\DTO\ReviewData;
use Src\Company\Project\Domain\Model\Entities\Review;

interface ReviewRepositoryInterface
{
    public function getReviews();

    public function store(Review $review): ReviewData;

}