<template>
  <div class="create-post container">
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

      <b-alert show variant="success">
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

      <div class="form-group">
        <textarea v-model="caption" class="form-control" style="height: 200px" name="caption" required
                  placeholder="Enter Caption"/>
      </div>
      <div class="preview text-center">

        <input accept="image/x-png,image/jpeg" style="width: 100%;" @change="addFile('photo', $event)" type="file" name="photo"
               id="UploadedFile"/>
        <div class="preview-icon"><i class="fa fa-picture-o" aria-hidden="true"></i></div>
      </div>
      <Br/>
      <div class="form-group">
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
                photo: '',
                createPostLoading: true
            }
        },
        mounted() {
        },
        methods: {
            addFile(fileKey, event) {
                this[fileKey] = event.target.files[0];
            },
            async createPost() {
                this.createPostLoading = false;
                let rawData = {
                    caption: this.caption
                }
                rawData = JSON.stringify(rawData)
                let formData = new FormData();
                if (typeof (this.photo.name) !== "undefined" && this.photo !== null) {
                    formData.append('photo', this.photo, this.photo.name)
                }
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
                                document.getElementById('UploadedFile').value = null;
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

  img.preview-img {
    width: 100%;
  }

  .form-group.file-downloads {
    background: #fff;
    padding: 19px;
    border: 1px solid #ddd;
  }

  .file-downloads {
    padding: 15px;
    background: #fff;
    border: 1px solid #ccc;
  }

  input.form-control.file {
    border: 0;
  }

  .preview {
    border: 1px solid #ccc;
    position: relative;
    margin-bottom: 15px;
  }

  .preview-icon{
    position: absolute;
    right: 2px;
    top: 0px;
  }
  .preview-icon i{
    font-size: 30px;
    color: #555;
  }

  .browse-button {
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0px;
    background: -webkit-gradient(linear, left top, left bottom, from(transparent), to(black));
    background: linear-gradient(180deg, transparent, black);
    opacity: 0;
    -webkit-transition: 0.3s ease;
    transition: 0.3s ease;
  }

  .browse-button:hover {
    opacity: 1;
  }

  .browse-input[data-v-9ca2f330] {
    width: 100%;
    height: 100%;
    -webkit-transform: translate(-1px, -26px);
    transform: translate(-1px, -26px);
    opacity: 0;
    border: 0;
  }

  .form-group input {
    transition: 0.3s linear;
  }

  .form-group input:focus {
    border: 1px solid crimson;
    box-shadow: 0 0 0 0;
  }

  .Error {
    color: crimson;
    font-size: 13px;
  }

  .Back {
    font-size: 25px;
  }
</style>
