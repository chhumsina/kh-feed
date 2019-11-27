<template>
  <div class="feed">
    <nav class="navbar navbar-expand-lg navbar-light bg-white top-nav">
      <div class="container">
        <div class="has-search" style="width: 100%">
          <span class="fa fa-search form-control-feedback"></span>
          <input
            type="text"
            class="form-control"
            placeholder="Search Post"
            @input="searchFeed" v-model="search"
          />
        </div>
      </div>
    </nav>

    <div class="container">
      <b-modal id="post-modal" scrollable>
        <template v-slot:modal-title>
          <div class="user-block">
            <img
              class="img-circle"
              src="https://bootdey.com/img/Content/avatar/avatar1.png"
              alt="User Image"
            />
            <span class="username"><a href="#">Jonathan Burke Jr</a></span>
            <span class="description">Shared publicly - 7:30 PM Today</span>
          </div>
        </template>
        <div v-show="loadingModal" class="loading">
          <div class="loader"></div>
        </div>
        <div class="box box-widget" v-show="loadingModal == false">
          <div class="box-body">
            <p>
              Far far away, behind the word mountains, far from the countries
              Vokalia and Consonantia, there live the blind texts. Separated they
              live in Bookmarksgrove right at
            </p>
            <div class="attachment-pushed">
              <h5 class="attachment-heading">
                <a href="http://www.lipsum.com/">Lorem ipsum text generator</a>
              </h5>
            </div>
            <div class="attachment-block clearfix">
              <img
                class="attachment-img"
                src="https://lorempixel.com/400/300/nature/4/"
                alt="Attachment Image"
              />
            </div>
            <button type="button" class="btn btn-default btn-xs">
              <i class="fa fa-thumbs-o-up"></i> Like
            </button>
          </div>
        </div>
        <template v-slot:modal-footer>
          <nuxt-link :to="`/post/${dataModal.id}`">
            <button class="nav-item">
              <i class="fa fa-gift" aria-hidden="true"></i> Download files <small class="text-muted">and</small> Donate
              creator
            </button>
          </nuxt-link
          >
        </template>
      </b-modal>

      <div
        v-for="(item, $index) in feeds"
        :key="$index"
        :data-num="$index + 1"
        class="box box-widget"
        @click="showPostModal(item.id)"
      >
        <div class="box-header with-border">
          <div class="user-block">
            <img
              class="img-circle"
              :src="item.avatar  | getImgUrl('avatar','sm_avatar')"
              alt="User Image"
            />
            <span class="username"
            ><a href="#">{{item.name}}</a></span
            >
            <span class="description">
              {{item.created_at}}
            </span>
          </div>
        </div>
        <div class="box-body">
          <p>
            {{item.caption | truncate(150, '...')}}
          </p>

          <div class="attachment-block clearfix">
            <img
              class="attachment-img"
              :src="item.photo | getImgUrl('photo','sm_post')"
              alt="Attachment Image"
            />
            <div class="attachment-pushed">
              <p class="attachment-heading">
                {{item.title | truncate(60, '...')}}
              </p>
            </div>
          </div>
          <div class="download-files" v-if="item.num_download_file > 0">
            <ul>
              <li class="file-item">Download:</li>
              <li class="file-item" v-for="file in (item.files.split(','))">
                <i v-if="itemsContains(file,'.pdf')" class="fa fa-file-pdf-o" aria-hidden="true"></i>
                <i v-if="itemsContains(file,'.doc') || itemsContains(file,'.docx')" class="fa fa-file-word-o" aria-hidden="true"></i>
              </li>
            </ul>
          </div>
<!--          not yet -->
<!--          <hr class="hr-divider"/>-->
<!--          <button type="button" class="btn btn-default btn-xs">-->
<!--            <i class="fa fa-thumbs-o-up"></i> Like-->
<!--          </button>-->
<!--          <button type="button" class="btn btn-default btn-xs">-->
<!--            <i class="fa fa-thumbs-o-down"></i> Unlike-->
<!--          </button>-->
        </div>
      </div>
      <infinite-loading
        @infinite="onInfinite"
        @identifier="infiniteId"
        spinner="bubbles"
        ref="infiniteLoading"
      ></infinite-loading>
      <br/>
      <br/>
    </div>
  </div>
</template>

