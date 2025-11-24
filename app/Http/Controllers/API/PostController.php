<?php

namespace App\Http\Controllers\API;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Validation\Rule;

class PostController extends BaseController
{
    public function index(Request $request)
    {
        $query = Post::query();

        if ($search = $request->string('q')->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('content', 'like', "%$search%");
            });
        }

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        $sort = $request->string('sort')->toString();
        $direction = $request->string('direction')->toString() ?: 'desc';
        if ($sort === 'published_at') {
            $query->orderBy('published_at', $direction)->orderBy('id', 'desc');
        } else {
            $query->orderBy('id', 'desc');
        }

        $perPage = $request->integer('per_page') ?: 15;
        return response()->json($query->paginate($perPage));
    }

    public function show(Post $post)
    {
        return response()->json($post);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'author' => ['required', 'string', 'max:255'],
            'published_at' => ['nullable', 'date'],
            'status' => ['nullable', Rule::in(['draft', 'published', 'archived'])],
            'slug' => ['nullable', 'string', 'max:255', 'unique:posts,slug'],
        ]);

        $post = Post::create($data);

        return response()->json($post, 201);
    }

    public function update(Request $request, Post $post)
    {
        $data = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'content' => ['sometimes', 'required', 'string'],
            'author' => ['sometimes', 'required', 'string', 'max:255'],
            'published_at' => ['nullable', 'date'],
            'status' => ['nullable', Rule::in(['draft', 'published', 'archived'])],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('posts', 'slug')->ignore($post->id)],
        ]);

        $post->update($data);

        return response()->json($post);
    }

    public function destroy(Post $post)
    {
        $post->delete();

        return response()->noContent();
    }
}

