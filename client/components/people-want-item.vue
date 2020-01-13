<template>
  <div class="feed">

    <div style="background: rgb(255, 255, 255); height: 56px; margin-bottom: 10px; box-shadow: rgba(0, 0, 0, 0.1) 0px 2px 4px 0px; padding: 15px; position: fixed; width: 100%; top: 0; z-index: 1;">
      <div style="float: left;" @click="$router.go(-1)">
        <i class="fa fa-arrow-left" aria-hidden="true"></i>
      </div>
      <ul class="reaction-filter">
        <li :class="filterType == 'other-want' ? 'other-want' : ''" @click="filterWantList('other-want')">អ្នកចង់បាន</li>
        <li :class="filterType == 'he-want' ? 'he-want' : ''" @click="filterWantList('he-want')">គាត់ចង់បាន</li>
      </ul>
    </div>

    <div class="container c_post" style="margin-top: 67px;">

      <b-modal class="fullscreen" id="post-modal" hide-title="true" no-enforce-focus>
        <post-modal
          :postId="postId" :page_name="page_name"
        />
      </b-modal>

        <ul class="media-list">
          <li class="media" v-for="(item, $index) in feeds" :key="$index" :data-num="$index + 1">
            <p class="header">    
              <nuxt-link :to="`/profile/${item.user_id}`">         
              <img
                :src="item.user_avatar  | getImgUrl('avatar','sm_avatar')"
                alt="User Image"
              />
              <span class="username">{{item.user_name}}</span>
              </nuxt-link>

              <span class="text-muted pull-right">
                                    <small class="text-muted"><timeago :datetime="item.want_date" :auto-update="10"></timeago></small>
                                </span>
            </p>
            <img @click="showPostModal(item.post_id)"
                 class="post-image"
                 :src="item.photo | getImgUrl('photo','m_post')"
                 alt="User Image"
            />
            <div class="media-body">
              <p @click="showPostModal(item.post_id)">{{item.caption | truncate(90, '...')}}</p>   
              <span class="text-muted">
                                    <small class="text-muted"><timeago :datetime="item.post_date" :auto-update="10"></timeago></small>
                                </span>
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
        components:{
            PostModal
        },
        data() {
            return {
                page: 1,
                feeds: [],
                filterType: null,
                postId: null,
                filterType: 'other-want',
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
                    .get('user/people-want-list', {
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
            filterWantList(type) {
                this.filterType = type;
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
ul.media-list {
    padding: 0;
  }

  .media {
    display: -webkit-box;
    display: grid;
    -webkit-box-align: start;
    align-items: flex-start;
  }

  ul.media-list .media {
    background: #fff;
    padding: 13px;
    margin-bottom: 5px;
  }

  ul.media-list .media img.post-image {
    width: 100px;
    height: 120px;
    border-radius: 5px;
    margin-right: 10px;
    text-shadow: 0 0 black;
    float: left;
  }
.media-body {
    -webkit-box-flex: 1;
    flex: 1;
    float: left;
    margin-left: 110px;
    margin-top: -108px;
  }
  .media .header{
    border-bottom: 1px solid #f7f7f7;
    padding-bottom: 7px;
    margin-bottom: 10px;
    color:#929292;
  }
  .media .footer {
    border-top: 1px solid #f7f7f7;
    margin-top: 14px;
    padding-top: 5px;
    margin-bottom: 0;
  }
  .media-body p{
    margin-bottom: 0 !important;
  }

  .menu-active {
    background: #eaeaea !important;
  }
  
  ul.reaction-filter {
    padding: 0;
    list-style: none;
    text-align: center;
    margin-top: 3px;
  }
  ul.reaction-filter li {
    display: inline;
    padding: 3px 10px;
    font-weight: 400;
  }
  ul.reaction-filter li.other-want,ul.reaction-filter li.he-want {
    color: rgba(0, 119, 255, 0.781)
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
