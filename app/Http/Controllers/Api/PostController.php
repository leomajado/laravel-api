<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
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
     *     description="Post ID",
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

    /**
     * @OA\Get(
     *   tags={"posts"},
     *   path="/api/user/{id}/posts",
     *   summary="Show Post By Id",
     *   security={
     *      {"bearerAuth": {}},
     *   },
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Post ID",
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
    public function getAllByUser(Request $request){
        return Posts::with('user')->where('user_id',$request->id)->get();
    }

    /**
     * @OA\Post(
     *   tags={"posts"},
     *   path="/api/post",
     *   summary="Store User Posts",
     *   security={
     *      {"bearerAuth": {}},
     *   },
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       type="object",
     *       required={
     *          "title",
     *          "description"
     *       },
     *       @OA\Property(property="title", type="string"),
     *       @OA\Property(property="description", type="string")
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\MediaType(
     *       mediaType="application/json"
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
    public function store(Request $request){
        try {
            $header = ['Content-Type: application/json'];
            $new = [
                'title' => $request->title,
                'description' => $request->description,
                'user_id' => Auth::user()->id
            ];
            $p = new Posts($new);
            $p->save();
            return response()->json([
                'status' => 'ok',
                'data' => $p
            ],200,$header);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 'error',
                'message' => $ex->getMessage()
            ],500,$header);
        }
    }

    /**
     * @OA\Put(
     *   tags={"posts"},
     *   path="/api/post/{id}",
     *   summary="Update User Post",
     *   security={
     *      {"bearerAuth": {}},
     *   },
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Post ID",
     *     required=true,
     *   ),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       type="object",
     *       required={
     *          "title",
     *          "description"
     *       },
     *       @OA\Property(property="title", type="string"),
     *       @OA\Property(property="description", type="string")
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\MediaType(
     *       mediaType="application/json"
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
    public function update(Request $request){
        try {
            $header = ['Content-Type: application/json'];
            $p = Posts::find($request->id);
            if(!empty($p)){
                $upd = [
                    'title' => $request->title,
                    'description' => $request->description,
                    'updated_at' => date('c')
                ];
                $p->update($upd);
                return response()->json([
                    'status' => 'ok',
                    'data' => $p
                ],200,$header);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Post not found.'
                ],404,$header);
            }
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 'error',
                'message' => $ex->getMessage()
            ],500,$header);
        }

    }

    /**
     * @OA\Delete(
     *   tags={"posts"},
     *   path="/api/post/{id}",
     *   summary="Post destroy",
     *   security={
     *      {"bearerAuth": {}},
     *   },
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Post ID",
     *     required=true,
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\MediaType(
     *       mediaType="application/json"
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
    public function destroy(Request $request){
        try {
            $p = Posts::find($request->id);
            $header = ['Content-Type: application/json'];
            if(!empty($p)){
                $p->delete();

                return response()->json([
                    'status' => 'ok',
                    'message' => 'Post ('.$request->id.') Successfuly Destroyed.'
                ],200,$header);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Post not found.'
                ],404,$header);
            }
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 'error',
                'message' => $ex->getMessage()
            ],500,$header);
        }
    }

}
