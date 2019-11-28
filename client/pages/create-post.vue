<template>
  <div class="create-post container">
    <h4 class="text-center header-title"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Create Post</h4>
    <form @submit.prevent="createPost"
          enctype="multipart/form-data">
      <div class="form-group">
        <label>Creator caption</label>
        <textarea v-model="caption" class="form-control" style="height: 150px" name="caption" required
                  placeholder="Enter Your Full Name"/>
        <span class="Error"></span>
      </div>
      <div class="form-group">
        <label>Post title</label>
        <input v-model="title" class="form-control" type="text" name="tile" required placeholder="Post title"/>
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
        <label>Download files (.pdf, .doc, .xlsx)</label>
        <div class="file-downloads">
          <input @change="addFile('file1', $event)" class="form-control file" type="file" name="UploadedFile"/>
<!--          not yet-->
<!--          <input @change="addFile('file2', $event)" class="form-control file" type="file" name="UploadedFile"/>-->
<!--          <input @change="addFile('file3', $event)" class="form-control file" type="file" name="UploadedFile"/>-->
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
                title: '',
                photo: '',
                file1: '',
                file2: '',
                file3: '',
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
                    caption: this.caption,
                    title: this.title,
                }
                rawData = JSON.stringify(rawData)
                let formData = new FormData();
                if (typeof (this.file1.name) !== "undefined" && this.file2 !== null) {
                    formData.append('file1', this.file1, this.file1.name)
                }
                if (typeof (this.file2.name) != "undefined" && this.file2 !== null) {
                    formData.append('file2', this.file1, this.file2.name)
                }
                if (typeof (this.file3.name) != "undefined" && this.file3 !== null) {
                    formData.append('file3', this.file3, this.file3.name)
                }
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
