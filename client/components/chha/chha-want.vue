<template>
  <div class="post">
    <div class="filter">
      <h4 class="text-center">People Want</h4>
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
            <input
              v-model="filter.username"
              type="text"
              class="form-control input-text"
              placeholder="Username"
            />
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
            Created date: <small class="text-muted"
                    ><timeago
                      :datetime="detailData.created_at"
                      :auto-update="10"
                    ></timeago
                  ></small> 
          </li>
          <li class="list-group-item">
            Updated date: <small class="text-muted"
                    ><timeago
                      :datetime="detailData.updated_at"
                      :auto-update="10"
                    ></timeago
                  ></small> 
          </li>
          <li class="list-group-item">
            Gave status: <span :class="detailData.give_status">{{ detailData.give_status }}</span> 
          </li>
          <li :class="detailData.accept_status" class="list-group-item">Gave date: {{ detailData.give_date }}</li>
          <li class="list-group-item">posted by: {{ detailData.name }}</li>
        </ul>
        <div class="card-body">
          <p class="card-text">
            {{ detailData.caption | truncate(150, "...") }}
          </p>
        </div>
        <hr />
        <div class="table-responsive-lg">
          <table class="table table-striped">
            <tbody>
              <tr
                v-for="(item, $index) in detailDataPeople"
                :key="$index"
                @click="showDetailInfo(item)"
              >
                <th scope="row">{{ $index + 1 }}</th>
                <td>
                  <img
                    :src="item.avatar | getImgUrl('avatar', 'sm_avatar')"
                    alt="User Image"
                  />
                </td>
                <td>{{ item.name }}</td>
                <td>
                  <small class="text-muted"
                    ><timeago
                      :datetime="item.created_at"
                      :auto-update="10"
                    ></timeago
                  ></small>
                </td>
                <td :class="item.accept_status">{{ item.accept_status }}</td>
                <td>
                  <small class="text-muted"
                    ><timeago
                      :datetime="item.accept_date"
                      :auto-update="10"
                    ></timeago
                  ></small>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="data">
      <table class="table" style="background:#fff;">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">Caption</th>
            <th scope="col">Wants</th>
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
            <td class="font-weight">{{ item.wants }}</td>
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
    <br /><br />
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
        username: null,
        giveStatus: "pending",
        numPage: 20
      },
      shortByOpt: ["asc", "desc"],
      orderByOpt: ["created_at", "wants", "caption", "give_date"],
      giveStatusOpt: ["pending","selected", "active"],
      saveFilterItemsLoading: true,
      detailShow: false,
      detailData: null,
      detailDataPeople: null
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
      this.detailDataPeople = null;
      this.saveFilterItemsLoading = false;
      this.items = {};
      this.$axios.post("chha/people-want", this.filter).then(response => {
        this.items = response.data;
        this.saveFilterItemsLoading = true;
      });
    },
    showDetail(item) {
      this.detailShow = true;
      this.detailData = item;

      this.$axios
        .post("chha/people-want-detail", this.detailData)
        .then(response => {
          this.detailDataPeople = response.data;
        });
    },
    showDetailInfo(item) {
      this.$swal
        .fire({
          title: item.name,
          text: item.desc,
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "green",
          cancelButtonColor: "#aaa",
          confirmButtonText: "Select"
        })
        .then(result => {
          if (result.value) {
            this.$axios
              .post("chha/people-want-select", {id:item.want_id})
              .then(response => {
                if (response) {
                  if (response.data.status == true) {
                    this.detailShow = false;
                    this.$swal.fire(response.data.msg, "", "success");
                    this.items.splice(this.items.indexOf(this.detailData), 1);
                    this.detailData = null;
                    this.detailDataPeople = null;
                  } else {
                    this.$swal.fire(response.data.msg, "", "error");
                  }
                }
              });
          }
        });
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
.selected {
  color: orangered;
}
</style>
