<template>
<div class="blurred-box">
  
    <p style="margin-top: 40px; color: #333; font-size: 30px;">
        បរិច្ចាគសៀវភៅរបស់អ្នក<br/>នៅទីនេះ
    </p>
  <h1 style="color: #fff; margin-top: 66px; text-shadow: 1px 1px 0 #000; font-weight: bold;">khfeed.com</h1>
  <div class="user-login-box">

    <p style="margin-bottom: 60px; margin-top: -45px; color: #828282;">
        បណ្ដាញ​​សង្គមសម្រាប់បរិច្ចាគសៀវភៅ
    </p>
    
    <div class="text-center">
            <a style="color: #806f6f; padding: 10px; border-radius: 5px; box-shadow: inset 0 0 4px #aaa; }" @click="socialLogin('google')" class="social-login-btn google">
              ចូលគណនីតាម <b style="text-shadow: 0px 1px 0px #ddd;color: #4285F4">Google <span v-if="loginloading==true">
              <img :src="loader"/>
            </span></b>
            </a>
          </div>

  </div>

<div class="home-footer">
  <ul>
    <li>មតិ​យោបល់ -</li>
    <li>ការប្រើប្រាស់ -</li>
    <li>អំពី​ khfeed.com</li>
  </ul>
  </div>

</div>
</template>

<script>
    export default {
        data() {
            return {
                error: this.$route.query.error,
                loginloading: false,
                loader: require('../assets/default/loader.gif'),
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
@font-face {
  font-family: "Battambang";
  src: url("~assets/fonts/battambang.woff2") format("woff2");
}

body{
 background-repeat: no-repeat;
 background-attachment: fixed;
 background-size: cover;
 background-position: top;
 background-image:url(https://images.unsplash.com/photo-1516979187457-637abb4f9353?ixlib=rb-1.2.1&auto=format&fit=crop&w=1500&q=80);
 width: 100%;
 height: 100%;
 font-family: Battambang,Arial,Helvetica,sans-serif !important;
 letter-spacing: 0.02em;
  font-weight: 400;
 -webkit-font-smoothing: antialiased; 
}
.home-footer ul {
    padding: 0;
    list-style: none;
    bottom: 0;
    position: fixed;
    left: 23px;
}
.home-footer ul li {
    display: inline;
    color: #316bc3;
    font-size: 13px;
}
.blurred-box {
    text-align: center;
    position: relative;
    top: calc(50% - 175px);
    background: rgba(234, 234, 234, 0.03922);
    border-radius: 2px;
    width: 317px;
    margin-left: 12px;
}
.blurred-box:after{
 content: ''; 
 background: inherit; 
 position: absolute;
 left: -25px;
 left: position;
 right: 0;
 top: -25px;  
 top: position ;
 bottom: 0;
//  box-shadow: inset 0 0 0 200px rgba(255,255,255,0.05);
 filter: blur(10px);
}


/* Form which you dont need */
.user-login-box{
  position: relative;
  margin-top: 50px;
  text-align: center;
  z-index: 1;
}



input.user-password{
  width: 120px;
  height: 18px;
  opacity: 0.4;
  border-radius: 2px;
  padding: 5px 15px;
  border: 0;
}
</style>
