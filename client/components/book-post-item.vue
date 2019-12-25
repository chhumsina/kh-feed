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
            placeholder="Search Book"
            v-on:keyup.enter="searchFeed" v-model="search"
          />
        </div>
      </div>
    </nav>


    <div class="container c_post">

      <div v-if="userTop.length>1">
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
<!--            <swiper-slide>-->
<!--              View allow-->
<!--            </swiper-slide>-->
          </swiper>
        </div>
      </div>
      <b-alert show variant="success">
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

      <ul class="list">
        <li v-for="(item, $index) in feeds" :key="$index" :data-num="$index + 1" v-if="item.photo!='no'">
          <div class="thumbnail">
            <img class="img" v-lazy="getImgUrl(item.photo, 'photo', 'm_post')"/>
            <div class="poster">
              <img
                class="img-circle"
                :src="item.avatar  | getImgUrl('avatar','sm_avatar')"
                alt="User Image"
              />
              <small class="price">$5</small>
            </div>
          </div>
          <div class="footer-img">
            <p class="title">{{item.caption | truncate(25, '...')}}</p>
            <div class="sub-footer">
                <small><timeago :datetime="item.created_at" :auto-update="10"></timeago></small>
            </div>
          </div>
        </li>
      </ul>

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
  .list {
    list-style: none;
    flex-wrap: wrap;
    display: flex;
    padding: 0;
    margin: 0;
  }

  .list li {
    flex: 1 0 30%;
    padding: 0px;
    margin-bottom: 15px;
    text-align: center;
    color: #000;
    background: #fff;
    box-shadow: 0 1px 1px #ddd;
    border-radius: 3px;
  }

  .list li:nth-child(3n + 1) {
    /*background: blue;*/
    margin-right: 2%;
    margin-left: 2%;
  }

  .list li:nth-child(3n + 2) {
    /*background: orange;*/
  }

  .list li:nth-child(3n + 3) {
    /*background: green;*/
    margin-right: 2%;
    margin-left: 2%;
  }
  .list .thumbnail{
    position: relative;
  }
  .list .thumbnail .img{
    width: 100%;
    height: 150px;
    border-top-left-radius: 3px;
    border-top-right-radius: 3px;
  }
  .list .footer-img .title{
    font-size: 13px;
    margin-bottom: -7px;
  }
  .list .footer-img small time{
    font-size: 11px;
    color: #999;
  }
  .list .thumbnail .poster {
    height: 30px;
    background: #fdfdfd5c;
    position: absolute;
    bottom: 0px;
    width: 100%;
  }
  .list .thumbnail .poster img {
    width: 25px;
    height: 25px;
    float: left;
    margin-top: 2px;
    margin-left: 2px;
  }

  .list .thumbnail .poster .price {
    float: right;
    position: absolute;
    font-size: 16px !important;
    right: 4px;
    top: 2.5px;
    font-weight: bold;
    text-shadow: 0 1px 2px #555;
    color: #f7a121;
  }
  .list li .footer-img {padding-bottom: 5px;padding-top: 5px;}
  .list li .sub-footer {
    margin-top: 5px;
  }

</style>
