<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Comment;
use DB;
use App\Http\Resources\Article as ArticleResource;
use Carbon\Carbon;

class ArticleController extends Controller
{
    public function getArticleById($id){
        $article=Article::find($id)->with('comments','tags')->first();
        // return $article;
        return view('admin_article');
    }


    public function getArticleData($id){
        $article=Article::where('article_id',$id)->with('tags')->get();
        return $article;         
    }


    public function articlesByCategory($category,$is_search=false){
        $where_clause=[['category_name',$category]];
        
        if($is_search){
            $where_clause=[['title','LIKE',"%".$category."%"],['body','LIKE',"%".$category."%"]];
        }

        $articles=Article::join('categories_articles','categories_articles.article_id','articles.article_id')
            ->join('categories','categories.category_id','categories_articles.category_id')
            ->where($where_clause)
            ->select('articles.*',
                DB::raw("(SELECT count(*) from article_likes where article_likes.article_id=articles.article_id) as total_likes"),
                DB::raw("(SELECT count(*) from comments where comments.article_id=articles.article_id) as total_comments"))
            ->orderBy('article_id','desc')
            ->groupBy('article_id')
            ->get();
            
        foreach($articles as &$a){
            $a->human_readable_time=Carbon::createFromTimeStamp($a->created_at_t)->diffForHumans();
        }
        return $articles;
    }

    public function searchArticle($keyword){
        return $this->articlesByCategory($keyword,true);
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
