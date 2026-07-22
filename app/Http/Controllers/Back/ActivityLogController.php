<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('user_name', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhere('properties', 'like', "%{$search}%");
            });
        }

        // Module filter
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        // Action filter
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // User filter
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Date filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(20)->withQueryString();

        // Unique modules and actions for dropdowns
        $modules = ActivityLog::distinct()->pluck('module')->filter()->sort();
        $actions = ActivityLog::distinct()->pluck('action')->filter()->sort();
        $users   = User::select('id', 'name', 'email')->get();

        // Statistics
        $stats = [
            'today'        => ActivityLog::whereDate('created_at', today())->count(),
            'total_crud'   => ActivityLog::whereIn('action', ['Create', 'Update', 'Delete', 'Import'])->count(),
            'total_auth'   => ActivityLog::whereIn('action', ['Login', 'Logout', 'Login As', 'Failed Login'])->count(),
            'total_scan'   => ActivityLog::whereIn('action', ['QR Access', 'QR Lookup'])->count(),
        ];

        return view('back.activity_log.index', compact('logs', 'modules', 'actions', 'users', 'stats'));
    }

    public function show($id)
    {
        $log = ActivityLog::with('user')->findOrFail($id);
        return response()->json($log);
    }
}
