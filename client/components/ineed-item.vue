<template>
  <div class="feed">

    <nav
         class="navbar navbar-expand-lg navbar-light bg-white top-nav">
      <div class="container">
        <div class="has-search" style="width: 100%">
          <span class="fa fa-search form-control-feedback"></span>
          <input
            type="text"
            class="form-control input-box"
            placeholder="Search INeed"
            v-on:keyup.enter="searchFeed" v-model="search"
          />
        </div>
      </div>
    </nav>


    <div class="container c_post">

      <b-modal class="fullscreen" id="post-modal" hide-title="true" no-enforce-focus>
        <post-modal
          :postId="postId" :page_name="page_name"
        />
      </b-modal>
      <p class="text-center" style="padding: 5px; color:#bbb;padding-bottom: 0;">List of the book which you request to Contributors</p>
      <div v-for="(item, $index) in feeds" :key="$index" :data-num="$index + 1" class="box box-widget">
          <div class="box-header with-border" @click="showPostModal(item.post_id)">
            <div class="user-block">
              <img
                class="img-circle"
                :src="item.photo | getImgUrl('photo','sm_post')"
                alt="User Image"
              />
              <span class="username">{{item.caption | truncate(35, '...')}}</span>
              <span class="description">
              <timeago :datetime="item.post_date" :auto-update="10"></timeago>
            </span>
              <div v-bind:class="item.accept_status" style="float: right; margin-top: -46px; text-align: center">
                <p style="margin-top: 3px; margin-bottom: -4px;">{{item.accept_status}}</p>
                <p style="font-size: 11px; color: rgb(156, 156, 156); margin-top: 9px; margin-bottom: 0;"><timeago :datetime="item.created_at" :auto-update="10"></timeago></p>
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
                    .get('post/i-need-request-list', {
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
