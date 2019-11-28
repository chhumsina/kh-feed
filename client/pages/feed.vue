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

    <b-modal id="post-modal" scrollable>
      <template v-slot:modal-title>
        <div class="post-modal">
          <div class="user-block" v-show="loadingModal == false">
            <img
              class="img-circle"
              :src="dataModal.avatar  | getImgUrl('avatar','sm_avatar')"
            />
            <span class="username">
              {{dataModal.name}}
            </span>
            <span class="description">{{dataModal.created_at}}</span>
          </div>
        </div>
      </template>
      <div class="post-modal post-modal-content">
        <div v-show="loadingModal" class="loading">
          <div class="loader"></div>
        </div>
        <div v-show="loadingModal == false">
          <h5 class="title">
            {{dataModal.title}}
          </h5>
          <div class="photo-content">
            <img
              class="photo"
              :src="dataModal.photo  | getImgUrl('photo','m_post')"
            />
          </div>
          <p class="caption">
            {{dataModal.caption}}
          </p>
        </div>
      </div>
      <template v-slot:modal-footer>
        <div class="post-modal" v-show="loadingModal == false">
          <div class="download-files" v-if="dataModal.num_download_file > 0">
            <p style="
    float: left;
    left: 12px;
    position: absolute;
    text-align: center;
    font-size: 13px;
    ">Download <br><span style="font-size: 18px;color: #ad0140d1;font-weight: bold;">Free</span></p>
            <ul>
              <li class="file-item" v-for="file in (dataModal.files.split(','))">
                <i v-on:click="downloadFile(file)" v-if="itemsContains(file,'.pdf')" class="fa fa-file-pdf-o" aria-hidden="true"></i>
                <i v-on:click="downloadFile(file)"  v-if="itemsContains(file,'.doc') || itemsContains(file,'.docx')" class="fa fa-file-word-o"
                   aria-hidden="true"></i>
                &nbsp;
                <span v-on:click="downloadFile(file)">{{file | truncate(45, '...')}}</span>
              </li>
            </ul>
          </div>
        </div>

        <!--        <nuxt-link :to="`/post/${dataModal.id}`">-->
        <!--          <button class="nav-item">-->
        <!--            <i class="fa fa-gift" aria-hidden="true"></i> Download files <small class="text-muted">and</small> Donate-->
        <!--            creator-->
        <!--          </button>-->
        <!--        </nuxt-link>-->
      </template>
    </b-modal>

    <div class="container">
      <div
        v-for="(item, $index) in feeds"
        :key="$index"
        :data-num="$index + 1"
        class="box box-widget"
        @click="showPostModal(item.post_id)"
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
                <i v-if="itemsContains(file,'.doc') || itemsContains(file,'.docx')" class="fa fa-file-word-o"
                   aria-hidden="true"></i>
                <i v-if="itemsContains(file,'.pdf')" class="fa fa-file-pdf-o" aria-hidden="true"></i>
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
            downloadFile($file){
                $file = $file.trim();
                this.$axios
                    .get('file/'+$file)
                    .then((response) => {
                        const url = window.URL.createObjectURL(new Blob([response.data]));
                        const link = document.createElement('a');
                        link.href = url;
                        link.setAttribute('download', $file);
                        document.body.appendChild(link);
                        link.click();
                    })
            },
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
                if (this.search.length >= 5) {
                    this.page = 1;
                    this.feeds = [];
                    this.infiniteId += 1;
                    this.onInfinite();
                }
            },
            async onInfinite() {
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
                        }
                    })
            },
            async showPostModal(id) {
                this.loadingModal = true;
                this.dataModal = '';
                // this.$router.push({ name: 'feed', hash: '#post' });
                this.$root.$emit('bv::show::modal', 'post-modal')
                this.$axios.get('post/detail/' + id).then(({data}) => {
                    if (data) {
                        this.dataModal = data[0]
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

  .feed .download-files {
    text-align: right;
  }

  .feed .download-files ul .file-item {
    display: inline-block;
    padding: 0px 3px;
  }

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
