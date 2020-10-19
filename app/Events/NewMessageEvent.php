<?php

namespace App\Events;

use App\Http\Resources\UserResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewMessageEvent extends Event implements ShouldBroadcast
{
    /**
     * Room Id
     *
     * @var int
     */
    public $roomId;

    /**
     * The user object.
     *
     * @var User
     */
    public $sender;

    /**
     * The message.
     *
     * @var String
     */
    public $message;

    /**
     * The created at
     *
     * @var String
     */
    public $createdAt;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($roomId, User $sender, string $message)
    {
        $this->sender = new UserResource($sender);
        $this->message = $message;
        $this->roomId = $roomId;

        $date = Carbon::now();
        $this->createdAt = $date->format('H:i');
    }

    public function broadcastOn()
    {
        return new PrivateChannel("chat.{$this->roomId}");
    }

    public function broadcastAs()
    {
        return 'new-message';
    }
}
