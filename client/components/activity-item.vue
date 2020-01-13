<template>
  <div class="feed">

    <div style="background: rgb(255, 255, 255); height: 56px; margin-bottom: 10px; box-shadow: rgba(0, 0, 0, 0.1) 0px 2px 4px 0px; padding: 15px; position: fixed; width: 100%; top: 0; z-index: 1;">
      <ul class="activity-filter">
        <li :class="filterType == 'comment' ? 'comment' : ''" @click="filterReactionList('comment')">Comment</li>
        <li :class="filterType == 'profile' ? 'profile' : ''" @click="filterReactionList('profile')">Profile</li>
        <li :class="filterType == 'post' ? 'post' : ''" @click="filterReactionList('post')">Post</li>
<!--        <li :class="filterType == 'recommend' ? 'recommend' : ''" @click="filterReactionList('recommend')">Recom.</li>-->
      </ul>
    </div>

    <div class="container c_post" style="margin-top: 67px;">

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

      <b-modal class="fullscreen" id="post-modal" hide-title="true" no-enforce-focus>
        <post-modal
          :postId="postId" :page_name="page_name"
        />
      </b-modal>

      <div v-for="(item, $index) in feeds" :key="$index" :data-num="$index + 1" class="box box-widget">
        <div v-if="item.type=='profile'" class="box-header with-border">
          <nuxt-link :to="`/profile/${item.post_id}`">
          <div class="user-block">
            <img
              class="img-circle"
              :src="item.photo | getImgUrl('avatar','sm_avatar')"
              alt="User Image"
            />
            <span class="description " v-bind:class="item.type">
                {{item.description | truncate(20, '...')}}
                -
              <timeago :datetime="item.activity_date" :auto-update="10"></timeago>
            </span>
            <small style="margin-left: 10px;"><span>Profile:</span> {{item.caption}}</small>
          </div>
          </nuxt-link>
        </div>
        <div v-else class="box-header with-border" @click="showPostModal(item.post_id)">

          <div class="user-block" v-if="item.photo!='no'">
            <img
              class="img-circle"
              :src="item.photo | getImgUrl('photo','sm_post')"
              alt="User Image"
            />
            <span class="description " v-bind:class="item.type">
                {{item.description | truncate(20, '...')}}
                -
              <timeago :datetime="item.activity_date" :auto-update="10"></timeago>
            </span>
            <small style="margin-left: 10px;"><span>Post:</span> {{item.caption | truncate(30, '...')}}</small>
          </div>
          <div class="user-block" v-else>
            <span style="margin-left: 0"  class="description " v-bind:class="item.type">
                {{item.description | truncate(20, '...')}}
                -
              <timeago :datetime="item.activity_date" :auto-update="10"></timeago>
            </span>
            <small><span>Post:</span> {{item.caption | truncate(30, '...')}}</small>
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
    import PostModal from '../components/PostModal';

    export default {
        components:{
            PostModal
        },
        data() {
            return {
                page: 1,
                feeds: [],
                filterType: null,
                postId: null,
                page_name: this.$route.name,
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
                            filter_type: this.filterType
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
            filterReactionList(type) {
                if(type == this.filterType){
                    this.filterType = null;
                }else{
                    this.filterType = type;
                }
                this.page = 1;
                this.feeds = [];
                this.onInfinite();
            },
            async showPostModal(id) {
                this.postId = id;
                this.$router.push({ to: this.$route.fullPath, hash: '#post' });
                this.$root.$emit('bv::show::modal', 'post-modal');
            }
        }
    }
</script>

<style scoped>
  ul.activity-filter {
    padding: 0;
    list-style: none;
    text-align: center;
    margin-top: 3px;
  }
  ul.activity-filter li {
    display: inline;
    padding: 3px 10px;
    font-weight: 400;
  }
  span.description.recommend, ul.activity-filter .recommend {
    color: #1648ff !important;
  }
  span.description.comment, ul.activity-filter .comment {
    color: #6a9e26 !important;
  }
  span.description.post, ul.activity-filter .post {
    color: #35bfbd !important;
  }
  span.description.profile, ul.activity-filter .profile {
    color: #dc3545 !important;
  }

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
