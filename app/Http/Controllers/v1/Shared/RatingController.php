<?php

namespace App\Http\Controllers\v1\Shared;

use App\Http\Controllers\Controller;
use App\Http\Traits\HttpResponse;
use App\Models\Hotel;
use App\Models\Rating;
use App\Models\RatingReply;
use App\Models\TopReviews;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Validator;

class RatingController extends Controller
{
    use HttpResponse;

    public function toggleTopReview(Request $request)
    {
        try {
            $validated = $request->validate([
                'review_id' => 'required|exists:ratings,id',
            ]);

            $topReview = TopReviews::where('review_id', $validated['review_id'])->first();

            if ($topReview) {
                if ($topReview->delete()) {
                    return $this->success(
                        message: 'Top review removed successfully!'
                    );
                } else {
                    return $this->error(
                        message: 'Failed to remove top review'
                    );
                }
            } else {
                //checking if there is already 5 top reviews
                $topReviews = TopReviews::count();
                if ($topReviews >= 5) {
                    return $this->error(
                        message: 'You can only add 5 top reviews'
                    );
                }
                //adding review to top reviews
                $topReview = new TopReviews();
                $topReview->review_id = $validated['review_id'];

                if ($topReview->save()) {
                    return $this->success(
                        message: 'Top review added successfully!'
                    );
                } else {
                    return $this->error(
                        message: 'Failed to add top review'
                    );
                }
            }
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function getTopReviews()
    {
        try {
            $topReviews = TopReviews::all();
            foreach ($topReviews as $topReview) {
                $review = Rating::where('id', $topReview->review_id)->first();
                $topReview->review = $review;
                $property = Hotel::where('id', $review->hotel_id)->first();
                $topReview->property_name = $property->name;

                $user = User::where('id', $review->user_id)->first();
                $topReview->user_name = $user->name;
                $topReview->user_image = $user->image;

                if ($user->image) {
                    //checking image is not url for google logined
                    if (!filter_var($user->image, FILTER_VALIDATE_URL)) {
                        $topReview->user_image = asset('storage/user/image/' . $user->image);
                    }
                }
            }
            return $this->success(
                data: $topReviews
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function addRatingReply(Request $request)
    {
        try {
            $validated = $request->validate([
                'rating_id' => 'required|exists:ratings,id',
                'reply' => 'required|string|min:5'
            ]);

            $ratingReply = new RatingReply();
            $ratingReply->rating_id = $validated['rating_id'];
            $ratingReply->user_id = auth()->user()->id;
            $ratingReply->reply = $validated['reply'];

            if ($ratingReply->save()) {
                return $this->success(
                    message: 'Reply added successfully!'
                );
            } else {
                return $this->error(
                    message: 'Failed to add reply'
                );
            }
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function deleteRatingReply(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:rating_reply,id'
            ]);
            $ratingReply = RatingReply::where('id', $validated['id'])->first();
            if ($ratingReply->delete()) {
                return $this->success(
                    message: 'Reply deleted successfully!'
                );
            } else {
                return $this->error(
                    message: 'Failed to delete reply'
                );
            }
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function paginate($items, $perPage = 3, )
    {
        // Get current page form url e.x. &page=1
        $page = LengthAwarePaginator::resolveCurrentPage() ?: 1;
        // Get total
        $total = count($items);
        $currentpage = $page;
        // Slice the collection to get the products to display in current page
        $offset = ($currentpage * $perPage) - $perPage;
        $itemstoshow = array_slice($items, $offset, $perPage);
        // Create our paginator
        $paginatedItems = new LengthAwarePaginator($itemstoshow, $total, $perPage);
        // Set Url
        $paginatedItems->setPath(request()->url());
        return $paginatedItems;
    }

    public function getTypeReviews($type)
    {
        try {
            $reviews = null;
            $search = request()->query('search') ?? '';

            if ($type == "positive") {
                $reviews = Rating::where('rating', '>=', 4)
                    ->join('hotels', 'ratings.hotel_id', '=', 'hotels.id')
                    ->select('ratings.*', 'hotels.name')
                    ->where('hotels.name', 'LIKE', "%{$search}%")
                    ->with('ratingReply')
                    ->orderBy('ratings.created_at', 'desc')
                    ->paginate();
            } else if ($type == "neutral") {
                $reviews = Rating::where('rating', '>=', 2)
                    ->join('hotels', 'ratings.hotel_id', '=', 'hotels.id')
                    ->select('ratings.*', 'hotels.name')
                    ->where('hotels.name', 'LIKE', "%{$search}%")
                    ->where('rating', '<', 4)
                    ->with('ratingReply')
                    ->orderBy('created_at', 'desc')
                    ->paginate();
            } else if ($type == "negative") {
                $reviews = Rating::where('rating', '<', 2)
                    ->join('hotels', 'ratings.hotel_id', '=', 'hotels.id')
                    ->select('ratings.*', 'hotels.name')
                    ->where('hotels.name', 'LIKE', "%{$search}%")
                    ->orderBy('created_at', 'desc')
                    ->with('ratingReply')
                    ->paginate();
            } else if ($type == "topreviews") {
                $topReviews = TopReviews::with('ratings')->get();

                foreach ($topReviews as $topReview) {
                    $reviews[] = $topReview->ratings;
                }
                //paginate reviews
                $reviews = $this->paginate($reviews, 10);
            } else {
                return $this->error(
                    message: 'Invalid type'
                );
            }

            foreach ($reviews as $review) {
                $user = User::where('id', $review->user_id)->first();
                $review->user_name = $user->name;
                $review->user_image = $user->image;

                if ($user->image) {
                    //checking image is not url for google logined
                    if (!filter_var($user->image, FILTER_VALIDATE_URL)) {
                        $review->user_image = asset('storage/user/image/' . $user->image);
                    }
                }

                //checking if review is top review
                $topReview = TopReviews::where('review_id', $review->id)->first();
                if ($topReview) {
                    $review->is_top_review = true;
                } else {
                    $review->is_top_review = false;
                }

                //user image of reply
                foreach ($review->ratingReply as $reply) {
                    $user = User::where('id', $reply->user_id)->first();
                    $reply->user_name = $user->name;
                    $reply->user_image = $user->image;
                    $reply->role = $user->role;

                    if ($user->image) {
                        //checking image is not url for google logined
                        if (!filter_var($user->image, FILTER_VALIDATE_URL)) {
                            $reply->user_image = asset('storage/user/image/' . $user->image);
                        }
                    }
                }

                $property = Hotel::where('id', $review->hotel_id)->first();
                $review->property_name = $property->name;
                $review->property_slug = $property->slug;
                if ($property->banner_image) {
                    $review->property_image = asset('storage/hotels/banner_image/' . $property->banner_image);
                }
            }
            return $this->success(
                data: $reviews
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function blockReview(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:ratings,id',
            ]);

            $review = Rating::where('id', $validated['id'])->first();

            $review->status = !$review->status;

            if ($review->update()) {
                return $this->success(
                    message: 'Review status updated successfully!'
                );
            } else {
                return $this->error(
                    message: 'Failed to update review status'
                );
            }
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function getAllRatings()
    {
        try {
            $search = request()->query('search');
            $properties = Hotel::
                where('name', 'LIKE', "%{$search}%")
                ->pluck('id');

            $reviews = Rating::whereIn('hotel_id', $properties)
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            foreach ($reviews as $review) {
                $property = Hotel::where('id', $review->hotel_id)->first();
                $review->property_name = $property->name;

                //checking if it's top review
                $topReview = TopReviews::where('review_id', $review->id)->first();
                if ($topReview) {
                    $review->is_top_review = true;
                } else {
                    $review->is_top_review = false;
                }

                $vendor = Vendor::where('id', $property->vendor_id)->first();
                $review->vendor_name = $vendor->name;

                $user = User::where('id', $review->user_id)->first();
                $review->user_name = $user->name;
                $review->user_image = $user->image;
                if ($user->image) {
                    //checking image is not url for google logined
                    if (!filter_var($user->image, FILTER_VALIDATE_URL)) {
                        $review->user_image = asset('storage/user/image/' . $user->image);
                    }
                }
            }
            return $this->success(
                data: $reviews
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function getAuthPropertyRatings()
    {
        try {
            $properties = Hotel::where(
                'vendor_id',
                Vendor::where('user_id', auth()->user()->id)
                    ->first()
                    ->id
            )->get()
                ->pluck('id');

            $reviews = Rating::whereIn('hotel_id', $properties)
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            foreach ($reviews as $review) {
                $property = Hotel::where('id', $review->hotel_id)->first();
                $review->property_name = $property->name;

                $user = User::where('id', $review->user_id)->first();
                $review->user_name = $user->name;
                $review->user_image = $user->image;
                if ($user->image) {
                    //checking image is not url for google logined
                    if (!filter_var($user->image, FILTER_VALIDATE_URL)) {
                        $review->user_image = asset('storage/user/image/' . $user->image);
                    }
                }
            }
            return $this->success(
                data: $reviews
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function getPropertyRatings($hotel_id)
    {
        try {
            $ratings = Rating::where('hotel_id', $hotel_id)
                ->where('status', 1)
                ->limit(5)
                ->get();

            foreach ($ratings as $rating) {
                $user = User::where('id', $rating->user_id)->first();
                $rating->user_name = $user->name;
                $rating->user_image = $user->image;
                if ($user->image) {
                    //checking image is not url for google logined
                    if (!filter_var($user->image, FILTER_VALIDATE_URL)) {
                        $rating->user_image = asset('storage/user/image/' . $user->image);
                    }
                }
            }
            return $this->success(
                data: $ratings
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }
    public function getPropertyAllRatings($hotel_id)
    {
        try {
            $ratings = Rating::where('hotel_id', $hotel_id)
                ->where('status', 1)
                ->get();

            foreach ($ratings as $rating) {
                $user = User::where('id', $rating->user_id)->first();
                $rating->user_name = $user->name;
                $rating->user_image = $user->image;
                if ($user->image) {
                    //checking image is not url for google logined
                    if (!filter_var($user->image, FILTER_VALIDATE_URL)) {
                        $rating->user_image = asset('storage/user/image/' . $user->image);
                    }
                }
            }
            return $this->success(
                data: $ratings
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function addPropertyRating(Request $request)
    {
        try {
            $validated = $request->validate([
                'hotel_id' => 'required|exists:hotels,id',
                'rating' => 'required|numeric|min:1|max:5',
                'review' => 'required|string|min:5'
            ]);

            //user can only submit 3 review to a hotel
            $userRatings = Rating::where('hotel_id', $validated['hotel_id'])
                ->where('user_id', auth()->user()->id)
                ->count();

            if ($userRatings >= 3) {
                return $this->error(
                    message: "You can only submit 3 reviews to a Property!"
                );
            }

            $rating = new Rating();
            $rating->hotel_id = $validated['hotel_id'];
            $rating->user_id = auth()->user()->id;
            $rating->rating = $validated['rating'];
            $rating->review = $validated['review'];

            if ($rating->save()) {
                return $this->success(
                    message: "Rating added successfully"
                );
            } else {
                return $this->error(
                    message: "Failed to add rating"
                );
            }
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }

    public function getUserPropertyReviews($hotel_id)
    {
        try {
            $reviews = Rating::where('hotel_id', $hotel_id)
                ->where('user_id', auth()->user()->id)
                ->get();
            if ($reviews->count() > 0) {
                return $this->success(
                    data: $reviews
                );
            }
            return $this->notFound(
                message: 'No reviews found'
            );
        } catch (\Throwable $e) {
            return $this->internalError($e->getMessage());
        }
    }
}
