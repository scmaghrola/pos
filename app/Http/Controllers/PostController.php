<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class PostController extends Controller
{
    //LIST POSTS
    public function index(Request $request)
    {
        //listing posts 
        info('request user id: ' . $request->user()->id);

        info('Fetching all posts');
        $post = Post::with('user')->get();
        if ($post->isEmpty()) {
            return response()->json(['message' => 'No posts found'], 404);
        }
        return response()->json([
            'message' => 'Posts retrieved successfully',
            'posts' => $post,
        ], 200);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'image'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'status' => false
            ], 422);
        }

        $validated = $validator->validated();

        // $pathTest = "not set";
        // if ($request->hasFile('image')) {
        //     $pathTest = $request->file('image')->getRealPath();
        // }

        // return response()->json([
        //     'message' => 'Post created successfully',
        //     'validated' => $validated,
        //     'pathTest' => $pathTest,
        //     'status' => true
        // ], 201);
        

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $uploadResult = Cloudinary::upload($request->file('image')->getRealPath(), [
                'folder' => 'posts'
            ]);
            $imageUrl = $uploadResult->getSecurePath();
        }

        $post = Post::create([
            'user_id' => $request->user()->id,
            'title'   => $validated['title'],
            'content' => $validated['content'],
            'image'   => $imageUrl,
        ]);

        return response()->json([
            'message' => 'Post created successfully',
            'post' => $post,
            'status' => true
        ], 201);
    }

    //CREATE POST
    public function storeOld(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
        ]);

        $post = Post::create([
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'content' => $validated['content'],
        ]);

        return response()->json([
            'message' => 'Post created successfully',
            'post' => $post,
            'status' => true
        ], 201);
    }

    //SHOW POST
    public function show($id)
    {
        $post = Post::with('user')->find($id);
        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => 'Post not found'
            ], 404);
        }
        return response()->json([
            'message' => 'Post retrieved successfully',
            'status' => true,
            'post' => $post
        ], 200);
    }

    // UPDATE POST
    public function update(Request $request, $id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'message' => 'Post not found',
                'status' => false,
            ], 404);
        }

        // Automatically validates and throws if invalid
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
        ]);

        if($request->hasfile('image')) {
            $uploadResult = Cloudinary::upload($request->file('image')->getRealPath(), [
                'folder' => 'posts'
            ]);
            $validated['image'] = $uploadResult->getSecurePath();
        }

        $post->update($validated);

        return response()->json([
            'message' => 'Post updated successfully',
            'post' => $post,
            'status' => true,
        ], 200);
    }



    //DELETE POST
    public function destroy($id)
    {
        $post = Post::find($id);
        if (!$post) {
            return response()->json([
                'message' => 'Post not found',
                'status' => false
            ], 404);
        }
        $post->delete();
        return response()->json([
            'message' => 'post deleted successfully',
            'status' => true
        ], 200);
    }
}
