<template>
  <div class="account">
    <div class="container">
      <div class="d-flex justify-content-between align-items-center py-4">
        <div>
          <nuxt-link to="create-post">
            <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Create Post
          </nuxt-link>
        </div>
        <div>
          <div @click.prevent="logout">
            <i class="fa fa-sign-out"/>
            Logout
          </div>
        </div>
      </div>
    </div>
    <hr class="m-0"/>

    <div class="container">
      <div class="text-center profile">
        <div style="position: relative;">
          <img v-lazy="getImgUrl(user.avatar, 'avatar', 'm_avatar')" class="ui-w-100 rounded-circle"/>
          <div
            style="position: absolute; margin: 0 auto; left: 0; right: 0; bottom: -15px; color: #035398; font-size: 11px;"
            @click="$refs.avatar.click()">
            Change
            <input accept="image/x-png,image/jpeg" ref="avatar" style="width: 100%; display: none;"
                   @change="changeAvatar('avatar', $event)" type="file" name="avatar"
                   id="addAvatarId"/>
          </div>
        </div>

        <div class="col-md-8 col-lg-6 col-xl-5 p-0 mx-auto">
          <h4 class="font-weight-bold my-4"> {{user.name}}</h4>

          <div v-if="user.bio" class="text-muted mb-4">
            <i class="fa fa-quote-left" aria-hidden="true"></i> {{user.bio}} <i class="fa fa-quote-right" aria-hidden="true"></i>
          </div>
        </div>
      </div>
    </div>

    <no-ssr>
      <div class="account-tabs">
        <b-tabs content-class="mt-3" align="center">
          <b-tab title="Giveaways" active>
            <post-item/>
          </b-tab>
          <b-tab title="Overview">
            <div class="overview-list">
              <form @submit.prevent="overview">

                <div class="input-group mb-3">
                  <input v-model="form.name" type="text" class="form-control input-text"
                         :class="{ 'is-invalid': errors.name }"
                         placeholder="Name">
                  <div class="input-group-prepend">
                    <span class="input-group-text no-border-left" id="basic-addon1"><i class="fa fa-user"
                                                                                       aria-hidden="true"></i></span>
                  </div>
                  <div class="invalid-feedback" v-if="errors.name">
                    {{errors.name[0]}}
                  </div>
                </div>
                <div class="input-group mb-3">
                  <input v-model="form.email" type="text" class="form-control input-text"
                         :class="{ 'is-invalid': errors.email }"
                         placeholder="E-mail">
                  <div class="input-group-prepend">
                  <span class="input-group-text no-border-left" id="basic-addon1"><i class="fa fa-envelope"
                                                                                     aria-hidden="true"></i></span>
                  </div>
                  <div class="invalid-feedback" v-if="errors.email">
                    {{errors.email[0]}}
                  </div>
                </div>
                <div class="input-group mb-3">
                  <input v-model="form.phone" type="text" class="form-control input-text"
                         :class="{ 'is-invalid': errors.phone }"
                         placeholder="Phone">
                  <div class="input-group-prepend">
                    <span class="input-group-text no-border-left" id="basic-addon1"><i class="fa fa-phone"
                                                                                       aria-hidden="true"></i></span>
                  </div>
                  <div class="invalid-feedback" v-if="errors.phone">
                    {{errors.phone[0]}}
                  </div>
                </div>
                <div class="input-group mb-3">
                  <input v-model="form.bio" type="text" class="form-control input-text"
                         :class="{ 'is-invalid': errors.bio }"
                         placeholder="Quote">
                  <div class="input-group-prepend">
                <span class="input-group-text no-border-left" id="basic-addon1"><i class="fa fa-quote-right"
                                                                                   aria-hidden="true"></i></span>
                  </div>
                  <div class="invalid-feedback" v-if="errors.bio">
                    {{errors.bio[0]}}
                  </div>
                </div>

                <Br/>
                <div class="form-group">
                  <button v-if="saveOverViewLoading==true" type="submit" class="btn btn-secondary btn-block">Save
                  </button>
                  <button v-else type="button" class="btn btn-secondary btn-block">Save...</button>
                </div>
              </form>
            </div>
          </b-tab>
          <b-tab title="Reaction" @click="listReaction()">

          </b-tab>
        </b-tabs>
      </div>
    </no-ssr>
  </div>
</template>

