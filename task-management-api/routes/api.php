<?php

use App\Events\JoinRequestCreated;
use App\Events\TestNotification;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TeamJoinRequestController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/test', function () {
    return response()->json(['message' => 'API is working!']);
});

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);



Route::post('/addNewUser/{team}', [TeamController::class, 'addNewUser'])->middleware('auth:sanctum');





// Team Invitations
// POST /api/teams/{team}/join-request

Route::post('/teams/{team}/join-request', [TeamController::class, 'requestToJoin'])->middleware('auth:sanctum');
// POST /api/teams/{team}/handle-request
Route::get('/teams/{team}/join-requests', [TeamController::class, 'getAllJoinRequests'])->middleware('auth:sanctum');



Route::middleware('auth:sanctum')
    ->scopeBindings()
    ->group(function () {
        // User routes
        Route::post('/logout', [UserController::class, 'logout']);
        Route::get('/user', [UserController::class, 'me']);
        Route::get('/user-numbers-data', [UserController::class, 'userNumbersData']);
        Route::put('/user', [UserController::class, 'update']);
        Route::delete('/user', [UserController::class, 'deleteAccount']);

        Route::get('/dashboard/join-requests', [DashboardController::class, 'latestJoinRequests']);





        //Team routes
        Route::get('/teams', [TeamController::class, 'index']);
        Route::post('/team', [TeamController::class, 'store']);
        Route::get('/teams/{team}', [TeamController::class, 'show']);
        Route::put('/teams/{team}', [TeamController::class, 'update']);
        Route::delete('/teams/{team}', [TeamController::class, 'destroy']);
        Route::get('/my-teams', [TeamController::class, 'myTeams']);
        Route::get('/user/teams/summarize', [TeamController::class, 'summarizeUserTeams']);
        Route::get('teams/{team}/teamNumbersData', [TeamController::class, 'numbersData']);
        Route::get('/teams/{team}/members', [TeamController::class, 'teamMember']);
        Route::get('/teams/{team}/join-requests', [TeamController::class, 'teamJoinRequests']);
        Route::post('/check-role/{team}', [TeamController::class, 'checkUserRole']);

        // Delete User From Team
        Route::post('/teams/{team}/delete-user', [TeamController::class, 'removeUser']);

        // Team Join Request routes
        Route::post(
            '/teams/join-request',
            [TeamJoinRequestController::class, 'storeRequest']
        );
        Route::post('/teams/join', [TeamController::class, 'checkCode']);


        Route::post("/teams/{team}/add-user", [TeamJoinRequestController::class, 'addNewUser']);



        Route::get('/my-join-requests', [TeamJoinRequestController::class, 'myJoinRequests']);
        Route::get('/teams/{team}/join-requests', [TeamJoinRequestController::class, 'teamJoinRequests']);

        Route::post(
            '/join-requests/approve',
            [TeamJoinRequestController::class, 'approve']
        );

        Route::post(
            '/join-requests/reject',
            [TeamJoinRequestController::class, 'reject']
        );


        // ===============================
        // TASKS MODULE ROUTES
        // - Admin: create / update / delete
        // - Members: update status
        // - Policies enforced
        // ===============================
        Route::post('/teams/{team}/tasks', [TaskController::class, 'store']);
        Route::get('/teams/{team}/tasks/{task}', [TaskController::class, 'show']);
        Route::get('/teams/{team}/tasks', [TaskController::class, 'index']);
        Route::put('/teams/{team}/tasks/{task}', [TaskController::class, 'update']);
        Route::delete('/teams/{team}/tasks/{task}', [TaskController::class, 'destroy']);
        Route::put('/teams/{team}/tasks/{task}/status', [TaskController::class, 'updateStatus']);

        // Notification routes
        Route::get('/notifications', [UserController::class, 'getNotifications']);
        Route::post('/notifications/{notification}/mark-as-read', [UserController::class, 'markNotificationAsRead']);
        Route::post('/notifications/mark-all-as-read', [UserController::class, 'MarkAllAsRead']);
        Route::get('/notifications/unread-count', [UserController::class, 'getUnReadNotificationsCount']);
        Route::get('/notifications/unread', [UserController::class, 'getUnReadNotifications']);


        //  User Plan //

        Route::post('/check-team-chat', [PlanController::class, 'checkTeamChat']);
        Route::post('/user/check-create-team', [PlanController::class, 'checkCreateTeam']);
        Route::post('/user/check-join-team', [PlanController::class, 'checkJoinTeam']);
        // Route::post('/user/upgrade-plan', [PlanController::class, 'upgradePlan']);

        Route::post('/create-payment-intent', [CheckoutController::class, 'createPaymentIntent']);
        Route::post('/confirm-payment', [CheckoutController::class, 'confirmPayment']);
        Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);

        // Chats Routes
        Route::get('/teams/{team}/global-chat', [MessageController::class, 'getGlobalChatMessages']);
        Route::get('/teams/{team}/private-chat', [MessageController::class, 'getPrivateChatMessages']);
        Route::post('/teams/{chat}/message', [MessageController::class, 'sendMessage']);
        
        Route::get('/{team}/members', [TeamController::class, 'teamChatMembers']);
        
        Route::post('/teams/{team}/private-chat', [ChatController::class, 'getOrCreatePrivateChat']);
        
        Route::get('/teams/{team}/global-chat/un-read-messages', [TeamController::class, 'globalChatUnread']);

        // Mark As Read Private
        Route::post('/chats/{chat}/mark-as-read', [MessageController::class, 'MarkAsRead']);

    });
