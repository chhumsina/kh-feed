<template>
  <div class="recommend container">
    <div class="post">
      <div class="card">
        <div class="header">
          <h2><strong>Top</strong> Post <small>Recommended to buy</small></h2>
        </div>
        <div class="body">

          <div v-show="loadingPost" class="loading-inline">
            <div class="loader"></div>
          </div>

          <ul v-show="loadingPost==false" class="new_friend_list list-unstyled row">
            <li v-for="(item, $index) in posts"
                :key="$index" class="col-lg-4 col-md-2 col-sm-6 col-4">
              <nuxt-link :to="`/post/${item.id}`">
                <img
                  :src="item.photo | getImgUrl('photo','sm_post')"
                  class="img-thumbnail"
                  alt="User Image"
                />
                <h6 class="users_name">
                  {{item.caption | truncate(20, '...')}}
                </h6>
<!--                <small class="join_date">Today</small>-->
              </nuxt-link>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <div class="creator">
      <div class="card">
        <div class="header">
          <h2>
            <strong>Top</strong> Creator <small>Recommended to follow</small>
          </h2>
        </div>
        <div class="body">

          <div v-show="loadingUser" class="loading-inline">
            <div class="loader"></div>
          </div>

          <ul v-show="loadingUser==false" class="new_friend_list list-unstyled row">
            <li v-for="(item, $index) in users"
                :key="$index" class="col-lg-4 col-md-2 col-sm-6 col-4">
              <nuxt-link :to="`/profile/${item.id}`">
                <img
                  :src="item.avatar  | getImgUrl('avatar','sm_avatar')"
                  class="img-thumbnail"
                  alt="User Image"
                />
                <h6 class="users_name">{{item.name | truncate(20, '...')}}</h6>
              </nuxt-link>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
    export default {
        middleware: 'auth',
        data() {
            return {
                strategy: this.$auth.$storage.getUniversal('strategy'),
                users: [],
                posts: [],
                loadingUser: true,
                loadingPost: true,
            }
        },
        mounted() {
            this.listUser();
            this.listPost();
        },
        methods: {
            async logout() {
                await this.$auth.logout()
            },
            async listUser() {
                this.loadingUser = true
                this.$axios
                    .get('user/list', {
                        params: {
                            list_type: 'recommend'
                        }
                    })
                    .then(({data}) => {
                        if (data.length) {
                            this.users = data;
                        }
                        this.loadingUser = false
                    })
            },
            async listPost() {
                this.loadingPost = true
                this.$axios
                    .get('post/list', {
                        params: {
                            list_type: 'recommend'
                        }
                    })
                    .then(({data}) => {
                        if (data.length) {
                            this.posts = data;
                        }
                        this.loadingPost = false
                    })
            },
        }
    }
</script>

<style scoped>
  .recommend {
    margin-top: 67px;
    margin-bottom: 50px;
  }
  .recommend h6.post_title {
    text-align: left;
  }

  .recommend .card {
    -moz-transition: all 0.5s;
    -o-transition: all 0.5s;
    -webkit-transition: all 0.5s;
    transition: all 0.5s;
    background: #fff;
    border-radius: 0.1875rem;
    margin-bottom: 15px;
    border: 0;
    display: inline-block;
    position: relative;
    width: 100%;
    box-shadow: none;
    border-bottom: 1px solid #ddd;
    border-top: 1px solid #ddd;
  }

  .recommend .card .header {
    color: #424242;
    padding: 20px;
    position: relative;
    box-shadow: none;
  }

  .recommend .card .header h2 {
    color: #757575;
    position: relative;
  }

  .recommend .card .header h2:before {
    background: #6572b8;
  }

  .recommend .card .header h2 {
    font-size: 15px;
  }

  .recommend .card .header h2::before {
    position: absolute;
    width: 2px;
    height: 18px;
    left: -20px;
    top: 0;
    content: '';
  }

  .recommend .theme-purple .card .header h2 strong {
    color: #6572b8;
  }

  .recommend .card .header h2 small {
    color: #9e9e9e;
    line-height: 15px;
  }

  .recommend .card .body {
    color: #424242;
    font-weight: 400;
    padding: 20px;
    padding-top: 0;
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
    position: relative;
  }

  .recommend .new_friend_list {
    margin-bottom: 0px;
  }

  .recommend .new_friend_list li .users_name {
    color: #424242;
    text-transform: capitalize;
  }

  .recommend .new_friend_list li .users_name {
    margin-top: 5px;
  }

  .recommend .new_friend_list li .users_name {
    margin-bottom: 0px;
  }

  .recommend  .new_friend_list li .join_date {
    color: #757575;
  }

  .recommend ul.new_friend_list.list-unstyled.row li {
    margin-bottom: 15px;
    text-align: center;
  }

  .recommend .creator .body img {
    border-radius: 50%;
  }
</style>
