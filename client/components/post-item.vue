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
             style="padding: 25px 10px;display: block; margin-bottom: 10px; border-top: 1px solid #dcdcdc; border-bottom: 1px solid #dcdcdc;">
          <img style="margin-top: 0px; height: 35px !important; width: 35px !important;"
               class="img-responsive img-circle img-sm" :src="user.avatar | getImgUrl('avatar','sm_avatar')"
               alt="Alt Text">
          <div class="img-push">
            <div style="padding-top: 8px;border-radius: 25px !important;background: #fafafa;"
                 class="form-control input-sm input-box">
             I want to share Book Giveaway...
            </div>
          </div>
        </div>
      </nuxt-link>

      <div v-if="userTop.length>1 && this.$route.name == 'feed'">
        <h6 style="padding: 15px; padding-bottom: 10px; padding-top: 10px; color: #555;" class="alert-heading"><i
          class="fa fa-users" aria-hidden="true"></i> User by most view </h6>
        <div class="slide-profile">
          <swiper :options="swiperOption" :data="userTop">
            <swiper-slide v-for="(item, $index) in userTop" :key="$index">
              <nuxt-link :to="`/profile/${item.id}`">
                <img
                  class="img-circle"
                  :src="item.avatar  | getImgUrl('avatar','m_avatar')"
                  alt="User Image"
                />
                <p style="font-size: 15px;">{{item.name}}</p>
              </nuxt-link>
            </swiper-slide>
          </swiper>
        </div>
      </div>

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
          <p v-if="item.photo=='no'" style="margin-bottom: 5px; white-space: unset; word-break: break-all;"
             class="caption">
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

          <img class="img-responsive img-circle img-sm avatar-comment"
               :src="user.avatar | getImgUrl('avatar','sm_avatar')"
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
        components: {
            PostModal
        },
        data() {
            return {
                swiperOption: {
                    slidesPerView: 5,
                    spaceBetween: 50,
                    // init: false,
                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true
                    },
                    breakpoints: {
                        768: {
                            slidesPerView: 3,
                            spaceBetween: 5
                        }
                    }
                },
                userTop: [],
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
                if (to.hash == '' || to.hash == '#post') {
                    this.$root.$emit('bv::hide::modal', 'post-modal');
                }
            }
        },
        computed: {
            postModal: function () {
                return this.dataModal
            },
        },
        created() {
            this.listUserByTopView();
        },
        methods: {
            getImgUrl(image, type, size) {
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
                if (this.search.length >= 2 || this.search.length == 0) {
                    this.page = 1;
                    this.feeds = [];
                    this.onInfinite();
                } else {
                    this.$swal.fire(
                        'Please enter more than 2 characters!'
                    );
                }
            },
            async showPostModal(id) {
                this.postId = id;
                this.$router.push({to: this.$route.fullPath, hash: '#post'});
                this.$root.$emit('bv::show::modal', 'post-modal');
            },
            async listUserByTopView() {
                if(this.$route.name == 'feed'){
                    this.$axios
                        .get('user/list-user-by-top-view')
                        .then(({data}) => {
                            if (data.length) {
                                this.userTop.push(...data);
                            }
                        });
                }
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

  .slide-profile {
    margin-bottom: 18px;
    text-align: center;
  }

  .slide-profile .swiper-slide {
    background: #fff;
    height: 150px;
    text-align: center;
    padding: 13px 0;
    box-shadow: 0 1px 1px #bbb;
  }

  .slide-profile .swiper-slide img.img-circle {
    margin-bottom: 4px;
  }

  .more-menu {
    text-align: center;
  }

  ul.more-menu-item {
    padding: 0;
    list-style: none;
    display: inline-flex;
  }

  ul.more-menu-item li {
    width: 60px;
    background: #fff;
    margin-left: 5px;
    /* border-radius: 38px; */
    height: 60px;
    padding: 6px;
    box-shadow: 0 1px 1px #aaaaaaa6;
    padding-top: 8px;
  }

  ul.more-menu-item li i {
    font-size: 27px;
  }

  ul.more-menu-item li p {
    font-size: 12px;
    margin-bottom: -10px;
    margin-top: -5px;
  }
  .swiper-container {
    margin-left: auto;
    margin-right: auto;
    position: relative;
    overflow: hidden;
    list-style: none;
    padding: 0;
    z-index: 1;
    padding: 1px 0;
  }
</style>
