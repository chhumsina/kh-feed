<template>
  <div class="create-post container">
    <h4 class="text-center header-title"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Create Post</h4>
    <form @submit.prevent="createPost"
          enctype="multipart/form-data">
      <div class="form-group">
        <textarea v-model="caption" class="form-control" style="height: 150px" name="caption" required
                  placeholder="Enter Caption"/>
        <span class="Error"></span>
      </div>
      <div class="preview text-center">
        <img class="preview-img" src="http://i.imgur.com/I86rTVl.jpg" alt="Preview Image"/>
        <div class="browse-button">
          <input @change="addFile('photo', $event)" class="browse-input" type="file" required name="photo"
                 id="UploadedFile"/>
        </div>
        <span class="Error"></span>
      </div>
      <div class="form-group">
        <input class="btn btn-primary btn-block" type="submit" value="Submit"/>
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
                error: this.$route.query.error
            }
        },
        mounted() {
        },
        methods: {
            addFile(fileKey, event) {
                this[fileKey] = event.target.files[0];
            },
            async createPost() {
                let rawData = {
                    caption: this.caption
                }
                rawData = JSON.stringify(rawData)
                let formData = new FormData();
                formData.append('photo', this.photo, this.photo.name)
                formData.append('data', rawData);
                try {
                    let response = await this.$axios.post('create-post', formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }).then(({data}) => {
                        if (data) {
                            location.href = location.href;
                        }
                    })
                } catch (e) {
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

  .preview i {
    color: white;
    font-size: 35px;
    transform: translate(50px, 130px);
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
