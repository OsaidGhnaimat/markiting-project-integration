<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;


class AssistantController extends Controller
{

    public function showAssistant(){
        return view('assistant');
    }

    public function generateAssistantsResponse(Request $request)
    {
        $assistantId = 'asst_vIVDbmAMRTELKG6HM6FJAAPE';
        $userMessage = $request->message;

        // Check if user uploaded a file
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            // Validate image and convert to base64
            // $imageData = base64_encode(file_get_contents($image->path()));

            // dd( $imageData);
            $imageData = 'https://images.unsplash.com/photo-1575936123452-b67c3203c357';

            // Concatenate the base64 image data to the user message
            $userMessage .= ' ' . $imageData;
        }

        // Create thread, submit user message, and run assistant
        [$thread, $message, $run] = $this->createThreadAndRun($assistantId, $userMessage);

        // Wait for the assistant run to complete
        $run = $this->waitOnRun($run, $thread->id);

        // Process the completed assistant run
        if ($run->status == 'completed') {
            // Retrieve messages from the assistant run
            $messages = $this->getMessages($run->threadId, 'asc', $message->id);

            $messagesData = $messages->data;

            if (!empty($messagesData)) {
                $assistantResponseMessage = '';

                // Concatenate assistant response messages
                foreach ($messagesData as $message) {
                    $assistantResponseMessage .= $message->content[0]->text->value . "\n\n";
                }

                // Remove the last new line
                $assistantResponseMessage = rtrim($assistantResponseMessage);

                return response()->json([
                    "assistant_response" => $assistantResponseMessage,
                ]);
            } else {
                Log::error('Something went wrong; assistant didn\'t respond');
            }
        } else {
            Log::error('Something went wrong; assistant run wasn\'t completed successfully');
        }
    }



    private function submitMessage($assistantId, $threadId, $userMessage)
    {
        // $userMessage = substr($userMessage, 0, 32768);

        // Create the message with truncated content
        $message = OpenAI::threads()->messages()->create($threadId, [
            'role' => 'user',
            'content' => $userMessage,
        ]);

        $run = OpenAI::threads()->runs()->create(
            threadId: $threadId,
            parameters: [
                'assistant_id' => $assistantId,
            ],
        );

        return [
            $message,
            $run
        ];
    }

    private function createThreadAndRun($assistantId, $userMessage)
    {
        $thread = OpenAI::threads()->create([]);

        [$message, $run] = $this->submitMessage($assistantId, $thread->id, $userMessage);

        return [
            $thread,
            $message,
            $run
        ];
    }

    private function waitOnRun($run, $threadId)
    {
        while ($run->status == "queued" || $run->status == "in_progress")
        {
            $run = OpenAI::threads()->runs()->retrieve(
                threadId: $threadId,
                runId: $run->id,
            );

            sleep(1);
        }

        return $run;
    }

    private function getMessages($threadId, $order = 'asc', $messageId = null)
    {
        $params = [
            'order' => $order,
            'limit' => 10
        ];

        if($messageId) {
            $params['after'] = $messageId;
        }

        return OpenAI::threads()->messages()->list($threadId, $params);
    }


    private function processRunFunctions($run)
    {
        // check if the run requires any action
        while ($run->status == 'requires_action' && $run->requiredAction->type == 'submit_tool_outputs')
        {
            // Extract tool calls
            // multiple calls possible
            $toolCalls = $run->requiredAction->submitToolOutputs->toolCalls;
            $toolOutputs = [];

            foreach ($toolCalls as $toolCall) {
                $name = $toolCall->function->name;
                $arguments = json_decode($toolCall->function->arguments);

                if ($name == 'describe_image') {
                    $visionResponse = OpenAI::chat()->create([
                        'model' => 'gpt-4-vision-preview',
                        'messages' => [
                            [
                                'role' => 'user',
                                'content' => [
                                    [
                                        "type" => "text",
                                        "text" => $arguments?->user_message
                                    ],
                                    [
                                        "type" => "image_url",
                                        "image_url" => [
                                            "url" => $arguments?->image,
                                        ],
                                    ],
                                ]
                            ],
                        ],
                        'max_tokens' => 2048
                    ]);

                    // you get 1 choice by default
                    $toolOutputs[] = [
                        'tool_call_id' => $toolCall->id,
                        'output' => $visionResponse?->choices[0]?->message?->content
                    ];
                }
            }

            $run = OpenAI::threads()->runs()->submitToolOutputs(
                threadId: $run->threadId,
                runId: $run->id,
                parameters: [
                    'tool_outputs' => $toolOutputs,
                ]
            );

            $run = $this->waitOnRun($run, $run->threadId);
        }

        return $run;
    }
}
