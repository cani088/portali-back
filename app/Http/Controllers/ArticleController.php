<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Comment;
use DB;
use App\Http\Resources\Article as ArticleResource;

class ArticleController extends Controller
{
    public function getArticleById($id){
        $article=Article::find($id)->with('comments','tags')->first();
        // return $article;
        return view('admin_article');
    }


    public function getArticleApi($id){
        $article=Article::find($id)->with('comments','tags')->first();
        return $article;         
    }


    public function articles(){
        DB::enableQueryLog();
        $articles=Article::orderBy('article_id','desc')->with('comments','tags')->paginate(2);
        return ArticleResource::collection($articles);
    }

    public function delete(Request $request){
        
        Article::findOrFail($request->article_id)->delete();
        return response()->json(['message'=>'Article removed','success'=>1]);
    }

    public function add(Request $request){
        $article=new Article;
        $article->title=$request->title;
        $article->body=$request->body;
        $article->created_at_t=time();
        if($article->save()){
            return response()->json(['message'=>'Article Inserted','success'=>1,'article'=>$article]);
        }
    }
}