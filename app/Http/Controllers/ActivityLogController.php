<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller as BaseController;

class ActivityLogController extends BaseController
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index()
    {
        $activityLogs = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('activity-logs.index', compact('activityLogs'));
    }
} 