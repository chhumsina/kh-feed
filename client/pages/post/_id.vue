<template>
  <div class="post-detail">
    <div class="post-modal">
      <nuxt-link :to="`/profile/${post_data.user_id}`">
        <div class="user-block" v-show="loadingPost == false">
          <img
            class="img-circle"
            :src="post_data.avatar  | getImgUrl('avatar','sm_avatar')"
          />
          <span class="username">
              {{post_data.name}}
            </span>
          <span class="description">{{post_data.created_at}}</span>
        </div>
      </nuxt-link>
    </div>
    <div class="post-modal post-modal-content">
      <div v-show="loadingPost" class="loading">
        <div class="loader"></div>
      </div>
      <div v-show="loadingPost == false">
        <h5 class="title">
          {{post_data.title}}
        </h5>
        <div class="photo-content">
          <img
            class="photo"
            :src="post_data.photo  | getImgUrl('photo','m_post')"
          />
        </div>
        <p class="caption">
          {{post_data.caption}}
        </p>
      </div>
    </div>
    <div class="post-modal" v-show="loadingPost == false">
      <div class="download-files" v-if="post_data.num_download_file > 0">
        <p style="
    float: left;
    left: 12px;
    position: absolute;
    text-align: center;
    font-size: 13px;
    ">Download <br><span style="font-size: 18px;color: #ad0140d1;font-weight: bold;">Free</span></p>
        <ul>
          <li class="file-item" v-for="file in (post_data.files.split(','))">
            <i v-on:click="downloadFile(file)" v-if="itemsContains(file,'.pdf')" class="fa fa-file-pdf-o"
               aria-hidden="true"></i>
            <i v-on:click="downloadFile(file)" v-if="itemsContains(file,'.doc') || itemsContains(file,'.docx')"
               class="fa fa-file-word-o"
               aria-hidden="true"></i>
            &nbsp;
            <span v-on:click="downloadFile(file)">{{file | truncate(45, '...')}}</span>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>


<script>
    export default {
        data() {
            return {
                post_data: '',
                loadingPost: true
            }
        },
        created() {
            this.getPost(this.$route.params.id)
        },
        methods: {
            downloadFile($file) {
                $file = $file.trim();
                this.$axios
                    .get('file/' + $file)
                    .then((response) => {
                        const url = window.URL.createObjectURL(new Blob([response.data]));
                        const link = document.createElement('a');
                        link.href = url;
                        link.setAttribute('download', $file);
                        document.body.appendChild(link);
                        link.click();
                    })
            },
            itemsContains(text, word) {
                return text.includes(word);
            },
            getImgUrl(type, image) {
                return `${process.env.baseUrl}image/${type}/${image}`;
            },
            async getPost(id) {
                this.loadingPost = true;
                this.$axios.get('post/detail/' + id).then(({data}) => {
                    if (data) {
                        this.post_data = data[0]
                        this.loadingPost = false
                    }
                })
            }
        }
    }
</script>

<style scoped>
.post-detail{
  background: #fff;
}
</style>
