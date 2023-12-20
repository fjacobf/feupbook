<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private Message $message;
    private string $emitter_name;

    public function __construct(Message $_message, string $_emitter_name)
    {
        $this->message = $_message;
        $this->emitter_name = $_emitter_name;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): Channel
    {
        return new PrivateChannel('group-chat.' . $this->message->group_id);
    }

    public function broadcastWith() {
        return [
            'content' => $this->message->content,
            'emitter_id' => $this->message->emitter_id,
            'emitter_name' => $this->emitter_name,
            'date' => $this->message->date,
            'viewed' => $this->message->viewed,
        ];
    }

}
