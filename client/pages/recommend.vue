<template>
  <div class="recommend container">
    <b-modal class="fullscreen" id="post-modal" hide-title="true">
      <post-modal
        :postId="postId" :page_name="page_name"
      />
    </b-modal>
    <div class="top-recommend">
      <h4 style="margin-bottom: 20px; color: #1648ff !important;" class="text-center"><i class="fa fa-thumbs-o-up" aria-hidden="true"></i> Top Recommend</h4>
      <swiper :options="swiperOption" :data="feeds">
        <swiper-slide  v-for="(item, $index) in feeds">

          <div v-if="item.photo!='no'" @click="showPostModal(item.post_id)">
            <img
              class="post_img"
              :src="item.photo | getImgUrl('photo','m_post')"
            />

            <div class="post_user">
              <p class="text-center">{{item.caption | truncate(35, '...')}}</p>
              <img
                class="user_img"
                :src="item.avatar | getImgUrl('avatar','sm_avatar')"
              />
              <small>{{item.name}}</small>
              <div class="num_recom">
                {{item.num_recom}}
              </div>
            </div>
          </div>
          <div v-else @click="showPostModal(item.post_id)">
            <p style="padding: 20px;" class="text-center">{{item.caption | truncate(200, '...')}}</p>
            <div class="post_user">
              <img
                class="user_img"
                :src="item.avatar | getImgUrl('avatar','sm_avatar')"
              />
              <small>{{item.name}}</small>
              <div class="num_recom">
                {{item.num_recom}}
              </div>
            </div>
          </div>

        </swiper-slide>
      </swiper>
    </div>
  </div>
</template>

<script>
    import PostModal from '../components/PostModal';

    export default {
        components:{
            PostModal
        },
        middleware: 'auth',
        data() {
            return {
                page: 1,
                feeds: [],
                search: null,
                postId: null,
                page_name: this.$route.name,
                swiperOption: {
                    effect: 'coverflow',
                    grabCursor: true,
                    centeredSlides: true,
                    slidesPerView: 'auto',
                    coverflowEffect: {
                        rotate: 50,
                        stretch: 0,
                        depth: 100,
                        modifier: 1,
                        slideShadows : true
                    },
                    pagination: {
                        el: '.swiper-pagination'
                    }
                }
            }
        },
        beforeMount() {
            this.topRecommendLIst()
        },
        methods: {
            async showPostModal(id) {
                this.postId = id;
                this.$router.push({ to: this.$route.fullPath, hash: '#post' });
                this.$root.$emit('bv::show::modal', 'post-modal');
            },
            async topRecommendLIst() {
                var id = this.$route.params.id;

                this.$axios
                    .get('post/top-recommend-list', {
                        params: {
                            page: this.page,
                            id: id,
                            search: this.search
                        }
                    })
                    .then(({data}) => {
                        if (data.length) {
                            this.feeds.push(...data);
                        }
                    })
            }
        }
    }
</script>

<style scoped>
  .recommend.container {
    margin-top: 20px;
  }
  .swiper-inner {
    width: 100%;
    height: 400px;
    padding-top: 50px;
    padding-bottom: 50px;
  }
  .swiper-slide{
    background-position: center;
    background-size: cover;
    width: 300px;
    height: 300px;
    background: #fff;
    box-shadow: 1px 1px 1px #ddd;
    position: relative;
    padding: 7px;
    overflow: hidden;
  }
  .swiper-slide .post_img{
    opacity: 0.5;
    width: 100%;
  }
  .post_user {
    position: absolute;
    bottom: 0px;
    background: #fff;
    padding: 13px;
    width: 100%;
    margin: 0 auto;
    left: 0;
    right: 0;
    box-shadow: 0px 1px 0px #ccc;
  }
  .num_recom{
    position: absolute;
    right: 11px;
    bottom: 16px;
    font-weight: bold;
    font-size: 19px;
    background: #5f72fd;
    color: #fff;
    width: 30px;
    text-align: center;
    border-radius: 4px;
    padding-bottom: 5px;
  }
</style>
