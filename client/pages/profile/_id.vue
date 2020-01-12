<template>
  <div>

    <div class="account">

      <div class="container">
        <div class="text-center profile">
          <div v-show="loadingAccount==false">
            <img :src="user_data.avatar | getImgUrl('avatar','m_avatar')" class="ui-w-100 rounded-circle"
            />

            <div class="col-md-8 col-lg-6 col-xl-5 p-0 mx-auto">
              <h4 class="font-weight-bold my-4"> {{user_data.name}}</h4>

              <div v-if="user_data.bio" class="text-muted mb-4">
                <i class="fa fa-quote-left" aria-hidden="true"></i> {{user_data.bio}} <i class="fa fa-quote-right"
                                                                                         aria-hidden="true"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <no-ssr>
        <div class="account-tabs">
          <b-tabs content-class="mt-3" align="center">
            <b-tab title="បរិច្ចាគសៀវភៅ" active>
              <post-item/>
            </b-tab>
            <b-tab title="ពណ៍រមាន">
              <div class="overview-list">
                  <div class="input-group mb-3">
                    <input disabled v-model="user_data.name" type="text" class="form-control input-text no-border"
                           placeholder="Name">
                    <div class="input-group-prepend">
                      <span class="input-group-text no-border" id="basic-addon1"><i class="fa fa-user" aria-hidden="true"></i></span>
                    </div>
                  </div>
                  <div class="input-group mb-3">
                    <input disabled v-model="user_data.email" type="text" class="form-control input-text no-border"
                           placeholder="E-mail">
                    <div class="input-group-prepend">
                  <span class="input-group-text no-border" id="basic-addon1"><i class="fa fa-envelope"
                                                                      aria-hidden="true"></i></span>
                    </div>
                  </div>
                  <div class="input-group mb-3">
                    <input disabled v-model="user_data.phone" type="text" class="form-control input-text no-border"
                           placeholder="Phone">
                    <div class="input-group-prepend">
                      <span class="input-group-text no-border" id="basic-addon1"><i class="fa fa-phone" aria-hidden="true"></i></span>
                    </div>
                  </div>
                  <div class="input-group mb-3">
                    <input disabled v-model="user_data.bio" type="text" class="form-control input-text no-border"
                           placeholder="Quote">
                    <div class="input-group-prepend">
                <span class="input-group-text no-border" id="basic-addon1"><i class="fa fa-quote-right"
                                                                    aria-hidden="true"></i></span>
                    </div>
                  </div>
              </div>
            </b-tab>
          </b-tabs>
        </div>
      </no-ssr>
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
                user_data: '',
                loadingAccount: true
            }
        },
        components: {
            PostItem
        },
        computed: {},
        watch: {},
        mounted() {

        },
        created() {
            this.getProfile(this.$route.params.id)
        },
        methods: {
            async getProfile(id) {
                this.loadingAccount = true
                try {
                    this.$axios.get('account/' + id).then(({data}) => {
                        this.user_data = data
                        this.loadingAccount = false
                    })
                } catch (e) {
                    console.log(e);
                    return;
                }
            },
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
            getImgUrl(type, image) {
                return `${process.env.baseUrl}image/${type}/${image}`;
            },
            async logout() {
                await this.$auth.logout()
            }
        }
    }
</script>

<style scoped>
  .account .no-border-left{
    border-left: 0;
    background: #fff;
  }
  .account .nav-tabs {
    border-bottom: 0 !important;
    margin-bottom: -14px !important;
  }
  .account .nav-tabs .nav-link.active, .account .nav-tabs .nav-item.show .nav-link {
    color: #495057;
    background-color: initial;
    border: 0 !important;
  }
  .account .nav-tabs .nav-link {
    border: 0px solid transparent;
    border-top-left-radius: 0.25rem;
    border-top-right-radius: 0.25rem;
    font-weight: bold;
  }

  .account .ui-w-100 {
    width: 100px !important;
    height: auto;
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
    border-top: 1px solid #dcdcdc;
    border-radius: 2px;
    border-bottom: 1px solid #aaa;
  }

  .account .overview-list .input-text {
    border-top-left-radius: 20px !important;
    border-bottom-left-radius: 20px !important;
    padding-bottom: 8px;
    padding-left: 18px;
    border-right: 0;
  }

  .account .input-group.mb-3 .input-group-prepend span {
    width: 40px !important;
    text-align: center;
  }

  .account .profile {
    margin-bottom: 1px;
    margin-top: 0px;
    background: #fff;
    margin-left: -15px;
    margin-right: -15px;
    padding: 25px;
    height: 275px;
    border-bottom: 1px solid #ddd;
  }

  .account .rounded-circle {
    border-radius: 50% !important;
    border: 1px solid #dee2e6;
    padding: 2px;
  }


</style>
