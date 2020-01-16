<template>
  <div class="c_post">
    <div v-if="loadingPost == false" class="card">
      <div v-if="post != null">
        <div class="user-block" style="padding: 15px;">
          <img
            class="img-circle"
            :src="post.avatar | getImgUrl('avatar', 'sm_avatar')"
          />
          <span class="username">
            {{ post.name }}
          </span>
          <span class="description">
            <timeago :datetime="post.created_at" :auto-update="10"></timeago>
          </span>
        </div>

        <img
          class="card-img-top"
          :src="getImgUrl(post.photo, 'photo', 'm_post')"
          alt="Card image cap"
        />
        <div class="card-body">
          <div class="post_property">
            <small class="post_view_num text-muted"
              >{{ numView }} views · {{ lastRead }} last read</small
            >
            <a href="#comment"
              ><small style="float: right;" class="post_view_num"
                >{{ numComment }} មតិ</small
              ></a
            >
          </div>
          <p
            style="white-space: pre-line; word-break: break-all;"
            class="caption"
          >
            <span v-html="post.caption"></span>
          </p>
        </div>

        <div v-if="$auth.user" class="text-center">
      <div
        v-if="post.give_status=='active'" 
        style="position: fixed; bottom: 0px; width: 100%; background: #fff; padding: 14px; box-shadow: 0 0px 3px #555;"
      >
        <nuxt-link :to="`/feed`">
          <p>Go home page</p>
        </nuxt-link>
        <span style="color:green;"
          >សៀវភៅបរិច្ចាគនេះ <br />
          ត្រូវបាន​យកទៅដល់ដៃ​អ្នកដែលបានជ្រើសរើសហើយ</span
        >
      </div>
      <div
        v-else
        @click="iNeed(post.give_status)"
        style="width: 125px; height: 125px; background: #4c94ff; text-align: center; margin: 0 auto; line-height: 125px; border-radius: 125px; font-weight: bold; color: #fff5f5; box-shadow: 0 2px 3px #aaa; position: fixed; bottom: 12px; left: 0; right: 0; font-size: 20px; text-shadow: 0 1px 4px #000;"
      >
        ខ្ញុំចង់បាន
      </div>
    </div>
    <div
      v-else
      style="text-align:center;position: fixed; bottom: 0px; width: 100%; background: #fff; padding: 14px; box-shadow: 0 0px 3px #555;"
    >
      <a
        style="color: #806f6f; padding: 10px; border-radius: 5px; background:#fff;"
        @click="socialLogin('google')"
        class="social-login-btn google"
      >
        ចូលគណនីតាម
        <b style="text-shadow: 0px 1px 0px #ddd;color: #4285F4"
          >Google
          <span v-if="loginloading == true"> <img :src="loader" /> </span
        ></b>
      </a>
    </div>

      </div>
      <div v-else>
        <div class="card-body">
          <p class="alert alert-warning​ text-center">
            មិនមានសៀវភៅបរិច្ចាគមួយទេ។
          </p>
        </div>
      </div>

      <div class="card-body box-comments">
        <p style="border-bottom: 1px solid #ddd;padding-bottom: 9px;">
          <i class="fa fa-comments-o" aria-hidden="true"></i>
          មតិ
        </p>

        <div
          v-for="(item, $index) in dataModalComment"
          :key="$index"
          class="box-comment"
        >
          <img
            class="img-circle img-sm"
            :src="item.avatar | getImgUrl('avatar', 'sm_avatar')"
          />
          <div class="comment-text">
            <small class="username">
              {{ item.name | truncate(10, "...") }}
              <span class="text-muted pull-right">
                <timeago
                  :datetime="item.created_at"
                  :auto-update="10"
                ></timeago>
              </span>
            </small>
            <small>{{ item.comments }}</small>
          </div>
        </div>
        <div class="text-center" v-if="numComment == 0">
          <i
            class="fa fa-comments-o"
            style="font-size: 75px; color: #ccc;"
            aria-hidden="true"
          ></i>
          <p style="margin-bottom: -5px; font-weight: bold; color: #aaa;">
            មិនទាន់មានមតិទេ
          </p>
        </div>
        <p id="comment"></p>
        <br />
      </div>
    </div>
    <div v-else class="text-center" style="margin-top:30%">
      <img :src="loader" />
    </div>
  </div>
</template>

<script>
import myfilter, { getImgUrl } from "~/plugins/myfilter";
export default {
  head() {
    return {
      titleTemplate: 'សៀវភៅបរិច្ចាគ - %s'
    };
  },
  data() {
    return {
      post: null,
      dataModalComment: "",
      loadingModalComment: false,
      loadingPost: true,
      numComment: 0,
      numView: 1,
      lastRead: 1,
      loginloading: false,
      loader: require("assets/default/loader.gif")
    };
  },
  created() {
    this.getPost(this.$route.params.id);
    this.listComment(this.$route.params.id);
  },
  methods: {
    iNeed(status) {
      if (status == "active") {
        this.$swal.fire(
          "Sorry, this contributed book has been closed!",
          "",
          "info"
        );
      } else {
        if (this.user.phone == "") {
          this.$swal
            .fire({
              title:
                '"ដើម្បីងាយស្រួលក្នុងការទំនាក់ទំនង"  \nសូមបំពេញលេខទូរសព្ទ \n  ហើយត្រឡប់មកទីនេះម្តងទៀត។',
              text: "Account -> ព័ត៌មាន -> លេខទូរសព្ទ",
              icon: "warning",
              showCancelButton: false,
              confirmButtonColor: "#3085d6",
              cancelButtonColor: "#d33",
              confirmButtonText: "ទៅកាន់ Account"
            })
            .then(result => {
              this.$router.push({ path: "/account" });
            });
        } else {
          this.$swal
            .fire({
              title: "ពណ៍នានៃការចង់បានសៀវភៅនេះ",
              input: "textarea",
              inputAttributes: {
                autocapitalize: "off"
              },
              showCancelButton: true,
              confirmButtonText: "Submit",
              showLoaderOnConfirm: true,
              preConfirm: desc => {
                if (desc) {
                  return this.$axios
                    .post("post/i-need", { desc: desc, id: this.$route.params.id })
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
                  this.$swal.fire(result.value.data.msg, "success", "success");
                  window.location.href = "/dashboard";
                } else {
                  this.$swal.fire(result.value.data.msg, "error", "error");
                }
              }
            });
        }
      }
    },
    socialLogin(service) {
      this.loginloading = true;
      window.location.href = `${process.env.baseUrl}auth/login/${service}`;
    },
    itemsContains(text, word) {
      return text.includes(word);
    },
    getImgUrl(image, type, size) {
      return getImgUrl(image, type, size);
    },
    async getPost(id) {
      if (this.loadingPost == false) {
        return false;
      }
      this.$axios.get("pub/post/" + id).then(({ data }) => {
        this.post = data.detail[0];
        this.numView = data["num_view"][0].num_view;
        this.numComment = data["num_comment"][0].num_comment;
        this.lastRead = data["last_read"];
        this.loadingPost = false;
      });
    },
    async listComment(id) {
      this.loadingModalComment = true;
      this.dataModalComment = "";
      this.$axios.get("pub/post/comment/" + id).then(({ data }) => {
        if (data) {
          this.dataModalComment = data;
          this.loadingModalComment = false;
        }
      });
    }
  }
};
</script>

<style scoped>
.post-detail {
  background: #fff;
}
</style>
