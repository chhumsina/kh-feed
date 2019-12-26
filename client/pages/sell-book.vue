<template>
  <div class="create-post container">
    <div style="float: left;" @click="$router.go(-1)">
      <i class="fa fa-arrow-left" aria-hidden="true"></i>
    </div>
    <h4 class="text-center header-title"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Sell book</h4>
    <div v-if="user.status == 'pending'">
      <b-alert show variant="warning">
        <h4 class="alert-heading"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> Postponement </h4>
        <div>
          Sorry, We need to postpone for your creating post due to some of the posts are not satisfy with our policy and
          mission.
          <ul>
            <li>Will not post Drug and Sexual Content</li>
            <li>Will not post Spam (unuseful)</li>
            <li>Keep post as learning curve</li>
            <li>Short with meaningful</li>
            <li>Interesting topic</li>
            <li>Be able to help people</li>
          </ul>
        </div>
        <hr>
        <p class="mb-0">
          Your account will be able to create post again in next future.
        </p>
      </b-alert>
    </div>

    <form v-else @submit.prevent="sellBook"
          enctype="multipart/form-data">

      <div class="image-preview" v-if="photo!=null">
        <img class="preview" :src="photo">
        <div @click="$refs.photo.click()"
             style="position: absolute; right: 1px; bottom: 3px; background: rgba(255,255,255, .6); padding: 3px 5px; font-weight: bold; font-size: 13px; padding-right: 9px; padding-bottom: 7px;">
          Change <i class="fa fa-picture-o" aria-hidden="true"></i>
        </div>
      </div>

      <div class="image-preview" v-if="photo==null">
        <img class="preview" :src="placeholder_photo.photo">
        <div @click="$refs.photo.click()"
             style="position: absolute; right: 1px; bottom: 3px; background: rgba(255,255,255, .6); padding: 3px 5px; font-weight: bold; font-size: 13px; padding-right: 9px; padding-bottom: 7px;">
          Browse <i class="fa fa-picture-o" aria-hidden="true"></i>
        </div>
      </div>

      <div class="form-group">
        <input v-model="name" class="form-control" name="name" required placeholder="Title*"/>
      </div>
      <div class="form-group">
        <input v-model="isbn" class="form-control" name="isbn" placeholder="ISBN"/>
      </div>
      <div class="form-group">
        <input v-model="num_page" class="form-control" name="num_page" placeholder="Number of page"/>
      </div>
      <div class="form-group">
        <input v-model="author" class="form-control" name="author" placeholder="Author name"/>
      </div>

      <div class="form-group">
        <textarea v-model="description" class="form-control" style="height: 20vh;" name="description"
                  placeholder="Short description"/>
      </div>
      <div class="form-group">
        <textarea v-model="outline" class="form-control" style="height: 20vh;" name="outline"
                  placeholder="Outline"/>
      </div>
      <div class="preview-photo text-center">

        <input accept="image/x-png,image/jpeg" ref="photo" style="width: 100%; display: none;"
               @change="addPhoto('photo', $event)" type="file" name="photo" required
               id="addPhotoId"/>
      </div>
      <div class="form-group">
        <b-form-group label="Price and Currency">
          <b-form-radio-group
            v-model="currency"
            :options="options"
            name="radio-inline"
          ></b-form-radio-group>
        </b-form-group>
        <input v-model="price" class="form-control" name="price" required placeholder="Price*"/>
      </div>

      <div class="form-group btn-post">
        <button v-if="sellBookLoading==true" type="submit" class="btn btn-secondary btn-block">Submit</button>
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
                currency:'usd',
                options: [
                    { text: 'USD($)', value: 'usd' },
                    { text: 'Riel(៛)', value: 'riel' }
                ],
                name:null,
                price:null,
                isbn:null,
                author:null,
                num_page:null,
                description:null,
                outline:null,
                strategy: this.$auth.$storage.getUniversal('strategy'),
                photo: null,
                sellBookLoading: true,
                placeholder_photo: {
                    photo: require('../assets/default/placeholder-300x200.png')
                }
            }
        },
        mounted() {
        },
        methods: {
            addPhoto(fileKey, event) {
                // this[fileKey] = event.target.files[0];
                // Reference to the DOM input element
                var resize_width = 200;//without px

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
                            elem.height = el.target.height * scaleFactor;

                            //draw in canvas
                            var ctx = elem.getContext('2d');
                            ctx.drawImage(el.target, 0, 0, elem.width, elem.height);

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
            async sellBook() {
                this.sellBookLoading = false;
                let rawData = {
                    name: this.name,
                    price: this.price,
                    author: this.author,
                    currency: this.currency,
                    description: this.description,
                    outline: this.outline,
                    photo: this.photo,
                    isbn: this.isbn,
                    num_page: this.num_page
                }
                rawData = JSON.stringify(rawData)
                let formData = new FormData();
                formData.append('data', rawData);
                try {
                    let response = await this.$axios.post('book/sell-book', formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }).then(({data}) => {
                        if (data) {
                            if (data.status == true) {
                                this.$swal.fire(
                                    data.msg
                                );
                                location.href = location.href;
                            } else {
                                this.$swal.fire(
                                    data.msg,
                                    'error',
                                    'error'
                                )
                            }
                        }
                        this.sellBookLoading = true;
                    })
                } catch (e) {
                    this.sellBookLoading = true;
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

  .image-preview {
    background: #fff;
    width: 202px;
    /* height: 250px; */
    /* line-height: 250px; */
    text-align: center;
    margin-bottom: 10px;
    padding: 5px;
    border: 1px solid #ccc;
    position: relative;
    margin: 0 auto;
    margin-bottom: 15px;
  }

  .image-preview > img {
    vertical-align: middle;
    width: 190px;
    height: 250px;
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
