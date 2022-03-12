<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

use App\Models\Posts;


class PostController extends Controller
{
    use SoftDeletes;

    public function getAll(){
        return Posts::with('user')->get('*');
    }
}
