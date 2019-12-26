<template>
  <div class="create-post container">
    <div style="float: left;" @click="$router.go(-1)">
      <i class="fa fa-arrow-left" aria-hidden="true"></i>
    </div>
    <h4 class="text-center header-title"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Create Shop</h4>

    <form @submit.prevent="createShop"
          enctype="multipart/form-data">

      <div class="image-preview" v-if="photo!=null">
        <img class="preview" :src="photo">
        <div @click="$refs.photo.click()"
             style="position: absolute; bottom: 3px; padding: 3px 9px 7px 5px; font-weight: bold; font-size: 13px; left: 67px;">
          Change <i class="fa fa-picture-o" aria-hidden="true"></i>
        </div>
      </div>

      <div class="image-preview" v-if="photo==null">
        <img class="preview" :src="placeholder_photo.photo">
        <div @click="$refs.photo.click()"
             style="position: absolute; bottom: 3px; padding: 3px 9px 7px 5px; font-weight: bold; font-size: 13px; left: 67px;">
          Browse <i class="fa fa-picture-o" aria-hidden="true"></i>
        </div>
      </div>

      <div class="form-group">
          <input v-model="name" class="form-control" name="name" required placeholder="Shop name"/>
      </div>
      <div class="form-group">
        <input v-model="phone" class="form-control" name="phone" required placeholder="Phone"/>
      </div>
      <div class="form-group">
        <input v-model="email" class="form-control" name="email" required placeholder="E-mail"/>
      </div>
      <div class="form-group">
        <textarea v-model="address" class="form-control" style="height: 15vh;" name="address" required
                  placeholder="Enter address"/>
      </div>
      <div class="preview-photo text-center">

        <input accept="image/x-png,image/jpeg" ref="photo" style="width: 100%; display: none;"
               @change="addPhoto('photo', $event)" type="file" name="photo"
               id="addPhotoId"/>
      </div>

      <div class="form-group btn-post">
        <button v-if="createShopLoading==true" type="submit" class="btn btn-secondary btn-block">Submit</button>
        <button v-else type="button" class="btn btn-secondary btn-block">Submit...</button>
      </div>
    </form>
  </div>
</template>


<script>
    export default {
        middleware: 'auth',
        data() {
            return {
                strategy: this.$auth.$storage.getUniversal('strategy'),
                name: '',
                email: '',
                address: '',
                phone: '',
                photo: null,
                createShopLoading: true,
                placeholder_photo: {
                    photo: require('../assets/default/placeholder-300x200.png')
                }
            }
        },
        methods: {
            addPhoto(fileKey, event) {
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
                            _this.photo = finalImage;
                        }

                    }
                } else {
                    this.photo = null;
                }
            },
            async createShop() {
                this.createShopLoading = false;
                let rawData = {
                    name: this.name,
                    phone: this.phone,
                    email: this.email,
                    address: this.address,
                    photo: this.photo
                }
                rawData = JSON.stringify(rawData)
                let formData = new FormData();
                formData.append('data', rawData);
                try {
                     await this.$axios.post('shop/create-shop', formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }).then(({data}) => {
                        if (data) {
                            if (data.status == true) {
                                this.$swal.fire(
                                    data.msg
                                );
                            } else {
                                this.$swal.fire(
                                    data.msg,
                                    'error',
                                    'error'
                                )
                            }
                        }
                        this.createShopLoading = true;
                        // this.$router.push({ path: 'account' });
                    })
                } catch (e) {
                    this.createShopLoading = true;
                    console.log(e);
                    return;
                }
            }
        }
    }
</script>

<style scoped>
  .header-title {
    text-align: center !important;
    border-bottom: 1px solid #ddd;
    padding-bottom: 18px;
    margin-bottom: 14px;
  }

  .create-post.container {
    margin-top: 25px;
    margin-bottom: 90px;
  }

  .image-preview{
    background: #fff;
    width: 100%;
    text-align: center;
    margin-bottom: 10px;
    padding: 5px;
    border: 1px solid #ccc;
    position: relative;
    width: 200px;
    height: 200px;
    border-radius: 50%;
    margin: 0 auto;
    margin-bottom: 30px;
  }

  .image-preview > img{
    vertical-align: middle;
    width: 100%;
    height: 100%;
    border-radius: 50%;
  }

  .btn-post {
    position: fixed;
    width: 100%;
    bottom: -17px;
    left: 0;
    padding: 10px;
    padding-bottom: 0;
    background: #fff;
    height: 60px;
    border-top: 1px solid #ddd;
  }
</style>
