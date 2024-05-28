<?php

use App\Http\Controllers\v1\admin\BlogController;
use App\Http\Controllers\v1\admin\PageController;
use App\Http\Controllers\v1\admin\UserController;
use App\Http\Controllers\v1\global\BookingController;
use App\Http\Controllers\v1\global\CouponController;
use App\Http\Controllers\v1\global\PlaceController;
use App\Http\Controllers\v1\global\SocialsController;
use App\Http\Controllers\v1\hotel\HotelController;
use App\Http\Controllers\v1\Shared\OfferController;
use App\Http\Controllers\v1\Shared\RatingController;
use App\Http\Controllers\v1\SharedAuthRoutes;
use App\Http\Controllers\v1\user\RoomController;
use App\Http\Controllers\v1\userAdmin\SupportController;
use App\Jobs\admin\SendBulkMails;
use App\Mail\admin\Custom;
use App\Mail\Email;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;


Route::get('send-email', function () {
    $details = [
        'title' => 'Mail from ItSolutionStuff.com',
        'body' => 'This is for testing email using smtp'
    ];

    $data = [
        'subject' => 'Test Subject',
        'message' => 'Test Message',
        'bcc' => ['bcc'],
        'cc' => ['cc']
    ];

    // Mail::to('vermamanav117@gmail.com')
    // ->send(new Custom($data));

    Mail::to('vermamanav117@gmail.com')
        ->send(
            new Custom(
                'Subject',
                'Message'
            )
        );

    // SendBulkMails::dispatch(['vermamanav117@gmail.com'], 'Test Subject', 'Test Message', 'bcc', 'cc');
});

//Offer Controller
Route::controller(OfferController::class)->group(function () {
    Route::get("getOffers", "getOffers");
});

Route::controller(BlogController::class)->group(function () {
    Route::get('getMostPopularBlog', 'getMostPopularBlog');
    Route::get('getBlog/{slug}', 'getBlogBySlug');
    Route::get('getPopularBlogs', 'getPopularBlogs');
    Route::get('getLatestBlogs', 'getLatestBlogs');
});

Route::controller(SupportController::class)->group(function () {
    Route::get('getOfficeLocations', 'getAllOfficeLocationForUsers');
    Route::get('getSupportHelplines', 'getAllSupportHelplines');
    Route::get('getSupportCategory', 'getAllSupportCategory');
    Route::post('getSupportCategoryQuestions', 'getSupportCategoryQuestions');
});

Route::controller(PageController::class)->group(function () {
    Route::get('getLogo', 'getLogo');
    Route::get('getAllAvailablePages', 'getAllAvailablePages');
    Route::get('getPage/{slug}', 'getPage');
});

Route::controller(SocialsController::class)->group(function () {
    Route::get('getSocials', 'getSocials');
});

Route::controller(RatingController::class)->group(function () {
    Route::get('getReviews/{type}', 'getTypeReviews');
    Route::get('getTopReviews', 'getTopReviews');
    Route::get('getPropertyRatings/{hotel_id}', 'getPropertyRatings');
    Route::get('getPropertyAllRatings/{hotel_id}', 'getPropertyAllRatings');
});

Route::controller(PlaceController::class)->group(function () {
    Route::get('getCityProperties/{city}', 'getCityProperties');
    Route::get('getAllCities', 'getAllCities');
    Route::get('getSomeCities', 'getSomeCities');
    Route::get('getAllStates', 'getAllStates');
    Route::get('states', 'getStates')->name('states');
    Route::get('stateImage/{filename}', 'getStateImage');
    Route::get('cities', 'getCities')->name('cities');
    Route::get('cityImage/{filename}', 'getCityImage');
    Route::get('getState/{id}', 'getState')->name('getState');
    Route::get('getCity/{id}', 'getCity')->name('getCity');
    Route::get('getCityByState/{id}', 'getCityByState')->name('getCityByState');
});

Route::controller(BookingController::class)->group(function () {
    Route::get('/getBookingTypes', 'getBookingTypes');
    Route::get('/getBookingTypes/{hotel_id}', 'getBookingTypesForHotel');
});

Route::controller(RoomController::class)->group(function () {
    Route::get('getRooms/{property_id}', 'getRooms');
    Route::get('getRoomTypeImage/{filename}', 'getRoomTypeImage')->name('getRoomTypeImage');
    Route::get('propertyType/getAvailablePropertiesCount/{slug}', 'propertyTypeAvailablePropertiesCount');
    Route::get('propertyType/getAvailableProperties/{slug}', 'propertyTypeAvailableProperties');
    Route::get('getRoomImage/{filename}', 'getRoomImage');
    Route::get('getRoomType/{id}', 'getRoomType');
    Route::get('getRoomTypes', 'getRoomTypes');
});

Route::controller(CouponController::class)->group(function () {
    Route::post('getCouponDiscountedPrice', 'getCouponDiscountedPrice');
});

Route::controller(HotelController::class)->group(function () {
    Route::get('getAmenities', 'getAmenities');
    Route::get('getPropertiesDataForSearch', 'getPropertiesDataForSearch');
    Route::get('getDataForSearch', 'getDataForSearch');
    Route::post('searchHotel', 'searchHotel');
    Route::get('getHotels', 'getHotels');
    Route::get('getHotel/{slug}', 'getHotel');
    Route::get('getCheckInOutTime/{hotel_slug}', 'getCheckInOutTimeForHotel');
    Route::get('getHotelGalleryImage/{filename}', 'getHotelGalleryImage');
    Route::get('hotelBannerImage/{filename}', 'getHotelBannerImage');
    Route::get('hotelGalleryImages/{hotel_id}', 'getHotelGalleryImages');
    Route::get('hotel/calculateTodayPrice/{hotel_id}/{room_type_id}/{guests}', 'calculateTodayRoomPrice');
    //    Route::post('hotel/calculatePrice', 'calculateRoomPrice');
    Route::post('hotel/calculateAllRoomsPrice', 'calculateAllRoomsPrice');
});

Route::controller(SharedAuthRoutes::class)->group(function () {
    Route::get('getPropertyTypes', 'getPropertyTypes')->name('getPropertyTypes');
    Route::get('getPropertyType/{id}', 'getPropertyType')->name('getPropertyType');
});
