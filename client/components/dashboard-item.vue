<template>
  <div class="feed">

    <!--    <nav-->
    <!--         class="navbar navbar-expand-lg navbar-light bg-white top-nav">-->
    <!--      <div class="container">-->
    <!--        <div class="has-search" style="width: 100%">-->
    <!--          <span class="fa fa-search form-control-feedback"></span>-->
    <!--          <input-->
    <!--            type="text"-->
    <!--            class="form-control input-box"-->
    <!--            placeholder="Search I Want"-->
    <!--            v-on:keyup.enter="searchFeed" v-model="search"-->
    <!--          />-->
    <!--        </div>-->
    <!--      </div>-->
    <!--    </nav>-->

    <div
      style="z-index: 1;top: 68px;position: fixed; background: rgb(255, 255, 255); margin-top: -67px; padding: 15px 16px 1px; text-align: center; margin-bottom: 15px; box-shadow: 0 1px 1px #ddd; width: 100%;">
      <img
        :src="placeholder_photo.i_want_banner"
        style="width: 80%;"
      />
      <div class="total-book">
        <ul>
          <li :class="book_filter=='iwant' ? 'menu-active' : ''" @click="dashboardList('iwant','first')">Yes, I want<br/><span>{{totalBook.total_want}} pending</span>
            <p style="margin: 0 auto; font-size: 12px; color: orange; position: absolute; left: 0; right: 0; bottom: 7px;">{{totalBook.total_want_other}} wants now</p></li>
          <li :class="book_filter=='contributes' ? 'menu-active' : ''"  @click="dashboardList('contributes','first')">Contributes<br/><span>{{totalBook.total_contributes}}  open</span>
            <p style="margin: 0 auto; font-size: 12px; color: orange; position: absolute; left: 0; right: 0; bottom: 7px;">{{totalBook.total_want_now}} wants now</p></li>
          <li :class="book_filter=='giving' ? 'menu-active' : ''"  @click="dashboardList('giving','first')">Total Giving<br/><span>{{totalBook.total_giving}}  closed</span>
            <p style="margin: 0 auto; font-size: 12px; color: #0d863d; position: absolute; left: 0; right: 0; bottom: 7px;">{{totalBook.total_want_giving}} wanted</p></li>
          <li :class="book_filter=='getting' ? 'menu-active' : ''"  @click="dashboardList('getting','first')">Total Getting<br/><span>{{totalBook.total_getting}}  closed</span>
            <p style="margin: 0 auto; font-size: 12px; color: #0d863d; position: absolute; left: 0; right: 0; bottom: 7px;">{{totalBook.total_want_getting}} wanted</p></li>
        </ul>
      </div>
  </div>

  <div class="container c_post" style="margin-top: 170px;">

    <b-modal class="fullscreen" id="post-modal" hide-title="true" no-enforce-focus>
      <post-modal
        :postId="postId" :page_name="page_name" :switch_i_want="true"
      />
    </b-modal>

    <p v-if="book_filter=='iwant'" class="text-center" style="padding: 5px; color:#000;padding-bottom: 0;">List of <span class="font-weight-bold">Yes, I want</span> books
      been wanting</p>
    <p v-else-if="book_filter=='contributes'" class="text-center" style="padding: 5px; color:#000;padding-bottom: 0;">List of <span class="font-weight-bold">Contributes</span> books
      for wanted people</p>
    <p v-else-if="book_filter=='giving'" class="text-center" style="padding: 5px; color:#000;padding-bottom: 0;">List of <span class="font-weight-bold">Total Giving</span> books
      that had given</p>
    <p v-else="book_filter=='getting'" class="text-center" style="padding: 5px; color:#000;padding-bottom: 0;">List of <span class="font-weight-bold">Total Getting</span> books
      that had gotten</p>


    <div v-if="book_filter=='iwant'">
      <div v-for="(item, $index) in bookItem" :key="$index" :data-num="$index + 1" class="box box-widget">
        <div class="box-header with-border">
          <div class="user-block">
            <img
              class="img-circle"
              :src="item.photo | getImgUrl('photo','sm_post')"
              alt="User Image"
              @click="showPostModal(item.post_id)"
            />
            <span class="username" @click="showPostModal(item.post_id)">{{item.caption | truncate(35, '...')}}</span>
            <span class="description">
              <timeago :datetime="item.post_date" :auto-update="10"></timeago>
            </span>
            <div v-bind:class="item.accept_status" style="float: right; margin-top: -46px; text-align: center">
              <p style="margin-top: 3px; margin-bottom: -4px;">{{item.accept_status}}</p>
              <p style="font-size: 11px; color: rgb(156, 156, 156); margin-top: 9px; margin-bottom: 0;">
                <timeago :datetime="item.created_at" :auto-update="10"></timeago>
              </p>
            </div>
          </div>
          <div style="margin-top: 10px;" class="font-13">
            {{item.desc | truncate(40, '...')}} <span v-if="item.desc.length>40" @click="showMoreDesc(item.desc)"
                                                      style="color: #2f8be0">more</span>
          </div>
        </div>
      </div>
      <p v-if="bookItemLoading==true" class="btn_load_more" @click="moreDashboardList()">{{load_more_text}}</p>
      <p v-else class="btn_load_more">
        <img :src="placeholder_photo.loader"/>
      </p>

    </div>

    <div v-else-if="book_filter=='contributes'">
      <div v-for="(item, $index) in bookItem" :key="$index" :data-num="$index + 1" class="box box-widget" @click="showPostModal(item.post_id)">
        <div class="box-header with-border">
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
            <div class="open" style="float: right; margin-top: -46px; text-align: center">
              <p style="margin-top: 3px; margin-bottom: -4px;">Open</p>
              <p style="font-size: 11px; color: rgb(156, 156, 156); margin-top: 9px; margin-bottom: 0;">
                <timeago :datetime="item.created_at" :auto-update="10"></timeago>
              </p>
            </div>
          </div>
        </div>
      </div>
      <p v-if="bookItemLoading==true" class="btn_load_more" @click="moreDashboardList()">{{load_more_text}}</p>
      <p v-else class="btn_load_more">
        <img :src="placeholder_photo.loader"/>
      </p>
    </div>

    <br/>
    <br/>
    <br/>
    <br/>

  </div>
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
                book_filter: 'iwant',
                page: 1,
                bookItem: [],
                bookItemLoading: true,
                load_more_text: 'Load more',
                postId: null,
                page_name: this.$route.name,
                totalBook: [],
                placeholder_photo: {
                    i_want_banner: require('../assets/default/book-banner.jpg'),
                    loader: require('../assets/default/loader.gif')
                }
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
            }
        },
        created(){
          this.dashboardList(this.book_filter);
          this.dashboardTotal();
        },
        methods: {
            getImgUrl(image, type, size) {
                return getImgUrl(image, type, size);
            },
            goto(id) {
                document.getElementById(id).scrollIntoView();
            },
            itemsContains(text, word) {
                return text.includes(word);
            },
            moreDashboardList(){
                this.page += 1;
                this.dashboardList(this.book_filter, 'more')
            },
            async dashboardTotal(){
                this.$axios
                    .get('post/dashboard-total')
                    .then(({data}) => {
                        this.totalBook = data[0]
                    })
            },
            async dashboardList(type, loading) {
                if(this.bookItemLoading == false){
                    return false
                }
                this.bookItemLoading = false;

                if(loading == 'first'){
                    this.page = 1;
                    this.bookItem = [];
                    this.load_more_text = 'Load more';
                }

                this.book_filter = type;
                this.$axios
                    .get('post/dashboard', {
                        params: {
                            page: this.page,
                            type: type
                        }
                    })
                    .then(({data}) => {
                        if (data.length) {
                            this.bookItem.push(...data);
                        }else{
                            this.load_more_text = 'No more data!';
                        }

                        this.bookItemLoading=true;
                    })

            },
            async showPostModal(id) {
                this.postId = id;
                this.$router.push({to: this.$route.fullPath, hash: '#post'});
                this.$root.$emit('bv::show::modal', 'post-modal');
            },
            showMoreDesc(desc) {
                this.$swal.fire(
                    'Your description:',
                    desc
                )
            }
        }
    }
</script>

<style scoped>
  .menu-active{
    background: #eaeaea !important;
  }
  .btn_load_more{
    text-align: center;
    margin-top: 27px;
    font-weight: bold; color: #303440;
  }
  .total-book ul {
    padding: 0;
    list-style: none;
    margin-bottom: 4px;
  }

  .total-book ul li {
    text-align: center;
    display: inline-grid;
    padding: 11px 10px 19px 6px;
    color: #888;
    font-size: 14px;
    background: #f7f7f7;
    margin-bottom: 5px;
    border-radius: 4px;
    position: relative;
    height: 82px;
  }

  .total-book {margin-top: 11px;}

  .total-book ul li span {
    font-weight: bold;
    color: #4c94ff;
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
