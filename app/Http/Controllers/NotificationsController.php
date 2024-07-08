<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NotificationsController extends Controller
{
    /**
     * Returns all notifications
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function allNotifications()
    {
        return NotificationResource::collection(auth()->user()->notifications);
    }

    /**
     * Marks a notification as read and returns all the notifications
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function markAsRead(Request $request)
    {
        $request->validate(['id' => 'required|string']);

        $notification = auth()->user()->notifications()->find($request->id);
        if ($notification) {
            $notification->markAsRead();
            return NotificationResource::collection(auth()->user()->notifications);
        }
        abort(404);
    }

    /**
     * Deletes all user's notifications
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAll()
    {
        auth()->user()->notifications()->delete();
        return response(status:Response::HTTP_NO_CONTENT);
    }
}
