<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Posts;

class PostController extends Controller
{
    use SoftDeletes;

    /**
    * @OA\Get(
    *     path="/api/posts",
    *     summary="Show all Posts",
    *     security={{"bearerAuth": {}}},
    *     @OA\Response(
    *         response=200,
    *         description="Get all Posts from user."
    *     ),
    *     @OA\Response(
    *         response="default",
    *         description="An error occurred."
    *     )
    * )
    */
    public function getAll(){
        return Posts::with('user')->get('*');
    }

    public function getPostById(Request $request){
        return Posts::with('user')->where('id',$request->id)->first();
    }

    public function getAllByUser(Request $request){
        return Posts::with('user')->where('user_id',$request->id)->get();
    }

    public function store(Request $request){
        $new = [
            'title' => $request->title,
            'description' => $request->description,
            'user_id' => Auth::user()->id
        ];
        $p = new Posts($new);
        $p->save();
        return $p;
    }

    public function update(Request $request){
        $upd = [
            'title' => $request->title,
            'description' => $request->description,
            'updated_at' => date('c')
        ];
        $p = Posts::find($request->id);
        $p->update($upd);
        return $p;
    }

    public function destroy(Request $request){
        $p = Posts::find($request->id);
        $p->delete();
        $header = ['Content-Type: application/json'];
        return response()->json([
            'status' => 'ok',
            'message' => 'Post ('.$request->id.') Successfuly Destroyed.'
        ],200,$header);
    }
}
