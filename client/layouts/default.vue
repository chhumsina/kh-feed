<template>
  <div>
    <TopNav v-if="$auth.user && showTopNav.includes(this.$route.name)" />
    <nuxt />
    <Menu v-if="$auth.user && !showMenu.includes(this.$route.name)" />
  </div>
</template>
<script>
    import Menu from '~/components/Menu'
    import TopNav from '~/components/TopNav'

    export default {
        components: {
            Menu,
            TopNav
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
  .btn-primary {
    background-color: #652828f2;
    border-color: #3e0b0b;
  }
  .go-back{
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
