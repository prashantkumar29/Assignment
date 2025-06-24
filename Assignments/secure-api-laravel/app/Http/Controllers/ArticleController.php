<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    public static function boot()
    {
        parent::boot();
    }

    public function index()
    {
        $articles = Auth::user()->articles()->get();
        return response()->json($articles);
    }

    public function show($id)
    {
        $article = Auth::user()->articles()->findOrFail($id);
        return response()->json($article);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);
        $article = Auth::user()->articles()->create($request->only('title', 'content'));
        return response()->json($article, 201);
    }

    public function update(Request $request, $id)
    {
        $article = Auth::user()->articles()->findOrFail($id);
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
        ]);
        $article->update($request->only('title', 'content'));
        return response()->json($article);
    }

    public function destroy($id)
    {
        $article = Auth::user()->articles()->findOrFail($id);
        $article->delete();
        return response()->json(['message' => 'Article deleted']);
    }
}
