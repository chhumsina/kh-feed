<template>
  <div class="post-id container">
    <p class="go-back">
      <a href="javascript:history.back()">
        <i class="fa fa-arrow-left" aria-hidden="true"></i>
      </a>
    </p>
    <div class="container">
      <div class="text-center profile">
        <img
          src="http://linhd.uned.es/wp-content/uploads/2014/03/inicio2-300x200.jpg"
        />

        <div class="col-md-8 col-lg-6 col-xl-5 p-0 mx-auto">
          <h4 class="font-weight-bold my-4">Mike Greene</h4>

          <div class="text-muted mb-4">
            {{dataModal.caption}}
          </div>
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
      page: 1,
      dataModal: '',
      loadingModal: true
    }
  },
  computed: {
    postModal: function() {
      return this.dataModal
    }
  },
  watch: {
    $route(to, from) {
      // if the current history index isn't at the last position, use 'back' transition
      if (to.hash == '') {
        this.$modal.hide('post-modal')
      }
    }
  },
  mounted() {},
  methods: {
    async logout() {
      await this.$auth.logout()
    },
    async showPostModal(id) {
      this.loadingModal = true
      // this.$router.push({ name: 'feed', hash: '#post' });
      this.$root.$emit('bv::show::modal', 'post-modal')
      this.$axios.get('post/detail/' + id).then(({ data }) => {
        if (data) {
          this.dataModal = data
          this.loadingModal = false
        }
      })
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
.post-id {
  margin-top: 15px;
}
.post-id .ui-w-100 {
  width: 100px !important;
  height: auto;
}
.post-id .font-weight-bold {
  font-weight: 700 !important;
}

.post-id .tinytabs .tabs {
  width: 100%;
  text-align: center;
  border-top: 1px solid #ddd;
  border-bottom: 1px solid #ddd;
  padding: 10px 0px;
  z-index: 1;
}

.post-id .tinytabs .tabs .tab .close {
  padding-left: 5px;
}
.post-id .tinytabs .tabs .tab {
  margin: 0 3px 0 0;
  padding: 6px 15px;
  text-decoration: none;
  font-weight: bold;
}
.post-id .tinytabs .section {
  overflow: hidden;
  clear: both;
  width: 100%;
  th: 100%;
  margin-top: -25px;
}
.post-id .tinytabs .tab.sel {
  color: #000;
  text-shadow: none;
}
.post-id .nav-tabs {
  border-bottom: 0;
  margin-bottom: 50px;
}

.post-id .posts-content {
  margin-top: 20px;
}
.post-id .ui-w-40 {
  width: 40px !important;
  height: auto;
}
.post-id .default-style .ui-bordered {
  border: 1px solid rgba(24, 28, 33, 0.06);
}
.post-id .ui-bg-cover {
  background-color: transparent;
  background-position: center center;
  background-size: cover;
}
.post-id .ui-rect {
  padding-top: 50% !important;
}
.post-id .ui-rect,
.post-id .ui-rect-30,
.post-id .ui-rect-60,
.post-id .ui-rect-67,
.post-id .ui-rect-75 {
  position: relative !important;
  display: block !important;
  padding-top: 100% !important;
  width: 100% !important;
}
.post-id .card-footer,
.post-id .card hr {
  border-color: rgba(24, 28, 33, 0.06);
}
.post-id .ui-rect-content {
  position: absolute !important;
  top: 0 !important;
  right: 0 !important;
  bottom: 0 !important;
  left: 0 !important;
}
.post-id .default-style .ui-bordered {
  border: 1px solid rgba(24, 28, 33, 0.06);
}

.post-id .card-body {
  padding: 0;
}
.post-id .post-desc {
  padding: 10px;
  padding-bottom: 0px;
  font-size: 13px;
  line-height: 14px;
  margin-bottom: 9px;
}
.post-id .media {
  padding: 10px;
  padding-bottom: 0;
  margin-bottom: -5px !important;
}
.post-id .media-body {
  font-size: 13px;
  margin-left: 10px !important;
}
.post-id img.d-block.ui-w-40.rounded-circle {
  width: 33px !important;
}
.post-id .post-item {
  margin-top: 2px;
  margin-bottom: 10px;
}
.post-id .posts {
  margin-right: -15px;
  margin-left: -15px;
  margin-top: 65px;
}
.post-id ul.follower-list {
  list-style: none;
  background: #fff;
  border-bottom: 1px solid #ccc;
  padding: 5px;
  margin-top: 2px;
}
.post-id ul.follower-list li {
  display: contents;
  margin: 15px;
}
.post-id ul.follower-list li img {
  width: 23.9%;
  border: 3px solid #f7f9fb;
}

.post-id .profile {
  margin-bottom: 1px;
  margin-top: 0px;
  background: #fff;
  margin-left: -15px;
  margin-right: -15px;
  padding: 25px;
}
.post-id .rounded-circle {
  border-radius: 50% !important;
  border: 1px solid #dee2e6;
  padding: 2px;
}
</style>
