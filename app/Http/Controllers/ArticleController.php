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
        $article=Article::where('article_id',$id)
            ->select('articles.*',
                DB::raw("(SELECT count(*) from article_likes where article_likes.article_id=articles.article_id) as total_likes"),
                DB::raw("(SELECT count(*) from comments where comments.article_id=articles.article_id) as total_comments"),
                DB::raw("(SELECT like_type from article_likes where article_likes.article_id=articles.article_id and article_likes.user_id=1) as like_type")
            )
            ->with('tags')
            ->first();
        $article->human_readable=Carbon::createFromTimeStamp($article->created_at_t)->diffForHumans();        
        return $article;         
    }

    public function articlesByCategory(Request $request,$category,$is_search=false){
        // $user_id=$request->user->user_id;
        $user_id=1;
        $articles=Article::join('categories_articles','categories_articles.article_id','articles.article_id')
            ->join('categories','categories.category_id','categories_articles.category_id')
            ->where(function($query) use ($category,$is_search){
                if($is_search){
                    $query->where('articles.title','LIKE',"%".$category."%")
                        ->orWhere('articles.body','LIKE',"%".$category."%");
                }else{
                    $query->where('category_name',$category);
                }
            })
            ->select('articles.*',
                DB::raw("(SELECT count(*) from article_likes where article_likes.article_id=articles.article_id) as total_likes"),
                DB::raw("(SELECT like_type from article_likes where article_likes.article_id=articles.article_id and user_id=$user_id) as total_likes"),
                DB::raw("(SELECT count(*) from comments where comments.article_id=articles.article_id) as total_comments"))
            ->groupBy('articles.article_id')
            ->orderBy('articles.article_id','desc')
            ->get();
        foreach($articles as &$a){
            $a->human_readable_time=Carbon::createFromTimeStamp($a->created_at_t)->diffForHumans();
            $a->article_date=Carbon::createFromTimeStamp($a->article_date)->format('d.m.Y');
            //temp
            $a->is_like=1;
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
    
    public function likeArticle(Request $request){
        return self::likeUnlikeArticle($request,1,0);
    }

    public function unLikeArticle(Request $request){
        return self::likeUnlikeArticle($request,0,1);
    }

    
    public static function likeUnlikeArticle(Request $request,$type,$oposite){
        // $user_id=$request->user->user_id;
        $user_id=1;
        //1 like
        //0 unlike
        $status=DB::table('article_likes')
            ->where([['user_id',$user_id],['article_id',$request->article_id],['like_type',$type]])
            ->count();
    
        if($status>0){
            return response()->json(['success'=>0]);        
        }    

        //delete existing like or dislike from the user on the article
        DB::table('article_likes')
            ->where(['user_id'=>$user_id,'article_id'=>$request->article_id,'like_type'=>$oposite])
            ->delete();

        //add like/dislike to article
        DB::table('article_likes')
            ->insert(['user_id'=>$user_id,'article_id'=>$request->article_id,'like_type'=>$type]);
        
        return response()->json(['success'=>1]);     
    }


    public function removeVote(Request $request){
        $user_id=1;
        DB::table('article_likes')->where([['user_id',$user_id],['article_id',$request->article_id]])->delete();
        return response()->json(['success'=>1]);    
    }
}
