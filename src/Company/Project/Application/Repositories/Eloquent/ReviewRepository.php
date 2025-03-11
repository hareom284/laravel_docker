<?php

namespace Src\Company\Project\Application\Repositories\Eloquent;

use Src\Company\Project\Application\DTO\ReviewData;
use Src\Company\Project\Application\Mappers\ReviewMapper;
use Src\Company\Project\Domain\Model\Entities\Review;
use Src\Company\Project\Domain\Repositories\ReviewRepositoryInterface;
use Src\Company\Project\Infrastructure\EloquentModels\ReviewEloquentModel;
use Src\Company\Project\Domain\Resources\ReviewResource;

class ReviewRepository implements ReviewRepositoryInterface
{

    public function getReviews()
    {
        $perPage = $filters['perPage'] ?? 10;

        $reviewEloquent = ReviewEloquentModel::with('project.property','saleperson','customer')->orderBy('id', 'desc')->paginate($perPage);

        $reviews = ReviewResource::collection($reviewEloquent);

        $links = [
            'first' => $reviews->url(1),
            'last' => $reviews->url($reviews->lastPage()),
            'prev' => $reviews->previousPageUrl(),
            'next' => $reviews->nextPageUrl(),
        ];
        $meta = [
            'current_page' => $reviews->currentPage(),
            'from' => $reviews->firstItem(),
            'last_page' => $reviews->lastPage(),
            'path' => $reviews->url($reviews->currentPage()),
            'per_page' => $perPage,
            'to' => $reviews->lastItem(),
            'total' => $reviews->total(),
        ];
        $responseData['data'] = $reviews;
        $responseData['links'] = $links;
        $responseData['meta'] = $meta;
        
        return $reviewEloquent;
    }

    public function store(Review $review): ReviewData
    {
        $reviewEloquent = ReviewMapper::toEloquent($review);

        $reviewEloquent->save();

        return ReviewMapper::fromEloquent($reviewEloquent);
    }

}