<template>
  <div class="">
    <div class="articles card" v-if="items != null">
      <div class="card-header d-flex align-items-center">
        <h2 class="h3" style="margin-top: 10px;">១០០នាក់ដំបូង</h2>
        <div
          class="badge badge-rounded bg-green​"
          style="position: absolute; right: 0; color: #dc8b38;"
        >
          {{ items.length }} នាក់​ ឥលូវនេះ
        </div>
      </div>
        <p style="padding: 20px; padding-bottom: 0; color: #ccc;">ពួកគេនឹងទទួលបានសៀវភៅម្នាក់មួយក្បាល។</p>
      <div
        class="card-body no-padding"
        v-for="(item, $index) in items"
        :key="$index"
      >
        <div class="item d-flex align-items-center">
          <div class="image">
            <img
              :src="item.avatar | getImgUrl('avatar', 'sm_avatar')"
              :alt="item.name"
            />
          </div>
          <div class="text">
            <a href="#">
              <h3 class="h5">{{ item.name }}</h3></a
            ><small>ក្នុងថ្ងៃទី {{ item.date }} </small>
          </div>
        </div>
      </div>
    </div>
    <div
        v-if="!$auth.user" 
        style="position: fixed; bottom: 0px; width: 100%; background: #fff; padding: 14px;padding-bottom: 0; box-shadow: 0 0px 3px #555;"
      >
        <nuxt-link :to="`/feed`">
          <p>Go home page</p>
        </nuxt-link>
      </div>
  </div>
</template>

<script>
import myfilter, { getImgUrl } from "~/plugins/myfilter";
export default {
  head() {
    return {
      titleTemplate: "១០០នាក់ - %s"
    };
  },
  data() {
    return {
      items: null,
      loader: require("assets/default/loader.gif"),
      loadingItem: true
    };
  },
  created() {
    this.getItems();
  },
  methods: {
    async getItems() {
      if (this.loadingItem == false) {
        return false;
      }
      this.$axios
        .post("pub/campaign", { type: "cam100neak" })
        .then(({ data }) => {
          this.items = data;
          this.loadingItem = false;
        });
    }
  }
};
</script>

<style scoped>
.card-body {
    padding: 0;
}
.articles a {
  text-decoration: none !important;
  display: block;
  margin-bottom: 0;
  color: #555;
}

.articles .badge {
  font-size: 0.7em;
  padding: 5px 10px;
  line-height: 1;
  margin-left: 10px;
}

.articles .item {
  padding: 20px;
}

.articles .item:nth-of-type(even) {
  background: #fafafa;
}

.articles .item .image {
  margin-right: 15px;
}

.articles .item h3 {
  color: #555;
  font-weight: 400;
  margin-bottom: 0;
  font-size: 14px;
}

.articles .item small {
  color: #aaa;
  font-size: 0.75em;
}

.card-close {
  position: absolute;
  top: 15px;
  right: 15px;
}

.card-close .dropdown-toggle {
  color: #999;
  background: none;
  border: none;
}

.card-close .dropdown-toggle:after {
  display: none;
}

.card-close .dropdown-menu {
  border: none;
  min-width: auto;
  font-size: 0.9em;
  border-radius: 0;
  -webkit-box-shadow: 3px 3px 3px rgba(0, 0, 0, 0.1),
    -2px -2px 3px rgba(0, 0, 0, 0.1);
  box-shadow: 3px 3px 3px rgba(0, 0, 0, 0.1), -2px -2px 3px rgba(0, 0, 0, 0.1);
}

.card-close .dropdown-menu a {
  color: #999 !important;
}

.card-close .dropdown-menu a:hover {
  background: #796aee;
  color: #fff !important;
}

.card-close .dropdown-menu a i {
  margin-right: 10px;
  -webkit-transition: none;
  transition: none;
}
</style>
