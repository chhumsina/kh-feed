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
            placeholder="ស្វែងរក សៀវភៅ"
            v-on:keyup.enter="searchFeed" v-model="search"
          />
        </div>
      </div>
    </nav>

    <div class="container c_post">

      <div class="book-announce" v-if="show_shop_annoucement==true && user.level==10"> 
        <div v-if="getShopLoading==true">
          Loading...
        </div>
        <div v-else>
          <div v-if="shop==null">
            <h6 class="alert-heading"><i class="fa fa-book-o" aria-hidden="true"></i> Are you a book seller? </h6>
            <ul>
              <li @click="show_shop_annoucement=false" style="color: red;">No</li>
              <li style="font-weight: 100;">or</li>
              <li @click="show_create_shop=true" style="color: green;">Yes</li>
            </ul>
            <div class="btn-become-book-seller" v-if="show_create_shop==true">
              <nuxt-link :to="`/create-shop`">
                Create Shop
              </nuxt-link>
            </div>
          </div>
          <div v-else class="row" style="margin-left: 0; margin-right: 0;">
              <div class="col" style="border-right: 1px solid #ddd;">
                <nuxt-link :to="`/create-shop`" style="color:#555">
                  <img style="height: 70px !important; margin-bottom: 3px;"
                    class="img-circle"
                    :src="shop.avatar  | getImgUrl('page','m_page')"
                    alt="Shop Image"
                  />
                  <p style="margin-bottom: 0;">{{shop.name | truncate(20, '...')}}</p>
                </nuxt-link>
              </div>
              <div class="col" style="position: relative;">
                <nuxt-link :to="`/sell-book`" style="padding: 14px; font-weight: bold; color: #2f8be0; position: absolute; width: 100%; left: 0; top: 24px; margin: 0 auto;">
                  <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                  Sell book
                </nuxt-link>
              </div>
          </div>
        </div>

      </div>

      <b-modal class="fullscreen" id="book-modal" hide-title="true">
        <book-modal
          :bookId="bookId" :page_name="page_name"
        />
      </b-modal>

      <!-- <hr> -->
      <ul class="list">
        <li v-for="(item, $index) in feeds" :key="$index" :data-num="$index + 1"  @click="showPostModal(item.id)">
          <div class="thumbnail">
            <img class="img" v-lazy="getImgUrl(item.photo, 'book', 'sm_book')"/>
            <div class="poster">
              <img
                class="img-circle"
                :src="item.avatar  | getImgUrl('page','sm_page')"
                alt="Page Image"
              />
              <small class="price">{{item.price}} {{item.currency}} </small>
            </div>
          </div>
          <div class="footer-img">
            <p class="title">{{item.name | truncate(25, '...')}}</p>
            <div class="sub-footer">
                <small><timeago :datetime="item.created_at" :auto-update="10"></timeago></small>
            </div>
          </div>
        </li>
        <li style="visibility: hidden"></li>
        <li style="visibility: hidden"></li>
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
    import BookModal from '../components/BookModal';

    export default {
        components: {
            BookModal
        },
        data() {
            return {
                page: 1,
                feeds: [],
                search: null,
                bookId: null,
                page_name: this.$route.name,
                show_shop_annoucement: true,
                getShopLoading: true,
                shop: null,
                show_create_shop: false
            }
        },
        watch: {
            $route(to, from) {
                // if the current history index isn't at the last position, use 'back' transition
                console.log(to.hash);
                if (to.hash == '' || to.hash == '#post') {
                    this.$root.$emit('bv::hide::modal', 'book-modal');
                }
            }
        },
        computed: {
            postModal: function () {
                return this.dataModal
            },
        },
        created() {
            this.getShop();
        },
        methods: {
            getImgUrl(image, type, size) {
                return getImgUrl(image, type, size);
            },
            itemsContains(text, word) {
                return text.includes(word);
            },
            async getShop() {
                this.$axios
                    .get('shop/get-shop')
                    .then(({data}) => {
                       if(data.status==true){
                           this.shop = data.data;
                       }
                       this.getShopLoading = false;
                    });
            },
            async onInfinite($state) {
                var id = this.$route.params.id;
                var auth_id = this.$root.user.id;
                if (this.$route.name == 'shop') {
                    var id = auth_id;
                }

                this.$axios
                    .get('book/list', {
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
                this.bookId = id;
                this.$router.push({to: this.$route.fullPath, hash: '#book'});
                this.$root.$emit('bv::show::modal', 'book-modal');
            },
        }
    }
</script>

<style scoped>
  .book-announce ul {
    padding: 0;
    list-style: none;
    display: inline-flex;
  }

  .book-announce ul li {
    padding: 10px;
    font-weight: bold;
  }
  .book-announce {
    background: #fff;
    margin-top: 10px;
    margin-bottom: 10px;
    padding: 13px;
    border-top: 1px solid #ddd;
    border-bottom: 1px solid #ddd;
    text-align: center;
  }
  .btn-become-book-seller {
    background: #a7a6ff;
    font-weight: bold;
    color: #4f4e9e;
    text-align: center;
    width: fit-content;
    padding: 3px 12px;
    border-radius: 3px;
    margin: 0 auto;
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
    background: rgba(255, 255, 255, 0.9);
    position: absolute;
    bottom: -1px;
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
    /*text-shadow: 0 1px 2px #555;*/
    color: #f7a121;
  }
  .list li .footer-img {padding-bottom: 5px;padding-top: 5px;}
  .list li .sub-footer {
    margin-top: 5px;
  }

</style>
