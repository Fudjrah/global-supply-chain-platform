<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ArticleController extends Controller
{
    public function index()
    {
        Gate::authorize('manage-admin');

        $articles = Article::paginate(10);
        return view('admin.articles.index', compact('articles'));
    }

    public function create()
    {
        Gate::authorize('manage-admin');

        return view('admin.articles.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('manage-admin');

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'source' => ['nullable', 'string', 'max:255'],
            'url' => ['nullable', 'url', 'max:255'],
            'published_at' => ['required', 'date'],
        ]);

        Article::create($request->all());

        return redirect()->route('articles.index')->with('success', 'Artikel berhasil ditambahkan.');
    }

    public function edit(Article $article)
    {
        Gate::authorize('manage-admin');

        return view('admin.articles.edit', compact('article'));
    }

    public function update(Request $request, Article $article)
    {
        Gate::authorize('manage-admin');

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'source' => ['nullable', 'string', 'max:255'],
            'url' => ['nullable', 'url', 'max:255'],
            'published_at' => ['required', 'date'],
        ]);

        $article->update($request->all());

        return redirect()->route('articles.index')->with('success', 'Artikel berhasil diperbarui.');
    }

    public function destroy(Article $article)
    {
        Gate::authorize('manage-admin');

        $article->delete();

        return redirect()->route('articles.index')->with('success', 'Artikel berhasil dihapus.');
    }
}
