<template>
  <div class="create-post container">
    <div style="float: left;" @click="$router.go(-1)">
      <i class="fa fa-arrow-left" aria-hidden="true"></i>
    </div>
    <h4 class="text-center header-title"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Create Post</h4>
    <div v-if="user.status == 'pending'">
      <b-alert show variant="warning">
        <h4 class="alert-heading"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> Postponement </h4>
        <div>
          Sorry, We need to postpone for your creating post due to some of the posts are not satisfy with our policy and
          mission.
          <ul>
            <li>Short with meaningful</li>
            <li>Useful thing</li>
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
    <form v-else @submit.prevent="createPost"
          enctype="multipart/form-data">

      <b-alert show variant="success" v-if="photo==null">
        <h6 class="alert-heading"><i class="fa fa-check-circle-o" aria-hidden="true"></i> Great Post should be: </h6>
        <div>
          <ul>
            <li>Short with meaningful</li>
            <li>Useful thing</li>
            <li>Interesting topic</li>
            <li>Be able to help people</li>
          </ul>
        </div>
      </b-alert>

      <div class="image-preview" v-if="photo!=null">
        <img class="preview" :src="photo">
      </div>

      <div class="form-group">
        <textarea v-model="caption" class="form-control" style="height: 35vh;" name="caption" required
                  placeholder="Enter Caption"/>
      </div>
      <div class="preview-photo text-center">

        <input accept="image/x-png,image/jpeg" ref="photo"  style="width: 100%; display: none;" @change="addPhoto('photo', $event)" type="file" name="photo"
               id="addPhotoId"/>
        <div class="" @click="$refs.photo.click()"><i style="font-size: 40px;" class="fa fa-picture-o" aria-hidden="true"></i></div>
      </div>

      <div class="form-group btn-post">
        <button v-if="createPostLoading==true" type="submit" class="btn btn-secondary btn-block">Submit</button>
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
                caption: '',
                photo: null,
                createPostLoading: true
            }
        },
        mounted() {
        },
        methods: {
            addPhoto(fileKey, event) {
                // this[fileKey] = event.target.files[0];
                // Reference to the DOM input element
                var resize_width = 300;//without px

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
                    reader.onload = function(event) {
                        var img = new Image();//create a image
                        img.src = event.target.result;//result is base64-encoded Data URI
                        img.name = event.target.name;//set name (optional)
                        img.size = event.target.size;//set size (optional)
                        var finalImage = null;
                        img.onload = function(el) {
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
                }
            },
            async createPost() {
                this.createPostLoading = false;
                let rawData = {
                    caption: this.caption,
                    photo: this.photo
                }
                rawData = JSON.stringify(rawData)
                let formData = new FormData();
                formData.append('data', rawData);
                try {
                    let response = await this.$axios.post('create-post', formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }).then(({data}) => {
                        if (data) {
                            if(data.status == true){
                                this.caption = '';
                                this.photo = null;
                                document.getElementById('addPhotoId').value = null;
                                this.$swal.fire(
                                    data.msg,
                                    'success'
                                );
                            }else{
                                this.$swal.fire(
                                    data.msg,
                                    'error'
                                )
                            }
                        }
                        this.createPostLoading = true;
                    })
                } catch (e) {
                    this.createPostLoading = true;
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
    width: 100%;
    /*height: 250px;*/
    /*line-height: 250px;*/
    text-align: center;
    margin-bottom: 10px;
    border: 1px solid #ccc;
  }
  .image-preview > img{
    vertical-align: middle;
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
