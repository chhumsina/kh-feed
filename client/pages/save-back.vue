<template>
  <div class="messages container">
    <ul class="media-list">
      <li class="media">
        <a href="#" class="pull-left">
          <img
            src="https://bootdey.com/img/Content/user_1.jpg"
            alt=""
            class="img-circle"
          />
        </a>
        <div class="media-body">
          <span class="text-muted pull-right">
            <small class="text-muted">30 min ago</small>
          </span>
          <strong class="text-dark">CHHUM Sina</strong>
          <p>
            Lorem ipsum dolor sit amet...
          </p>
        </div>
      </li>
      <li class="media">
        <a href="#" class="pull-left">
          <img
            src="https://bootdey.com/img/Content/user_1.jpg"
            alt=""
            class="img-circle"
          />
        </a>
        <div class="media-body">
          <span class="text-muted pull-right">
            <small class="text-muted">30 min ago</small>
          </span>
          <strong class="text-dark">CHHUM Sina</strong>
          <p>
            Lorem ipsum dolor sit amet...
          </p>
        </div>
      </li>
      <li class="media">
        <a href="#" class="pull-left">
          <img
            src="https://bootdey.com/img/Content/user_1.jpg"
            alt=""
            class="img-circle"
          />
        </a>
        <div class="media-body">
          <span class="text-muted pull-right">
            <small class="text-muted">30 min ago</small>
          </span>
          <strong class="text-dark">CHHUM Sina</strong>
          <p>
            Lorem ipsum dolor sit amet...
          </p>
        </div>
      </li>
      <li class="media">
        <a href="#" class="pull-left">
          <img
            src="https://bootdey.com/img/Content/user_1.jpg"
            alt=""
            class="img-circle"
          />
        </a>
        <div class="media-body">
          <span class="text-muted pull-right">
            <small class="text-muted">30 min ago</small>
          </span>
          <strong class="text-dark">CHHUM Sina</strong>
          <p>
            Lorem ipsum dolor sit amet...
          </p>
        </div>
      </li>
      <li class="media">
        <a href="#" class="pull-left">
          <img
            src="https://bootdey.com/img/Content/user_1.jpg"
            alt=""
            class="img-circle"
          />
        </a>
        <div class="media-body">
          <span class="text-muted pull-right">
            <small class="text-muted">30 min ago</small>
          </span>
          <strong class="text-dark">CHHUM Sina</strong>
          <p>
            Lorem ipsum dolor sit amet...
          </p>
        </div>
      </li>
      <li class="media">
        <a href="#" class="pull-left">
          <img
            src="https://bootdey.com/img/Content/user_1.jpg"
            alt=""
            class="img-circle"
          />
        </a>
        <div class="media-body">
          <span class="text-muted pull-right">
            <small class="text-muted">30 min ago</small>
          </span>
          <strong class="text-dark">CHHUM Sina</strong>
          <p>
            Lorem ipsum dolor sit amet...
          </p>
        </div>
      </li>
      <li class="media">
        <a href="#" class="pull-left">
          <img
            src="https://bootdey.com/img/Content/user_1.jpg"
            alt=""
            class="img-circle"
          />
        </a>
        <div class="media-body">
          <span class="text-muted pull-right">
            <small class="text-muted">30 min ago</small>
          </span>
          <strong class="text-dark">CHHUM Sina</strong>
          <p>
            Lorem ipsum dolor sit amet...
          </p>
        </div>
      </li>
    </ul>
    <br />
    <br />
  </div>
</template>

<script>
export default {
  middleware: 'auth',
  data() {
    return {
      strategy: this.$auth.$storage.getUniversal('strategy')
    }
  },
  mounted() {},
  methods: {
    async logout() {
      await this.$auth.logout()
    }
  }
}
</script>

<style scoped>
.messages ul.media-list {
  padding: 0;
  margin-top: 65px;
  box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
}

.messages .media-list li img {
  border-radius: 50%;
  width: 48px;
  margin-right: 10px;
}

.messages .media-list li:first-child {
  border-top: 1px solid #ddd;
}
.messages .media-list li {
  display: flex;
  align-items: flex-start;
  border-bottom: 1px solid #efefef;
  padding-top: 15px;
  background: #fff;
  padding-left: 15px;
  padding-right: 15px;
}
</style>
