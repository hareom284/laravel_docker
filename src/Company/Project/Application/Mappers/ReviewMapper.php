<?php

namespace Src\Company\Project\Application\Mappers;

use Illuminate\Http\Request;
use Src\Company\Project\Domain\Model\Entities\Review;
use Src\Company\Project\Infrastructure\EloquentModels\ReviewEloquentModel;
use Src\Company\Project\Application\DTO\ReviewData;

class ReviewMapper {
    
    public static function fromRequest(Request $request, ?int $review_id = null): Review
    {
        $review_by = auth('sanctum')->user()->id;

        return new Review(
            id: $review_id,
            title: $request->string('title'),
            comments: $request->string('comments'),
            stars: $request->integer('stars'),
            date: $request->string('date'),
            project_id: $request->integer('project_id'),
            review_by: $review_by,
            salesperson_id: $request->integer('salesperson_id')
        );
    }

    public static function fromEloquent(ReviewEloquentModel $reviewEloquent): ReviewData
    {
        return new ReviewData(
            id: $reviewEloquent->id,
            title: $reviewEloquent->title,
            comments: $reviewEloquent->comments,
            stars: $reviewEloquent->stars,
            date: $reviewEloquent->date,
            project_id: $reviewEloquent->project_id,
            review_by: $reviewEloquent->review_by,
            salesperson_id: $reviewEloquent->salesperson_id
        );
    }

    public static function toEloquent(Review $review): ReviewEloquentModel
    {
        $reviewEloquent = new ReviewEloquentModel();
        if($review->id)
        {
            $reviewEloquent = ReviewEloquentModel::query()->findOrFail($review->id);
        }
        $reviewEloquent->title = $review->title;
        $reviewEloquent->comments = $review->comments;
        $reviewEloquent->stars = $review->stars;
        $reviewEloquent->date = $review->date;
        $reviewEloquent->project_id = $review->project_id;
        $reviewEloquent->review_by = $review->review_by;
        $reviewEloquent->salesperson_id = $review->salesperson_id;

        return $reviewEloquent;
    }

}