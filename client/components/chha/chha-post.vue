<template>
  <div class="post">
    <div class="filter">
      <h4 class="text-center">Post</h4>
      <hr />
      <form @submit.prevent="filterItems">
        <div class="form-row">
          <div class="col">
            <input
              v-model="filter.caption"
              type="text"
              class="form-control input-text"
              placeholder="Caption"
            />
          </div>
          <div class="col">
            <input
              v-model="filter.numPage"
              type="text"
              class="form-control input-text"
              placeholder="Num page"
            />
          </div>
        </div>
        <div class="form-row">
          <div class="col">
            <b-form-select
              v-model="filter.status"
              :options="statusOpt"
            ></b-form-select>
          </div>
          <div class="col">
            <b-form-select
              v-model="filter.giveStatus"
              :options="giveStatusOpt"
            ></b-form-select>
          </div>
        </div>
        <div class="form-row">
          <div class="col">
            <b-form-select
              v-model="filter.orderBy"
              :options="orderByOpt"
            ></b-form-select>
          </div>
          <div class="col">
            <b-form-select
              v-model="filter.shortBy"
              :options="shortByOpt"
            ></b-form-select>
          </div>
        </div>

        <Br />
        <div class="form-group">
          <button
            v-if="saveFilterItemsLoading == true"
            type="submit"
            class="btn btn-secondary btn-block"
          >
            Search
          </button>
          <button v-else type="button" class="btn btn-secondary btn-block">
            Search...
          </button>
        </div>
      </form>
    </div>

    <div class="detail" v-if="detailShow == true">
      <div class="card">
        <img
          class="card-img-top"
          v-lazy="getImgUrl(detailData.photo, 'photo', 'm_post')"
          alt="Card image cap"
        />
        <ul class="list-group list-group-flush">
          <li class="list-group-item">
            Created date: {{ detailData.created_at }}
          </li>
          <li class="list-group-item">
            Updated date: {{ detailData.updated_at }}
          </li>
          <li class="list-group-item">
            Gave status: {{ detailData.give_status }}
          </li>
          <li class="list-group-item">Gave date: {{ detailData.give_date }}</li>
          <li class="list-group-item">posted by: {{ detailData.name }}</li>
        </ul>
        <div class="card-body">
          <p class="card-text">
            <span v-html="detailData.caption"></span>
          </p>
        </div>
        <hr />
        <div class="card-body text-center">
          <button
            @click="changeStatus()"
            style="border:1px solid #aaa;"
            class="btn"
            :class="detailData.status"
          >
            {{ detailData.status }}
          </button>
          |
          <button
            @click="changeStatusDelete()"
            style="border:1px solid #aaa;"
            class="btn btn-danger"
          >
            Delete
          </button>
        </div>
      </div>
    </div>

    <div class="data">
      <table class="table" style="background:#fff;">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">Caption</th>
            <th scope="col">Status</th>
            <th scope="col">Created</th>
            <th scope="col">Photo</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(item, $index) in items"
            :key="$index"
            @click="showDetail(item)"
          >
            <th scope="row">{{ $index + 1 }}</th>
            <td>{{ item.caption | truncate(10, "...") }}</td>
            <td :class="item.status">{{ item.status }}</td>
            <td>{{ item.created_at }}</td>
            <td>
              <img
                v-lazy="getImgUrl(item.photo, 'photo', 'sm_post')"
                class="img-fluid"
                alt="quixote"
              />
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import myfilter, { getImgUrl } from "../../plugins/myfilter";
export default {
  data() {
    return {
      items: {},
      filter: {
        caption: null,
        numPage: 20,
        shortBy: "asc",
        orderBy: "created_at",
        status: "pending",
        giveStatus: "not-yet-give",
        numPage: 20
      },
      shortByOpt: ["asc", "desc"],
      orderByOpt: ["created_at", "caption", "give_date"],
      statusOpt: ["pending", "active"],
      giveStatusOpt: ["not-yet-give", "active"],
      saveFilterItemsLoading: true,
      detailShow: false,
      detailData: null
    };
  },

  mounted() {
    this.canHere();
  },

  methods: {
    canHere() {
      if (this.user.level != 111) {
        this.$router.push({ path: "/feed" });
      }
    },
    getImgUrl(image, type, size) {
      return getImgUrl(image, type, size);
    },
    filterItems() {
      this.detailShow = false;
      this.detailData = null;
      this.saveFilterItemsLoading = false;
      this.items = {};
      this.$axios.post("chha/post", this.filter).then(response => {
        this.items = response.data;
        this.saveFilterItemsLoading = true;
      });
    },
    changeStatus() {
      this.$swal
        .fire({
          title: "Are you sure?",
          text: "You want to change status!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#d33",
          cancelButtonColor: "#aaa",
          confirmButtonText: "Yes!"
        })
        .then(result => {
          if (result.value) {
            this.$axios
              .post("chha/post-change-status", this.detailData)
              .then(response => {
                if (response) {
                  if (response.data.status == true) {
                    this.detailShow = false;
                    this.$swal.fire(response.data.msg, "", "success");
                    this.items.splice(this.items.indexOf(this.detailData), 1);
                    this.detailData = null;
                  } else {
                    this.$swal.fire(response.data.msg, "", "error");
                  }
                }
              });
          }
        });
    },
    changeStatusDelete() {
      this.$swal
        .fire({
          title: "Are you sure?",
          text: "You want to delete!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#d33",
          cancelButtonColor: "#aaa",
          confirmButtonText: "Yes, delete it!"
        })
        .then(result => {
          if (result.value) {
            this.$axios
              .post("chha/post-delete", this.detailData)
              .then(response => {
                if (response) {
                  if (response.data.status == true) {
                    this.detailShow = false;
                    this.$swal.fire(response.data.msg, "", "success");
                    this.items.splice(this.items.indexOf(this.detailData), 1);
                    this.detailData = null;
                  } else {
                    this.$swal.fire(response.data.msg, "", "error");
                  }
                }
              });
          }
        });
    },
    showDetail(item) {
      this.detailShow = true;
      this.detailData = item;
    }
  }
};
</script>

<style scoped>
.filter {
  background: #fff;
  padding: 15px;
  padding-bottom: 1px;
}

.data,
.detail {
  margin-top: 10px;
}

.filter .form-row {
  margin-bottom: 10px;
}
.active {
  color: green;
}
</style>
