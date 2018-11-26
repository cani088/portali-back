<template>
    <div>
        <h2>
            Articles
        </h2>
        <form @submit.prevent="addArticle">
            <div class="form-group">
                <input type="text" class='form-control col-md-4 col-sm-12' placeholder='Title' v-model='article.title'/>
            </div>
            <div class="form-group">
                <textarea type="textarea" class='form-control col-md-8 col-sm-12' placeholder='Body' v-model='article.body'/>
            </div>
            <button type='submit' class='btn btn-light btn-block col-md-2 mb-2 col-sm-12'>
                Save
            </button>

        </form>

        <nav aria-label="Page navigation example">
            <ul class="pagination">
                <li v-bind:class="[{disabled: !pagination.prev_page_url}]" class="page-item">
                    <a @click="fetchArticles(pagination.prev_page_url)" class="page-link" href="#">Previous</a>
                </li>

                <li class="page-item disabled">
                    <a class="page-link text-dark" href="#">Page {{pagination.current_page}} of  {{pagination.last_page}}</a>
                </li>

                <li v-bind:class="[{disabled: !pagination.next_page_url}]" class="page-item">
                    <a @click="fetchArticles(pagination.next_page_url)" class="page-link" href="#">Next</a>
                </li>
            </ul>
        </nav>
        
        <div class='card card-body mb-2' v-for='article in articles' v-bind:key="article.id">
            <h3>{{article.title}}</h3>
            <p>{{article.body}}</p>
            <button class='btn btn-danger col-md-2 col-sm-12' @click="deleteArticle(article.id)">Delete</button>
        </div>
    </div>
</template>

<script>
export default {
    data(){
        return {
            articles:[],
            article:{
                id:'',
                title:'',
                body:'',
                ss:''
            },
            article_id:'',
            pagination:{},
            edit:false
        }
    },
    created(){
        this.fetchArticles();
    },

    methods:{
        fetchArticles(page_url){
            let vm=this;
            page_url=page_url || '/articles';
            fetch(page_url)
            .then(res=>res.json())
            .then(res=>{
                this.articles=res.data;
                vm.makePagination(res.meta,res.links);
            })
            .catch(err=>console.log(err));
        },

        makePagination(meta, links){
            let pagination={
                current_page:meta.current_page,
                last_page:meta.last_page,
                next_page_url:links.next,
                prev_page_url:links.prev
            };

            this.pagination=pagination;
        },

        deleteArticle(id){
            if(confirm('Are you sure you want to delete this article')){
                axios.post(`/article/delete`,{
                    "article_id":id
                })
                .then(res=>{
                    console.log(res.data.message);
                    this.fetchArticles();
                })
                .catch(err=>console.log(err));
            }
        },

        addArticle(){
            axios.post('article/add',{
                title:this.article.title,
                body:this.article.body
            }).then(res=>{
                console.log(res.data.message);
                this.fetchArticles();
                this.article.title='';
                this.article.body='';
            });
        }
    }
}
</script>
