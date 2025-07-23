<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Resources\ArticleResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ArticleController extends Controller
{
    //méthode pour voir tous les articles
    public function index(){
        $article = Article::with('user')->paginate(12);
        return ArticleResource::collection($article);
    }

    //méthode pour la création d'un article
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

    //méthode pour la mise à jour d'un Article
    public function update(Request $request, Article $article){
        try{
            $user = auth()->user();

            //on verifie si l'utilisateur connecté est celui qui a crée l'article
            if(auth()->user()->id !== $article->user_id){
                return response()->json([
                    "Erreur"=> "Vous n'avez pas le droit de modifier cet article"
                ]);
            }

            //dans le cas contraire on passe par la validation
            $validator = Validator::make($request->all(),[
                'title'=> 'required|string|max:50',
                'content'=> 'required|string',
                'slug'=> 'required|string'
            ]);

            //si la validation échoue
            if($validator->fails()){
                return response()->json([
                    'error'=>$validator->errors()
                ]);
            }

            //dans le cas contraire on met à jour l'article
            $article->update($validator->validated());
            return new ArticleResource($article);

        //on capture l'erreur
        }catch(\Exception $exception){
            return response()->json([
                'error'=>$exception->getMessage()
            ],500);
        }
    }

    public function destroy($id){
        $article = Article::findOrFail($id);
        if(auth()->user()->id !== $article ->user_id){
            return response()->json([
                "Error"=>"Vous n'avez pas le droit de supprimer cet article"
            ],403);
        }else{
            $article->delete();
            return response()->json([
                "Message"=>"Article supprimé"
            ]);
        }
    }

}
