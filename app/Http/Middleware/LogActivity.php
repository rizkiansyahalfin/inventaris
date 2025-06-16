<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LogActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $module = null, string $action = null): Response
    {
        $response = $next($request);

        if (Auth::check()) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => $action ?? $this->guessAction($request),
                'module' => $module ?? $this->guessModule($request),
                'description' => $this->generateDescription($request),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        return $response;
    }

    /**
     * Guess the action based on the request method.
     */
    protected function guessAction(Request $request): string
    {
        $method = $request->method();

        return match ($method) {
            'GET' => 'view',
            'POST' => 'create',
            'PUT', 'PATCH' => 'update',
            'DELETE' => 'delete',
            default => 'other',
        };
    }

    /**
     * Guess the module based on the request path.
     */
    protected function guessModule(Request $request): string
    {
        $path = $request->path();
        $segments = explode('/', $path);

        return $segments[0] ?? 'unknown';
    }

    /**
     * Generate a description of the action.
     */
    protected function generateDescription(Request $request): string
    {
        $method = $request->method();
        $path = $request->path();
        $id = $request->route('id') ?? $request->route('*.id');

        $description = "User performed {$method} request to {$path}";

        if ($id) {
            $description .= " for ID: {$id}";
        }

        return $description;
    }
} 