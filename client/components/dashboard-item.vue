<template>
  <div class="feed">
    <!--    <nav-->
    <!--         class="navbar navbar-expand-lg navbar-light bg-white top-nav">-->
    <!--      <div class="container">-->
    <!--        <div class="has-search" style="width: 100%">-->
    <!--          <span class="fa fa-search form-control-feedback"></span>-->
    <!--          <input-->
    <!--            type="text"-->
    <!--            class="form-control input-box"-->
    <!--            placeholder="Search I Want"-->
    <!--            v-on:keyup.enter="searchFeed" v-model="search"-->
    <!--          />-->
    <!--        </div>-->
    <!--      </div>-->
    <!--    </nav>-->

    <div
      style="z-index: 1;top: 68px;position: fixed; background: rgb(255, 255, 255); margin-top: -67px; padding: 15px 0px 1px; text-align: center; margin-bottom: 15px; box-shadow: 0 1px 1px #ddd; width: 100%;"
    >
      <img :src="placeholder_photo.i_want_banner" style="width: 80%;" />
      <div class="total-book">
        <ul>
          <!-- <li :class="book_filter=='contributes' ? 'menu-active' : ''" @click="dashboardList('contributes','first')">
           កម្មង់សៀវភៅ<br/><span>{{totalBook.total_contributes}} ក្បាល</span>
            </li> -->
          <li
            :class="book_filter == 'iwant' ? 'menu-active' : ''"
            @click="dashboardList('iwant', 'first')"
          >
            ខ្ញុំចង់បាន<br /><span>{{ totalBook.total_want }} ក្បាល</span>
          </li>
          <li
            :class="book_filter == 'getting' ? 'menu-active' : ''"
            @click="dashboardList('getting', 'first')"
          >
            ខ្ញុំទទួលបាន<br /><span>{{ totalBook.total_getting }} ក្បាល</span>
          </li>
          <li
            :class="book_filter == 'giving' ? 'menu-active' : ''"
            @click="dashboardList('giving', 'first')"
          >
            ខ្ញុំបានបរិច្ចាគ<span>{{ totalBook.total_giving }} ក្បាល</span>
          </li>
        </ul>
      </div>
    </div>

    <div class="container c_post" style="margin-top: 170px;">
      <b-modal
        class="fullscreen"
        id="post-modal"
        hide-title="true"
        no-enforce-focus
      >
        <post-modal
          :postId="postId"
          :page_name="page_name"
          :switch_i_want="true"
        />
      </b-modal>

      <p
        v-if="book_filter == 'iwant'"
        class="text-center"
        style="padding: 5px; color:#000;padding-bottom: 0;"
      >
        បញ្ចីនៃសៀវភៅបរិច្ចាគដែល​
        <span class="font-weight-bold">ខ្ញុំចង់បាន</span>
      </p>
      <!-- <p v-else-if="book_filter=='contributes'" class="text-center" style="padding: 5px; color:#000;padding-bottom: 0;">
        បញ្ចីនៃ <span class="font-weight-bold">Contributes</span> books
        for wanted people</p> -->
      <p
        v-else-if="book_filter == 'giving'"
        class="text-center"
        style="padding: 5px; color:#000;padding-bottom: 0;"
      >
        បញ្ចីនៃសៀវភៅបរិច្ចាគដែល​ ​<span class="font-weight-bold"
          >ខ្ញុំបានបរិច្ចាគ</span
        >​
      </p>
      <p
        v-else="book_filter == 'getting'"
        class="text-center"
        style="padding: 5px; color:#000;padding-bottom: 0;"
      >
        បញ្ចីនៃសៀវភៅបរិច្ចាគដែល​
        <span class="font-weight-bold">ខ្ញុំទទួលបាន</span>
      </p>

      <div v-if="book_filter == 'iwant'">
        <ul class="media-list">
          <li
            :class="
              item.accept_status == 'selected' ? 'media item-selected' : 'media'
            "
            v-for="(item, $index) in bookItem"
            :key="$index"
            :data-num="$index + 1"
          >
            <p class="header">
              <timeago :datetime="item.created_at" :auto-update="10"></timeago>
              <span :class="item.accept_status" class="pull-right">
                {{ item.accept_status }}
              </span>
            </p>
            <img
              @click="showPostModal(item.post_id)"
              class="post-image"
              :src="item.photo | getImgUrl('photo', 'm_post')"
              alt="User Image"
            />
            <div class="media-body">
              <span class="text-muted pull-right">
                <small class="text-muted"
                  ><timeago
                    :datetime="item.post_date"
                    :auto-update="10"
                  ></timeago
                ></small>
              </span>
              <p @click="showPostModal(item.post_id)">
                {{ item.caption | truncate(90, "...") }}
              </p>
            </div>
            <p class="footer">
              {{ item.desc | truncate(45, "...") }}
              <span
                v-if="item.desc.length > 40"
                @click="showMoreDesc(item.desc)"
                style="color: #2f8be0"
                >more</span
              >
            </p>
            <div
              class="dashboard-seleced"
              v-if="item.accept_status == 'selected'"
            >
              <div class="got-book">
                <i class="fa fa-check" aria-hidden="true"></i>
                {{ item.accept_desc }}
                <p
                  style="color: orangered; margin-top: 3px; margin-bottom: -2px;"
                >
                  ពួកយើងនឹងធ្វើការទំនាក់ទំនងទៅកាន់អ្នកក្នុងពេលឆាប់ៗនេះ
                </p>
              </div>
              <div class="get-book">
                <button
                  v-if="recieveBookLoading == true"
                  @click="recieveBook(item)"
                  class="btn btn-secondary"
                >
                  បានទទួលសៀវភៅ
                </button>
                <button v-else class="btn btn-secondary">
                  បានទទួលសៀវភៅ...
                </button>
              </div>
            </div>
          </li>
        </ul>
        <p
          v-if="bookItemLoading == true"
          class="btn_load_more"
          @click="moreDashboardList()"
        >
          {{ load_more_text }}
        </p>
        <p v-else class="btn_load_more">
          <img :src="placeholder_photo.loader" />
        </p>
      </div>

      <div v-else-if="book_filter == 'contributes'">
        <ul class="media-list">
          <li
            class="media"
            v-for="(item, $index) in bookItem"
            :key="$index"
            :data-num="$index + 1"
          >
            <p class="header">
              <span class="open">{{ item.num_want }}</span> people wanted
              <span class="open pull-right">
                Open
              </span>
            </p>
            <img
              @click="showPostModal(item.post_id)"
              class="post-image"
              :src="item.photo | getImgUrl('photo', 'm_post')"
              alt="User Image"
            />
            <div class="media-body">
              <span class="text-muted pull-right">
                <small class="text-muted"
                  ><timeago
                    :datetime="item.post_date"
                    :auto-update="10"
                  ></timeago
                ></small>
              </span>
              <p @click="showPostModal(item.post_id)">
                {{ item.caption | truncate(90, "...") }}
              </p>
            </div>
          </li>
        </ul>
        <p
          v-if="bookItemLoading == true"
          class="btn_load_more"
          @click="moreDashboardList()"
        >
          {{ load_more_text }}
        </p>
        <p v-else class="btn_load_more">
          <img :src="placeholder_photo.loader" />
        </p>
      </div>

      <div v-else-if="book_filter == 'giving'">
        <ul class="media-list">
          <li
            class="media"
            v-for="(item, $index) in bookItem"
            :key="$index"
            :data-num="$index + 1"
          >
            <p class="header">
              <span>
                ​បានផ្តល់
                <span class="font-weight-bold" style="color: green;">{{
                  item.num_active
                }}</span>
                នាក់ ក្នុងចំនោមអ្នកចង់បាន<span
                  class="font-weight-bold"
                  style="color: orange"
                >
                  {{ item.num_want }}​ </span
                >នាក់
              </span>
              <span class="closed pull-right">
                Closed
              </span>
            </p>
            <img
              @click="showPostModal(item.post_id)"
              class="post-image"
              :src="item.photo | getImgUrl('photo', 'm_post')"
              alt="User Image"
            />
            <div class="media-body">
              <span class="text-muted pull-right">
                <small class="text-muted"
                  ><timeago
                    :datetime="item.post_date"
                    :auto-update="10"
                  ></timeago
                ></small>
              </span>
              <p @click="showPostModal(item.post_id)">
                {{ item.caption | truncate(90, "...") }}
              </p>
            </div>
          </li>
        </ul>
        <p
          v-if="bookItemLoading == true"
          class="btn_load_more"
          @click="moreDashboardList()"
        >
          {{ load_more_text }}
        </p>
        <p v-else class="btn_load_more">
          <img :src="placeholder_photo.loader" />
        </p>
      </div>

      <div v-else-if="book_filter == 'getting'">
        <ul class="media-list">
          <li
            class="media"
            v-for="(item, $index) in bookItem"
            :key="$index"
            :data-num="$index + 1"
          >
            <p class="header">
              <timeago :datetime="item.created_at" :auto-update="10"></timeago>
              <span class="closed pull-right">
                Closed
              </span>
            </p>
            <img
              @click="showPostModal(item.post_id)"
              class="post-image"
              :src="item.photo | getImgUrl('photo', 'm_post')"
              alt="User Image"
            />
            <div class="media-body">
              <span class="text-muted pull-right">
                <small class="text-muted"
                  ><timeago
                    :datetime="item.post_date"
                    :auto-update="10"
                  ></timeago
                ></small>
              </span>
              <p @click="showPostModal(item.post_id)">
                {{ item.caption | truncate(90, "...") }}
              </p>
            </div>
            <p class="footer">
              {{ item.desc | truncate(45, "...") }}
              <span
                v-if="item.desc.length > 40"
                @click="showMoreDesc(item.desc)"
                style="color: #2f8be0"
                >more</span
              >
            </p>
            <p class="footer">
              <span class="font-weight-bold">មតិអ្នកផ្តល់អោយ:</span>
              {{ item.accept_desc | truncate(40, "...") }}
              <span
                v-if="item.accept_desc.length > 40"
                @click="showMoreDesc(item.accept_desc)"
                style="color: #2f8be0"
                >more</span
              >
            </p>
          </li>
        </ul>
        <p
          v-if="bookItemLoading == true"
          class="btn_load_more"
          @click="moreDashboardList()"
        >
          {{ load_more_text }}
        </p>
        <p v-else class="btn_load_more">
          <img :src="placeholder_photo.loader" />
        </p>
      </div>

      <br />
      <br />
      <br />
      <br />
    </div>
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
      book_filter: "iwant",
      recieveBookLoading: true,
      page: 1,
      bookItem: [],
      bookItemLoading: true,
      load_more_text: "Load more",
      postId: null,
      page_name: this.$route.name,
      totalBook: [],
      placeholder_photo: {
        i_want_banner: require("../assets/default/book-banner.jpg"),
        loader: require("../assets/default/loader.gif")
      }
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
    this.dashboardList(this.book_filter);
    this.dashboardTotal();
  },
  methods: {
    getImgUrl(image, type, size) {
      return getImgUrl(image, type, size);
    },
    goto(id) {
      document.getElementById(id).scrollIntoView();
    },
    itemsContains(text, word) {
      return text.includes(word);
    },
    moreDashboardList() {
      this.page += 1;
      this.dashboardList(this.book_filter, "more");
    },
    async dashboardTotal() {
      this.$axios.get("post/dashboard-total").then(({ data }) => {
        this.totalBook = data[0];
      });
    },
    async dashboardList(type, loading) {
      if (this.bookItemLoading == false) {
        return false;
      }
      this.bookItemLoading = false;

      if (loading == "first") {
        this.page = 1;
        this.bookItem = [];
        this.load_more_text = "Load more";
      }

      this.book_filter = type;
      this.$axios
        .get("post/dashboard", {
          params: {
            page: this.page,
            type: type
          }
        })
        .then(({ data }) => {
          if (data.length) {
            this.bookItem.push(...data);
          } else {
            this.load_more_text = "No more data!";
          }

          this.bookItemLoading = true;
        });
    },
    async showPostModal(id) {
      this.postId = id;
      this.$router.push({ to: this.$route.fullPath, hash: "#post" });
      this.$root.$emit("bv::show::modal", "post-modal");
    },
    showMoreDesc(desc) {
      this.$swal.fire("Your description:", desc);
    },
    async recieveBook(item) {
      this.$swal
        .fire({
          title: "អ្នកពិតជាទទួលបានសៀវភៅហើយ?",
          text: "",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "green",
          cancelButtonColor: "#aaa",
          confirmButtonText: "បាទ ទទួលបានហើយ"
        })
        .then(result => {
          if (result.value) {
        this.recieveBookLoading = false;
            this.$axios
              .post("post/recieve-book", {
                id: item.id
              })
              .then(({ data }) => {
                if (data.status == true) {
                  this.$swal.fire(data.msg, '', "success");
                } else {
                  this.$swal.fire(data.msg, '', "error");
                }
                this.recieveBookLoading = true;
              });
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

.menu-active {
  background: #eaeaea !important;
}

.btn_load_more {
  text-align: center;
  margin-top: 27px;
  font-weight: bold;
  color: #303440;
}

.total-book ul {
  padding: 0;
  list-style: none;
  margin-bottom: 4px;
}

.total-book ul li {
  text-align: center;
  display: inline-grid;
  padding: 11px 10px 19px 6px;
  color: #888;
  font-size: 14px;
  background: #f7f7f7;
  margin-bottom: 5px;
  border-radius: 4px;
  position: relative;
  height: 82px;
  /* width: 23%; */
  width: 30%;
}

.total-book {
  margin-top: 11px;
}

.total-book ul li span {
  font-weight: bold;
  color: #4c94ff;
}

.c_post .box {
  margin-bottom: -2px;
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
</style>
