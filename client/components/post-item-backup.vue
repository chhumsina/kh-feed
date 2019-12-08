<template>
  <div class="feed">

    <nav v-if=" this.$route.params.id == undefined && this.$route.name == 'feed' "
         class="navbar navbar-expand-lg navbar-light bg-white top-nav">
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

    <div class="container c_post">


      <b-modal id="post-modal" scrollable>
        <template v-slot:modal-title>
          <div class="post-modal">
            <nuxt-link :to="`/profile/${dataModal.user_id}`">
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
            </nuxt-link>
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
                  <i v-on:click="downloadFile(file)" v-if="itemsContains(file,'.pdf')" class="fa fa-file-pdf-o"
                     aria-hidden="true"></i>
                  <i v-on:click="downloadFile(file)" v-if="itemsContains(file,'.doc') || itemsContains(file,'.docx')"
                     class="fa fa-file-word-o"
                     aria-hidden="true"></i>
                  &nbsp;
                  <span v-on:click="downloadFile(file)">{{file | truncate(45, '...')}}</span>
                </li>
              </ul>
            </div>
          </div>
        </template>
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
              {{item.created_at}}
            </span>
            </div>
          </div>
        </nuxt-link>
        <div class="box-body" @click="showPostModal(item.post_id)">
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
        spinner="bubbles"
        ref="infiniteLoading"
      ></infinite-loading>
      <br/>
      <br/>
      <br/>
    </div>
  </div>
</template>

<script>
    export default {
        data() {
            return {
                page: 1,
                feeds: [],
                search: null,
                dataModal: '',
                loadingModal: true
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
        computed: {
            postModal: function () {
                return this.dataModal
            }
        },
        methods: {
            downloadFile($file) {
                $file = $file.trim();
                this.$axios
                    .get('file/' + $file)
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
                if (this.search.length >= 5) {
                    this.page = 1;
                    this.feeds = [];
                    this.onInfinite();
                }
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
</style>