<script>

    export default {
        middleware: 'auth',
        data() {
            return {
                strategy: this.$auth.$storage.getUniversal('strategy'),
                page: 1,
                feeds: [],
                search: null,
                infiniteId: +new Date(),
                dataModal: '',
                loadingModal: true
            }
        },
        computed: {
            postModal: function () {
                return this.dataModal
            }
        },
        watch: {
            $route(to, from) {
                // if the current history index isn't at the last position, use 'back' transition
                if (to.hash == '') {
                    this.$modal.hide('post-modal')
                }
            }
        },
        mounted() {
        },
        methods: {
            itemsContains(text, word) {
                return text.includes(word);
            },
            getImgUrl(type, image) {
                return `${process.env.baseUrl}image/${type}/${image}`;
            },
            async logout() {
                await this.$auth.logout()
            },
            searchFeed() {
                if(this.search.length >= 5){
                    this.page = 1;
                    this.feeds = [];
                    this.infiniteId += 1;
                    this.onInfinite();
                }
            },
            async onInfinite($state) {
                this.$axios
                    .get('post/list', {
                        params: {
                            page: this.page,
                            search: this.search
                        }
                    })
                    .then(({data}) => {
                        if (data.length) {
                            this.page += 1
                            this.feeds.push(...data)
                            $state.loaded()
                        } else {
                            $state.complete()
                        }
                    })
            },
            async showPostModal(id) {
                this.loadingModal = true
                // this.$router.push({ name: 'feed', hash: '#post' });
                this.$root.$emit('bv::show::modal', 'post-modal')
                this.$axios.get('post/detail/' + id).then(({data}) => {
                    if (data) {
                        this.dataModal = data
                        this.loadingModal = false
                    }
                })
            }
        }
    }
</script>

<style scopped>

  .feed {
    margin-top: 67px;
  }

  .feed .download-files ul {
    list-style: none;
    padding: 0;
  }
  .feed .download-files{
    text-align: right;
  }
  .feed .download-files ul .file-item {display: inline;}
  .feed .download-files ul .file-item i {
    color: brown;
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

  .feed box-widget {
    border: none;
    position: relative;
  }

  .feed .box {
    position: relative;
    border-radius: 2px;
    background: #ffffff;
    border-top: 3px solid #ffffff;
    margin-top: 10px;
    width: 100%;
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
  }

  .feed .box-header.with-border {
    border-bottom: 1px solid #eee;
  }

  .feed .box-header {
    color: #444;
    display: block;
    padding: 10px;
    position: relative;
  }

  .feed .user-block img {
    width: 40px;
    height: 40px;
    float: left;
  }

  .feed .user-block .username {
    font-size: 16px;
    font-weight: 600;
  }

  .feed .user-block .description {
    color: #999;
    font-size: 13px;
  }

  .feed .user-block .username,
  .user-block .description,
  .user-block .comment {
    display: block;
    margin-left: 50px;
  }

  .feed .box-header > .box-tools {
    position: absolute;
    right: 10px;
    top: 5px;
  }

  .feed .btn-box-tool {
    padding: 5px;
    font-size: 12px;
    background: transparent;
    color: #97a0b3;
  }

  .feed .box-body {
    border-top-left-radius: 0;
    border-top-right-radius: 0;
    border-bottom-right-radius: 3px;
    border-bottom-left-radius: 3px;
    padding: 10px;
  }

  .feed .pad {
    padding: 10px;
  }

  .feed .box .btn-default {
    background-color: #f4f4f4;
    color: #444;
    border-color: #ddd;
  }

  .feed .img-sm + .img-push {
    margin-left: 40px;
  }

  .feed .box .form-control {
    border-radius: 0;
    box-shadow: none;
    border-color: #d2d6de;
  }

  .feed .attachment-block {
    border: 1px solid #f4f4f4;
    padding: 5px;
    margin-bottom: 20px;
    background: #fbfbfb;
  }

  .feed .attachment-block .attachment-img {
    max-width: 100px;
    max-height: 100px;
    height: auto;
    float: left;
  }

  .feed .attachment-block .attachment-pushed {
    margin-left: 110px;
  }

  .feed .attachment-block .attachment-heading {
    margin: 0;
  }

  .feed .attachment-block .attachment-heading .h4,
  .feed .attachment-block .attachment-heading h4 {
    font-size: 18px;
  }

  .feed .attachment-block .attachment-text {
    color: #555;
  }
</style>
