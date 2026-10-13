<?php

namespace App\Http\Controllers\Admin\Aggregator;

use App\Http\Controllers\Controller;
use App\Models\DestinationChannel;
use Illuminate\Http\Request;

class DestinationChannelController extends Controller
{
    public function index()
    {
        $destinations = DestinationChannel::latest()->paginate(15);
        return view('admin.pages.aggregator.destinations.index', compact('destinations'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'platform' => 'required|in:telegram,bale',
            'bot_token' => 'required|string',
            'channel_identifier' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        DestinationChannel::create([
            'name' => $data['name'],
            'platform' => $data['platform'],
            'bot_token' => $data['bot_token'],
            'channel_identifier' => trim($data['channel_identifier']),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'ربات مقصد با موفقیت اضافه شد. ربات را در کانال عضو و ادمین کنید.');
    }

    public function update(Request $request, DestinationChannel $destination)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'platform' => 'required|in:telegram,bale',
            'bot_token' => 'nullable|string',
            'channel_identifier' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $destination->update([
            'name' => $data['name'],
            'platform' => $data['platform'],
            'bot_token' => !empty($data['bot_token']) ? $data['bot_token'] : $destination->bot_token,
            'channel_identifier' => trim($data['channel_identifier']),
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'مقصد ویرایش شد.');
    }

    public function destroy(DestinationChannel $destination)
    {
        $destination->delete();
        return back()->with('success', 'مقصد حذف شد.');
    }
}
