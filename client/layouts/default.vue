<template>
  <div>
    <!--    <TopNav v-if="$auth.user && showTopNav.includes(this.$route.name)" />-->
    <nuxt />
    <Menu v-if="$auth.user && !hideMenu.includes(this.$route.name)" />
  </div>
</template>
<script>
import Menu from "~/components/Menu";

export default {
  head() {
    return {
      titleTemplate: this.$route.name+' - %s'
    };
  },
  components: {
    Menu
  },
  data() {
    return {
      showTopNav: ["recommend", "feed", "messages"],
      hideMenu: [
        "post-id",
        "create-post",
        "reaction",
        "create-shop",
        "sell-book",
        "people-want-id"
      ]
    };
  }
};
</script>
<style>
.selected {
  color: orangered !important;
}
.pending {
  color: orange !important;
}

.dashboard-seleced {
  background: #f3f3f37d;
  margin-left: -15px;
  margin-right: -15px;
  padding: 0px 21px;
  padding-bottom: 20px;
}
li.media.item-selected {
  padding-bottom: 0 !important;
}
.get-book {
  border-top: 1px solid #f7f7f7;
  margin-top: 17px;
  padding-top: 10px;
  text-align: center;
}
.got-book {
  border-top: 1px solid #f7f7f7;
  margin-top: 12px;
  padding-top: 13px;
  font-size: 12px;
  color: green;
  text-align: right;
}

.media-body p {
  font-size: 15px !important;
  line-height: 24px !important;
}
.modal-content .caption {
  font-size: 15px !important;
  line-height: 24px;
}
div,
span,
p,
h1,
h2,
h3,
h4,
h5,
h6,
b,
time {
  font-family: Battambang, Arial, Helvetica, sans-serif !important;
}
a:hover,
a:visited,
a:link,
a:active {
  text-decoration: none !important;
}
body {
  background-color: #eeeeee;
}
.alert.alert-success {
  border-radius: 0;
  background: #f1f1f1;
  border: 1px solid #dfdfdf;
  padding-bottom: 0px;
}
.username {
  font-size: 14px !important;
  padding-top: 2px;
  color: #555;
}
.margin-left-0 {
  margin-left: 0 !important;
}
.no-border {
  border: 0 !important;
}
.c_post,
.recommend,
.messages {
  padding-left: 0;
  padding-right: 0;
}
.modal-header-post .user-block img {
  width: 40px;
  height: 40px;
  float: left;
}

.modal-header-post .user-block .username {
  font-size: 16px;
  font-weight: 600;
}

.modal-header-post .user-block .description {
  color: #999;
  font-size: 13px;
}

.modal-header-post-content {
  margin-left: -1rem;
  margin-right: -1rem;
}
.modal-dialog {
  max-width: 100%;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  display: flex;
  height: 100vh;
  margin: 0;
}

.post-modal-content .photo-content .photo {
  /*position: absolute;*/
  left: 0;
  right: 0;
  top: 0;
  bottom: 0;
  margin: 0 auto;
  height: 100%;
}

.post-modal-content .loader,
.post-modal-content .loader:after {
  border-radius: 50%;
  width: 5em;
  height: 5em;
  left: 0;
  right: 0;
  bottom: 0;
  position: inherit;
  top: 15px;
  margin: 0 auto;
}

.post-modal-content .loading {
  position: relative;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  background: #fff;
}

.post-modal-content .photo-content {
  background: #ddd;
  /*height: 224px;*/
  overflow: hidden;
  position: relative;
}

.thumbnail {
  /*height: 250px;*/
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  background: #ddd;
}

.thumbnail img {
  max-width: 100%;
  max-height: 100%;
  display: block;
  margin: 0 auto;
  width: 100%;
}

.modal-header-post .user-block .username,
.user-block .description,
.user-block .comment {
  display: block;
  margin-left: 50px;
}

.modal-header-post .modal-body {
  padding: 0;
}

.modal-header-post .download-files ul {
  list-style: none;
  padding: 0;
  padding-top: 12px;
}

.modal-header-post .download-files {
  text-align: right;
  margin-bottom: -10px;
}

.modal-header-post .download-files ul .file-item {
  display: flex;
}

.modal-header-post .download-files ul .file-item i {
  color: brown;
  padding-top: 2px;
}

.form-control {
  border-radius: 0 !important;
}

.btn-primary {
  background-color: #652828f2;
  border-color: #3e0b0b;
}

.hr-divider {
  margin-top: -9px;
  margin-bottom: 9px;
}

.go-back {
  position: fixed;
  z-index: 1;
  background: #fff;
  padding: 10px;
  border-radius: 50%;
  height: 40px;
  width: 40px;
  text-align: center;
  padding-top: 8px;
  box-shadow: 0 0px 3px;
}

.btn-save-post {
  padding: 0 7px;
  border-radius: 2px;
  width: auto;
  height: 25px;
  margin-top: -1px;
  margin-bottom: 9px;
  right: 10px;
  position: absolute;
  top: 26px;
  font-size: 14px;
}

