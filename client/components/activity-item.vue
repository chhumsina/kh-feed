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

      <b-modal class="fullscreen" id="post-modal" hide-title="true">
        <post-modal
          :postId="postId" :page_name="page_name"
        />
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
    import PostModal from '../components/PostModal';

    export default {
        components:{
            PostModal
        },
        data() {
            return {
                page: 1,
                feeds: [],
                search: null,
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
                this.$router.push({ to: this.$route.fullPath, hash: '#post' });
                this.$root.$emit('bv::show::modal', 'post-modal');
            }
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
