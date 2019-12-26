<template>
  <div class="comment-section" v-if="bookId!=''">

    <div class="modal-header-post" v-if="loadingModal == false">
      <nuxt-link :to="`/shop/${dataModal.page_id}`">
        <div class="user-block">
          <img
            class="img-circle"
            :src="dataModal.avatar  | getImgUrl('page','sm_page')"
          />
          <span class="username">
              {{dataModal.page_name}}
            </span>
          <span class="description">
                  <timeago :datetime="dataModal.created_at" :auto-update="10"></timeago>
                </span>
        </div>
      </nuxt-link>
    </div>

    <div class="post-modal post-modal-content">
      <div v-if="loadingModal" class="loading">
        <div class="loader"></div>
      </div>
      <div style="margin-top: 45px;" v-if="loadingModal == false">
        <div class="thumbnail">
          <img v-lazy="getImgUrl(dataModal.photo, 'book', 'm_book')"
          />
        </div>
        <div style="padding: 10px;">
          <div class="post_property">
            <small class="post_view_num text-muted">{{numView}} views · {{lastRead}} last read</small>
          </div>

          <table>
            <tr>
              <td width="125">Book's name</td>
              <td>:</td>
              <td class="font-weight-bold" style="padding-left: 10px;">{{dataModal.name}}</td>
            </tr>
            <tr>
              <td width="125">Price</td>
              <td>:</td>
              <td class="font-weight-bold" style="color: #ff8c2a;padding-left: 10px;"><span>{{dataModal.price}} {{dataModal.currency}}</span></td>
            </tr>
            <tr>
              <td>ISBN</td>
              <td>:</td>
              <td style="padding-left: 10px;">{{dataModal.isbn}}</td>
            </tr>
            <tr>
              <td>Number of Page</td>
              <td>:</td>
              <td style="padding-left: 10px;">{{dataModal.num_page}}</td>
            </tr>
            <tr>
              <td>Author's name</td>
              <td>:</td>
              <td style="padding-left: 10px;">{{dataModal.author}}</td>
            </tr>
          </table>

          <div class="box-modal-desc">
            <b>Description:</b>
            <p style="white-space: pre-line; word-break: break-all;" class="caption">
              <span v-html="dataModal.description"></span>
            </p>
          </div>
          <div class="box-modal-desc">
            <b>Outline</b>
            <p style="white-space: pre-line; word-break: break-all;" class="caption">
              <span v-html="dataModal.outline"></span>
            </p>
          </div>

        </div>
      </div>
    </div>
  </div>
</template>

<script>
    import myfilter, {getImgUrl} from "../plugins/myfilter";

    export default {
        props: {
            bookId:null,
            page_name: null,
        },
        watch: {
            bookId: {
                immediate: true,
                handler (bookId) {
                    this.getBookDetail(bookId);
                }
            }
        },
        data() {
            return {
                dataModal: '',
                loadingModal: true,
                numView: 0,
                lastRead: null
            }
        },
        methods:{
            goto(id){
                document.getElementById(id).scrollIntoView();
            },
            getImgUrl(image, type, size){
                return getImgUrl(image, type, size);
            },
            async getBookDetail(id) {
                this.loadingModal = true;
                this.dataModal = '';
                this.$router.push({ to: this.$route.fullPath, hash: '#book' });
                this.$root.$emit('bv::show::modal', 'book-modal')
                this.$axios.get('book/detail/' + id).then(({data}) => {
                    if (data) {
                        this.dataModal = data['detail'][0];
                        this.numView = data['num_view'][0].num_view;
                        this.lastRead = data['last_read'];
                        this.loadingModal = false
                    }
                });
            }
        }
    }
</script>
<style scoped>
  .box-modal-desc{
    margin-top: 20px;
    background: #eee;
    margin-left: -10px;
    margin-right: -10px;
    padding: 10px;
  }
</style>
