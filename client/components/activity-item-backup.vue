<template>
  <div class="feed">


    <div style="background: #fff;
    height: 56px;
    margin-bottom: 10px;
    box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1);
    padding: 15px;">
      <p>Activity for  <small class="badge">View post</small> <small>and</small> <small class="badge">Comment</small></p>
    </div>

    <div class="container c_post">

      <nuxt-link to="/create-post" v-if=" this.$route.params.id == undefined && this.$route.name == 'feed' ">
        <div class="box-footer"
             style="padding: 25px 10px;display: block; margin-bottom: 10px; border-top: 1px solid #ccc; border-bottom: 1px solid #aaa;">
          <img style="margin-top: 0px; height: 35px !important; width: 35px !important;"
               class="img-responsive img-circle img-sm" :src="user.avatar | getImgUrl('avatar','sm_avatar')"
               alt="Alt Text">
          <div class="img-push">
            <input style="border-radius: 25px !important;background: #fafafa;" type="text" class="form-control input-sm input-box"
                   placeholder="What's you want to share?">
          </div>
        </div>
      </nuxt-link>

      <b-modal class="fullscreen" id="post-modal">
        <template v-slot:modal-title>
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
            <div @click="savePost()" class="btn-save-post" v-if="savePostLoading==true">
              <i class="fa fa-download" aria-hidden="true"></i> Save
            </div>
            <div class="btn-save-post" v-else>
              <i class="fa fa-download" aria-hidden="true"></i> Save...
            </div>
          </div>
        </template>
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
                <small class="post_view_num text-muted">{{numView.num_view}} views · {{lastRead}} last read</small>
                <a href="#comment"><small style="float: right;"
                                          class="post_view_num">{{numComment.num_comment}} comments</small></a>
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
        <template v-slot:modal-footer>
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
        </template>
      </b-modal>

      <div v-for="(item, $index) in feeds" :key="$index" :data-num="$index + 1" class="box box-widget">
          <div class="box-header with-border" @click="showPostModal(item.post_id)">
            <div class="user-block">
              <img
                v-if="item.photo!='no'"
                class="img-circle"
                :src="item.photo | getImgUrl('photo','sm_post')"
                alt="User Image"
              />
              <span class="username" :class="item.photo!='no' ? '' : 'margin-left-0'">{{item.caption | truncate(35, '...')}}</span>
              <span class="description" :class="item.photo!='no' ? '' : 'margin-left-0'">
                {{item.description | truncate(20, '...')}}
                -
              <timeago :datetime="item.activity_date" :auto-update="10"></timeago>
            </span>
            </div>
          </div>
      </div>
    </div>
    <infinite-loading
      @infinite="onInfinite"
      spinner="bubbles"
      ref="infiniteLoading"
    ></infinite-loading>
    <br/>
    <br/>
    <br/>
  </div>
</template>

<script>
    import myfilter, {getImgUrl} from "../plugins/myfilter";

    export default {
        data() {
            return {
                page: 1,
                feeds: [],
                search: null,
                dataModal: '',
                dataModalComment: '',
                loadingModal: true,
                loadingModalComment: true,
                comment: '',
                postId: null,
                numView: 0,
                numComment: 0,
                lastRead: null,
                savePostLoading: true
            }
        },
        watch: {
            $route(to, from) {
                // if the current history index isn't at the last position, use 'back' transition
                console.log(to.hash);
                if (to.hash == '' || to.hash=='#post') {
                    this.$root.$emit('bv::hide::modal', 'post-modal');
                }
            }
        },
        computed: {
            postModal: function () {
                return this.dataModal
            }
        },
        methods: {
            getImgUrl(image, type, size){
                return getImgUrl(image, type, size);
            },
            goto(id){
                document.getElementById(id).scrollIntoView();
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
                            this.numComment.num_comment = this.dataModalComment.length;
                        }
                    })
                } catch (e) {
                    console.log(e);
                    this.loadingModalComment = false
                    return;
                }
            },
            itemsContains(text, word) {
                return text.includes(word);
            },
            async onInfinite($state) {
                var id = this.$route.params.id;
                var auth_id = this.$root.user.id;
                if (this.$route.name == 'account') {
                    var id = auth_id;
                }

                this.$axios
                    .get('post/activity-list', {
                        params: {
                            page: this.page,
                            id: id,
                            search: this.search
                        }
                    })
                    .then(({data}) => {
                        if (data.length) {
                            this.page += 1
                            this.feeds.push(...data);
                            $state.loaded();
                        } else {
                            $state.complete();
                        }
                    })
            },
            searchFeed() {
                if (this.search.length >= 2) {
                    this.page = 1;
                    this.feeds = [];
                    this.onInfinite();
                }
            },
            async showPostModal(id) {
                this.postId = id;
                this.loadingModal = true;
                this.loadingModalComment = true;
                this.dataModal = '';
                this.dataModalComment = '';
                this.$router.push({ to: this.$route.fullPath, hash: '#post' });
                this.$root.$emit('bv::show::modal', 'post-modal')
                this.$axios.get('post/detail/' + id).then(({data}) => {
                    if (data) {
                        this.dataModal = data['detail'][0]
                        this.numView = data['num_view'][0]
                        this.numComment = data['num_comment'][0]
                        this.lastRead = data['last_read']
                        this.loadingModal = false
                    }
                });
                this.$axios.get('post/detail-comment/' + id).then(({data}) => {
                    if (data) {
                        this.dataModalComment = data;
                        this.loadingModalComment = false
                    }
                })
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
        }
    }
</script>

<style scoped>

  .c_post .box {
    margin-bottom: -2px;
  }

  .feed .top-nav {
    font-weight: 600;
    box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1);
    position: fixed;
    width: 100%;
    z-index: 2;
    top: 0;
  }

  .feed .has-search .form-control {
    background: #fafafa;
    padding-left: 2.375rem;
    border: 1px solid #efefef;
    color: #bbb;
    border-radius: 20px;
    width: 100%;
  }

  .feed .has-search .form-control-feedback {
    position: absolute;
    z-index: 2;
    display: block;
    width: 2.375rem;
    height: 2.375rem;
    line-height: 2.375rem;
    text-align: center;
    pointer-events: none;
    color: #aaa;
  }
</style>
