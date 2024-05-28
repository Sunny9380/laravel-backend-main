<?php

use App\Http\Controllers\v1\admin\{
    ReportController,
    PageController,
    EmailController,
    ChargesController,
    AdminController,
    BlogController,
    PlaceController,
    UserController,
    VendorController
};
use App\Http\Controllers\v1\global\{
    PaymentController,
    SocialsController,
    SendMailController,
    CouponController
};
use App\Http\Controllers\v1\global\BookingController;
use App\Http\Controllers\v1\hotel\{
    HotelController,
    RoomController
};
use App\Http\Controllers\v1\Shared\{
    AdminVendor,
    ComplainController,
    OfferController,
    RatingController
};
use App\Http\Controllers\v1\userAdmin\SupportController;
use App\Http\Controllers\v1\SharedAuthRoutes;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:api')->group(function () {

    Route::controller(SharedAuthRoutes::class)->group(function () {
        Route::post('changePassword', 'changePassword');
        Route::get('userDetails', 'details');
        Route::post('updateDetails', 'updateDetails');
    });
    //Global Booking Routes
    Route::controller(BookingController::class)->group(function () {
        Route::get('getBookingDetails/{booking_id}', 'getBookingDetails');
    });
    //Receipt Routes
    Route::controller(\App\Http\Controllers\v1\Shared\ReceiptController::class)->group(function () {
        Route::post('generateReceipt', 'generateReceipt');
    });

    //Payment Routes
    Route::controller(PaymentController::class)->group(function () {
        Route::post('createPayment', 'createPayment')->name('payment');
        Route::post('verifyPayment', 'verifyPayment')->name('paymentCallback');
        Route::post('transferMoney', 'transferMoney');
    });
    Route::controller(BookingController::class)->group(function () {
        Route::post('createPayOnPropertyBooking', 'createPayOnPropertyBooking');
    });
    //Shared Routes
    Route::controller(AdminVendor::class)->group(function () {
        Route::post('getVendor', 'getVendor')->name('getVendor');
    });

    //Global Routes
    Route::post('isUserExists', [\App\Http\Controllers\v1\user\UserController::class, 'isUserExists'])->name('isUserExists');

    Route::controller(SupportController::class)->group(function () {
        Route::post('submitContactForm', 'submitContactForm');
    });

    //User Routes
    Route::prefix('user')->group(function () {
        Route::controller(ComplainController::class)->group(function () {
            Route::post('addComplain', 'addUserComplain');
        });
        Route::controller(\App\Http\Controllers\v1\user\HotelController::class)->group(function () {
            Route::post('requestProperty', 'requestProperty');
            Route::post('addRemoveHotelToWishList', 'addRemoveHotelToWishList');
            Route::post('getHotelWishlistStatus', 'getHotelWishlistStatus');
            Route::get('getWishlist', 'getWishlist');
            Route::get('verifyPropertyMail/{email}', 'verifyPropertyMail');
            Route::get('verify-business-email/{token}', 'verifyBusinessEmailToken');
            Route::get('isBusinessEmailVerified/{email}', 'isBusinessEmailVerified');
        });
        Route::controller(HotelController::class)->group(function () {
            Route::post('calculateAllRoomsPrice', 'calculateAllRoomsPrice');
        });
        Route::controller(\App\Http\Controllers\v1\user\RoomController::class)->group(function () {
            Route::post('getHotelDetailsForBooking', 'getHotelDetailsForBooking');
            Route::get('getBookedRooms', 'getBookedRooms');
            Route::get('getBookings', 'getBookings');
        });

        Route::controller(RatingController::class)->group(function () {
            Route::get('getUserPropertyReviews/{hotel_id}', 'getUserPropertyReviews');
            Route::post('addPropertyRating', 'addPropertyRating');
            Route::get('getRatings/{hotel_id}', 'getHotelRatings');
        });
    });

    //Vendor Routes
    Route::prefix('vendor')->group(function () {
        Route::group(['middleware' => 'isVendor'], function () {
            //Getting auth vendor Details
            Route::controller(VendorController::class)->group(function () {
                Route::get('showPolicies', 'showPolicies');
                Route::get('showAllPolicies', 'showAllPolicies');
                Route::post('addPolicy', 'addPolicy');
                Route::post('deletePolicy', 'deletePolicy');
                Route::get('isAccountDeactive', 'isAccountDeactive');
                Route::get('accountActivateDisable', 'accountActivateDisable');
                Route::get('authAccountDetails', 'authAccountDetails');
                Route::get('getAuthVendor', 'getAuthVendor');
                Route::post('addBankDetails', 'addBankDetails');
            });
            //Controller for managing bookings
            Route::controller(\App\Http\Controllers\v1\hotel\BookingController::class)->group(function () {
                Route::get('getBookings', 'getBookings');
            });
            //Controller for Review
            Route::controller(RatingController::class)->group(function () {
                Route::get('getAuthPropertyRatings', 'getAuthPropertyRatings');
            });
            //Controller for managing hotels
            Route::controller(HotelController::class)->group(function () {
                Route::get('getAllHotels', 'getAllHotels');
                Route::post('addHotel', 'addPropertyByVendor');
                Route::get('getHotels', 'getVendorHotels');
                Route::post('setCheckInOutTime', 'setCheckInOutTime');
                Route::post('getCheckInOutTime', 'getCheckInOutTime');
            });
            //Controller for managing rooms
            Route::controller(RoomController::class)->group(function () {
                Route::post('addRoom', 'addRoomByVendor');
                Route::get('getHourlyRoomRates/{hotel_slug}', 'getHourlyRoomRates');
                Route::post('updateRoom', 'updateRoom');
                Route::get('getRooms/{hotel_slug}', 'getRooms');
                Route::get('getRoom/{room_slug}', 'getRoom');
                Route::get('getRoomRates/{room_id}', 'getRoomRates');
                Route::get('getRoomAvailability/{room_id}', 'getRoomAvailability');
                Route::post('addRoomAvailability', 'addRoomAvailability');
                Route::post('addRoomRates', 'addRoomRates');
                Route::post('addHourlyRoomRates', 'addHourlyRoomRates');
                Route::post('changeHotelStatus', 'changeHotelStatus');
            });
            Route::controller(CouponController::class)->group(function () {
                Route::get("getVendorCoupons", "getVendorCoupons");
                Route::post("editPropertyCoupon", "editPropertyCoupon");
                Route::post("addPropertyCoupon", "addPropertyCoupon");
                Route::post("togglePropertyCouponStatus", "togglePropertyCouponStatus");
                Route::post("deletePropertyCoupon", "deletePropertyCoupon");
            });
        });
    });

    //Admin Routes
    Route::prefix('admin')->group(function () {
        Route::group(['middleware' => 'isAdmin'], function () {
            Route::controller(ComplainController::class)->group(function () {
                Route::get('getAllComplains', 'getAllComplains');
            });
            Route::controller(ReportController::class)->group(function () {
                Route::post('getComplainReport', 'getComplainReport');
                Route::post('getCouponReport', 'getCouponReport');
                Route::post('getPropertyOccupancyReport', 'getPropertyOccupancyReport');
                Route::post('getBookingCancellationReport', 'getBookingCancellationReport');
                Route::post('getBookingReport', 'getBookingReport');
            });
            Route::controller(EmailController::class)->group(function () {
                Route::get('getEmailTemplate/{type}', 'getEmailTemplate');
                Route::get('getEmailTemplates', 'getEmailTemplates');
                Route::post('sendMail', 'sendMail');
                Route::post('send-bulk-emails', 'sendBulkEmail');
                Route::post('toggleEmailStatus', 'toggleEmailStatus');
                Route::post('updateMailTemplate', 'updateMailTemplate');
            });
            Route::controller(HotelController::class)->group(function () {
                Route::post('addProperty', 'addPropertyByAdmin');
            });
            Route::controller(SocialsController::class)->group(function () {
                Route::get('getSocials', 'getSocials');
                Route::post('updateSocials', 'updateSocials');
            });
            Route::controller(PageController::class)->group(function () {
                Route::post('addLogo', 'addLogo');
                Route::get('getPage/{slug}', 'getPageForAdmin');
                Route::get('getAllPages', 'getAllPages');
                Route::post('addPage', 'addPage');
                Route::post('togglePageStatus', 'togglePageStatus');
                Route::post('updatePage', 'updatePage');
                Route::post('deletePage', 'deletePage');
            });
            Route::controller(SupportController::class)->group(function () {
                Route::get('getSupportTickets', 'getSupportTickets');
                Route::get('getSupportQueries', 'getSupportQueries');
                Route::post('supportTicketResolved', 'supportTicketResolved');
                Route::post('updateSupportQuery', 'updateSupportQuery');
                Route::post('deleteSupportQuery', 'deleteSupportQuery');
                Route::post('addSupportQuery', 'addSupportQuery');
                Route::post('editSupportCategory', 'editSupportCategory');
                Route::post('deleteSupportCategory', 'deleteSupportCategory');
                Route::get('getSupportCategory', 'getSupportCategory');
                Route::post('addSupportCategory', 'addSupportCategory');
                Route::post('deleteHelpline', 'deleteHelpline');
                Route::post('editSupportHelpline', 'editSupportHelpline');
                Route::post('deleteOfficeLocation', 'deleteOfficeLocation');
                Route::post('updateOfficeLocation', 'updateOfficeLocation');
                Route::post('addSupportHelpline', 'addSupportHelpline');
                Route::get('getSupportHelplines', 'getSupportHelplines');
                Route::post('addOfficeLocation', 'addOfficeLocation');
                Route::get('getOfficeLocations', 'getOfficeLocations');
            });
            Route::controller(AdminController::class)->group(function () {
                Route::get('getAllStatics', 'getAllStatics');
                Route::get('getPolicies', 'getPolicies');
                Route::post('addPolicyHeading', 'addPolicyHeading');
                Route::post('deletePolicyHeading', 'deletePolicyHeading');
                Route::post('addPolicyItem', 'addPolicyItem');
                Route::post('deletePolicyItem', 'deletePolicyItem');
                Route::get("togglePolicyStatus/{id}", "togglePolicyStatus");
            });
            //Controller for managing bookings
            Route::controller(\App\Http\Controllers\v1\hotel\BookingController::class)->group(function () {
                Route::get('getAllPropertiesBookings', 'getAllPropertiesBookings');
            });
            //Route for rating
            Route::controller(RatingController::class)->group(function () {
                Route::post('addRatingReply', 'addRatingReply');
                Route::post('deleteRatingReply', 'deleteRatingReply');
                Route::post('toggleTopReview', 'toggleTopReview');
                Route::post('blockReview', 'blockReview');
                Route::get('getAllRatings', 'getAllRatings');
            });
            //Route for Tax
            Route::controller(ChargesController::class)->group(function () {
                Route::get('getCompanyCharges', 'getCompanyCharges');
                Route::get('getTaxCharges', 'getTaxCharges');
                Route::get('getRazorpayConfig', 'getRazorpayConfig');
                Route::post('setRazpayConfig', 'setRazpayConfig');
                Route::post('deleteTaxCharge', 'deleteTaxCharge');
                Route::post('deleteCompanyCharge', 'deleteCompanyCharge');
                Route::post('addCompanyCharge', 'addCompanyCharge');
                Route::post('toggleCompanyChargeStatus', 'toggleCompanyChargeStatus');
                Route::post('addTaxCharge', 'addTaxCharge');
                Route::post('toggleTaxStatus', 'toggleTaxStatus');
            });
            Route::controller(RoomController::class)->group(function () {
                Route::post('addRoomAvailability', 'addRoomAvailability');
                Route::get('getRoomAvailability/{room_id}', 'getRoomAvailability');
                Route::post('addHourlyRoomRates', 'addHourlyRoomRates');
                Route::get('getHourlyRoomRates/{hotel_slug}', 'getHourlyRoomRates');
                Route::post('addRoom', 'addRoomByAdmin');
                Route::post('addRoomRates', 'addRoomRates');
                Route::get('getRooms/{slug}', 'getPropertyRooms');
                Route::get('getRoomRates/{room_id}', 'getRoomRates');
                Route::post('toggleRoomStatus', 'toggleRoomStatusByAdmin');
            });
            Route::controller(HotelController::class)->group(function () {
                Route::post('setCheckInOutTime', 'setCheckInOutTime');
                Route::get('togglePropertyStatus/{slug}', 'togglePropertyStatus');
                Route::post('verifyVendorRequest', 'verifyVendorRequest');
                Route::post('deleteVendorRequest', 'deleteVendorRequest');
                Route::post('deleteVendorRequestsMail', 'deleteVendorRequestsMail');
                Route::post('verifyVendorRequestsMail', 'verifyVendorRequestsMail');
                Route::get('vendorRequestsMails', 'vendorRequestsMails');
                Route::get('vendorRequests', 'allVendorRequests');
                Route::get('unBanProperty/{slug}', 'unBanProperty');
                Route::get('banProperty/{slug}', 'banProperty');
                Route::get('verifyProperty/{slug}', 'verifyProperty');
                Route::get('getPropertyDetails/{slug}', 'getPropertyDetails');
                Route::get('getPropertiesRequestList', 'getPropertiesRequestList');
                Route::get('getAmenity/{id}', 'getAmenity');
                Route::post('addAmenity', 'addAmenity');
                Route::post('updateAmenity', 'updateAmenity');
                Route::post('deleteAmenity', 'deleteAmenity');
            });
            //Admin vendor routes
            Route::controller(VendorController::class)->group(function () {
                Route::get('downloadVendorDetails/{vendor}', 'downloadVendorDetails');
                Route::get('downloadAllVendo`rsDetails', 'downloadAllVendorsDetails');
                Route::post('updateVendorRazorpayId', 'updateVendorRazorpayId');
                Route::post('downloadVendorBankDetails', 'downloadVendorBankDetails');
                Route::post('downloadVendorBankDetailsRequest', 'downloadVendorBankDetailsRequest');
                Route::post('approveBankDetails', 'approveBankDetails');
                Route::post('approveBankDetailsRequest', 'approveBankDetailsRequest');
                Route::post('rejectBankDetailsRequest', 'rejectBankDetailsRequest');
                Route::get('vendorAccounts', 'vendorAccounts');
                Route::get('accountsRequests', 'accountsRequests');
                Route::get('vendors', 'index')->name('vendors');
                Route::post('addVendor', 'addVendor')->name('addVendor');
                Route::post('editVendor', 'editVendor')->name('editVendor');
                Route::post('deleteVendor', 'deleteVendor')->name('deleteVendor');
                Route::post('blockVendor', 'blockVendor')->name('blockVendor');
            });
            //Admin City Routes
            Route::controller(PlaceController::class)->group(function () {
                //city methods
                Route::post('addCity', 'addCity');
                Route::post('editCity', 'editCity')->name('editCity');
                Route::post('deleteCity', 'deleteCity')->name('deleteCity');
                Route::post('stopCity', 'stopCity')->name('stopCity');
                //State Methods
                Route::post('addState', 'addState')->name('addState');
                Route::post('deleteState', 'deleteState')->name('deleteState');
                Route::post('editState', 'editState')->name('editState');
            });
            //Admin Users Routes
            Route::controller(UserController::class)->group(function () {
                Route::get('users', 'index')->name('users');
                Route::get('allUsersExport', 'allUsersExport');
                Route::get('userExport/{id}', 'userExport');
                Route::post('deleteUser', 'deleteUser')->name('deleteUser');
                Route::post('blockUser', 'blockUser')->name('blockUser');
                Route::post('promoteUserToAdmin', 'promoteUserToAdmin');
            });
            //Blogs controller
            Route::controller(BlogController::class)->group(function () {
                Route::get('allBlogs', 'allBlogs');
                Route::get('getBlog/{id}', 'getBlog');
                Route::post('addBlog', 'addBlog');
                Route::post('toggleBlogStatus', 'toggleBlogStatus');
                Route::post('deleteBlog', 'deleteBlog');
                Route::post('updateBlog', 'updateBlog');
            });
            //Room Controller
            Route::controller(\App\Http\Controllers\v1\admin\RoomController::class)->group(function () {
                Route::post("addPropertyType", "addPropertyType");
                Route::post("addRoomType", "addRoomType");
                Route::get("getRoomTypes", "getRoomTypes");
                Route::post("toggleRoomType", "toggleRoomType");
                Route::post("editRoomType", "editRoomType");
                Route::post("deleteRoomType", "deleteRoomType");
                Route::get("getAllHotelsBookings", "getAllHotelsBookings");
            });
            //Coupon Controller
            Route::controller(CouponController::class)->group(function () {
                Route::get("getCoupons", "getCoupons");
                Route::post("editAdminCoupon", "editAdminCoupon");
                Route::post("addAdminCoupon", "addAdminCoupon");
                Route::post("toggleCouponStatus", "toggleCouponStatus");
                Route::post("deleteAdminCoupon", "deleteAdminCoupon");
            });
            //Offer Controller
            Route::controller(OfferController::class)->group(function () {
                Route::post("updateOffer", "updateOffer");
                Route::post("deleteOffer", "deleteOffer");
                Route::post("toggleOfferStatus", "toggleOfferStatus");
                Route::post("addOffer", "addOffer");
                Route::get("getOffers", "getAllOffersForAdmin");
            });
        });
    });
});
