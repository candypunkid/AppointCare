<?php

namespace Modules\AICall\Console\Commands;

use Illuminate\Console\Command;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Modules\Appointment\Models\Appointment;

class VoiceStreamServer extends Command
{
    protected $signature = 'aicall:stream-server';
    protected $description = 'Starts the WebSocket server to bridge Twilio and OpenAI Realtime';

    public function handle()
    {
        $this->info("Starting AI Voice Stream Server on port 8080...");

        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new class implements MessageComponentInterface {
                        protected $clients = [];
                        protected $openAiConnections = [];

                        public function onOpen(ConnectionInterface $conn) {
                            $this->clients[$conn->resourceId] = [
                                'conn' => $conn,
                                'streamSid' => null,
                                'appointmentId' => null
                            ];
                            dump("New Twilio Connection: {$conn->resourceId}");
                        }

                        public function onMessage(ConnectionInterface $from, $msg) {
                            $data = json_decode($msg, true);

                            switch ($data['event']) {
                                case 'start':
                                    $this->clients[$from->resourceId]['streamSid'] = $data['streamSid'];
                                    $this->clients[$from->resourceId]['appointmentId'] = $data['start']['customParameters']['appointment_id'] ?? null;
                                    $this->initOpenAi($from);
                                    break;

                                case 'media':
                                    // Forward audio chunk to OpenAI
                                    if (isset($this->openAiConnections[$from->resourceId])) {
                                        $this->openAiConnections[$from->resourceId]->send(json_encode([
                                            'type' => 'input_audio_buffer.append',
                                            'audio' => $data['media']['payload']
                                        ]));
                                    }
                                    break;
                            }
                        }

                        protected function initOpenAi($from) {
                            $loop = \React\EventLoop\Loop::get();
                            $connector = new \Ratchet\Pawl\Connector($loop);
                            $url = config('aicall.openai.websocket_url') . "?model=" . config('aicall.openai.realtime_model');
                            
                            $headers = [
                                'Authorization' => 'Bearer ' . config('aicall.openai.api_key'),
                                'OpenAI-Beta' => 'realtime=v1'
                            ];

                            $connector($url, [], $headers)->then(function($ws) use ($from) {
                                $this->openAiConnections[$from->resourceId] = $ws;
                                
                                // Send Initial System Instructions
                                $ws->send(json_encode([
                                    'type' => 'session.update',
                                    'session' => [
                                        'instructions' => "You are a professional assistant for AppointCare. Confirm the appointment with the customer naturally. If they agree, say 'CONFIRMED'.",
                                        'voice' => 'alloy',
                                        'input_audio_format' => 'g711_ulaw',
                                        'output_audio_format' => 'g711_ulaw',
                                    ]
                                ]));

                                $ws->on('message', function($msg) use ($from) {
                                    $response = json_decode($msg, true);
                                    if ($response['type'] === 'response.audio.delta') {
                                        // Send AI Audio back to Twilio
                                        $from->send(json_encode([
                                            'event' => 'media',
                                            'streamSid' => $this->clients[$from->resourceId]['streamSid'],
                                            'media' => ['payload' => $response['delta']]
                                        ]));
                                    }
                                });
                            });
                        }

                        public function onClose(ConnectionInterface $conn) {
                            unset($this->clients[$conn->resourceId]);
                            if (isset($this->openAiConnections[$conn->resourceId])) {
                                $this->openAiConnections[$conn->resourceId]->close();
                            }
                        }

                        public function onError(ConnectionInterface $conn, \Exception $e) {
                            $conn->close();
                        }
                    }
                )
            ),
            8080
        );

        $server->run();
    }
}
