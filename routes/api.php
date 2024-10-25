<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\app\ChatController;
use App\Http\Controllers\api\app\HomeController;
use App\Http\Controllers\api\app\LikeController;
use App\Http\Controllers\api\app\PostController;
use App\Http\Controllers\api\app\TagsController;
use App\Http\Controllers\api\FCMTokenController;
use App\Http\Controllers\api\app\FollowController;
use App\Http\Controllers\api\app\CommentController;
use App\Http\Controllers\api\app\ProfileController;
use App\Http\Controllers\api\app\BookmarkController;
use App\Http\Controllers\api\app\NotificationController;
use App\Http\Controllers\api\app\user\auth\AuthenticationController;
use App\Http\Controllers\api\app\user\auth\UserResetPasswordController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('v1/login', [AuthenticationController::class, 'login']);
Route::post('v1/register', [AuthenticationController::class, 'register']);
Route::post('v1/reset-password', [UserResetPasswordController::class, 'sendEmail']);
Route::post('v1/validate-otp', [UserResetPasswordController::class, 'validateOtp']);
Route::post('v1/change-password', [UserResetPasswordController::class, 'changePassword']);

// Home Page Posts Random
Route::get('v1/trending-photos', [HomeController::class, 'trendingPhotos']);
Route::get('v1/trending-videos', [HomeController::class, 'trendingVideos']);
Route::get('v1/trending-stories', [HomeController::class, 'trendingStories']);
Route::get('v1/posts/comments', [HomeController::class, 'comments']);
Route::get('v1/bottom-stories', [HomeController::class, 'stories']);

Route::post('v1/login/google', [AuthenticationController::class, 'loginWithGoogle']);

//Authenticated user
Route::group(['middleware' => ['jwtUser:user-api', 'jwt.auth'], 'prefix' => 'v1/'], function () {
    Route::post('user/logout', [AuthenticationController::class, 'userLogout']);

    // add post
    Route::post('add-new-post', [PostController::class, 'storePost']);
    Route::get('edit-post', [PostController::class, 'editPost']);
    Route::post('update-post', [PostController::class, 'updatePost']);
    Route::get('delete-post', [PostController::class, 'deletePost']);
    Route::get('post-tag-users', [PostController::class, 'postTagUsers']);
    Route::get('post-details', [PostController::class, 'postDetails']);

    // search user
    Route::get('search-user', [HomeController::class, 'searchUsers']);
    Route::get('post-info', [HomeController::class, 'postInfo']);

    // Home Page Posts Following
    Route::get('following-photos', [HomeController::class, 'followingPhotos']);
    Route::get('following-videos', [HomeController::class, 'followingVideos']);
    Route::get('following-stories', [HomeController::class, 'followingStories']);

    // user stories
    Route::get('user-stories', [HomeController::class, 'userStories']);

    // post view count
    Route::post('post-view-count', [HomeController::class, 'postViewCount']);

    // Follow Routes
    Route::post('follow-unfollow', [FollowController::class, 'followUnFollow']);
    Route::get('check-follow-status', [FollowController::class, 'followStatus']);

    // Post Like Routes
    Route::post('post-like-unlike', [LikeController::class, 'likeUnlike']);
    Route::get('check-post-like-status', [LikeController::class, 'likeStatus']);

    // Comment Routes
    Route::post('add-comment', [CommentController::class, 'addComment']);
    Route::post('delete-comment', [CommentController::class, 'deleteComment']);
    Route::post('comment-like-unlike', [CommentController::class, 'likeUnlike']);
    Route::get('check-comment-like-status', [CommentController::class, 'likeStatus']);

    // Bookmark Routes
    Route::post('add-to-bookmark', [BookmarkController::class, 'addToBookmark']);
    Route::get('check-bookmark-status', [BookmarkController::class, 'bookmarkStatus']);
    Route::get('my-bookmarks', [BookmarkController::class, 'myBookmarks']);

    // Notification Routes
    Route::get('user/notifications', [NotificationController::class, 'notifications']);

    // Chat Routes
    Route::get('user/all-chats', [ChatController::class, 'allChats']);
    Route::get('user/all-chats/messages', [ChatController::class, 'chatMessages']);
    Route::post('user/all-chats/send-message', [ChatController::class, 'sendMessage']);
    Route::post('user/start-chat', [ChatController::class, 'startChat']);

    // Profile Routes
    Route::get('user/my-profile', [ProfileController::class, 'myProfile']);
    Route::get('user/my-profile/photos', [ProfileController::class, 'myPhotos']);
    Route::get('user/my-profile/videos', [ProfileController::class, 'myVideos']);
    Route::get('user/my-profile/stories', [ProfileController::class, 'myStories']);
    Route::post('user/my-profile/update', [ProfileController::class, 'updateMyProfile']);

    Route::get('user-profile', [ProfileController::class, 'userProfile']);
    Route::get('user-profile/photos', [ProfileController::class, 'userPhotos']);
    Route::get('user-profile/videos', [ProfileController::class, 'userVideos']);
    Route::get('user/followers-followings', [ProfileController::class, 'userFollowersFollowings']);

    // Tags Routes
    Route::get('user/my-tags', [TagsController::class, 'allTags']);

    // User Profile
    Route::get('user/profile', [AuthenticationController::class, 'userProfile']);

    // Email Verification
    Route::post('user/verify-email', [AuthenticationController::class, 'verifyEmail']);
    Route::post('user/resend-verification-code', [AuthenticationController::class, 'resendCode']);

    Route::post('user/store-fcm-token', [FCMTokenController::class, 'storeToken']);
});

