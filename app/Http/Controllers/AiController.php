<?php

namespace App\Http\Controllers;

use App\Services\AIService;
use Illuminate\Http\Request;

class AiController extends Controller
{
    public function respond(Request $request, AIService $ai)
    {
        $request->validate(['prompt' => 'required|string|max:2000']);

        if (! $ai->isAvailable()) {
            return response()->json(['error' => 'AI not configured.'], 503);
        }

        $answer = '';
        try {
            $answer = $ai->chat($request->input('prompt'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json(['answer' => $answer]);
    }
}
