<template>
  <div class="comment-section" v-if="postId!=''">

    <div class="modal-header-post" v-if="loadingModal == false">
      <nuxt-link :to="`/profile/${dataModal.user_id}`">
        <div class="user-block">
          <img
            class="img-circle"
            :src="dataModal.avatar  | getImgUrl('avatar','sm_avatar')"
          />
          <span class="username">
              {{dataModal.name}}
            </span>
          <span class="description">
                  <timeago :datetime="dataModal.created_at" :auto-update="10"></timeago>
                </span>
        </div>
      </nuxt-link>


      <div v-if="page_name == 'account'">
        <div @click="deletePost()" class="btn-delete-post" v-if="deletePostLoading==true">
          <i class="fa fa-trash-o" aria-hidden="true"></i> Delete
        </div>
        <div class="btn-delete-post" v-else>
          <i class="fa fa-trash-o" aria-hidden="true"></i> Delete...
        </div>
      </div>
      <div v-else>
        <div @click="savePost()" class="btn-save-post" v-if="savePostLoading==true">
          <i class="fa fa-download" aria-hidden="true"></i> Save
        </div>
        <div class="btn-save-post" v-else>
          <i class="fa fa-download" aria-hidden="true"></i> Save...
        </div>
      </div>
    </div>

    <div class="post-modal post-modal-content">
      <div v-if="loadingModal" class="loading">
        <div class="loader"></div>
      </div>
      <div v-if="loadingModal == false">
        <div v-if="dataModal.photo!='no'" class="thumbnail">
          <img v-lazy="getImgUrl(dataModal.photo, 'photo', 'm_post')"
          />
        </div>
        <div style="padding: 10px;">
          <div class="post_property">
            <small class="post_view_num text-muted">{{numView}} views · {{lastRead}} last read</small>
            <a href="#comment"><small style="float: right;"
                                      class="post_view_num">{{numComment}} comments</small></a>
          </div>
          <p style="white-space: pre-line; word-break: break-all;" class="caption">
            <span v-html="dataModal.caption"></span>
          </p>

          <div class="c_post">
            <div class="box-comments" style="display: block;">
              <p style="border-bottom: 1px solid #ddd;padding-bottom: 9px;">
                <i class="fa fa-comments-o" aria-hidden="true"></i>
                Comments
              </p>

              <div v-for="(item, $index) in dataModalComment" :key="$index" class="box-comment">
                <img class="img-circle img-sm" :src="item.avatar  | getImgUrl('avatar','sm_avatar')">
                <div class="comment-text">
                  <small class="username">
                    {{item.name}}
                    <span class="text-muted pull-right">
            <timeago :datetime="item.created_at" :auto-update="10"></timeago>
          </span>
                  </small>
                  <small>{{item.comments}}</small>
                </div>
              </div>
              <div class="text-center" v-if="numComment==0">
                <i class="fa fa-comments-o" style="font-size: 75px; color: #ccc;" aria-hidden="true"></i>
                <p style="margin-bottom: -5px; font-weight: bold; color: #aaa;">No any comments yet</p>
                <small>Be the first to comment</small>
              </div>

              <div class="box-comment text-center" v-if="loadingModalComment==true">
                <p>loading...</p>
              </div>
              <p id="comment"></p>
              <br/>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="create-comment">
      <div class="post-modal c_post" v-show="loadingModal == false">
        <div style="border: 0;padding-bottom: 10px !important;" class="box-footer">
          <form @submit.prevent="createComment">
            <img class="img-responsive img-circle img-sm avatar-comment" :src="user.avatar | getImgUrl('avatar','sm_avatar')"
                 alt="Alt Text">
            <div class="img-push">
              <input @click="goto('comment')" required v-model="comment" type="text" class="form-control input-sm input-box"
                     placeholder="Press enter to post comment">
              <input type="hidden" v-model="postId"/>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
    import myfilter, {getImgUrl} from "../plugins/myfilter";

    export default {
        props: {
            postId:null,
            page_name: null,
        },
        watch: {
            postId: {
                immediate: true,
                handler (postId) {
                    this.listComment(postId);
                    this.getPostDetail(postId);
                }
            }
        },
        data() {
            return {
                comment: '',
                dataModalComment: '',
                loadingModalComment: false,
                dataModal: '',
                loadingModal: true,
                numView: 0,
                numComment: 0,
                lastRead: null,
                savePostLoading: true,
                deletePostLoading: true,
            }
        },
        methods:{
            goto(id){
                document.getElementById(id).scrollIntoView();
            },
            async savePost(){
                this.savePostLoading = false;
                this.$axios
                    .post('post/save-post', {
                        id: this.postId
                    })
                    .then(({data}) => {
                        if(data.status == true){
                            this.$swal.fire(
                                data.msg,
                                'success'
                            );
                        }else{
                            this.$swal.fire(
                                data.msg,
                                'error'
                            )
                        }
                        this.savePostLoading = true

                    })
            },
            async deletePost(){

                this.$swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {

                        this.deletePostLoading = false;

                        this.$axios
                            .post('post/delete-post', {
                                id: this.postId
                            })
                            .then(({data}) => {
                                if(data.status == true){
                                    this.$swal.fire(
                                        data.msg,
                                        'success'
                                    );
                                    location.reload();
                                }else{
                                    this.$swal.fire(
                                        data.msg,
                                        'error'
                                    )
                                }

                                this.deletePostLoading = true

                            })
                    }else{
                        this.deletePostLoading = true
                    }
                });
            },
            async createComment() {
                this.loadingModalComment = true
                let rawData = {
                    comment: this.comment,
                    id: this.postId
                }
                rawData = JSON.stringify(rawData)
                let formData = new FormData();
                formData.append('data', rawData);
                try {
                    this.comment = '';
                    let response = await this.$axios.post('create-comment', formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }).then(({data}) => {
                        if (data.status) {
                            this.dataModalComment.push(data.data[0]);
                            this.loadingModalComment = false;
                            this.goto('comment');
                            this.numComment = this.dataModalComment.length;
                        }
                    })
                } catch (e) {
                    console.log(e);
                    this.loadingModalComment = false
                    return;
                }
            },
            getImgUrl(image, type, size){
                return getImgUrl(image, type, size);
            },
            async getPostDetail(id) {
                this.loadingModal = true;
                this.dataModal = '';
                this.$router.push({ to: this.$route.fullPath, hash: '#post' });
                this.$root.$emit('bv::show::modal', 'post-modal')
                this.$axios.get('post/detail/' + id).then(({data}) => {
                    if (data) {
                        this.dataModal = data['detail'][0]
                        this.numView = data['num_view'][0].num_view;
                        this.numComment = data['num_comment'][0].num_comment
                        this.lastRead = data['last_read']
                        this.loadingModal = false
                    }
                });
            },
            async listComment(id) {
                this.loadingModalComment = true;
                this.dataModalComment = '';
                this.$axios.get('post/detail-comment/' + id).then(({data}) => {
                    if (data) {
                        this.dataModalComment = data;
                        this.loadingModalComment = false
                    }
                })
            }
        }
    }
</script>
<style scoped>

</style>
