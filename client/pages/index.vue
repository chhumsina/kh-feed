<template>
  <div class="splash">
    <div class="page-header clear-filter" filter-color="orange">
      <div
        class="page-header-image"></div>
      <div class="content">
        <div class="card-login card-plain">
          <div class="text-center">
            <div class="logo-container">
              <img
                src="https://raw.githack.com/creativetimofficial/now-ui-kit/master/assets/img/now-logo.png"
                alt=""
              />
            </div>
          </div>
          <h4>Welcomesss to IHere</h4>
          <h5 class="text-muted">Bring people the tip of world class feed</h5>
          <br />
          <div class="text-center">
            <button @click="socialLogin('google')" class="social-login-btn google">
              <i class="fab fa-google"></i>
            </button>
          </div>
          <a class="term" href="#">Terms & Conditions</a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
    export default {
        methods: {
            socialLogin(service) {
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

<style scopped>
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
      rgba(224, 23, 3, 0.6)
    );
    background: -o-linear-gradient(
      90deg,
      rgba(44, 44, 44, 0.2),
      rgba(224, 23, 3, 0.6)
    );
    background: -moz-linear-gradient(
      90deg,
      rgba(44, 44, 44, 0.2),
      rgba(224, 23, 3, 0.6)
    );
    background: linear-gradient(
      0deg,
      rgba(44, 44, 44, 0.2),
      rgba(224, 23, 3, 0.6)
    );
  }
</style>
