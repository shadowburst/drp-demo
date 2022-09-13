<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class EventController extends Controller
{
    public function index()
    {
        return Inertia::render('Events/Index', [
            'events' => Event::isBetween(Request::get('starts_at'), Request::get('ends_at'))->orderByDate()->get()
        ]);
    }

    public function store()
    {
        $data = Request::validate([
            'title' => ['required', 'max:255'],
            'starts_at' => ['required', 'date:H:i d/m/Y']
        ]);

        Event::create([
            ...$data,
            'starts_at' => Carbon::createFromFormat('H:i d/m/Y', $data['starts_at'])
        ]);

        return Redirect::route('events.index');
    }

    public function update(int $id)
    {
        $event = Event::findOrFail($id);

        $data = Request::validate([
            'title' => ['required', 'max:255'],
            'starts_at' => ['required', 'date:H:i d/m/Y']
        ]);

        $event->update([
            ...$data,
            'starts_at' => Carbon::createFromFormat('H:i d/m/Y', $data['starts_at'])
        ]);

        return Redirect::route('events.index');
    }

    public function delete(int $id)
    {
        $event = Event::findOrFail($id);

        $event->delete();

        return Redirect::route('events.index');
    }
}