<script>
    import PostItem from '~/components/post-item'
    import myfilter, {getImgUrl} from "../plugins/myfilter";

    export default {
        middleware: 'auth',
        data() {
            return {
                strategy: this.$auth.$storage.getUniversal('strategy'),
                form: {
                    name: '',
                    email: '',
                    phone: '',
                    bio: '',
                },
                saveOverViewLoading: true
            }
        },
        components: {
            PostItem
        },
        computed: {},
        watch: {},
        mounted() {
        },
        created() {
            this.form.email = this.$store.state.auth.user.email
            this.form.bio = this.$store.state.auth.user.bio
            this.form.name = this.$store.state.auth.user.name
            this.form.phone = this.$store.state.auth.user.phone
        },
        methods: {
            getImgUrl(image, type, size) {
                return getImgUrl(image, type, size);
            },
            changeAvatar(fileKey, event) {
                // this[fileKey] = event.target.files[0];
                // Reference to the DOM input element
                var resize_width = 100;//without px

                //get the image selected
                var item = event.target.files[0];
                if (item) {
                    //create a FileReader
                    var reader = new FileReader();

                    //image turned to base64-encoded Data URI.
                    reader.readAsDataURL(item);
                    reader.name = item.name;//get the image's name
                    reader.size = item.size; //get the image's size
                    var _this = this;
                    reader.onload = function (event) {
                        var img = new Image();//create a image
                        img.src = event.target.result;//result is base64-encoded Data URI
                        img.name = event.target.name;//set name (optional)
                        img.size = event.target.size;//set size (optional)
                        var finalImage = null;
                        img.onload = function (el) {
                            var elem = document.createElement('canvas');//create a canvas

                            //scale the image to 600 (width) and keep aspect ratio
                            var scaleFactor = resize_width / el.target.width;
                            elem.width = resize_width;
                            elem.height = resize_width;

                            //draw in canvas
                            var ctx = elem.getContext('2d');
                            ctx.drawImage(el.target, 0, 0, elem.width, elem.height);
                            ctx.globalCompositeOperation='destination-in';
                            ctx.beginPath();
                            ctx.arc(50, 50, 50, 0, Math.PI*2);
                            ctx.fillStyle = "#0095DD";
                            ctx.fill();
                            // reset to default
                            ctx.globalCompositeOperation='source-over';
                            //get the base64-encoded Data URI from the resize image
                            var finalImage = ctx.canvas.toDataURL(el.target, 'image/jpeg', 0);

                            //assign it to thumb src
                            _this.saveChangeAvatar(finalImage);
                        }

                    }
                }
            },
            async saveChangeAvatar(avatar) {
                this.savePostLoading = false;
                this.$axios
                    .post('user/change-avatar', {
                        avatar: avatar
                    })
                    .then(({data}) => {
                        if (data.status == true) {
                            this.$swal.fire(
                                data.msg,
                                'success'
                            );
                        } else {
                            this.$swal.fire(
                                data.msg,
                                'error'
                            )
                        }
                        location.href = location.href;
                    })
            },
            async overview() {
                this.saveOverViewLoading = false;
                try {
                    this.$axios.post('user/update-overview', this.form).then(({data}) => {
                        if (data) {
                            if (data.status == true) {
                                this.$swal.fire(
                                    data.msg,
                                    'success'
                                );
                            } else {
                                this.$swal.fire(
                                    data.msg,
                                    'error'
                                )
                            }
                        }
                        location.href = location.href;
                        this.saveOverViewLoading = true;
                    })
                } catch (e) {
                    this.saveOverViewLoading = true;
                    console.log(e);
                    return;
                }
            },
            async listReaction(){
                this.$router.push({ path: 'reaction' });
            },
            async logout() {
                await this.$auth.logout()
            }
        }
    }
</script>

<style>

  .account .ui-w-100 {
    width: 100px !important;
    height: auto;
  }

  .account ul.follower-list {
    list-style: none;
    background: #fff;
    border-bottom: 1px solid #ccc;
    padding: 5px;
    margin-top: 2px;
  }

  .account ul.follower-list li {
    display: contents;
    margin: 15px;
  }

  .account ul.follower-list li img {
    width: 23.9%;
    border: 3px solid #f7f9fb;
  }

  .account .overview-list {
    padding: 0;
    background: #fff;
    padding: 29px 22px;
    border-top: 1px solid #dcdcdc;
    border-radius: 2px;
    border-bottom: 1px solid #aaa;
  }

  .account .overview-list .input-text {
    border-top-left-radius: 20px !important;
    border-bottom-left-radius: 20px !important;
    padding-bottom: 8px;
    padding-left: 18px;
    border-right: 0;
  }

  .account .input-group.mb-3 .input-group-prepend span {
    width: 40px !important;
    text-align: center;
  }

  .account .profile {
    margin-bottom: 1px;
    margin-top: 0px;
    background: #fff;
    margin-left: -15px;
    margin-right: -15px;
    padding: 25px;
    border-bottom: 1px solid #ddd;
  }

  .account .rounded-circle {
    border-radius: 50% !important;
    border: 1px solid #dee2e6;
    padding: 2px;
  }


</style>
