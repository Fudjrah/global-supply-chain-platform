<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Port;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AdminController extends Controller
{
    public function index()
    {
        Gate::authorize('manage-admin');

        $userCount = User::count();
        $portCount = Port::count();
        $articleCount = Article::count();

        return view('admin.dashboard', compact('userCount', 'portCount', 'articleCount'));
    }
}
