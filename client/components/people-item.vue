<template>
  <div class="feed">
    <nav
         class="navbar navbar-expand-lg navbar-light bg-white top-nav">
      <div class="container">
        <div class="has-search" style="width: 100%">
          <i class="fa fa-search form-control-feedback"></i>
          <input
            type="text"
            class="form-control input-box"
            placeholder="ស្វែងរក អ្នកបរិច្ចាគ"
            v-on:keyup.enter="searchFeed" v-model="search"
          />
        </div>
      </div>
    </nav>

    <div class="container c_post">

      <ul class="list">
        <li v-for="(item, $index) in feeds" :key="$index" :data-num="$index + 1">
          <nuxt-link :to="`/profile/${item.id}`">
            <img
              class="img-circle"
              :src="item.avatar  | getImgUrl('avatar','m_avatar')"
              alt="User Image"
            />
            <p style="font-size: 15px;margin-bottom: 0;margin-top: 7px;">{{item.name}}</p>
            <p style="background: #f9f9f9; font-weight: bold; color: #ecb109; margin-top: 13px;">{{item.num_post}} <small>បរិច្ចាគ</small></p>
          </nuxt-link>
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

    export default {
        components: {
        },
        data() {
            return {
                page: 1,
                feeds: [],
                search: null,
                page_name: this.$route.name,
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
                if (this.$route.name == 'shop') {
                    var id = auth_id;
                }

                this.$axios
                    .get('user/list', {
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
    padding-top: 13px;
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