.btn-delete-post {
  padding: 0 7px;
  border-radius: 2px;
  width: auto;
  height: 25px;
  margin-top: -1px;
  margin-bottom: 9px;
  right: 10px;
  position: absolute;
  top: 26px;
  font-size: 17px;
  color: #a21d00;
}

.loading-inline {
  height: 40px;
}

.loading {
  position: fixed;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  background: #fff;
}

.loader {
  left: 50%;
  margin-left: -2em;
  font-size: 7px;
  border: 0.5em solid rgba(218, 219, 223, 1);
  border-left: 0.5em solid #000;
  animation: spin 1.1s infinite linear;
}

.loader,
.loader:after {
  border-radius: 50%;
  width: 5em;
  height: 5em;
  display: block;
  position: absolute;
  top: 50%;
  margin-top: -4.05em;
}

@keyframes spin {
  0% {
    transform: rotate(360deg);
  }
  100% {
    transform: rotate(0deg);
  }
}

/*  post*/
.post_property {
  margin: 10px 0;
  margin-top: 0px;
  width: 100%;
}
.post_view_num {
  padding: 0px 10px;
}
.c_post .download-files ul {
  list-style: none;
  padding: 0;
}

.c_post .download-files {
  text-align: right;
}

.c_post .download-files ul .file-item {
  display: inline-block;
  padding: 0px 3px;
}

.c_post .download-files ul .file-item i {
  color: brown;
}
.c_post .box {
  position: relative;
  border-radius: 2px;
  background: #ffffff;
  border-top: 1px solid #f7f7f7;
  margin-bottom: 10px;
  width: 100%;
  border-bottom: 1px solid #f7f7f7;
}

.c_post .box-header.with-border {
  border-bottom: 1px solid #fff;
}

.c_post .box-header {
  color: #444;
  display: block;
  padding: 10px;
  position: relative;
}

.c_post .user-block img {
  width: 40px;
  height: 40px;
  float: left;
}

.c_post .user-block .username {
  font-size: 16px;
  font-weight: 600;
}

.c_post .user-block .description {
  color: #999;
  font-size: 11px;
}

.c_post .user-block .username,
.c_post .user-block .description,
.c_post .user-block .comment {
  display: block;
  margin-left: 50px;
}

.c_post .box-header > .box-tools {
  position: absolute;
  right: 10px;
  top: 5px;
}

.c_post .btn-box-tool {
  padding: 5px;
  font-size: 12px;
  background: transparent;
  color: #97a0b3;
}

.c_post .box-body {
  border-top-left-radius: 0;
  border-top-right-radius: 0;
  border-bottom-right-radius: 3px;
  border-bottom-left-radius: 3px;
  padding: 10px;
}

.c_post .attachment-block {
  border: 1px solid #f4f4f4;
  padding: 5px;
  margin-bottom: 20px;
  background: #fbfbfb;
}

.c_post .attachment-block .attachment-img {
  max-width: 100px;
  max-height: 100px;
  height: auto;
  float: left;
}

.c_post .attachment-block .attachment-pushed {
  margin-left: 110px;
}

.c_post .attachment-block .attachment-heading {
  margin: 0;
}

.c_post .attachment-block .attachment-heading .h4,
.c_post .attachment-block .attachment-heading h4 {
  font-size: 18px;
}

.c_post .attachment-block .attachment-text {
  color: #555;
}
.c_post .box-footer {
  border-top-left-radius: 0;
  border-top-right-radius: 0;
  border-bottom-right-radius: 3px;
  border-bottom-left-radius: 3px;
  background-color: #fff;
  padding-bottom: 20px;
  padding-left: 19px;
  padding-right: 13px;
  padding-top: 13px;
}
.c_post .box-comments .box-comment:first-of-type {
  padding-top: 0;
}
.c_post .box-comments .box-comment {
  padding: 8px 0;
  border-bottom: 1px solid #eee;
  padding-bottom: 5px;
}
.c_post .img-sm,
.c_post .box-comments .box-comment img,
.c_post .user-block.user-block-sm img {
  width: 30px;
  height: 30px;
}
.c_post .img-sm,
.c_post .img-md,
.c_post .img-lg,
.c_post .box-comments .box-comment img,
.c_post .user-block.user-block-sm img {
  float: left;
}
.c_post .box-comments .comment-text {
  margin-left: 40px;
  color: #808080;
}
.c_post .box-comments .username {
  color: #444;
  display: block;
  font-weight: 600;
  margin-bottom: -4px;
}
.c_post .box-comments .text-muted {
  font-weight: 400;
  font-size: 12px;
}
.c_post .img-sm + .img-push {
  margin-left: 40px;
}
.c_post .box .form-control {
  border-radius: 0;
  box-shadow: none;
  border-color: #d2d6de;
}
.c_post .post-img {
  margin-left: -10px;
  margin-right: -10px;
  text-align: center;
  padding-top: 7px;
}
/*  post*/

