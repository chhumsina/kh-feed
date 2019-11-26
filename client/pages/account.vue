<template>
  <div class="account">
    <div class="container">
      <div class="d-flex justify-content-between align-items-center py-4">
        <div>
          <nuxt-link to="create-post">
            <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Create Post
          </nuxt-link>
        </div>
        <div>
          <a href="#" @click.prevent="logout">
            <i class="fa fa-sign-out"/>
            Logout
          </a>
        </div>
      </div>
    </div>
    <hr class="m-0"/>

    <div class="container">
      <div class="text-center profile">
        <img :src="getImgUrl('avatar',user.avatar)" class="ui-w-100 rounded-circle"
        />

        <div class="col-md-8 col-lg-6 col-xl-5 p-0 mx-auto">
          <h4 class="font-weight-bold my-4"> {{user.name}}</h4>

          <div v-if="user.bio" class="text-muted mb-4">
            <i class="fa fa-quote-left" aria-hidden="true"></i> {{user.bio}} <i class="fa fa-quote-right"
                                                                                aria-hidden="true"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="account-tabs">
      <tiny-tabs
        class="nav nav-tabs tabs-alt justify-content-center"
        id="mytabs"
        :anchor="false"
        :closable="false"
        :hideTitle="true"
        @on-close="onClose"
        @on-before="onBefore"
        @on-after="onAfter"
      >
        <div class="section" id="posts">
          <h3 class="title">Posts</h3>
          <span style="visibility: hidden">Posts</span>
          <div>
            <b-modal id="post-modal" scrollable>
              <template v-slot:modal-title>
                <div class="user-block">
                  <img
                    class="img-circle"
                    src="https://bootdey.com/img/Content/avatar/avatar1.png"
                    alt="User Image"
                  />
                  <span class="username"
                  ><a href="#">Jonathan Burke Jr</a></span
                  >
                  <span class="description"
                  >Shared publicly - 7:30 PM Today</span
                  >
                </div>
              </template>
              <div v-show="loadingModal" class="loading">
                <div class="loader"></div>
              </div>
              <div class="box box-widget" v-show="loadingModal == false">
                <div class="box-body">
                  <p>
                    Far far away, behind the word mountains, far from the
                    countries Vokalia and Consonantia, there live the blind
                    texts. Separated they live in Bookmarksgrove right at
                  </p>
                  <div class="attachment-pushed">
                    <h5 class="attachment-heading">
                      <a href="http://www.lipsum.com/"
                      >Lorem ipsum text generator</a
                      >
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
                    <i class="fa fa-shopping-cart" aria-hidden="true"></i> Buy
                    now
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
                    src="https://bootdey.com/img/Content/avatar/avatar1.png"
                    alt="User Image"
                  />
                  <span class="username"
                  ><a href="#">Jonathan Burke Jr {{ $index }}.</a></span
                  >
                  <span class="description"
                  >Shared publicly - 7:30 PM Today</span
                  >
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
                  <button
                    type="button"
                    class="btn btn-box-tool"
                    data-widget="collapse"
                  >
                    <i class="fa fa-minus"></i>
                  </button>
                  <button
                    type="button"
                    class="btn btn-box-tool"
                    data-widget="remove"
                  >
                    <i class="fa fa-times"></i>
                  </button>
                </div>
              </div>
              <div class="box-body">
                <p>
                  Far far away, behind the word mountains, far from the
                  countries Vokalia and Consonantia, there live the blind texts.
                  Separated they live in Bookmarksgrove right at
                </p>

                <div class="attachment-block clearfix">
                  <img
                    class="attachment-img"
                    src="https://lorempixel.com/400/300/nature/4/"
                    alt="Attachment Image"
                  />
                  <div class="attachment-pushed">
                    <h5 class="attachment-heading">
                      <a href="http://www.lipsum.com/"
                      >Lorem ipsum text generator</a
                      >
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
        </div>
        <div class="section" id="Overview">
          <h3 class="title">Overview</h3>
          <span style="visibility: hidden;">Overview</span>

          <div class="overview-list">
            <form @submit.prevent="overview">

              <div class="input-group mb-3">
                <input v-model="form.name" type="text" class="form-control" :class="{ 'is-invalid': errors.name }"
                       placeholder="Name">
                <div class="input-group-prepend">
                  <span class="input-group-text" id="basic-addon1"><i class="fa fa-user" aria-hidden="true"></i></span>
                </div>
                <div class="invalid-feedback" v-if="errors.name">
                  {{errors.name[0]}}
                </div>
              </div>
              <div class="input-group mb-3">
                <input v-model="form.email" type="text" class="form-control" :class="{ 'is-invalid': errors.email }"
                       placeholder="E-mail">
                <div class="input-group-prepend">
                  <span class="input-group-text" id="basic-addon1"><i class="fa fa-envelope"
                                                                      aria-hidden="true"></i></span>
                </div>
                <div class="invalid-feedback" v-if="errors.email">
                  {{errors.email[0]}}
                </div>
              </div>
              <div class="input-group mb-3">
                <input v-model="form.phone" type="text" class="form-control" :class="{ 'is-invalid': errors.phone }"
                       placeholder="Phone">
                <div class="input-group-prepend">
                  <span class="input-group-text" id="basic-addon1"><i class="fa fa-phone" aria-hidden="true"></i></span>
                </div>
                <div class="invalid-feedback" v-if="errors.phone">
                  {{errors.phone[0]}}
                </div>
              </div>
              <div class="input-group mb-3">
                <input v-model="form.bio" type="text" class="form-control" :class="{ 'is-invalid': errors.bio }"
                       placeholder="Quote">
                <div class="input-group-prepend">
                  <span class="input-group-text" id="basic-addon1"><i class="fa fa-quote-right" aria-hidden="true"></i></span>
                </div>
                <div class="invalid-feedback" v-if="errors.bio">
                  {{errors.bio[0]}}
                </div>
              </div>

              <div class="form-group">
                <input type="submit" value="Save" class="btn btn-light w-100">
              </div>
            </form>
          </div>
        </div>
      </tiny-tabs>
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
                dataModal: '',
                loadingModal: true,
                form: {
                    name: '',
                    email: '',
                    phone: '',
                    bio: '',
                },
                error: this.$route.query.error
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
        created() {
            this.form.email = this.$store.state.auth.user.email
            this.form.bio = this.$store.state.auth.user.bio
            this.form.name = this.$store.state.auth.user.name
            this.form.phone = this.$store.state.auth.user.phone
        },
        methods: {
            async overview() {
                try {
                    // this.$axios.post('update-overview', this.form);
                    this.$axios.post('update-overview', this.form).then(({data}) => {
                        if (data) {
                            location.href = location.href;
                        }
                    })
                } catch (e) {
                    console.log(e);
                    return;
                }
            },
            getImgUrl(type,image) {
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
            },
            onClose(id) {
                console.log(
                    'Callback function that gets evaluated while closing the tab',
                    id
                )
            },
            onBefore(id, tab) {
                console.log(
                    'Callback function that gets evaluated before a tab is activated',
                    id,
                    tab
                )
            },
            onAfter(id, tab) {
                console.log(
                    'Callback function that gets evaluated after a tab is activated',
                    id,
                    tab
                )
            }
        }
    }
