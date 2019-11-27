<template>
  <div>
    <!--    <TopNav v-if="$auth.user && showTopNav.includes(this.$route.name)" />-->
    <nuxt/>
    <Menu v-if="$auth.user && !showMenu.includes(this.$route.name)"/>
  </div>
</template>
<script>
    import Menu from '~/components/Menu'

    export default {
        components: {
            Menu
        },
        data() {
            return {
                showTopNav: ['recommend', 'feed', 'messages'],
                showMenu: ['post-id', 'profile-id']
            }
        }
    }
</script>
<style>
  body {
    background-color: #eeeeee;
  }

  .post-modal .user-block img {
    width: 40px;
    height: 40px;
    float: left;
  }

  .post-modal .user-block .username {
    font-size: 16px;
    font-weight: 600;
  }

  .post-modal .user-block .description {
    color: #999;
    font-size: 13px;
  }

  .post-modal-content {
    margin-left: -1rem;
    margin-right: -1rem;
  }

  .post-modal-content .photo-content .photo {
    position: absolute;
    left: 0;
    right: 0;
    top: 0;
    bottom: 0;
    margin: 0 auto;
    height: 100%;
  }
  .post-modal-content .title, .post-modal-content .caption {
    padding: 10px;
  }

  .post-modal-content .photo-content {
    background: #ddd;
    height: 224px;
    overflow: hidden;
    position: relative;
  }

  .post-modal .user-block .username,
  .user-block .description,
  .user-block .comment {
    display: block;
    margin-left: 50px;
  }

  .post-modal .modal-body {
    padding: 0;
  }

  .post-modal .download-files ul {
    list-style: none;
    padding: 0;
  }

  .post-modal .download-files {
    text-align: right;
  }

  .post-modal .download-files ul .file-item {
    display: inline;
  }

  .post-modal .download-files ul .file-item i {
    color: brown;
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
</style>
<style scopped>
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
    font-family: 'Source Sans Pro', -apple-system, BlinkMacSystemFont, 'Segoe UI',
    Roboto, 'Helvetica Neue', Arial, sans-serif;
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
</style>
