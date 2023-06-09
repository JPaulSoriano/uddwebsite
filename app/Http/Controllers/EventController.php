<?php

namespace App\Http\Controllers;
use App\Event;
use App\News;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['allevents', 'show']);
    }

    public function index()
    {
        $events = Event::all();
        return view('admin.events.index',compact('events'))
        ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    public function allevents()
    {
        $events = Event::all();
        $news = News::inRandomOrder()->get();
        return view('allevents',compact('events', 'news'))
        ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    public function create()
    {
        return view('admin.events.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions:width=1000,height=600',
        ]);

        $input = $request->all();


        if ($image = $request->file('image')) {
            $destinationPath = 'image/';
            $coverImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
            $image->move($destinationPath, $coverImage);
            $input['image'] = "$coverImage";
        }

        Auth::user()->events()->create($input);

        return redirect()->route('events.index')->with('success', 'Created Succesfully');
    }

    public function show(Event $event)
    {
        return view ('admin.events.show', compact('event'));
    }

    public function unfeatureevent(Event $event)
    {
        $event->featured = '0';
        $event->save();

        return redirect()->route('events.index');
    }

    public function featureevent(Event $event)
    {
        $event->featured = '1';
        $event->save();

        return redirect()->route('events.index');
    }
}
