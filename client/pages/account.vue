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
        <img :src="user.avatar | getImgUrl('avatar','m_avatar')" class="ui-w-100 rounded-circle"
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
          <post-item/>
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
    import PostItem from '~/components/post-item'

    export default {
        middleware: 'auth',
        data() {
            return {
                strategy: this.$auth.$storage.getUniversal('strategy'),
                form: {
                    name: '',
                    email: '',
                    phone: '',
                    bio: '',
                },
                error: this.$route.query.error
            }
        },
        components: {
            PostItem
        },
        computed: {
        },
        watch: {
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

  .account .tinytabs .tabs {
    width: 100%;
    text-align: center;
    border-top: 1px solid #ccc;
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
    background: #fff;
    padding: 29px 22px;
    border-top: 1px solid #aaa;
    border-radius: 2px;
    border-bottom: 1px solid #aaa;
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
