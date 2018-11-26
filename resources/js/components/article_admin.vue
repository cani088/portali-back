<template>
    <div>
        <h2>
            {{article.title}}
        </h2>
        <div class='card card-body mb-2'>
            <p>{{article.body}}</p>
            <p>{{article.created_at}}</p>
            <h3>Comments:</h3>
            <div class='card-body mb-2' v-for='comment in article.comments' v-bind:key="article.comments.comment_id">
                <h3>{{comment.comment_body}}</h3>
            </div>
            <h3>Tags:</h3>
            <div class='card-body mb-2' v-for='tag in article.tags' v-bind:key="article.tags.tag">
                <h3>{{tag.tag_body}}</h3>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data(){
        return {
            article:[],
            articleInterface:{
                id:'',
                title:'',
                body:'',
                ss:'',
                comments:[],
                tags:[]
            },
            article_id:'',
        }
    },

    created(){
        this.fetchArticles(1);
    },

    methods:{
        fetchArticles(id){
            let vm=this;
            // page_url='api/article/'+id;
            fetch('/api/article/'+id)
            .then(res=>res.json())
            .then(res=>{
                this.article=res;
                console.log('title',this.article.title)
                // console.log('res',res);
            })
            .catch(err=>console.log(err));
        },


    }
}
</script>
