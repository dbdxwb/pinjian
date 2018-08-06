<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    $router->resource('AppIndexPic', AppIndexPicController::class);
    $router->resource('ServiceTime', ServiceTimeController::class);
    $router->resource('ShowTime', ShowTimeController::class);
    $router->resource('Problem', ProblemController::class);
    $router->resource('Business', BusinessController::class);
    $router->resource('HotSpot', HotSpotController::class);
    $router->resource('Restaurant', RestaurantController::class);
    $router->resource('RestRoom', RestRoomController::class);
    $router->resource('Bus', BusController::class);
});
