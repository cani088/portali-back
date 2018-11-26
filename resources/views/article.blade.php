<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{$article->title}}</title>
</head>
<body>
    <div class="body">
        {{$article->body}}
        <p>Time: {{\Carbon\Carbon::createFromTimeStamp($article->created_at_t)->diffForHumans()}}</p>
        @foreach($article->comments as $comment)
            <p>{{$comment->comment_body}}</p>
        @endforeach
        @foreach($article->tags as $tag)
            <p>{{$tag->tag_body}}</p>
        @endforeach
    </div>
</body>
</html>