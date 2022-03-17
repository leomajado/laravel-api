<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

use App\Models\Posts;

class PostController extends Controller {

    use SoftDeletes;

    /**
    * @OA\Get(
    *     tags={"posts"},
    *     path="/api/posts",
    *     summary="Show all Posts",
    *     security={{"bearerAuth": {}}},
    *     @OA\Response(
    *         response=200,
    *         description="Get all Posts from user."
    *     ),
    *     @OA\Response(
    *         response=401,
    *         description="Unauthorized"
    *     ),
    *     @OA\Response(
    *         response=404,
    *         description="Not Found"
    *     )
    * )
    */
    public function getAll(){
        return Posts::with('user')->get('*');
    }

    /**
     * @OA\Get(
     *   tags={"posts"},
     *   path="/api/post/{id}",
     *   summary="Show Post By Id",
     *   security={
     *      {"bearerAuth": {}},
     *   },
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Get By Id",
     *     required=true,
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\MediaType(
     *       mediaType="application/json",
     *     )
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Unauthorized"
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Not Found"
     *   )
     * )
     */
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
