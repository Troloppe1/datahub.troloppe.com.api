<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class RouteController extends Controller
{
    public function index()
    {
        $routes = collect(Route::getRoutes())->map(function ($route) {
            return [
                "uri" => $route->uri(),
                "method" => $route->methods()[0],
                "name" => $route->getName(),
                "action" => $route->getActionName(),
            ];
        })->filter(function($route){
            return str($route['uri'])->startsWith('api');
        });
        return view('routes.index')->with('routes', $routes);
    }
}
