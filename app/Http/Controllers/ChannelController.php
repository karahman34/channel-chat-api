<?php

namespace App\Http\Controllers;

use App\Events\NewMessageEvent;
use App\Helpers\Transformer;
use Illuminate\Http\Request;

class ChannelController extends Controller
{
    public function newMessage(Request $request, $roomId)
    {
        $this->validate($request, [
            'message' => 'required|string'
        ]);

        try {
            event(new NewMessageEvent($roomId, $request->user(), $request->get('message')));

            return Transformer::ok('Success to create the message.', null, 201);
        } catch (\Throwable $th) {
            return Transformer::fail('Failed to create the message.');
        }
    }
}
