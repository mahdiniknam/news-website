<?php

namespace App\Http\Controllers\Admin\Aggregator;

use App\Http\Controllers\Controller;
use App\Models\AggregatedMessage;
use Illuminate\Http\Request;

class AggregatorLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = AggregatedMessage::with(['sourceChannel', 'post'])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.pages.aggregator.logs.index', compact('logs'));
    }
}
