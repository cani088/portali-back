<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Carbon;
use DB;
use App\Models\Comment;



class CommentsController extends Controller
{
    public function getArticleComments(Request $request,$id){
        $user_id=1;
        // DB::enableQueryLog();
        $comments= Comment::where('article_id',$id)
			->leftJoin('users','users.user_id','comments.user_id')
            ->select('users.username','users.user_id','comments.*',
            DB::raw("(SELECT count(*) from comment_likes where user_id=$user_id and comment_likes.comment_id=comments.comment_id) as is_like"),
            DB::raw("(SELECT count(*) from comment_likes where comment_likes.comment_id=comments.comment_id) as total_likes")
            )
            ->get();
        foreach($comments as &$a){
            $a->human_readable=Carbon\Carbon::createFromTimeStamp($a->created_at_t)->diffForHumans();
        }

        return $comments;
    }

    public function addComment(Request $request){
        $depth=0;

        if($request->parent>0){
            $depth=Comment::findOrFail($request->parent)->depth;
        }

        $comment=new Comment;

        $comment->comment_body=$request->comment_body;
        $comment->article_id=$request->article_id;
        $comment->depth=(int) $depth++;
        $comment->parent=$request->parent;
        $comment->created_at_t=time();

        $comment->save();

        return response()->json(['comment'=>$comment,'success'=>1]);
    }
}
