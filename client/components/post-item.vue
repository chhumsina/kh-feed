<template>
  <div class="feed">
    <nav
      v-if="this.$route.params.id == undefined && this.$route.name == 'feed'"
      class="navbar navbar-expand-lg navbar-light bg-white top-nav"
    >
      <div class="container">
        <div class="has-search" style="width: 100%">
          <i class="fa fa-search form-control-feedback"></i>
          <input
            type="text"
            class="form-control input-box"
            placeholder="ស្វែងរក សៀវភៅបរិច្ចាគ"
            v-on:keyup.enter="searchFeed"
            v-model="search"
          />
        </div>
      </div>
    </nav>

    <div class="container c_post">
      <b-alert show variant="warning" v-if="this.$route.name == 'feed'">
        <h4 class="alert-heading">
          <i class="fa fa-exclamation-circle" aria-hidden="true"></i>
          ដំណាក់កាលសាកល្បង
        </h4>
        <div>
          <p>khfeed​ នឹងចេញជាផ្លូវការក្នុងពេលឆាប់ៗនេះ។</p>
          <hr />
          <p class="text-center">
            ចង់ក្លាយជាមនុស្សក្នុងចំណោម១០០នាក់ដំបូងដែលទទួលបានសៀវភៅបរិច្ចាគ នៅពេល
            khfeed ចេញជាផ្លូវការ?
          </p>
          <p class="text-center">
            <button @click="becomePeopleGetBook()" type="button" class="btn btn-primary">
              បាទ/ចាស យល់ព្រម
            </button>
          </p>
        </div>
      </b-alert>

      <nuxt-link
        to="/create-post"
        v-if="this.$route.params.id == undefined && this.$route.name == 'feed'"
      >
        <div
          class="box-footer"
          style="padding: 25px 10px;display: block; margin-bottom: 10px; border-top: 1px solid #dcdcdc; border-bottom: 1px solid #dcdcdc;"
        >
          <img
            style="margin-top: 0px; height: 35px !important; width: 35px !important;"
            class="img-responsive img-circle img-sm"
            :src="user.avatar | getImgUrl('avatar', 'sm_avatar')"
            alt="Alt Text"
          />
          <div class="img-push">
            <div
              style="padding-top: 8px;border-radius: 25px !important;background: #fafafa;color:#bbb;"
              class="form-control input-sm input-box"
            >
              ខ្ញុំចង់បរិច្ចាគសៀវភៅ...
            </div>
          </div>
        </div>
      </nuxt-link>

      <div v-if="userTop.length > 1 && this.$route.name == 'feed'">
        <h6
          style="padding: 15px; padding-bottom: 10px; padding-top: 10px; color: #555;"
          class="alert-heading"
        >
          <i class="fa fa-users" aria-hidden="true"></i>
          អ្នកបរិច្ចាគសៀវភៅច្រើនបំផុត
        </h6>
        <div class="slide-profile">
          <swiper :options="swiperOption" :data="userTop">
            <swiper-slide v-for="(item, $index) in userTop" :key="$index">
              <nuxt-link :to="`/profile/${item.id}`">
                <img
                  class="img-circle"
                  :src="item.avatar | getImgUrl('avatar', 'm_avatar')"
                  alt="User Image"
                />
                <p style="font-size: 15px;">{{ item.name }}</p>
              </nuxt-link>
            </swiper-slide>
            <swiper-slide>
              <nuxt-link :to="`/people/`" style="color: #666;">
                <p
                  style="top: 0; bottom: 0; left: 0; right: 0; margin-top: 45px;"
                >
                  អ្នកផ្សេងទៀត...
                </p>
              </nuxt-link>
            </swiper-slide>
          </swiper>
        </div>
      </div>

      <b-modal
        class="fullscreen"
        id="post-modal"
        hide-title="true"
        no-enforce-focus
      >
        <post-modal :postId="postId" :page_name="page_name" />
      </b-modal>

      <h6
        v-if="feeds.length > 0 && this.$route.name == 'feed'"
        style="padding: 15px; padding-bottom: 10px; padding-top: 10px; color: #555;"
        class="alert-heading"
      >
        <i class="fa fa-feed" aria-hidden="true"></i> សៀវភៅដែលកំពុងបរិច្ចាគ
      </h6>

      <ul class="media-list">
        <li
          class="media"
          v-for="(item, $index) in feeds"
          :key="$index"
          :data-num="$index + 1"
        >
          <p v-if="item.status == 'pending'" class="review-post">
            <i class="fa fa-exclamation" aria-hidden="true"></i>
            ពួកយើងសុំពេល​ទំនាក់ទំនង មុននឹងបង្ហាញជាសាធារណៈ​
          </p>
          <p class="header">
            <nuxt-link :to="`/profile/${item.user_id}`">
              <img
                :src="item.avatar | getImgUrl('avatar', 'sm_avatar')"
                alt="User Image"
              />
              <span class="username">{{ item.name }}</span>
            </nuxt-link>

            <span class="text-muted pull-right">
              <small class="text-muted"
                ><timeago
                  :datetime="item.created_at"
                  :auto-update="10"
                ></timeago
              ></small>
            </span>
          </p>
          <img
            @click="showPostModal(item.post_id)"
            class="post-image"
            v-lazy="getImgUrl(item.photo, 'photo', 'm_post')"
            alt="Post Image"
          />
          <div class="media-body">
            <p @click="showPostModal(item.post_id)">
              {{ item.caption | truncate(150, "...") }}
            </p>
          </div>
          <div class="got-book" v-if="item.give_status == 'active'">
            <i class="fa fa-check" aria-hidden="true"></i>
            ​អ្នក​ជ្រើសរើសបានទទួលសៀវភៅហើយ
          </div>
        </li>
      </ul>
    </div>
    <infinite-loading
      @infinite="onInfinite"
      spinner="bubbles"
      ref="infiniteLoading"
    ></infinite-loading>
    <br />
    <br />
    <br />
  </div>