.easy-bottom-bar {
  background-color: #19caad;
  color: #fff;
  border: none;
}

.easy-bottom-bar a {
  color: #cdd !important;
}

.easy-fa-color {
  color: #fff !important;
}

html {
  font-family: "Source Sans Pro", -apple-system, BlinkMacSystemFont, "Segoe UI",
    Roboto, "Helvetica Neue", Arial, sans-serif;
  font-size: 16px;
  word-spacing: 1px;
  -ms-text-size-adjust: 100%;
  -webkit-text-size-adjust: 100%;
  -moz-osx-font-smoothing: grayscale;
  -webkit-font-smoothing: antialiased;
  box-sizing: border-box;
}

*,
*:before,
*:after {
  box-sizing: border-box;
  margin: 0;
}

.button--green {
  display: inline-block;
  border-radius: 4px;
  border: 1px solid #3b8070;
  color: #3b8070;
  text-decoration: none;
  padding: 10px 30px;
}

.button--green:hover {
  color: #fff;
  background-color: #3b8070;
}

.button--grey {
  display: inline-block;
  border-radius: 4px;
  border: 1px solid #35495e;
  color: #35495e;
  text-decoration: none;
  padding: 10px 30px;
  margin-left: 15px;
}

.button--grey:hover {
  color: #fff;
  background-color: #35495e;
}
.swal2-actions button {
  padding: 6px 15px !important;
}
.swal2-title {
  font-size: 16px !important;
  line-height: 30px !important;
}
.modal-footer,
.modal-header {
  display: none;
}

.create-comment {
  display: inline;
  display: initial;
  -webkit-box-align: center;
  align-items: center;
  -webkit-box-pack: initial;
  justify-content: initial;
  padding: 0rem;
  border-top: 1px solid #dae1e7;
  border-bottom-right-radius: 0;
  border-bottom-left-radius: 0;
  position: fixed;
  bottom: 0;
  width: 100%;
  background: #fff;
  padding: 10px;
}
.modal-header-post {
  display: -webkit-box;
  display: flex;
  -webkit-box-align: start;
  align-items: flex-start;
  -webkit-box-pack: justify;
  justify-content: space-between;
  padding: 1rem 1rem;
  border-bottom: 0;
  border-top-left-radius: 0;
  border-top-right-radius: 0;
  position: fixed;
  background: #fff;
  z-index: 1;
  width: 100%;
  top: 0;
  border-bottom: 1px solid #dae1e7;
  left: 0;
}
.modal-body {
  position: relative;
  -webkit-box-flex: 1;
  flex: 1 1 auto;
  padding: 0px;
  margin-top: 28px;
  margin-bottom: 70px;
}
.modal-open .modal {
  background: #fff;
}
.modal-header .close {
  display: none;
}
.modal-content {
  position: relative;
  display: -webkit-box;
  display: flex;
  -webkit-box-orient: vertical;
  -webkit-box-direction: normal;
  flex-direction: column;
  width: 100%;
  pointer-events: auto;
  background-color: #fff;
  background-clip: padding-box;
  border: 0;
  border-radius: 0;
  outline: 0;
}

.input-box {
  border-radius: 25px !important;
  padding-left: 15px;
  font-size: 15px;
  height: 40px;
  border: 1px solid #efefef !important;
}
.avatar-comment {
  height: 41px !important;
  width: 41px !important;
  margin-left: -9px !important;
  margin-top: -2px !important;
}

.no-border-left {
  border-left: 0;
  background: #fff;
}

.nav-tabs {
  border-bottom: 0 !important;
  margin-bottom: -14px !important;
}

.nav-tabs .nav-link.active,
.nav-tabs .nav-item.show .nav-link {
  color: #495057;
  background-color: initial;
  border: 0 !important;
}

.nav-tabs .nav-link {
  border: 0px solid transparent;
  border-top-left-radius: 0.25rem;
  border-top-right-radius: 0.25rem;
  font-weight: bold;
}

.slide-profile .swiper-slide a,
.list a {
  color: #555;
}

/*Sina style*/

.sina-list-item .item-image {
  float: left;
  margin-right: 12px;
}

.sina-list-item {
  border-bottom: 1px solid #f3f2f2;
  padding: 10px;
  padding-top: 5px;
  padding-bottom: 10px;
}

.sina-list-item .item-name {
  margin-top: 3px;
  font-size: 14px;
  color: #555;
}

.sina-list-item .item-text {
  margin-left: 50px;
}

.sina-list-item .item-desc {
  margin-top: -15px;
  font-size: 11px;
  color: rgb(156, 156, 156);
}
.sina-list-item .item-desc time {
  font-size: 11px;
}
.pending {
  color: orange;
}
.open {
  color: orange;
}
.closed {
  color: green;
}

.font-13 {
  font-size: 13px !important;
}
</style>
