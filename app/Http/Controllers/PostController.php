<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        // Organizing the query to improve the performance and minimize the number of consults in database
        $posts = Post::latest()
            ->filter(request(['search', 'category', 'author']))
            ->with('category', 'author')
            ->paginate(3)
            ->withQueryString(); // Adding all query strings to paginate, because we need to add all things that search and filters

        return view('posts.index', [
            'posts' => $posts
        ]);
    }

    public function show(Post $post)
    {
        return view('posts.show', [
            "post" => $post,
            "categories" => Category::all()
        ]);
    }
}