</script>

<style>
  .account .ui-w-100 {
    width: 100px !important;
    height: auto;
  }

  .account .font-weight-bold {
    font-weight: 700 !important;
  }

  .account .tinytabs .tabs {
    width: 100%;
    text-align: center;
    border-top: 1px solid #ddd;
    border-bottom: 1px solid #ddd;
    padding: 10px 0px;
    z-index: 1;
  }

  .account .tinytabs .tabs .tab .close {
    padding-left: 5px;
  }

  .account .tinytabs .tabs .tab {
    margin: 0 3px 0 0;
    padding: 6px 15px;
    text-decoration: none;
    font-weight: bold;
  }

  .account .tinytabs .section {
    overflow: hidden;
    clear: both;
    width: 100%;
    th: 100%;
    margin-top: -25px;
  }

  .account .tinytabs .tab.sel {
    color: #000;
    text-shadow: none;
  }

  .account .nav-tabs {
    border-bottom: 0;
    margin-bottom: 50px;
  }

  .account .posts-content {
    margin-top: 20px;
  }

  .account .ui-w-40 {
    width: 40px !important;
    height: auto;
  }

  .account .default-style .ui-bordered {
    border: 1px solid rgba(24, 28, 33, 0.06);
  }

  .account .ui-bg-cover {
    background-color: transparent;
    background-position: center center;
    background-size: cover;
  }

  .account .ui-rect {
    padding-top: 50% !important;
  }

  .account .ui-rect,
  .account .ui-rect-30,
  .account .ui-rect-60,
  .account .ui-rect-67,
  .account .ui-rect-75 {
    position: relative !important;
    display: block !important;
    padding-top: 100% !important;
    width: 100% !important;
  }

  .account .card-footer,
  .account .card hr {
    border-color: rgba(24, 28, 33, 0.06);
  }

  .account .ui-rect-content {
    position: absolute !important;
    top: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    left: 0 !important;
  }

  .account .default-style .ui-bordered {
    border: 1px solid rgba(24, 28, 33, 0.06);
  }

  .account .card-body {
    padding: 0;
  }

  .account .post-desc {
    padding: 10px;
    padding-bottom: 0px;
    font-size: 13px;
    line-height: 14px;
    margin-bottom: 9px;
  }

  .account .media {
    padding: 10px;
    padding-bottom: 0;
    margin-bottom: -5px !important;
  }

  .account .media-body {
    font-size: 13px;
    margin-left: 10px !important;
  }

  .account img.d-block.ui-w-40.rounded-circle {
    width: 33px !important;
  }

  .account .post-item {
    margin-top: 2px;
    margin-bottom: 10px;
  }

  .account .posts {
    margin-right: -15px;
    margin-left: -15px;
    margin-top: 65px;
  }

  .account ul.follower-list {
    list-style: none;
    background: #fff;
    border-bottom: 1px solid #ccc;
    padding: 5px;
    margin-top: 2px;
  }

  .account ul.follower-list li {
    display: contents;
    margin: 15px;
  }

  .account ul.follower-list li img {
    width: 23.9%;
    border: 3px solid #f7f9fb;
  }

  .account .overview-list {
    padding: 0;
    margin-top: 2px;
    background: #fff;
    padding: 29px 22px;
  }

  .account .profile {
    margin-bottom: 1px;
    margin-top: 0px;
    background: #fff;
    margin-left: -15px;
    margin-right: -15px;
    padding: 25px;
  }

  .account .rounded-circle {
    border-radius: 50% !important;
    border: 1px solid #dee2e6;
    padding: 2px;
  }
</style>
