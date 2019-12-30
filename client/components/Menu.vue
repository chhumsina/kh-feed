<template>
  <nav class="menu menu-icon-top menu-expand-lg">
    <ul class="menu-nav mr-auto">
      <nuxt-link to="/book">
        <li class="nav-item">
          <i class="fa fa-book" aria-hidden="true"></i>
          <p>Book</p>
        </li>
      </nuxt-link>
      <nuxt-link to="/feed">
        <li class="nav-item">
          <i class="fa fa-rss" aria-hidden="true"></i>
          <p>Feed</p>
        </li>
      </nuxt-link>
<!--      <nuxt-link to="/save">-->
<!--        <li class="nav-item">-->
<!--          <i class="fa fa-download" aria-hidden="true"></i>-->
<!--          <p>Save</p>-->
<!--        </li>-->
<!--      </nuxt-link>-->
      <nuxt-link to="/i-need">
        <li class="nav-item">
          <i class="fa fa-smile-o" aria-hidden="true"></i>
          <p>INeed</p>
        </li>
      </nuxt-link>
      <nuxt-link to="/activity">
        <li class="nav-item">
          <i class="fa fa-tasks" aria-hidden="true"></i>
          <p>Activity</p>
        </li>
      </nuxt-link>
      <nuxt-link to="/account">
        <li class="nav-item">
          <i class="fa fa-user-circle" aria-hidden="true"></i>
          <p>Account</p>
        </li>
      </nuxt-link>
    </ul>
  </nav>
</template>

<style scoped>
@import url('//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');

.menu-icon-top .menu-nav a > .fa {
  position: relative;
  width: 36px;
  font-size: 24px;
}

.menu-icon-top.menu-expand-lg .menu-nav a {
  text-align: center;
  display: table-footer-group;
  height: 70px;
  vertical-align: middle;
  padding-top: 0;
  font-size: 12px;
  padding-bottom: 0;
  color: #5f5e5e;
  padding-left: 8px;
  padding-right: 8px;
  text-decoration: none;
}
.menu-icon-top.menu-expand-lg .menu-nav a:active{
  text-decoration: none;
}

.menu-icon-top.menu-expand-lg .menu-nav a.nuxt-link-active {
  color: #006fe2;
}

.menu-icon-top.menu-expand-lg .menu-nav a .fa {
  display: block;
  width: 48px;
  font-size: 18px;
  margin: 2px auto -1px auto;
  top: 0;
  line-height: 24px;
}

.menu-icon-top.menu-expand-lg .menu-nav a p {
  font-size: 12px;
}

.menu-nav {
  display: contents;
}

.menu {
  position: fixed;
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  padding: 0px;
  bottom: 0;
  width: 100%;
  height: 48px;
  box-shadow: 0 1px 3px;
  background: #fff;
  z-index: 2;
}

.menu li {
  list-style: none;
  padding: 0px;
  padding-top: 2px;
  padding-bottom: 0;
}
</style>
