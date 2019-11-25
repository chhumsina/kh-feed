<template>
  <div class="recommend container">
    <div class="post">
      <div class="card">
        <div class="header">
          <h2><strong>Top</strong> Post <small>Recommended to buy</small></h2>
        </div>
        <div class="body">
          <ul class="new_friend_list list-unstyled row">
            <li class="col-lg-4 col-md-2 col-sm-6 col-4">
              <nuxt-link :to="`/post/1`">
                <img
                  src="https://bootdey.com/img/Content/avatar/avatar7.png"
                  class="img-thumbnail"
                  alt="User Image"
                />
                <h6 class="users_name">Jackson</h6>
                <small class="join_date">Today</small>
              </nuxt-link>
            </li>
            <li class="col-lg-4 col-md-2 col-sm-6 col-4">
              <a href="">
                <img
                  src="https://bootdey.com/img/Content/avatar/avatar6.png"
                  class="img-thumbnail"
                  alt="User Image"
                />
                <h6 class="users_name">Aubrey</h6>
                <small class="join_date">Yesterday</small>
              </a>
            </li>
            <li class="col-lg-4 col-md-2 col-sm-6 col-4">
              <a href="">
                <img
                  src="https://bootdey.com/img/Content/avatar/avatar5.png"
                  class="img-thumbnail"
                  alt="User Image"
                />
                <h6 class="users_name">Oliver</h6>
                <small class="join_date">08 Nov</small>
              </a>
            </li>
            <li class="col-lg-4 col-md-2 col-sm-6 col-4">
              <a href="">
                <img
                  src="https://bootdey.com/img/Content/avatar/avatar3.png"
                  class="img-thumbnail"
                  alt="User Image"
                />
                <h6 class="users_name">Isabella</h6>
                <small class="join_date">12 Dec</small>
              </a>
            </li>
            <li class="col-lg-4 col-md-2 col-sm-6 col-4">
              <a href="">
                <img
                  src="https://bootdey.com/img/Content/avatar/avatar2.png"
                  class="img-thumbnail"
                  alt="User Image"
                />
                <h6 class="users_name">Jacob</h6>
                <small class="join_date">12 Dec</small>
              </a>
            </li>
            <li class="col-lg-4 col-md-2 col-sm-6 col-4">
              <a href="">
                <img
                  src="https://bootdey.com/img/Content/avatar/avatar1.png"
                  class="img-thumbnail"
                  alt="User Image"
                />
                <h6 class="users_name">Matthew</h6>
                <small class="join_date">17 Dec</small>
              </a>
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
          <ul class="new_friend_list list-unstyled row">
            <li v-for="(item, $index) in users"
                :key="$index" class="col-lg-4 col-md-2 col-sm-6 col-4">
              <nuxt-link :to="`/profile/1`">
                <img
                  src="https://bootdey.com/img/Content/avatar/avatar7.png"
                  class="img-thumbnail"
                  alt="User Image"
                />
                <h6 class="users_name">{{item.name}}</h6>
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
                users: []
            }
        },
        mounted() {
            this.listUser();
        },
        methods: {
            async logout() {
                await this.$auth.logout()
            },
            async listUser($state) {
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

  .card {
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

  .card .header {
    color: #424242;
    padding: 20px;
    position: relative;
    box-shadow: none;
  }

  .card .header h2 {
    color: #757575;
    position: relative;
  }

  .card .header h2:before {
    background: #6572b8;
  }

  .card .header h2 {
    font-size: 15px;
  }

  .card .header h2::before {
    position: absolute;
    width: 2px;
    height: 18px;
    left: -20px;
    top: 0;
    content: '';
  }

  .theme-purple .card .header h2 strong {
    color: #6572b8;
  }

  .card .header h2 small {
    color: #9e9e9e;
    line-height: 15px;
  }

  .card .body {
    color: #424242;
    font-weight: 400;
    padding: 20px;
    padding-top: 0;
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
  }

  .new_friend_list {
    margin-bottom: 0px;
  }

  .new_friend_list li .users_name {
    color: #424242;
    text-transform: capitalize;
  }

  .new_friend_list li .users_name {
    margin-top: 5px;
  }

  .new_friend_list li .users_name {
    margin-bottom: 0px;
  }

  .new_friend_list li .join_date {
    color: #757575;
  }

  ul.new_friend_list.list-unstyled.row li {
    margin-bottom: 15px;
    text-align: center;
  }

  .creator .body img {
    border-radius: 50%;
  }
</style>
