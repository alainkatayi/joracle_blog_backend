<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Resources\ArticleResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ArticleController extends Controller
{
    public function index(){
        $article = Article::with('user')->paginate(12);
        return ArticleResource::collection($article);
    }

    public function store(Request $request){

        //on recupère l'utilisateur connecté
        $user = auth()->user();




        //pour la capture d'erreur eventuelle
        try{

            //verifié si l'utilisateur connecté est admin

            if($user->role !=='admin'){
                return response()->json([
                    "Message"=>"Vous n'avez pas le droit de creer un Article"
                ],403);
            }

            //on passe par la validation
            $validator =  Validator::make($request->all(),[
                'title'=>'required|string|max:50',
                'content'=> 'required|string',
                'slug'=>'required|string'
            ]);

            //si la validatio echoue
            if($validator->fails()){
                return response()->json([
                    "error"=>$validator->errors()
                ],402);
            }
            
            //dans le cas contraire on creer l'article
            $article= Article::create([
                'title'=>$request->title,
                'content'=>$request->content,
                'slug'=>$request->slug,
                'user_id'=>$user->id
            ]);

            //on returne à travers une resource
            return new ArticleResource($article);
        }catch(\Exception $exception){
            return response()->json([
                $exception->getMessage()
            ]);
        }
    }

    public function show(int $id){
        $article = Article::findOrFail($id);
        return new ArticleResource($article);
    }

}
