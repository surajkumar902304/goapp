<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','check.user.shop']);
    }
    public function index()
    {
        $tags = Tag::paginate(10);
        return view('user.tags.index', compact('tags'));
    }
}
