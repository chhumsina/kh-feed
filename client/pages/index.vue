<template>
  <div class="splash">

    <div class="page-header clear-filter" filter-color="orange">
      <div v-if="error" class="alert alert-danger mb-2" role="alert">
        Your token appeared to be invalid. Please try again.
      </div>
      <div
        class="page-header-image"></div>
      <div class="content">
        <div class="card-login card-plain">
          <div class="text-center">
            <span class="text">khfeed</span>
          </div>
          <h6 style="color: #d2d2d2 !important; text-shadow: 0px 2px 3px #555;">a slim social network for Cambodian</h6>
          <small style="text-shadow: 1px 0px 2px #777;color: #d2d2d2;">Mission</small><h4 style="text-shadow: 0px 2px 3px #555;">Only Useful Post</h4>
          <br />
          <br />
          <br />

          <div class="text-center">
            <a style="background: #fff; color: #806f6f; padding: 10px; border-radius: 5px; border: 1px solid #aaa;" @click="socialLogin('google')" class="social-login-btn google">
              Login via <b style="text-shadow: 0px 1px 0px #ddd;color: #4285F4">Google<span v-if="loginloading==true">...</span></b>
            </a>
          </div>
          <a class="term" href="#">Terms & Conditions</a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
    export default {
        data() {
            return {
                error: this.$route.query.error,
                loginloading: false
            }
        },
        methods: {
            socialLogin(service) {
                this.loginloading = true;
                window.location.href = `${process.env.baseUrl}auth/login/${service}`;
            },
            checkHomePage(){
                if(this.$auth.user){
                    window.location.href =  '/feed';
                }
            }
        },
        mounted() {
            this.checkHomePage();
        }
    }
</script>

<style scopped  lang="scss">

  .text {
    right: 50%;
    transform: translate(50%,-50%);
    font-family: verdana;
    font-size: 90px;
    font-weight: 700;
    color: #f5f5f5;
    text-shadow: 1px 1px 1px #919191,
    1px 2px 1px #919191,
    1px 3px 1px #919191,
    1px 4px 1px #919191,
    1px 5px 1px #919191,
    1px 6px 1px #919191,
    1px 7px 1px #919191,
    1px 8px 1px #919191,
    1px 9px 1px #919191,
    1px 10px 1px #919191,
    1px 18px 6px rgba(16,16,16,0.4),
    1px 22px 10px rgba(16,16,16,0.2),
    1px 25px 35px rgba(16,16,16,0.2),
    1px 30px 60px rgba(16,16,16,0.4);
    margin-bottom: 20px;
  }
  .splash .button {
    background: none;
    border: 1px solid #fff;
  }
  .splash .term {
    bottom: 20px;
    position: fixed;
    margin: 0 auto;
    left: 0;
    right: 0;
    color: lightgoldenrodyellow;
    text-decoration: underline;
    line-height: 10px;
  }
  .page-header .page-header-image {
    position: absolute;
    background-size: cover;
    background-position: center center;
    width: 100%;
    height: 100%;
    z-index: -1;
  }
  .card-login {
    border: 0;
    border-radius: 0.1875rem;
    display: inline-block;
    position: relative;
    width: 100%;
    margin-bottom: 30px;
  }

  .card-plain {
    background: transparent;
    box-shadow: none;
  }

  .splash .card-login {
    border-radius: 0.25rem;
    padding-bottom: 0.7rem;
  }

  .splash .card-login .logo-container {
    width: 115px;
    margin: 0 auto;
    margin-bottom: 55px;
  }

  .splash .card-login .logo-container img {
    width: 100%;
  }

  .page-header {
    min-height: 100vh;
    max-height: 999px;
    padding: 0;
    color: #ffffff;
    position: relative;
    overflow: hidden;
  }

  .page-header > .content {
    margin-top: 20%;
    text-align: center;
    margin-bottom: 50px;
  }

  .clear-filter[filter-color='orange'] {
    background: rgba(44, 44, 44, 0.2);
    background: -webkit-linear-gradient(
      90deg,
      rgba(44, 44, 44, 0.2),
      rgba(66,133,244, 0.6)
    );
    background: -o-linear-gradient(
      90deg,
      rgba(44, 44, 44, 0.2),
      rgba(66,133,244, 0.6)
    );
    background: -moz-linear-gradient(
      90deg,
      rgba(44, 44, 44, 0.2),
      rgba(66,133,244, 0.6)
    );
    background: linear-gradient(
      0deg,
      rgba(44, 44, 44, 0.2),
      rgba(66,133,244, 0.6)
    );
  }
</style>
