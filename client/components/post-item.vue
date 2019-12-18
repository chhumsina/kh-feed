<template>
  <div class="feed">

    <nav v-if=" this.$route.params.id == undefined && this.$route.name == 'feed' "
         class="navbar navbar-expand-lg navbar-light bg-white top-nav">
      <div class="container">
        <div class="has-search" style="width: 100%">
          <span class="fa fa-search form-control-feedback"></span>
          <input
            type="text"
            class="form-control input-box"
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
            <div style="padding-top: 8px;border-radius: 25px !important;background: #fafafa;" class="form-control input-sm input-box">
              What's you want to share?
            </div>
          </div>
        </div>
      </nuxt-link>

      <b-alert show variant="success"  v-if=" this.$route.params.id == undefined && this.$route.name == 'feed' ">
        <h6 class="alert-heading"><i class="fa fa-check-circle-o" aria-hidden="true"></i> Great Post should be: </h6>
        <div>
          <ul>
            <li><small>Short with meaningful</small></li>
            <li><small>Useful thing</small></li>
            <li><small>Interesting topic</small></li>
            <li><small>Be able to help people</small></li>
          </ul>
        </div>
      </b-alert>

      <b-modal class="fullscreen" id="post-modal" hide-title="true">
        <post-modal
          :postId="postId" :page_name="page_name"
        />
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
          <p v-if="item.photo=='no'" style="margin-bottom: 5px; white-space: unset; word-break: break-all;" class="caption">
            {{item.caption | truncate(250, '...')}}
          </p>
          <p v-else style="margin-bottom: 5px; white-space: unset; word-break: break-all;" class="caption">
            {{item.caption | truncate(70, '...')}}
          </p>

          <div class="post-img" v-if="item.photo!='no'">
            <div class="thumbnail">
              <img v-lazy="getImgUrl(item.photo, 'photo', 'm_post')"/>
            </div>
          </div>

        </div>
        <div class="box-footer" style="display: block;" @click="showPostModal(item.post_id)">

          <img class="img-responsive img-circle img-sm avatar-comment" :src="user.avatar | getImgUrl('avatar','sm_avatar')"
               alt="Alt Text">
          <div class="img-push">
            <div type="text" class="form-control input-sm input-box">
              Press enter to post comment
            </div>
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
                this.$router.push({ to: this.$route.fullPath, hash: '#post' });
                this.$root.$emit('bv::show::modal', 'post-modal');
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
