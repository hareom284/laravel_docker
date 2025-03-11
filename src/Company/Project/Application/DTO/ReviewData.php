<?php

namespace Src\Company\Project\Application\DTO;

class ReviewData
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $title,
        public readonly string $comments,
        public readonly int $stars,
        public readonly string $date,
        public readonly int $project_id,
        public readonly int $review_by,
        public readonly int $salesperson_id,
    )
    {}
}