</template>

<script>
import myfilter, { getImgUrl } from "../plugins/myfilter";
import PostModal from "../components/PostModal";

export default {
  components: {
    PostModal
  },
  data() {
    return {
      swiperOption: {
        slidesPerView: 5,
        spaceBetween: 50,
        // init: false,
        pagination: {
          el: ".swiper-pagination",
          clickable: true
        },
        breakpoints: {
          768: {
            slidesPerView: 3,
            spaceBetween: 5
          }
        }
      },
      userTop: [],
      page: 1,
      feeds: [],
      search: null,
      postId: null,
      page_name: this.$route.name
    };
  },
  watch: {
    $route(to, from) {
      // if the current history index isn't at the last position, use 'back' transition
      console.log(to.hash);
      if (to.hash == "" || to.hash == "#post") {
        this.$root.$emit("bv::hide::modal", "post-modal");
      }
    }
  },
  computed: {
    postModal: function() {
      return this.dataModal;
    }
  },
  created() {
    this.listUserByTopContribution();
  },
  methods: {
    getImgUrl(image, type, size) {
      return getImgUrl(image, type, size);
    },
    itemsContains(text, word) {
      return text.includes(word);
    },
    async onInfinite($state) {
      var id = this.$route.params.id;
      var auth_id = this.$root.user.id;
      if (this.$route.name == "account") {
        var id = auth_id;
      }

      this.$axios
        .get("post/list", {
          params: {
            page: this.page,
            id: id,
            search: this.search
          }
        })
        .then(({ data }) => {
          if (data.length) {
            this.page += 1;
            this.feeds.push(...data);
            $state.loaded();
          } else {
            $state.complete();
          }
        });
    },
    searchFeed() {
      if (this.search.length >= 2 || this.search.length == 0) {
        this.page = 1;
        this.feeds = [];
        this.onInfinite();
      } else {
        this.$swal.fire("Please enter more than 2 characters!");
      }
    },
    async showPostModal(id) {
      this.postId = id;
      this.$router.push({ to: this.$route.fullPath, hash: "#post" });
      this.$root.$emit("bv::show::modal", "post-modal");
    },
    async listUserByTopView() {
      if (this.$route.name == "feed") {
        this.$axios.get("user/list-user-by-top-view").then(({ data }) => {
          if (data.length) {
            this.userTop.push(...data);
          }
        });
      }
    },
    async listUserByTopContribution() {
      if (this.$route.name == "feed") {
        this.$axios
          .get("user/list-user-by-top-contribution")
          .then(({ data }) => {
            if (data.length) {
              this.userTop.push(...data);
            }
          });
      }
    },
    becomePeopleGetBook() {
      this.$swal
            .fire({
              title: "សូមបញ្ចូលលេខទូរសព្ទរបស់អ្នក",
              input: "number",
              inputAttributes: {
                autocapitalize: "off"
              },
              showCancelButton: true,
              confirmButtonText: "បញ្ចូន",
              showLoaderOnConfirm: true,
              preConfirm: phone => {
                if (phone) {
                  return this.$axios
                    .post("pro/become-people-get-book", { phone: phone})
                    .then(function(res) {
                      return res;
                    })
                    .catch(error => {
                      this.$swal.showValidationMessage(
                        `Request failed: ${error}`
                      );
                    });
                }
              },
              allowOutsideClick: () => !this.$swal.isLoading()
            })
            .then(result => {
              if (result.value) {
                if (result.value.data.status == true) {
                  this.$swal.fire(result.value.data.msg, '', "success");
                } else {
                  this.$swal.fire(result.value.data.msg, '', "error");
                }
              }
            });
    }
  }
};
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
  margin-bottom: 10px;
  position: relative;
  border-top: 1px solid #ddd;
  border-bottom: 1px solid #ddd;
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
.media .header {
  border-bottom: 1px solid #f7f7f7;
  padding-bottom: 7px;
  margin-bottom: 10px;
  color: #929292;
}
.media .footer {
  border-top: 1px solid #f7f7f7;
  margin-top: 14px;
  padding-top: 5px;
  margin-bottom: 0;
}
.media-body p {
  margin-bottom: 0 !important;
}

.review-post {
  text-align: center;
  font-size: 13px;
  background: #ffebeba6;
  padding: 10px 0px;
  color: #d86d21;
  border-bottom: 1px solid #f7f7f7;
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
  border: 1px solid #bbb;
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

.slide-profile {
  margin-bottom: 18px;
  text-align: center;
}

.slide-profile .swiper-slide {
  background: #fff;
  height: 150px;
  text-align: center;
  padding: 13px 0;
  box-shadow: 0 1px 1px #eaeaea;
}

.slide-profile .swiper-slide img.img-circle {
  margin-bottom: 4px;
}

.more-menu {
  text-align: center;
}

ul.more-menu-item {
  padding: 0;
  list-style: none;
  display: inline-flex;
}

ul.more-menu-item li {
  width: 60px;
  background: #fff;
  margin-left: 5px;
  /* border-radius: 38px; */
  height: 60px;
  padding: 6px;
  box-shadow: 0 1px 1px #aaaaaaa6;
  padding-top: 8px;
}

ul.more-menu-item li i {
  font-size: 27px;
}

ul.more-menu-item li p {
  font-size: 12px;
  margin-bottom: -10px;
  margin-top: -5px;
}
.swiper-container {
  margin-left: auto;
  margin-right: auto;
  position: relative;
  overflow: hidden;
  list-style: none;
  padding: 0;
  z-index: 1;
  padding: 1px 0;
}
</style>
