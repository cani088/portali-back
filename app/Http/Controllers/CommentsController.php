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
            ->select('users.user_name','users.user_id','comments.*',
            DB::raw("(SELECT count(*) from comment_likes where user_id=$user_id and comment_likes.comment_id=comment_id) as is_like"))
            ->get();
        foreach($comments as &$a){
            $a->human_readable=Carbon\Carbon::createFromTimeStamp($a->created_at_t)->diffForHumans();
        }

        return $comments;
    }
}
