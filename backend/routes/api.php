<?php

use App\Http\Controllers\Api\Adoption\AdoptablePetController;
use App\Http\Controllers\Api\Adoption\AdoptionController;
use App\Http\Controllers\Api\Adoption\AdoptionFeedController;
use App\Http\Controllers\Api\Adoption\AdoptionMessageController;
use App\Http\Controllers\Api\Adoption\AdoptionSwipeController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\PasswordResetController;
use App\Http\Controllers\Api\Global\PreOngRegistrationController;
use App\Http\Controllers\Api\Global\Select\BreedController;
use App\Http\Controllers\Api\Global\Select\GoalController;
use App\Http\Controllers\Api\Global\Select\HealthRecordTypeController;
use App\Http\Controllers\Api\Global\Select\SizeController;
use App\Http\Controllers\Api\Global\Select\SpeciesController;
use App\Http\Controllers\Api\Notification\NotificationController;
use App\Http\Controllers\Api\Ong\MyOngController;
use App\Http\Controllers\Api\Ong\OngAdoptablePetController;
use App\Http\Controllers\Api\Ong\OngCampaignController;
use App\Http\Controllers\Api\Ong\OngController;
use App\Http\Controllers\Api\Ong\OngMemberController;
use App\Http\Controllers\Api\Ong\OngPetController;
use App\Http\Controllers\Api\Ong\OngPostController;
use App\Http\Controllers\Api\Ong\OngPublicCampaignController;
use App\Http\Controllers\Api\Ong\OngPublicPostController;
use App\Http\Controllers\Api\Ong\PostFeedController;
use App\Http\Controllers\Api\Pet\PetController;
use App\Http\Controllers\Api\Pet\PetDetailController;
use App\Http\Controllers\Api\Pet\PetHealthRecordController;
use App\Http\Controllers\Api\Pet\PetPhotoController;
use App\Http\Controllers\Api\Pet\PetVaccineController;
use App\Http\Controllers\Api\Pet\PetWeightController;
use App\Http\Controllers\Api\PetMatch\PetFeedController;
use App\Http\Controllers\Api\PetMatch\PetMatchController;
use App\Http\Controllers\Api\PetMatch\PetMessageController;
use App\Http\Controllers\Api\PetMatch\PetSwipeController;
use App\Http\Controllers\Api\Profile\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::middleware('throttle:60,1')->group(function (): void {
        Route::prefix('ongs')->group(function (): void {
            Route::get('/', [OngController::class, 'index']);
            Route::get('{ong}', [OngController::class, 'show']);
            Route::get('{ong}/pets', [OngAdoptablePetController::class, 'index']);
            Route::get('{ong}/posts', [OngPublicPostController::class, 'index']);
            Route::get('{ong}/campaigns', [OngPublicCampaignController::class, 'index']);
        });

        Route::prefix('posts')->group(function (): void {
            Route::get('/', [PostFeedController::class, 'index']);
            Route::get('{publicPost}', [PostFeedController::class, 'show']);
        });

        Route::prefix('selects')->group(function (): void {
            Route::get('species', [SpeciesController::class, 'index']);
            Route::get('breeds', [BreedController::class, 'index']);
            Route::get('sizes', [SizeController::class, 'index']);
            Route::get('goals', [GoalController::class, 'index']);
            Route::get('health-record-types', [HealthRecordTypeController::class, 'index']);
        });

        Route::prefix('adoptable-pets')->group(function (): void {
            Route::get('/', [AdoptablePetController::class, 'index']);
            Route::get('{adoptablePet}', [AdoptablePetController::class, 'show']);
        });
    });

    Route::prefix('ong-registrations')->middleware('throttle:ong-register')->group(function (): void {
        Route::post('/', [PreOngRegistrationController::class, 'store']);
        Route::get('status', [PreOngRegistrationController::class, 'status']);
    });

    Route::prefix('auth')->group(function (): void {
        Route::post('register', [AuthController::class, 'register'])->middleware('throttle:auth-register');
        Route::post('login', [AuthController::class, 'login'])->middleware('throttle:auth-login');
        Route::post('verify-email', [AuthController::class, 'verifyEmail'])->middleware('throttle:auth-sensitive');
        Route::post('resend-verification-code', [AuthController::class, 'resendVerificationCode'])->middleware('throttle:auth-sensitive');
        Route::post('forgot-password', [PasswordResetController::class, 'forgotPassword'])->middleware('throttle:auth-sensitive');
        Route::post('reset-password', [PasswordResetController::class, 'resetPassword'])->middleware('throttle:auth-sensitive');
    });

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::get('me', [AuthController::class, 'me']);
        Route::put('me', [ProfileController::class, 'update']);
        Route::post('me/avatar', [ProfileController::class, 'avatar']);
        Route::delete('me/avatar', [ProfileController::class, 'removeAvatar']);
        Route::put('me/password', [ProfileController::class, 'password'])->middleware('throttle:6,1');

        Route::prefix('pets')->group(function (): void {
            Route::get('/', [PetController::class, 'index']);
            Route::post('/', [PetController::class, 'store']);
            Route::get('{pet}', [PetController::class, 'show']);
            Route::put('{pet}', [PetController::class, 'update']);
            Route::delete('{pet}', [PetController::class, 'destroy']);

            Route::prefix('{pet}')->scopeBindings()->group(function (): void {
                Route::get('feed', [PetFeedController::class, 'index']);
                Route::post('swipes', [PetSwipeController::class, 'store']);

                Route::get('photos', [PetPhotoController::class, 'index']);
                Route::post('photos', [PetPhotoController::class, 'store']);
                Route::put('photos/{photo}', [PetPhotoController::class, 'update']);
                Route::delete('photos/{photo}', [PetPhotoController::class, 'destroy']);

                Route::get('detail', [PetDetailController::class, 'show']);
                Route::put('detail', [PetDetailController::class, 'update']);

                Route::get('vaccines', [PetVaccineController::class, 'index']);
                Route::post('vaccines', [PetVaccineController::class, 'store']);
                Route::put('vaccines/{vaccine}', [PetVaccineController::class, 'update']);
                Route::delete('vaccines/{vaccine}', [PetVaccineController::class, 'destroy']);

                Route::get('health-records', [PetHealthRecordController::class, 'index']);
                Route::post('health-records', [PetHealthRecordController::class, 'store']);
                Route::put('health-records/{healthRecord}', [PetHealthRecordController::class, 'update']);
                Route::delete('health-records/{healthRecord}', [PetHealthRecordController::class, 'destroy']);

                Route::get('weights', [PetWeightController::class, 'index']);
                Route::post('weights', [PetWeightController::class, 'store']);
                Route::put('weights/{weight}', [PetWeightController::class, 'update']);
                Route::delete('weights/{weight}', [PetWeightController::class, 'destroy']);
            });
        });

        Route::prefix('matches')->group(function (): void {
            Route::get('/', [PetMatchController::class, 'index']);
            Route::get('{petMatch}', [PetMatchController::class, 'show']);
            Route::delete('{petMatch}', [PetMatchController::class, 'destroy']);

            Route::get('{petMatch}/messages', [PetMessageController::class, 'index']);
            Route::post('{petMatch}/messages', [PetMessageController::class, 'store'])->middleware('throttle:chat-send');
            Route::put('{petMatch}/messages/read', [PetMessageController::class, 'read']);
        });

        Route::prefix('adoptions')->group(function (): void {
            Route::get('feed', [AdoptionFeedController::class, 'index']);
            Route::post('swipes', [AdoptionSwipeController::class, 'store']);
            Route::get('received', [AdoptionController::class, 'received']);
            Route::get('/', [AdoptionController::class, 'index']);
            Route::get('{adoptionMatch}', [AdoptionController::class, 'show']);
            Route::put('{adoptionMatch}/decision', [AdoptionController::class, 'decide']);
            Route::delete('{adoptionMatch}', [AdoptionController::class, 'cancel']);

            Route::get('{adoptionMatch}/messages', [AdoptionMessageController::class, 'index']);
            Route::post('{adoptionMatch}/messages', [AdoptionMessageController::class, 'store'])->middleware('throttle:chat-send');
            Route::put('{adoptionMatch}/messages/read', [AdoptionMessageController::class, 'read']);
        });

        Route::prefix('my-ongs')->group(function (): void {
            Route::get('/', [MyOngController::class, 'index']);

            Route::prefix('{managedOng}')->scopeBindings()->group(function (): void {
                Route::get('/', [MyOngController::class, 'show']);
                Route::put('/', [MyOngController::class, 'update']);
                Route::post('logo', [MyOngController::class, 'logo']);

                Route::get('members', [OngMemberController::class, 'index']);
                Route::post('members', [OngMemberController::class, 'store']);
                Route::put('members/{user}', [OngMemberController::class, 'update']);
                Route::delete('members/{user}', [OngMemberController::class, 'destroy']);

                Route::get('pets', [OngPetController::class, 'index']);
                Route::post('pets', [OngPetController::class, 'store']);

                Route::get('posts', [OngPostController::class, 'index']);
                Route::post('posts', [OngPostController::class, 'store']);
                Route::get('posts/{post}', [OngPostController::class, 'show']);
                Route::put('posts/{post}', [OngPostController::class, 'update']);
                Route::post('posts/{post}/cover', [OngPostController::class, 'cover']);
                Route::delete('posts/{post}', [OngPostController::class, 'destroy']);

                Route::get('campaigns', [OngCampaignController::class, 'index']);
                Route::post('campaigns', [OngCampaignController::class, 'store']);
                Route::put('campaigns/{campaign}', [OngCampaignController::class, 'update']);
                Route::delete('campaigns/{campaign}', [OngCampaignController::class, 'destroy']);
            });
        });

        Route::prefix('notifications')->group(function (): void {
            Route::get('/', [NotificationController::class, 'index']);
            Route::get('unread-count', [NotificationController::class, 'unreadCount']);
            Route::put('read-all', [NotificationController::class, 'readAll']);
            Route::put('{notification}/read', [NotificationController::class, 'read']);
        });

        Route::prefix('auth')->group(function (): void {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::post('logout-all-devices', [AuthController::class, 'logoutFromAllDevices']);
        });
    });
});
