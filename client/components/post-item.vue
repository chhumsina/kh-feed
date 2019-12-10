<template>
  <div class="feed">

    <nav v-if=" this.$route.params.id == undefined && this.$route.name == 'feed' "
         class="navbar navbar-expand-lg navbar-light bg-white top-nav">
      <div class="container">
        <div class="has-search" style="width: 100%">
          <span class="fa fa-search form-control-feedback"></span>
          <input
            type="text"
            class="form-control"
            placeholder="Search Post"
            v-on:keyup.enter="searchFeed" v-model="search"
          />
        </div>
      </div>
    </nav>


    <div class="container c_post">

      <nuxt-link to="/create-post" v-if=" this.$route.params.id == undefined && this.$route.name == 'feed' ">
        <div class="box-footer"
             style="padding: 25px 10px;display: block; margin-bottom: 10px; border-top: 1px solid #ccc; border-bottom: 1px solid #aaa;">
          <img style="margin-top: 0px; height: 35px !important; width: 35px !important;"
               class="img-responsive img-circle img-sm" :src="user.avatar | getImgUrl('avatar','sm_avatar')"
               alt="Alt Text">
          <div class="img-push">
            <input style="border-radius: 25px !important;background: #fafafa;" type="text" class="form-control input-sm"
                   placeholder="What's you want to share?">
          </div>
        </div>
      </nuxt-link>

      <b-modal id="post-modal" scrollable>
        <template v-slot:modal-title>
          <div class="post-modal">
            <nuxt-link :to="`/profile/${dataModal.user_id}`">
              <div class="user-block" v-if="loadingModal == false">
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
          </div>
        </template>
        <div class="post-modal post-modal-content">
          <div v-if="loadingModal" class="loading">
            <div class="loader"></div>
          </div>
          <div v-if="loadingModal == false">
            <div class="photo-content">
              <img
                class="photo"
                :src="dataModal.photo  | getImgUrl('photo','m_post')"
              />
            </div>
            <div class="post_property">
              <span class="post_view_num text-muted">{{numView.num_view}} views · {{lastRead}} last read</span>
              <span class="post_view_num"></span>
              <a href="#comment"><span style="float: right;"
                                       class="post_view_num">{{numComment.num_comment}} comments</span></a>
            </div>
            <p style="white-space: pre-line;" class="caption">
              <span v-html="dataModal.caption"></span>
            </p>

            <div class="c_post">
              <div id="comment" class="box-footer box-comments" style="display: block;">
                <p style="border-bottom: 1px solid #ddd;padding-bottom: 9px;"><i class="fa fa-comments-o"
                                                                                 aria-hidden="true"></i> Comments</p>
                <div class="box-footer"
                     style="display: block; padding: 0; background: none; border-bottom: 1px solid #ddd; padding-bottom: 15px; margin-bottom: 8px;">
                  <form @submit.prevent="createComment">
                    <img class="img-responsive img-circle img-sm" :src="user.avatar | getImgUrl('avatar','sm_avatar')"
                         alt="Alt Text">
                    <div class="img-push">
                      <input required v-model="comment" type="text" class="form-control input-sm"
                             placeholder="Press enter to post comment">
                      <input type="hidden" v-model="postId"/>
                    </div>
                  </form>
                </div>
                <p class="text-center" v-if="loadingModalComment==true">Loading...</p>
                <div v-for="(item, $index) in dataModalComment" :key="$index" class="box-comment">
                  <img class="img-circle img-sm" :src="item.avatar  | getImgUrl('avatar','sm_avatar')">
                  <div class="comment-text">
                    <small class="username">
                      {{item.name}}
                      <span class="text-muted pull-right">
            <timeago :datetime="item.created_at" :auto-update="10"></timeago>
          </span>
                    </small>
                    {{item.comments}}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <template v-slot:modal-footer>
          <div class="post-modal c_post" v-show="loadingModal == false">
            <div class="post_property">

              <div @click="savePost()" class="btn-save-post" v-if="savePostLoading==true">
                <i class="fa fa-download" aria-hidden="true"></i> Save
              </div>
              <div class="btn-save-post" v-else>
                <i class="fa fa-download" aria-hidden="true"></i> Save...
              </div>

            </div>
          </div>
        </template>
      </b-modal>

      <div v-for="(item, $index) in feeds" :key="$index" :data-num="$index + 1" class="box box-widget">
        <nuxt-link :to="`/profile/${item.user_id}`">
          <div class="box-header with-border">
            <div class="user-block">
              <img
                class="img-circle"
                :src="item.avatar  | getImgUrl('avatar','sm_avatar')"
                alt="User Image"
              />
              <span class="username">{{item.name}}</span>
              <span class="description">
              <timeago :datetime="item.created_at" :auto-update="10"></timeago>
            </span>
            </div>
          </div>
        </nuxt-link>
        <div class="box-body" @click="showPostModal(item.post_id)">
          <p style="margin-bottom: 5px; white-space: pre-line;" class="caption">
            {{item.caption | truncate(150, '...')}}
          </p>

          <div class="post-img">
            <img
              class="attachment-img"
              :src="item.photo | getImgUrl('photo','m_post')"
              alt="Attachment Image"
            />
          </div>

        </div>
        <div class="box-footer" style="display: block;" @click="showPostModal(item.post_id)">

          <img class="img-responsive img-circle img-sm" :src="user.avatar | getImgUrl('avatar','sm_avatar')"
               alt="Alt Text">
          <div class="img-push">
            <input type="text" class="form-control input-sm" placeholder="Press enter to post comment">
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
                if (to.hash == '') {
                    this.$modal.hide('post-modal')
                }
            }
        },
        computed: {
            postModal: function () {
                return this.dataModal
            }
        },
        methods: {
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
                            this.dataModalComment.unshift(data.data[0]);
                            this.loadingModalComment = false
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
            getImgUrl(type, image) {
                return `${process.env.baseUrl}image/${type}/${image}`;
            },
            async onInfinite($state) {
                var id = this.$route.params.id;
                var auth_id = this.$root.user.id;
                if (this.$route.name == 'account') {
                    var id = auth_id;
                }

                this.$axios
                    .get('post/list', {
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
                // this.$router.push({ name: 'feed', hash: '#post' });
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

                        this.savePostLoading = true

                    })
            }
        }
    }
</script>

<style scoped>

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
