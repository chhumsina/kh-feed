<template>
  <div class="comment-section" v-if="postId!=''">

    <div class="modal-header-post" v-if="loadingModal == false">
      <nuxt-link :to="`/profile/${dataModal.user_id}`">
        <div class="user-block">
          <img
            class="img-circle"
            :src="dataModal.avatar  | getImgUrl('avatar','sm_avatar')"
          />
          <span class="username">
              {{dataModal.name}}
            </span>
          <span class="description">
                  <timeago :datetime="dataModal.created_at" :auto-update="10"></timeago>
                </span>
        </div>
      </nuxt-link>


      <div v-if="page_name == 'account'">
        <div @click="deletePost()" class="btn-delete-post" v-if="deletePostLoading==true">
          <i class="fa fa-trash-o" aria-hidden="true"></i> Delete
        </div>
        <div class="btn-delete-post" v-else>
          <i class="fa fa-trash-o" aria-hidden="true"></i> Delete...
        </div>
      </div>

      <div v-else-if="page_name == 'save'">
        <div @click="unSavePost()" class="btn-save-post" v-if="unSavePostLoading==true">
          <i class="fa fa-download" aria-hidden="true"></i> Unsave
        </div>
        <div class="btn-save-post" v-else>
          <i class="fa fa-download" aria-hidden="true"></i> Unsave...
        </div>
      </div>
      <div v-else>
        <div v-if="showIneedLIst==false"  class="btn-save-post">
          <div @click="showIneedLIst=true" v-if="dataModal.give_status=='active'" style="color: green;">
            <i class="fa fa-smile-o" aria-hidden="true"></i> បង្ហាញអ្នកដែលបានសៀវភៅ
          </div>
          <div @click="showIneedLIst=true" v-else style="color: orange;">
            <i class="fa fa-smile-o" aria-hidden="true"></i> 
            អ្នកចង់បានសៀវភៅនេះ?
          </div>
        </div>
        <div v-else @click="showIneedLIst=false" class="btn-save-post">
          <i class="fa fa-picture-o" aria-hidden="true"></i> បង្ហាញ Post
        </div>

        <!--        <div @click="savePost()" class="btn-save-post" v-if="savePostLoading==true">-->
        <!--          <i class="fa fa-download" aria-hidden="true"></i> Save-->
        <!--        </div>-->
        <!--        <div class="btn-save-post" v-else>-->
        <!--          <i class="fa fa-download" aria-hidden="true"></i> Save...-->
        <!--        </div>-->
      </div>
    </div>

    <div v-if="showIneedLIst==true" style="margin-top: 57px;">
      <p v-if="ineedListDataLoading==false" class="text-center" style="padding: 5px; color: #000;">
        បញ្ជីឈ្មោះអ្នកដែលចង់បានសៀវភៅបរិច្ចាគមួយនេះ
        <Br/><span style="color:#bbb">
         អ្នកបរិច្ចាគនឹងជ្រើសរើសនរណាម្នាក់
          </span></p>

      <p class="text-center" v-if="ineedListDataLoading==true"><img :src="loader"/></p>

      <div  class="text-center" style="margin-top: 100px;" v-if="ineedListData.length<=0 && ineedListDataLoading==false">
         <div v-if="dataModal.user_id==user.id">
           <i class="fa fa-male" style="font-size: 75px; color: #ccc;" aria-hidden="true"></i>
           <p style="margin-bottom: -5px; font-weight: bold; color: #aaa;">No any people request this contributed book yet</p>
         </div>
        <div v-else>
          <i class="fa fa-male" style="font-size: 75px; color: #ccc;" aria-hidden="true"></i>
          <p style="margin-bottom: -5px; font-weight: bold; color: #aaa;">No any people want this contributed book yet</p>
          <small>Be the first to get a chance of having this contributed book</small>
        </div>
      </div>

      <b-form-checkbox-group v-else id="checkbox-group-2" v-model="giveto" name="flavour-2">
        <div v-for="(item, $index) in ineedListData" :key="$index" :data-num="$index + 1" class="sina-list-item">
            <div class="item-image">
              <div style="
              width: 30px;
              float: left;
              margin-top: 7px;
              font-size: 16px;
              font-weight: bold;
              color: #fff;
              background: #ddd;
              margin-left: -10px;
              text-align: center;
              margin-right: 13px;
              ">
                {{$index+1}}
              </div>
              <nuxt-link :to="`/profile/${item.user_id}`">
              <img
                class="img-circle"
                :src="item.avatar | getImgUrl('avatar','sm_avatar')"
                alt="User Image"
              />
              </nuxt-link>
            </div>
            <div class="item-text">
                <p class="item-name">
                  <nuxt-link :to="`/profile/${item.user_id}`">{{item.name}}</nuxt-link>
                </p>
                <div class="item-desc" v-if="dataModal.user_id==user.id">
                  {{item.phone}}
                </div>
              <div class="item-desc" v-else>
                {{item.phone | truncate(6, 'XXX')}}
              </div>
              <div v-bind:class="item.accept_status" style="float: right; margin-top: -46px; text-align: right">
                <div v-if="item.accept_status=='active'">
                  <p style="margin-top: 9px; margin-bottom: -8px;color:green;">selected</p>
                  <p style="font-size: 11px; color: rgb(156, 156, 156); margin-top: 9px; margin-bottom: 0;"><timeago :datetime="item.accept_date" :auto-update="10"></timeago></p>
                </div>
                <div v-else>
                  <p style="margin-top: 5px; margin-bottom: -8px;">{{item.accept_status}}</p>
                  <p style="font-size: 11px; color: rgb(156, 156, 156); margin-top: 9px; margin-bottom: 0;"><timeago :datetime="item.created_at" :auto-update="10"></timeago></p>
                </div>
              </div>
            </div>
          <div style="margin-top: 10px; margin-left: 35px; color: #555;" class="font-13">
            {{item.desc | truncate(20, '...')}} <span v-if="item.desc.length>40" @click="showMoreDesc(item.desc)" style="color: #2f8be0">more</span>
          </div>
          <div v-if="dataModal.give_status=='active'">
            <div style="margin-top: 10px; text-align: right; color: #555;" class="font-13">
              <div style="text-align: right; float: right; margin-top: -35px; padding: 4px 8px; color: green;" v-if="item.accept_status=='active'">
                <i class="fa fa-check" aria-hidden="true"></i> selected <b>{{item.name|truncate(10, '...')}}</b>
              </div>
            </div>
          </div>
          <div v-else>
            <!-- <div v-if="dataModal.user_id==user.id" style="margin-top: 10px; text-align: right; color: #555;" class="font-13">
                pick <b>{{item.name|truncate(10, '...')}}</b> &nbsp;  <b-form-checkbox :value="item.user_id"><span style="visibility: hidden">.</span></b-form-checkbox>
            </div> -->
          </div>
        </div>
      </b-form-checkbox-group>

      <div v-if="ineedListDataLoading==false">
        <div v-if="dataModal.give_status=='active'">

        </div>
        <div v-else>
          <div v-if="dataModal.user_id==user.id" class="create-comment" style="height: 90px; padding-top: 23px; text-align: center;">
            សូមអរគុណចំពោះការបរិច្ចាគសៀវភៅនេះ <br/> ពួកខ្ញុំនឹងផ្តល់អោយអ្នកដែលត្រូវការបំផុត
          </div>
          <div v-else @click="iNeed(dataModal.give_status)" style="width: 125px; height: 125px; background: #4c94ff; text-align: center; margin: 0 auto; line-height: 125px; border-radius: 125px; font-weight: bold; color: #fff5f5; box-shadow: 0 2px 3px #aaa; position: fixed; bottom: 12px; left: 0; right: 0; font-size: 20px; text-shadow: 0 1px 4px #000;">
            ខ្ញុំចង់បាន
          </div>
        </div>
      </div>
    </div>
    <div v-else>
      <div class="post-modal post-modal-content">
        <div v-if="loadingModal" class="loading">
          <div class="loader"></div>
        </div>
        <div style="margin-top: 45px;" v-if="loadingModal == false">
          <div v-if="dataModal.photo!='no'" class="thumbnail">
            <img v-lazy="getImgUrl(dataModal.photo, 'photo', 'm_post')"
            />
          </div>
          <div style="padding: 10px;">
            <div class="post_property">
              <small class="post_view_num text-muted">{{numView}} views · {{lastRead}} last read</small>
              <a href="#comment"><small style="float: right;"
                                        class="post_view_num">{{numComment}} មតិ</small></a>
            </div>
            <p style="white-space: pre-line; word-break: break-all;" class="caption">
              <span v-html="dataModal.caption"></span>
            </p>

