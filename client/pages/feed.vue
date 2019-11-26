<template>
  <div class="container feed">
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
          <p>
            Far far away, behind the word mountains, far from the countries
            Vokalia and Consonantia, there live the blind texts. Separated they
            live in Bookmarksgrove right at
          </p>
          <p>
            Far far away, behind the word mountains, far from the countries
            Vokalia and Consonantia, there live the blind texts. Separated they
            live in Bookmarksgrove right at
          </p>
          <p>
            Far far away, behind the word mountains, far from the countries
            Vokalia and Consonantia, there live the blind texts. Separated they
            live in Bookmarksgrove right at
          </p>
          <p>
            Far far away, behind the word mountains, far from the countries
            Vokalia and Consonantia, there live the blind texts. Separated they
            live in Bookmarksgrove right at
          </p>
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
            :src="getImgUrl('avatar',item.avatar)"
            alt="User Image"
          />
          <span class="username"
          ><a href="#">{{item.name}}</a></span
          >
          <span class="description">Shared publicly - 7:30 PM Today</span>
        </div>
        <div class="box-tools">
          <button
            type="button"
            class="btn btn-box-tool"
            data-toggle="tooltip"
            title=""
            data-original-title="Mark as read"
          >
            <i class="fa fa-circle-o"></i>
          </button>
          <button type="button" class="btn btn-box-tool" data-widget="collapse">
            <i class="fa fa-minus"></i>
          </button>
          <button type="button" class="btn btn-box-tool" data-widget="remove">
            <i class="fa fa-times"></i>
          </button>
        </div>
      </div>
      <div class="box-body">
        <p>
          {{item.caption}}
        </p>

        <div class="attachment-block clearfix">
          <img
            class="attachment-img"
            :src="getImgUrl('photo',item.photo)"
            alt="Attachment Image"
          />
          <div class="attachment-pushed">
            <h5 class="attachment-heading">
              <a href="http://www.lipsum.com/">
                {{item.title}}
              </a>
            </h5>
          </div>
        </div>
        <button type="button" class="btn btn-default btn-xs">
          <i class="fa fa-share"></i> Share
        </button>
        <button type="button" class="btn btn-default btn-xs">
          <i class="fa fa-thumbs-o-up"></i> Like
        </button>
      </div>
    </div>
    <infinite-loading
      @infinite="onInfinite"
      spinner="bubbles"
      ref="infiniteLoading"
    ></infinite-loading>
    <br/>
    <br/>
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
            getImgUrl(type, image) {
                return `${process.env.baseUrl}image/${type}/${image}`;
            },
            async logout() {
                await this.$auth.logout()
            },
            async onInfinite($state) {
                this.$axios
                    .get('post/list', {
                        params: {
                            page: this.page
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

  .box-widget {
    border: none;
    position: relative;
  }

  .box {
    position: relative;
    border-radius: 2px;
    background: #ffffff;
    border-top: 3px solid #ffffff;
    margin-top: 10px;
    width: 100%;
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
  }

  .box-header.with-border {
    border-bottom: 1px solid #f4f4f4;
  }

  .box-header {
    color: #444;
    display: block;
    padding: 10px;
    position: relative;
  }

  .user-block img {
    width: 40px;
    height: 40px;
    float: left;
  }

  .user-block .username {
    font-size: 16px;
    font-weight: 600;
  }

  .user-block .description {
    color: #999;
    font-size: 13px;
  }

  .user-block .username,
  .user-block .description,
  .user-block .comment {
    display: block;
    margin-left: 50px;
  }

  .box-header > .box-tools {
    position: absolute;
    right: 10px;
    top: 5px;
  }

  .btn-box-tool {
    padding: 5px;
    font-size: 12px;
    background: transparent;
    color: #97a0b3;
  }

  .box-body {
    border-top-left-radius: 0;
    border-top-right-radius: 0;
    border-bottom-right-radius: 3px;
    border-bottom-left-radius: 3px;
    padding: 10px;
  }

  .pad {
    padding: 10px;
  }

  .box .btn-default {
    background-color: #f4f4f4;
    color: #444;
    border-color: #ddd;
  }

  .box-comments {
    background: #f7f7f7 !important;
  }

  .box-footer {
    border-top-left-radius: 0;
    border-top-right-radius: 0;
    border-bottom-right-radius: 3px;
    border-bottom-left-radius: 3px;
    border-top: 1px solid #f4f4f4;
    padding: 10px;
    background-color: #fff;
  }

  .box-comments .box-comment:first-of-type {
    padding-top: 0;
  }

  .box-comments .box-comment {
    padding: 8px 0;
    border-bottom: 1px solid #eee;
  }

  .img-sm,
  .box-comments .box-comment img,
  .user-block.user-block-sm img {
    width: 30px !important;
    height: 30px !important;
  }

  .img-sm,
  .img-md,
  .img-lg,
  .box-comments .box-comment img,
  .user-block.user-block-sm img {
    float: left;
  }

  .box-comments .comment-text {
    margin-left: 40px;
    color: #555;
  }

  .box-comments .username {
    color: #444;
    display: block;
    font-weight: 600;
  }

  .box-comments .text-muted {
    font-weight: 400;
    font-size: 12px;
  }

  .img-sm + .img-push {
    margin-left: 40px;
  }

  .box .form-control {
    border-radius: 0;
    box-shadow: none;
    border-color: #d2d6de;
  }

  .attachment-block {
    border: 1px solid #f4f4f4;
    padding: 5px;
    margin-bottom: 10px;
    background: #f7f7f7;
  }

  .attachment-block .attachment-img {
    max-width: 100px;
    max-height: 100px;
    height: auto;
    float: left;
  }

  .attachment-block .attachment-pushed {
    margin-left: 110px;
  }

  .attachment-block .attachment-heading {
    margin: 0;
  }

  .attachment-block .attachment-heading .h4,
  .attachment-block .attachment-heading h4 {
    font-size: 18px;
  }

  .attachment-block .attachment-text {
    color: #555;
  }
</style>