<!--            <p v-if="recommendPostLoading==true" @click="recommendPost()"-->
<!--               style="text-align: center; background: #00a1ff; color: #fff; border-radius: 3px; padding: 6px;font-weight: 400;">-->
<!--              <i class="fa fa-thumbs-o-up" aria-hidden="true"></i> Recommend<span-->
<!--              v-if="recommendPostLoading==false">...</span>-->
<!--            </p>-->
<!--            <p v-if="recommendPostLoading==false"-->
<!--               style="text-align: center; background: #00a1ff; color: #fff; border-radius: 3px; padding: 6px;font-weight: 400;">-->
<!--              <i class="fa fa-thumbs-o-up" aria-hidden="true"></i> Recommend...-->
<!--            </p>-->

            <div class="c_post">
              <div class="box-comments" style="display: block;">
                <p style="border-bottom: 1px solid #ddd;padding-bottom: 9px;">
                  <i class="fa fa-comments-o" aria-hidden="true"></i>
                  មតិ
                </p>

                <div v-for="(item, $index) in dataModalComment" :key="$index" class="box-comment">
                  <nuxt-link :to="`/profile/${item.user_id}`">
                    <img class="img-circle img-sm" :src="item.avatar  | getImgUrl('avatar','sm_avatar')">
                    <div class="comment-text">
                      <small class="username">
                        {{item.name|truncate(10, '...')}}
                        <span class="text-muted pull-right">
            <timeago :datetime="item.created_at" :auto-update="10"></timeago>
          </span>
                      </small>
                      <small>{{item.comments}}</small>
                    </div>
                  </nuxt-link>
                </div>
                <div class="text-center" v-if="numComment==0">
                  <i class="fa fa-comments-o" style="font-size: 75px; color: #ccc;" aria-hidden="true"></i>
                  <p style="margin-bottom: -5px; font-weight: bold; color: #aaa;">មិនទាន់មានមតិទេ</p>
                  <small>បញ្ចេញមតិដំបូងគេ</small>
                </div>

                <div class="box-comment text-center" v-if="loadingModalComment==true">
                  <p>loading...</p>
                </div>
                <p id="comment"></p>
                <br/>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="create-comment">
        <div class="post-modal c_post" v-show="loadingModal == false">
          <div style="border: 0;padding-bottom: 10px !important;" class="box-footer">
            <form @submit.prevent="createComment">
              <img class="img-responsive img-circle img-sm avatar-comment"
                   :src="user.avatar | getImgUrl('avatar','sm_avatar')"
                   alt="Alt Text">
              <div class="img-push">
                <input @click="goto('comment')" required v-model="comment" type="text"
                       class="form-control input-sm input-box"
                       placeholder="បញ្ចេញមតិ...">
                <input type="hidden" v-model="postId"/>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
    import myfilter, {getImgUrl} from "../plugins/myfilter";

    export default {
        middleware: 'auth',
        props: {
            switch_i_want: false,
            postId: null,
            page_name: null,
            i_need_btn: true
        },
        watch: {
            postId: {
                immediate: true,
                handler(postId) {
                    this.listComment(postId);
                    this.getPostDetail(postId);
                    this.ineedList();
                    if(this.switch_i_want == true){
                        this.showIneedLIst = true
                    }
                }
            }
        },
        data() {
            return {
                giveto:[],
                showIneedLIst: false,
                comment: '',
                dataModalComment: '',
                loadingModalComment: false,
                dataModal: '',
                loadingModal: true,
                numView: 0,
                numComment: 0,
                lastRead: null,
                savePostLoading: true,
                unSavePostLoading: true,
                deletePostLoading: true,
                recommendPostLoading: true,
                ineedListData: [],
                ineedListDataLoading: true,
                i_want_banner: require('../assets/default/book-banner.jpg'),
                loader: require('../assets/default/loader.gif'),
            }
        },
        methods: {
            goto(id) {
                document.getElementById(id).scrollIntoView();
            },
            async createComment() {
                this.loadingModalComment = true
                let rawData = {
                    comment: this.comment,
                    id: this.postId
                }
                rawData = JSON.stringify(rawData)
                let formData = new FormData();
                formData.append('data', rawData);
                try {
                    this.comment = '';
                    let response = await this.$axios.post('create-comment', formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }).then(({data}) => {
                        if (data.status) {
                            this.dataModalComment.push(data.data[0]);
                            this.loadingModalComment = false;
                            this.goto('comment');
                            this.numComment = this.dataModalComment.length;
                        }
                    })
                } catch (e) {
                    console.log(e);
                    this.loadingModalComment = false
                    return;
                }
            },
            getImgUrl(image, type, size) {
                return getImgUrl(image, type, size);
            },
            async getPostDetail(id) {
                this.loadingModal = true;
                this.dataModal = '';
                this.$router.push({to: this.$route.fullPath, hash: '#post'});
                this.$root.$emit('bv::show::modal', 'post-modal')
                this.$axios.get('post/detail/' + id).then(({data}) => {
                    if (data) {
                        this.dataModal = data['detail'][0]
                        this.numView = data['num_view'][0].num_view;
                        this.numComment = data['num_comment'][0].num_comment
                        this.lastRead = data['last_read']
                        this.loadingModal = false
                    }
                });
            },
            async listComment(id) {
                this.loadingModalComment = true;
                this.dataModalComment = '';
                this.$axios.get('post/detail-comment/' + id).then(({data}) => {
                    if (data) {
                        this.dataModalComment = data;
                        this.loadingModalComment = false
                    }
                })
            },
            showMoreDesc(desc){
                this.$swal.fire(
                    'Your description:',
                    desc
                )
            },
            async ineedList() {
                this.ineedListDataLoading = true;
                  this.$axios.get('post/i-need-list/' + this.postId).then(({data}) => {
                  if (data) {
                      this.ineedListData = data;
                      this.ineedListDataLoading = false
                  }
                })
            },
            submitGiveTo(){
                if (this.giveto.length == 0) {
                    this.$swal.fire(
                        'Please select at last one person you will give',
                        '',
                        'info'
                    )
                } else {
                    var _label_num = 'these';
                    var _label_person = 'people';
                    if(this.giveto.length == 1){
                        var _label_num = 'this'
                        var _label_person = 'person';
                    }
                    this.$swal.fire({
                        title: 'Say something to '+_label_num+' '+this.giveto.length+' '+_label_person,
                        input: 'textarea',
                        inputAttributes: {
                            autocapitalize: 'off'
                        },
                        showCancelButton: true,
                        confirmButtonText: 'Submit',
                        showLoaderOnConfirm: true,
                        preConfirm: (desc) => {
                            if (desc) {
                                return this.$axios.post('post/submit-give-to', {desc: desc, id: this.postId, giveto: this.giveto})
                                    .then(function (res) {
                                        return res;
                                    })
                                    .catch(error => {
                                        this.$swal.showValidationMessage(
                                            `Request failed: ${error}`
                                        )
                                    })
                            }
                        },
                        allowOutsideClick: () => !this.$swal.isLoading()
                    }).then((result) => {
                        if (result.value) {
                            if (result.value.data.status == true) {
                                this.$swal.fire(
                                    result.value.data.msg,
                                    'success',
                                    'success'
                                );
                            } else {
                                this.$swal.fire(
                                    result.value.data.msg,
                                    'error',
                                    'error'
                                )
                            }
                            window.location.href = '/dashboard';
                        }
                    })
                }
            },
            iNeed(status) {
                if(status=='active'){
                    this.$swal.fire(
                        'Sorry, this contributed book has been closed!',
                        '',
                        'info'
                    )
                }else{
                    if (this.user.phone == '') {
                        this.$swal.fire({
                            title: '"ដើម្បីងាយស្រួលក្នុងការទំនាក់ទំនង"  \nសូមបំពេញលេខទូរសព្ទ \n  ហើយត្រឡប់មកទីនេះម្តងទៀត។',
                            text: "Account -> ព័ត៌មាន -> លេខទូរសព្ទ",
                            icon: 'warning',
                            showCancelButton: false,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'ទៅកាន់ Account'
                        }).then((result) => {
                            this.$router.push({path: '/account'})
                        });
                    } else {
                        this.$swal.fire({
                            title: 'Say something to Contributor \n "You really want this book"',
                            input: 'textarea',
                            inputAttributes: {
                                autocapitalize: 'off'
                            },
                            showCancelButton: true,
                            confirmButtonText: 'Submit',
                            showLoaderOnConfirm: true,
                            preConfirm: (desc) => {
                                if (desc) {
                                    return this.$axios.post('post/i-need', {desc: desc, id: this.postId})
                                        .then(function (res) {
                                            return res;
                                        })
                                        .catch(error => {
                                            this.$swal.showValidationMessage(
                                                `Request failed: ${error}`
                                            )
                                        })
                                }
                            },
                            allowOutsideClick: () => !this.$swal.isLoading()
                        }).then((result) => {
                            if (result.value) {
                                if (result.value.data.status == true) {
                                    this.$swal.fire(
                                        result.value.data.msg,
                                        'success',
                                        'success'
                                    )
                                    window.location.href = '/dashboard';
                                } else {
                                    this.$swal.fire(
                                        result.value.data.msg,
                                        'error',
                                        'error'
                                    )
                                }
                            }
                        })
                    }
                }

            },
            async unSavePost() {
                this.unSavePostLoading = false;
                this.$axios
                    .post('post/unsave-post', {
                        id: this.postId
                    })
                    .then(({data}) => {
                        if (data.status == true) {
                            this.$swal.fire(
                                data.msg
                            );
                        } else {
                            this.$swal.fire(
                                data.msg,
                                'error'
                            )
                        }
                        this.unSavePostLoading = true

                        window.location.href = '/save';

                    })
            },
            async savePost() {
                this.savePostLoading = false;
                this.$axios
                    .post('post/save-post', {
                        id: this.postId
                    })
                    .then(({data}) => {
                        if (data.status == true) {
                            this.$swal.fire(
                                data.msg
                            );
                        } else {
                            this.$swal.fire(
                                data.msg,
                                'error'
                            )
                        }
                        this.savePostLoading = true

                    })
            },
            async recommendPost() {
                this.recommendPostLoading = false;
                this.$axios
                    .post('post/recommend-post', {
                        id: this.postId
                    })
                    .then(({data}) => {
                        if (data.status == true) {
                            this.$swal.fire(
                                data.msg
                            );
                        } else {
                            this.$swal.fire(
                                data.msg,
                                'error'
                            )
                        }
                        this.recommendPostLoading = true

                    })
            },
            async deletePost() {

                this.$swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#aaa',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {

                        this.deletePostLoading = false;

                        this.$axios
                            .post('post/delete-post', {
                                id: this.postId
                            })
                            .then(({data}) => {
                                if (data.status == true) {
                                    this.$swal.fire(
                                        data.msg
                                    );
                                    location.reload();
                                } else {
                                    this.$swal.fire(
                                        data.msg,
                                        'error'
                                    )
                                }

                                this.deletePostLoading = true

                            })
                    } else {
                        this.deletePostLoading = true
                    }
                });
            }
        }
    }
</script>
<style scoped>

</style>
